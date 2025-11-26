<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::with('floor.branch')
            ->filter($request->all())
            ->paginate(10)
            ->appends($request->query());

        $totalRooms = Room::count();
        $fullRooms = Room::whereRaw('current_occupancy >= capacity')->count();
        $emptyRooms = Room::whereRaw('current_occupancy = 0')->count();
        $missingRooms = Room::whereRaw('current_occupancy < capacity AND current_occupancy > 0')->count();

        $branches = Branch::pluck('name', 'id')->toArray();

        return view('student.rooms.index', compact(
            'rooms',
            'totalRooms',
            'fullRooms',
            'emptyRooms',
            'missingRooms',
            'branches'
        ));
    }

    public function show(Room $room)
    {
        $room->load(['floor.branch', 'images', 'services', 'amenities', 'activeBookings'])
            ->loadCount('favourites')
            ->loadAvg('reviews', 'rating');

        $userReview = Auth::user()->review;
        $reviews = $room->reviews()->paginate(10);

        $similarRooms = $room->floor->rooms()->where('id', '!=', $room->id)->take(3)->get();

        return view('student.rooms.show', compact('room', 'userReview', 'reviews', 'similarRooms'));
    }
}
