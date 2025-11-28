<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $activeBooking = $user->bookings()
            ->where('status', 'active')
            ->latest()
            ->first();

        $latestBooking = $user->bookings()
            ->latest()
            ->first();

        $newBooking = null;
        if ($latestBooking && in_array($latestBooking->status, ['pending', 'approved'])) {
            $newBooking = $latestBooking;
            $newBooking->load('bills');
        }

        $room = null;
        $contract = null;
        $bills = collect();
        $payments = collect();
        $repairs = collect();

        if ($activeBooking) {
            $activeBooking->load(['bills.bill_items', 'bills.payments.transaction', 'room.floor.branch', 'contract']);

            $room = $activeBooking->room;
            $contract = $activeBooking->contract;
            $bills = $activeBooking->bills;

            $repairs = $user->repairs()->where('room_id', $room->id)->get();
        }

        return view('student.bookings.index', compact(
            'activeBooking',
            'newBooking',
            'room',
            'contract',
            'bills',
            'repairs'
        ));
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
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        if ($booking->user_id !== Auth::id() || $booking->status !== 'active') {
            abort(403, __('Unauthorized'));
        }

        $booking->update([
            'actual_check_out_date' => now(),
            'reason'                => $request->reason,
        ]);

        return redirect()->back()->with('success', 'Đã yêu cầu rời phòng thành công');
    }

    public function extend(Request $request, Booking $booking)
    {
        $request->validate([
            'rental_type' => 'required|in:monthly,daily',
            'duration'    => 'required|integer|min:1',
        ]);

        if ($booking->user_id !== Auth::id() || $booking->status !== 'active') {
            abort(403, __('Unauthorized'));
        }

        $daysRemaining = now()->diffInDays($booking->expected_check_out_date);

        if ($daysRemaining > 7 || $daysRemaining < 0) {
            return redirect()->back()->with('error', 'Bạn chưa thể thực hiện gia hạn');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->bookings()->whereIn('status', ['pending', 'approved'])->exists()) {
            return redirect()->back()->with('error', 'Bạn đã có yêu cầu đặt phòng khác');
        }

        $room = $booking->room;

        if ($room->floor->gender_type !== 'mixed' && optional($user->student)->gender !== $room->floor->gender_type) {
            return redirect()->back()->with('error', 'Giới tính không phù hợp với khu');
        }

        $duration = (int) $request->duration;

        $startDate = Carbon::parse($booking->expected_check_out_date)->addDay();

        $expectedCheckOut = $request->rental_type === 'daily'
            ? $startDate->clone()->addDays($duration)
            : $startDate->clone()->addMonthsNoOverflow($duration);

        return DB::transaction(function () use ($request, $user, $room, $startDate, $expectedCheckOut) {
            $activeBooking = $user->bookings()->where('status', 'active')->first();

            Booking::create([
                'user_id'                 => $user->id,
                'room_id'                 => $room->id,
                'booking_id'              => $activeBooking?->id,
                'booking_type'            => 'extension',
                'rental_type'             => $request->rental_type,
                'check_in_date'           => $startDate,
                'expected_check_out_date' => $expectedCheckOut,
                'status'                  => 'pending',
            ]);

            $room->increment('current_occupancy');

            return redirect()->back()->with('success', 'Đăng ký phòng thành công');
        });
    }

    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            return redirect()->back()->with('error', __('Bạn không có quyền huỷ booking này'));
        }

        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', __('Không thể huỷ booking này'));
        }

        try {
            if ($booking->room->current_occupancy > 0) {
                $booking->room->decrement('current_occupancy');
            }

            $booking->delete();

            return redirect()->back()->with('success', __('Đã huỷ thành công'));
        } catch (QueryException $e) {
            $msg = $e->getCode() === '23000'
                ? __('Không thể huỷ vì có dữ liệu liên quan')
                : __('Đã xảy ra lỗi khi huỷ');

            return redirect()->back()->with('error', $msg);
        }
    }

    public function history(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = $user->bookings()
            ->with([
                'room.images' => fn($q) => $q->limit(1),
                'room.floor.branch',
                'contract',
                'processedBy'
            ])
            ->latest();

        if ($request->filled('type')) {
            $query->where('booking_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->period === 'this_month') {
            $query->whereMonth('check_in_date', now()->month)
                ->whereYear('check_in_date', now()->year);
        }

        if ($request->period === 'last_month') {
            $query->whereMonth('check_in_date', now()->subMonth()->month)
                ->whereYear('check_in_date', now()->subMonth()->year);
        }

        if ($request->period === 'this_year') {
            $query->whereYear('check_in_date', now()->year);
        }

        $bookings = $query->paginate(10)->appends($request->query());

        $summary = $user->bookings()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return view('student.bookings.history', compact('bookings', 'summary'));
    }
}
