<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\RoomAssignment;
use Illuminate\Http\Request;

class RoomAssignmentController extends Controller
{
    public function index()
    {
        $assignments = RoomAssignment::with([
            'user:id,name,avatar',
            'user.student:id,user_id,student_code',
            'room:id,room_code',
        ])->paginate(10);

        $totalAssignments = RoomAssignment::count();

        return view('room_assignments.index', compact('assignments', 'totalAssignments'));
    }

    public function show(RoomAssignment $roomAssignment)
    {
        $roomAssignment->load(['user', 'room', 'registration', 'bills']);
        return view('room_assignments.show', ['assignment' => $roomAssignment]);
    }

    public function edit(RoomAssignment $roomAssignment)
    {
        return view('room_assignments.edit', compact('roomAssignment'));
    }

    public function update(Request $request, RoomAssignment $roomAssignment)
    {
        if ($roomAssignment->checked_out_at) {
            return redirect()->route('room_registrations.create')->with('error', "Bạn đã xác nhận hợp đồng này rồi");
        }

        $request->validate(['confirmation' => 'required']);
        $roomAssignment->update(['checked_in_at' => now()]);

        Bill::create([
            'user_id' => $roomAssignment->user_id,
            'room_assignment_id' => $roomAssignment->id,
            'amount' => $roomAssignment->room->price_per_month,
            'due_date' => now()->addDays(7),
        ]);

        return redirect()->route('room_registrations.create')->with('success', "Đã xác nhận hợp đồng thành công");
    }

    public function destroy(RoomAssignment $roomAssignment)
    {
        if ($roomAssignment->checked_in_at) {
            return redirect()->back()->with('error', "Không thể huỷ vì đã xác nhận hợp đồng");
        }

        $roomAssignment->room->decrement('current_occupancy');
        $roomAssignment->registration->delete();
        $roomAssignment->delete();
        return redirect()->back()->with('success', "Đã huỷ phân công phòng thành công");
    }
}
