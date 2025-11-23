<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Repair;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RepairController extends Controller
{
    // student gửi yêu cầu sửa chữa
    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id' => 'nullable|exists:rooms,id',
            'description' => 'required|string',
            'image_path' => 'nullable|string',
        ]);

        $user = $request->user()->loadMissing('activeBooking');
        $roomId = $data['room_id'] ?? $user->activeBooking?->room_id;

        if (!$roomId) {
            throw ValidationException::withMessages([
                'room_id' => __('Bạn chưa chọn phòng và không có hợp đồng hoạt động.'),
            ]);
        }

        $repair = Repair::create([
            'user_id' => $user->id,
            'room_id' => $roomId,
            'description' => $data['description'],
            'image_path' => $data['image_path'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json($repair->load('room'), 201);
    }

    // student xem yêu cầu của mình
    public function myRepairs(Request $request)
    {
        $perPage = min($request->integer('per_page', 10) ?? 10, 50);

        return response()->json(
            Repair::with('room')
                ->where('user_id', $request->user()->id)
                ->orderByDesc('id')
                ->paginate($perPage)
        );
    }

    // staff/admin quản lý yêu cầu
    public function index(Request $request)
    {
        $perPage = min($request->integer('per_page', 10) ?? 10, 50);

        return response()->json(
            Repair::with(['user', 'room.floor.branch'])
                ->when($request->filled('status'), fn($q, $status) => $q->where('status', $status))
                ->orderByDesc('id')
                ->paginate($perPage)
        );
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'in:pending,in_progress,completed,cancelled',
            'assigned_to' => 'nullable|exists:users,id',
            'completed_at' => 'nullable|date',
        ]);

        $repair = Repair::findOrFail($id);
        $repair->update($data);

        return response()->json($repair->load(['user', 'room']));
    }
}
