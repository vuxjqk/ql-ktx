<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Room;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::select('id', 'branch_id', 'room_code', 'block', 'floor', 'gender_type', 'capacity', 'current_occupancy')
            ->with(['branch'])
            ->filter($request->all());

        $rooms = $query->paginate(10)->appends($request->query());

        $totalRooms = Room::count();
        $fullRooms = Room::whereRaw('current_occupancy >= capacity')->count();
        $emptyRooms = Room::whereRaw('current_occupancy = 0')->count();
        $missingRooms = Room::whereRaw('current_occupancy < capacity AND current_occupancy > 0')->count();

        $blocks = Room::select('block')
            ->distinct()
            ->pluck('block')
            ->mapWithKeys(fn($block) => [$block => 'Khu ' . $block]);

        $floors = Room::select('floor')
            ->distinct()
            ->pluck('floor')
            ->mapWithKeys(fn($floor) => [$floor => 'Tầng ' . $floor]);

        $branches = Branch::pluck('name', 'id')->toArray();

        return view('rooms.index', compact(
            'rooms',
            'totalRooms',
            'fullRooms',
            'emptyRooms',
            'missingRooms',
            'blocks',
            'floors',
            'branches'
        ));
    }

    public function create()
    {
        $branches = Branch::pluck('name', 'id')->toArray();
        return view('rooms.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $rules = [
            'branch_id'         => 'required|exists:branches,id',
            'room_code'         => [
                'required',
                'string',
                'max:20',
                Rule::unique('rooms')->where(function ($query) use ($request) {
                    return $query->where('branch_id', $request->branch_id);
                })
            ],
            'block'             => 'required|string|size:1',
            'floor'             => 'required|integer|min:0',
            'gender_type'       => 'required|in:male,female,mixed',
            'price_per_month'   => 'required|numeric|min:0',
            'capacity'          => 'required|integer|min:1',
            'current_occupancy' => 'required|integer|min:0|lte:capacity',
            'description'       => 'nullable|string',
        ];

        $validated = $request->validate($rules);

        Room::create($validated);

        return redirect()->route('rooms.index')->with('success', 'Phòng đã được tạo thành công');
    }

    public function edit(Room $room)
    {
        $branches = Branch::pluck('name', 'id')->toArray();
        return view('rooms.edit', compact('room', 'branches'));
    }

    public function update(Request $request, Room $room)
    {
        $rules = [
            'branch_id'         => 'required|exists:branches,id',
            'room_code'         => [
                'required',
                'string',
                'max:20',
                Rule::unique('rooms')->where(function ($query) use ($request) {
                    return $query->where('branch_id', $request->branch_id);
                })->ignore($room->id)
            ],
            'block'             => 'required|string|size:1',
            'floor'             => 'required|integer|min:0',
            'gender_type'       => 'required|in:male,female,mixed',
            'price_per_month'   => 'required|numeric|min:0',
            'capacity'          => 'required|integer|min:1',
            'current_occupancy' => 'nullable|integer|min:0|lte:capacity',
            'description'       => 'nullable|string',
        ];

        $validated = $request->validate($rules);

        $room->update($validated);

        return redirect()->route('rooms.index')->with('success', 'Phòng đã được cập nhật thành công');
    }

    public function destroy(Room $room)
    {
        try {
            $room->delete();
            return redirect()->route('rooms.index')->with('success', 'Phòng đã được xoá thành công');
        } catch (QueryException $e) {
            $msg = $e->getCode() === '23000'
                ? 'Không thể xóa phòng này vì đang được sử dụng'
                : 'Đã xảy ra lỗi khi xoá phòng';
            return redirect()->back()->with('error', $msg);
        }
    }
}
