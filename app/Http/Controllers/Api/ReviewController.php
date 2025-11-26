<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function upsert(Request $request, $roomId)
    {
        $user = $request->user();

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $review = Review::updateOrCreate(
            [
                'user_id' => $user->id,
                'room_id' => $roomId,
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'review' => $review,
            'message' => 'Đánh giá đã được lưu thành công',
        ]);
    }

    public function index(Room $room)
    {
        $reviews = Review::with('user')
            ->where('room_id', $room->id)
            ->latest()
            ->get();

        $data = $reviews->map(function ($review) {
            return [
                'id' => $review->id,
                'user_name' => $review->user->name,
                'user_avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($review->user->name) . '&background=random',
                'rating' => $review->rating,
                'comment' => $review->comment,
                'created_at' => $review->created_at->format('d/m/Y H:i'),
                'is_owner' => $review->user_id === Auth::id()
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(Request $request, Room $room)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        // Check if user already reviewed this room
        $existingReview = Review::where('user_id', Auth::id())
            ->where('room_id', $room->id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã đánh giá phòng này rồi'
            ], 422);
        }

        $review = Review::create([
            'user_id' => Auth::id(),
            'room_id' => $room->id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đánh giá đã được gửi thành công',
            'data' => $review
        ], 201);
    }

    public function destroy(Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa đánh giá này'
            ], 403);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa đánh giá'
        ]);
    }
}
