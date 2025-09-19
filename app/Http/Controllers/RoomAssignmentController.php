<?php

namespace App\Http\Controllers;

use App\Models\RoomAssignment;
use Illuminate\Http\Request;

class RoomAssignmentController extends Controller
{
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
        return redirect()->route('room_registrations.create')->with('success', "Đã xác nhận hợp đồng thành công");
    }
}
