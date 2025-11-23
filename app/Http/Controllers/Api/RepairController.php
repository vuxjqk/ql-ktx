<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Repair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RepairController extends Controller
{
    // student gửi yêu cầu sửa chữa
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'nullable|exists:rooms,id',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'image_path' => 'nullable|string', // để tương thích API cũ
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user()->loadMissing('activeBooking');

        $roomId = $request->room_id ?? $user->activeBooking?->room_id;

        if (!$roomId) {
            throw ValidationException::withMessages([
                'room_id' => __('Bạn chưa chọn phòng và không có hợp đồng hoạt động.'),
            ]);
        }

        $data = [
            'user_id' => $user->id,
            'room_id' => $roomId,
            'description' => $request->description,
            'status' => 'pending',
        ];

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('repairs', 'public');
        } elseif ($request->image_path) {
            $data['image_path'] = $request->image_path;
        }

        $repair = Repair::create($data);

        return response()->json($repair->load('room'), 201);
    }

    // student xem yêu cầu của mình
    public function myRepairs(Request $request)
    {
        $perPage = min($request->integer('per_page', 10) ?? 10, 50);

        return response()->json(
            Repair::with('room')
                ->where('user_id', $request->user()->id)
                ->orderByDesc('id')
                ->paginate($perPage)
        );
    }

    // staff/admin quản lý yêu cầu
    public function index(Request $request)
    {
        $perPage = min($request->integer('per_page', 10) ?? 10, 50);

        return response()->json(
            Repair::with(['user', 'room.floor.branch'])
                ->when($request->filled('status'), fn($q, $status) => $q->where('status', $status))
                ->orderByDesc('id')
                ->paginate($perPage)
        );
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'in:pending,in_progress,completed,cancelled',
            'assigned_to' => 'nullable|exists:users,id',
            'completed_at' => 'nullable|date',
        ]);

        $repair = Repair::findOrFail($id);
        $repair->update($data);

        return response()->json($repair->load(['user', 'room']));
    }

    public function destroy(Request $request, Repair $repair)
    {
        if ($repair->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xoá báo cáo này.',
            ], 403);
        }

        if ($repair->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xoá báo cáo này.',
            ], 403);
        }

        if ($repair->image_path) {
            Storage::disk('public')->delete($repair->image_path);
        }

        $repair->delete();

        return response()->json([
            'success' => true,
            'message' => 'Báo cáo đã được xoá.',
        ], 200);
    }
}
