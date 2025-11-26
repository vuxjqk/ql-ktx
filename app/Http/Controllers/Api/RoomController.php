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
        $rooms = Room::with(['floor.branch', 'image'])
            ->withCount('favourites')
            ->withAvg('reviews', 'rating')
            ->filter($request->all())
            ->paginate(12)
            ->appends($request->query());

        $totalRooms = Room::count();
        $fullRooms = Room::whereRaw('current_occupancy >= capacity')->count();
        $emptyRooms = Room::whereRaw('current_occupancy = 0')->count();
        $missingRooms = Room::whereRaw('current_occupancy < capacity AND current_occupancy > 0')->count();

        $branches = Branch::pluck('name', 'id')->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'rooms' => $rooms->items(),
                'pagination' => [
                    'current_page' => $rooms->currentPage(),
                    'last_page' => $rooms->lastPage(),
                    'per_page' => $rooms->perPage(),
                    'total' => $rooms->total(),
                ],
                'statistics' => [
                    'total_rooms' => $totalRooms,
                    'full_rooms' => $fullRooms,
                    'empty_rooms' => $emptyRooms,
                    'missing_rooms' => $missingRooms,
                ],
                'branches' => $branches,
            ]
        ]);
    }

    public function show(Room $room)
    {
        $room->load(['floor.branch', 'images', 'services', 'amenities'])
            ->loadCount('favourites')
            ->loadCount('reviews')
            ->loadAvg('reviews', 'rating');

        return response()->json([
            'success' => true,
            'data' => [
                'room' => $room
            ]
        ]);
    }
}
