<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favourite;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    public function add(Request $request, $roomId)
    {
        $user = $request->user();

        $existing = Favourite::where('user_id', $user->id)
            ->where('room_id', $roomId)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Phòng đã được yêu thích trước đó',
            ], 422);
        }

        Favourite::create([
            'user_id' => $user->id,
            'room_id' => $roomId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm phòng vào yêu thích',
        ]);
    }

    public function remove(Request $request, $roomId)
    {
        $user = $request->user();

        $favourite = Favourite::where('user_id', $user->id)
            ->where('room_id', $roomId)
            ->first();

        if (!$favourite) {
            return response()->json([
                'success' => false,
                'message' => 'Phòng chưa được yêu thích',
            ], 404);
        }

        $favourite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa phòng khỏi yêu thích',
        ]);
    }

    public function index()
    {
        $favourites = Favourite::with(['room.floor.branch', 'room.amenities', 'room.images'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $rooms = $favourites->map(function ($favourite) {
            $room = $favourite->room;
            $room->loadAvg('reviews', 'rating');
            return $this->formatRoomData($room);
        });

        return response()->json([
            'success' => true,
            'data' => $rooms
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id'
        ]);

        $favourite = Favourite::firstOrCreate([
            'user_id' => Auth::id(),
            'room_id' => $request->room_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào danh sách yêu thích',
            'data' => $favourite
        ], 201);
    }

    public function destroy(Room $room)
    {
        Favourite::where('user_id', Auth::id())
            ->where('room_id', $room->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa khỏi danh sách yêu thích'
        ]);
    }

    private function formatRoomData(Room $room)
    {
        return [
            'id' => $room->id,
            'room_code' => $room->room_code,
            'branch_name' => $room->floor->branch->name,
            'floor_number' => $room->floor->floor_number,
            'price_per_month' => $room->price_per_month,
            'capacity' => $room->capacity,
            'current_occupancy' => $room->current_occupancy,
            'average_rating' => round($room->reviews_avg_rating ?? 0, 1),
            'main_image' => $room->images->first()->image_path ?? null,
            'amenities' => $room->amenities->map(fn($a) => ['id' => $a->id, 'name' => $a->name])
        ];
    }
}
