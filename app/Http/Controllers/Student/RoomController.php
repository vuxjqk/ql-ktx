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
        $rooms = Room::with([
            'floor.branch',
            'images' => fn($q) => $q->limit(1),
            'favourites',
            'amenities'
        ])
            ->withAvg('reviews', 'rating')
            ->filter($request->all())
            ->paginate(10)
            ->appends($request->query());

        $branches = Branch::pluck('name', 'id')->toArray();

        return view('student.rooms.index', compact('rooms', 'branches'));
    }

    public function show(Room $room)
    {
        $room->load(['floor.branch', 'images', 'favourites', 'services', 'amenities'])
            ->loadCount(['favourites', 'reviews'])
            ->loadAvg('reviews', 'rating');

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $userReview = $user?->reviews()->where('room_id', $room->id)->first();
        $reviews = $room->reviews()->with(['user'])->paginate(10);

        $similarRooms = $room->floor->rooms()
            ->with([
                'floor.branch',
                'images' => fn($q) => $q->limit(1),
                'favourites',
                'amenities',
            ])
            ->withAvg('reviews', 'rating')
            ->where('id', '!=', $room->id)
            ->take(3)
            ->get();

        return view('student.rooms.show', compact('room', 'userReview', 'reviews', 'similarRooms'));
    }
}
