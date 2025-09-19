<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::select('id', 'room_code', 'block', 'floor', 'gender_type', 'capacity', 'current_occupancy')
            ->filter($request->all());

        $rooms = $query->paginate(10)->appends($request->query());
        $totalRooms = Room::count();

        $blocks = Room::select('block')
            ->distinct()
            ->pluck('block')
            ->mapWithKeys(fn($block) => [$block => 'Khu ' . $block]);

        $floors = Room::select('floor')
            ->distinct()
            ->pluck('floor')
            ->mapWithKeys(fn($floor) => [$floor => 'Tầng ' . $floor]);

        return view('rooms.index', compact('rooms', 'totalRooms', 'blocks', 'floors'));
    }

    public function create()
    {
        return view('rooms.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'room_code'         => 'required|string|max:20|unique:rooms,room_code',
            'block'             => 'required|string|size:1',
            'floor'             => 'required|integer|min:0',
            'gender_type'       => 'required|in:male,female,mixed',
            'price_per_month'   => 'required|numeric|min:0',
            'capacity'          => 'required|integer|min:1',
            'current_occupancy' => 'nullable|integer|min:0|lte:capacity',
            'description'       => 'nullable|string',
        ];

        $validated = $request->validate($rules);

        Room::create($validated);

        return redirect()->route('rooms.index')->with('success', 'Phòng đã được tạo thành công');
    }

    public function edit(Room $room)
    {
        return view('rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $rules = [
            'room_code'         => 'required|string|max:20|unique:rooms,room_code,' . $room->id,
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
