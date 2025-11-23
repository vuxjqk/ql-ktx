<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $start = microtime(true);

        $perPage = min($request->integer('per_page', 10) ?? 10, 50);

        $rooms = Room::with(['floor.branch', 'image'])
            ->where('is_active', DB::raw('true'))
            ->when(
                $request->filled('q'),
                fn($q) => $q->where('room_code', 'like', '%' . $request->q . '%')
            )
            ->when(
                $request->filled('branch_id'),
                fn($q, $branchId) =>
                $q->whereHas(
                    'floor',
                    fn($sub) =>
                    $sub->where('branch_id', $branchId)
                )
            )
            ->when(
                $request->filled('gender_type'),
                fn($q, $gender) =>
                $q->whereHas(
                    'floor',
                    fn($sub) =>
                    $sub->where('gender_type', $gender)
                )
            )
            ->when(
                $request->filled('min_price'),
                fn($q, $min) => $q->where('price_per_month', '>=', $min)
            )
            ->when(
                $request->filled('max_price'),
                fn($q, $max) => $q->where('price_per_month', '<=', $max)
            )
            ->when(
                $request->filled('capacity'),
                fn($q, $capacity) => $q->where('capacity', '>=', $capacity)
            )
            ->when(
                $request->boolean('available_only'),
                fn($q) => $q->whereColumn('current_occupancy', '<', 'capacity')
            )
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

    public function show($id)
    {
        $room = Room::with(['floor.branch', 'images', 'amenities', 'services', 'reviews'])
            ->findOrFail($id);
        return response()->json($room);
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
            'data' => $review,
        ], $status);
    }
}
