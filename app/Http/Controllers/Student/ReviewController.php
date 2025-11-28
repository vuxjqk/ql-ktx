<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function reviewRoom(Request $request, Room $room)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = Review::where('user_id', $user->id)
            ->where('room_id', $room->id)
            ->first();

        if ($review) {
            $review->update($validated);
            $status = 'updated';
        } else {
            $review = Review::create([
                'user_id' => $user->id,
                'room_id' => $room->id,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null
            ]);
            $status = 'created';
        }

        return response()->json(['status' => $status]);
    }
}
