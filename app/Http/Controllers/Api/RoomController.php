<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $start = microtime(true);

        $perPage = min($request->integer('per_page', 10) ?? 10, 50);

        $rooms = Room::with(['floor.branch', 'image'])
            ->where('is_active', DB::raw('true'))
            ->filter($request->all())
            ->paginate($perPage);

        $afterQuery = microtime(true);

        Log::info('rooms index timing', [
            'query_ms' => ($afterQuery - $start) * 1000,
        ]);

        return response()->json(
            $rooms->through(function ($room) {
                $room->available_slots = max($room->capacity - $room->current_occupancy, 0);
                return $room;
            })
        );
    }

    public function myRoom(Request $request)
    {
        $user = $request->user()->loadMissing('activeBooking');
        $booking = $user->activeBooking;

        if (!$booking) {
            return response()->json(['message' => __('Bạn chưa có phòng đang ở')], 404);
        }

        $room = Room::with([
            'floor.branch',
            'images',
            'amenities',
            'services',
            'activeBookings.user.student',
        ])->findOrFail($booking->room_id);

        $room->available_slots = max($room->capacity - $room->current_occupancy, 0);
        $room->roommates = $room->activeBookings->map(function ($b) {
            return [
                'id' => $b->user->id,
                'name' => $b->user->name,
                'email' => $b->user->email,
                'avatar' => $b->user->avatar,
                'student_code' => $b->user->student->student_code ?? null,
            ];
        })->values();

        return response()->json($room);
    }

    public function show($id)
    {
        $room = Room::with(['floor.branch', 'images', 'amenities', 'services', 'reviews.user'])
            ->findOrFail($id);
        return response()->json($room);
    }

    public function toggleFavourite(Request $request, $roomId)
    {
        $user = $request->user();

        $existing = $user->favourites()->where('room_id', $roomId)->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['message' => 'Đã bỏ yêu thích'], 200);
        }

        $user->favourites()->create(['room_id' => $roomId]);
        return response()->json(['message' => 'Đã thêm vào yêu thích'], 201);
    }


    public function addReview(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $data = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $user = $request->user();

        $review = Review::updateOrCreate(
            [
                'user_id' => $user->id,
                'room_id' => $room->id,
            ],
            [
                'rating'  => $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]
        );

        $status = $review->wasRecentlyCreated ? 201 : 200;

        return response()->json([
            'message' => $review->wasRecentlyCreated
                ? 'Đã tạo đánh giá mới.'
                : 'Đã cập nhật đánh giá.',
            'data' => $review->load('user'),
        ], $status);
    }

    public function myFavourites(Request $request)
    {
        $user = $request->user();

        $rooms = Room::with(['floor.branch', 'image'])
            ->whereHas('favourites', fn($q) => $q->where('user_id', $user->id))
            ->paginate(10);

        return response()->json($rooms);
    }

    // staff/admin
    public function store(Request $request)
    {
        $data = $request->validate([
            'room_code' => 'required|string',
            'floor_id' => 'required|integer|exists:floors,id',
            'price_per_day' => 'required|numeric',
            'price_per_month' => 'required|numeric',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);
        $room = Room::create($data + ['is_active' => true, 'current_occupancy' => 0]);
        return response()->json($room, 201);
    }

    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        $room->update($request->only(['price_per_day', 'price_per_month', 'capacity', 'description', 'is_active']));
        return response()->json($room);
    }

    public function destroy($id)
    {
        Room::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
