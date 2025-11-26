<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $currentBooking = $user->bookings()->latest()->first();
        $room = $currentBooking?->room;
        $contract = $currentBooking?->contract;
        $repairs = $user->repairs->where('room_id', $room?->id);
        $bills = $currentBooking?->bills;
        $transactions = [];

        return view('student.bookings.index', compact('currentBooking', 'room', 'contract', 'repairs', 'bills', 'transactions'));
    }

    public function store(Request $request, Room $room)
    {
        $request->validate([
            'rental_type' => 'required|in:monthly,daily',
            'start_date'  => 'required|date|after_or_equal:today|before_or_equal:' . now()->addDays(7)->toDateString(),
            'duration'    => 'required|integer|min:1',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->bookings()->whereIn('status', ['pending', 'approved'])->exists()) {
            return redirect()->back()->with('error', 'Bạn đã có yêu cầu đặt phòng khác');
        }

        if ($room->current_occupancy >= $room->capacity) {
            return redirect()->back()->with('error', 'Phòng đã đầy');
        }

        if ($room->floor->gender_type !== 'mixed' && optional($user->student)->gender !== $room->floor->gender_type) {
            return redirect()->back()->with('error', 'Giới tính không phù hợp với khu');
        }

        $duration = (int) $request->duration;

        $startDate = Carbon::parse($request->start_date);

        $expectedCheckOut = $request->rental_type === 'daily'
            ? $startDate->clone()->addDays($duration)
            : $startDate->clone()->addMonthsNoOverflow($duration);

        return DB::transaction(function () use ($request, $user, $room, $startDate, $expectedCheckOut) {
            $activeBooking = $user->bookings()->where('status', 'active')->first();

            Booking::create([
                'user_id'                 => $user->id,
                'room_id'                 => $room->id,
                'booking_id'              => $activeBooking?->id,
                'booking_type'            => $activeBooking ? 'transfer' : 'registration',
                'rental_type'             => $request->rental_type,
                'check_in_date'           => $startDate,
                'expected_check_out_date' => $expectedCheckOut,
                'status'                  => 'pending',
            ]);

            $room->increment('current_occupancy');

            return redirect()->back()->with('success', 'Đăng ký phòng thành công');
        });
    }

    public function terminate(Request $request, Booking $booking)
    {
        // Chỉ cho phép chấm dứt booking đang active của chính sinh viên đó
        if ($booking->user_id !== auth()->id() || $booking->status !== 'active') {
            abort(403);
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $booking->update([
            'actual_check_out_date' => now()->toDateString(), // hoặc Carbon::today()
            'reason'               => $request->reason,
        ]);

        // Có thể thêm event, thông báo cho admin, tạo hóa đơn hoàn tiền, v.v...

        return redirect()->back()->with('success', 'Hợp đồng đã được chấm dứt thành công.');
    }
}
