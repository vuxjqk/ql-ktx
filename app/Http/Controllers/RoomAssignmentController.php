<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\RoomAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class RoomAssignmentController extends Controller
{
    public function index(User $user)
    {
        $user->load(['student', 'roomAssignments.room.branch']);

        return view('room_assignments.index', compact('user'));
    }

    public function show(User $user, RoomAssignment $roomAssignment)
    {
        $user->load(['student']);
        $roomAssignment->load(['room.branch', 'bills.transactions']);
        return view('room_assignments.show', ['user' => $user, 'assignment' => $roomAssignment]);
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
            'code' => $this->generateCode(),
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

    private function generateCode()
    {
        $date = now()->format('ymdHis');
        $countToday = Bill::whereDate('created_at', today())->count();
        return 'HD' . $date . str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);
    }
}
