<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::with(['floor.branch', 'image', 'amenities', 'services'])
            ->when($request->q, fn($q) => $q->where('room_code', 'ilike', "%{$request->q}%"))
            ->when($request->min_price, fn($q) => $q->where('price_per_month', '>=', $request->min_price))
            ->when($request->max_price, fn($q) => $q->where('price_per_month', '<=', $request->max_price))
            ->where('is_active', true)
            ->paginate(10);
        return response()->json($rooms);
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
}
