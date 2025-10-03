<?php

namespace App\Http\Controllers;

use App\Models\Repair;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RepairController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Repair::query();

        // Lọc theo vai trò: sinh viên chỉ thấy yêu cầu của mình, quản lý thấy tất cả
        if (Auth::user()->role === 'student') {
            $query->where('user_id', Auth::id());
        }

        // Lọc theo trạng thái
        if ($request->has('status') && in_array($request->status, ['open', 'in_progress', 'resolved', 'closed'])) {
            $query->where('status', $request->status);
        }

        // Lọc theo loại sửa chữa
        if ($request->has('type') && in_array($request->type, ['electric', 'water', 'furniture', 'other'])) {
            $query->where('type', $request->type);
        }

        $repairs = $query->with(['user', 'room', 'assignedTo'])
            ->orderBy('reported_at', 'desc')
            ->paginate(15);

        return view('repairs.index', compact('repairs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Chỉ sinh viên được tạo yêu cầu
        if (Auth::user()->role !== 'student') {
            return redirect()->route('repairs.index')->withErrors('Bạn không có quyền tạo yêu cầu sửa chữa.');
        }

        $rooms = Room::all();
        return view('repairs.create', compact('rooms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Chỉ sinh viên được tạo yêu cầu
        if (Auth::user()->role !== 'student') {
            return redirect()->route('repairs.index')->withErrors('Bạn không có quyền tạo yêu cầu sửa chữa.');
        }

        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'room_id'     => 'required|exists:rooms,id',
            'type'        => 'required|in:electric,water,furniture,other',
            'description' => 'required|string|max:1000',
            'photo'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Giới hạn file ảnh
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Xử lý upload ảnh
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('repair_photos', 'public');
        }

        // Tạo yêu cầu sửa chữa mới
        $repair = Repair::create([
            'user_id'     => Auth::id(),
            'room_id'     => $request->room_id,
            'type'        => $request->type,
            'description' => $request->description,
            'photo_url'   => $photoPath ? Storage::url($photoPath) : null,
            'status'      => 'open',
            'reported_at' => now(),
        ]);

        return redirect()->route('repairs.index')->with('success', 'Gửi yêu cầu sửa chữa thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Repair $repair)
    {
        // Kiểm tra quyền xem: sinh viên chỉ xem yêu cầu của mình, quản lý xem tất cả
        if (Auth::user()->role === 'student' && $repair->user_id !== Auth::id()) {
            return redirect()->route('repairs.index')->withErrors('Bạn không có quyền xem yêu cầu này.');
        }

        return view('repairs.show', compact('repair'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Repair $repair)
    {
        // Chỉ sinh viên được chỉnh sửa yêu cầu của mình và chỉ khi trạng thái là 'open'
        if (Auth::user()->role !== 'student' || $repair->user_id !== Auth::id() || $repair->status !== 'open') {
            return redirect()->route('repairs.index')->withErrors('Bạn không thể chỉnh sửa yêu cầu này.');
        }

        $rooms = Room::all();
        return view('repairs.edit', compact('repair', 'rooms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Repair $repair)
    {
        // Kiểm tra quyền chỉnh sửa
        if (Auth::user()->role === 'student' && ($repair->user_id !== Auth::id() || $repair->status !== 'open')) {
            return redirect()->route('repairs.index')->withErrors('Bạn không thể chỉnh sửa yêu cầu này.');
        }

        $rules = [
            'description' => 'required|string|max:1000',
            'photo'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        // Quản lý có thể cập nhật trạng thái và phân công
        if (Auth::user()->role === 'admin' || Auth::user()->role === 'maintenance') {
            $rules['status'] = 'required|in:open,in_progress,resolved,closed';
            $rules['assigned_to'] = 'nullable|exists:users,id';
            $rules['notes'] = 'nullable|string|max:2000';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Xử lý upload ảnh mới
        $photoPath = $repair->photo_url;
        if ($request->hasFile('photo')) {
            // Xóa ảnh cũ nếu có
            if ($photoPath) {
                Storage::disk('public')->delete(str_replace(Storage::url(''), '', $photoPath));
            }
            $photoPath = $request->file('photo')->store('repair_photos', 'public');
        }

        // Cập nhật thông tin
        $updateData = [
            'description' => $request->description,
            'photo_url'   => $photoPath ? Storage::url($photoPath) : $repair->photo_url,
        ];

        // Quản lý hoặc nhân viên bảo trì có thể cập nhật trạng thái và phân công
        if (Auth::user()->role === 'admin' || Auth::user()->role === 'maintenance') {
            $updateData['status'] = $request->status;
            $updateData['assigned_to'] = $request->assigned_to;
            $updateData['notes'] = $request->notes;
            if ($request->status === 'resolved' && !$repair->resolved_at) {
                $updateData['resolved_at'] = now();
            }
        }

        $repair->update($updateData);

        return redirect()->route('repairs.index')->with('success', 'Cập nhật yêu cầu sửa chữa thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Repair $repair)
    {
        // Chỉ quản lý hoặc sinh viên tạo yêu cầu (khi trạng thái là open) được xóa
        if (Auth::user()->role !== 'admin' && ($repair->user_id !== Auth::id() || $repair->status !== 'open')) {
            return redirect()->route('repairs.index')->withErrors('Bạn không có quyền xóa yêu cầu này.');
        }

        // Xóa ảnh nếu có
        if ($repair->photo_url) {
            Storage::disk('public')->delete(str_replace(Storage::url(''), '', $repair->photo_url));
        }

        $repair->delete();

        return redirect()->route('repairs.index')->with('success', 'Đã xóa yêu cầu sửa chữa.');
    }
}
