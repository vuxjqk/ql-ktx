<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
    public function index()
    {
        $favourites = auth()->user()->favouriteRooms()
            ->with(['images', 'floor.branch'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->latest('favourites.created_at')
            ->paginate(12);

        return view('student.favourites.index', compact('favourites'));
    }

    public function destroy(Room $room)
    {
        auth()->user()->favouriteRooms()->detach($room->id);

        // toast('Đã xóa khỏi danh sách yêu thích', 'success');

        return back();
    }
}
