<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::with(['floor.branch', 'image', 'services'])
            ->where('is_active', true)
            ->filter($request->all())
            ->paginate(10)
            ->appends($request->query());

        $totalRooms = Room::count();
        $fullRooms = Room::whereRaw('current_occupancy >= capacity')->count();
        $emptyRooms = Room::whereRaw('current_occupancy = 0')->count();
        $missingRooms = Room::whereRaw('current_occupancy < capacity AND current_occupancy > 0')->count();

        $branches = Branch::pluck('name', 'id')->toArray();

        return response()->json([
            'rooms' => $rooms,
            'totalRooms' => $totalRooms,
            'fullRooms' => $fullRooms,
            'emptyRooms' => $emptyRooms,
            'missingRooms' => $missingRooms,
            'branches' => $branches
        ]);
    }

    public function show(Room $room)
    {
        $room->load(['floor.branch', 'image', 'services']);
        return response()->json(['room' => $room]);
    }
}
