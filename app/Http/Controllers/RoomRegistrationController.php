<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Room;
use App\Models\RoomAssignment;
use App\Models\RoomRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomRegistrationController extends Controller
{
    public function create(Request $request)
    {
        $user = Auth::user();

        $rooms = Room::with('branch')
            ->filter($request->all())
            ->paginate(10)
            ->appends($request->query());

        $blocks = Room::select('block')
            ->distinct()
            ->pluck('block')
            ->mapWithKeys(fn($block) => [$block => 'Khu ' . $block]);

        $floors = Room::select('floor')
            ->distinct()
            ->pluck('floor')
            ->mapWithKeys(fn($floor) => [$floor => 'Tầng ' . $floor]);

        $branches = Branch::pluck('name', 'id')->toArray();

        $registration = $user->registration;

        return view('room_registrations.create', compact(
            'user',
            'rooms',
            'blocks',
            'floors',
            'branches',
            'registration'
        ));
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

    public function update(Request $request, RoomRegistration $registration)
    {
        if ($registration->status !== 'pending') {
            return redirect()->back()->with('error', 'Mục này đã được xử lý trước đó');
        }

        $validated = $request->validateWithBag('registrationUpdation', [
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string',
        ]);

        if ($validated['status'] === 'approved') {
            $status = 'phê duyệt';
            RoomAssignment::create([
                'user_id' => $registration->user_id,
                'room_id' => $registration->room_id,
                'registration_id' => $registration->id,
            ]);
        } else {
            $status = 'từ chối';
            if ($registration->room->current_occupancy > 0) {
                $registration->room->decrement('current_occupancy');
            }
        }

        $registration->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'],
            'processed_at' => now(),
            'processed_by' => Auth::id(),
        ]);

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
