<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favourite;
use Illuminate\Http\Request;

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
}
