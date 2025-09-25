<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomAssignment;
use App\Models\RoomRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomRegistrationController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        $rooms = Room::all();

        $registration = $user->roomRegistration;

        return view('room_registrations.create', compact('user', 'rooms', 'registration'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $room = Room::findOrFail($request->room_id);

        if (RoomRegistration::where('user_id', $user->id)->exists()) {
            return redirect()->back()->with('error', 'Bạn đã đăng ký phòng rồi');
        }

        if ($room->gender_type !== 'mixed' && $user->gender !== $room->gender_type) {
            return redirect()->back()->with('error', 'Giới tính không phù hợp với phòng này');
        }

        if ($room->current_occupancy >= $room->capacity) {
            return redirect()->back()->with('error', 'Phòng đã đầy, không thể đăng ký');
        }

        RoomRegistration::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
        ]);

        $room->increment('current_occupancy');

        return redirect()->back()->with('success', 'Đã đăng ký phòng thành công');
    }

    public function update(Request $request, RoomRegistration $roomRegistration)
    {
        if ($roomRegistration->status !== 'pending') {
            return redirect()->back()->with('error', 'Đơn này đã được xử lý trước đó');
        }

        $validated = $request->validateWithBag('registrationUpdation', [
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string',
        ]);

        if ($validated['status'] === 'approved') {
            RoomAssignment::create([
                'user_id' => $roomRegistration->user_id,
                'room_id' => $roomRegistration->room_id,
                'registration_id' => $roomRegistration->id,
            ]);
        }

        $roomRegistration->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'],
            'processed_at' => now(),
            'processed_by' => Auth::id(),
        ]);

        $status = 'phê duyệt';
        if ($validated['status'] === 'rejected') {
            $status = 'từ chối';
            $roomRegistration->room->decrement('current_occupancy');
        }

        return redirect()->back()->with('success', "Đã $status thành công");
    }

    public function destroy(RoomRegistration $roomRegistration)
    {
        if ($roomRegistration->status === 'approved') {
            return redirect()->back()->with('error', 'Bạn không được phép huỷ đăng ký phòng');
        }

        if ($roomRegistration->status === 'pending') {
            $roomRegistration->room->decrement('current_occupancy');
        }

        $roomRegistration->delete();
        return redirect()->back()->with('success', 'Đã huỷ đăng ký phòng thành công');
    }
}
