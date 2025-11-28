<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $favourites = $user->favouriteRooms()
            ->with([
                'images' => fn($q) => $q->limit(1),
                'floor.branch'
            ])
            ->paginate(10);

        return view('student.favourites.index', compact('favourites'));
    }

    public function toggleFavourite(Room $room)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->favouriteRooms()->where('room_id', $room->id)->exists()) {
            $user->favouriteRooms()->detach($room->id);
            $status = 'removed';
        } else {
            $user->favouriteRooms()->attach($room->id);
            $status = 'added';
        }

        return response()->json(['status' => $status]);
    }
}
