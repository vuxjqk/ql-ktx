<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Repair;
use Illuminate\Http\Request;

class RepairController extends Controller
{
    // student tạo ticket
    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'description' => 'required|string',
            'image_path' => 'nullable|string',
        ]);
        $repair = Repair::create([
            'user_id' => $request->user()->id,
            'room_id' => $data['room_id'],
            'description' => $data['description'],
            'image_path' => $data['image_path'] ?? null,
            'status' => 'pending',
        ]);
        return response()->json($repair, 201);
    }

    // student xem của mình
    public function myRepairs(Request $request)
    {
        return response()->json(
            Repair::where('user_id', $request->user()->id)->orderByDesc('id')->paginate(10)
        );
    }

    // staff/admin quản lý
    public function index()
    {
        return response()->json(
            Repair::with(['user', 'room'])->orderByDesc('id')->paginate(10)
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
        return response()->json($repair);
    }
}
