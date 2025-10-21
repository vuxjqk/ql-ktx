<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with(['user.student', 'room.floor.branch'])
            ->filter($request->all())
            ->paginate(10)
            ->appends($request->query());

        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $activeBookings = Booking::where('status', 'active')->count();
        $expiredBookings = Booking::whereIn('status', ['expired', 'terminated'])->count();

        return view('bookings.index', compact(
            'bookings',
            'totalBookings',
            'pendingBookings',
            'activeBookings',
            'expiredBookings'
        ));
    }

    public function show(Booking $booking)
    {
        $booking->load(['contract', 'user', 'room.floor.branch', 'processedBy']);
        return view('bookings.show', compact('booking'));
    }

    public function update(Request $request, Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return redirect()->back()->with('warning', __('Mục này đã được xử lý trước đó'));
        }

        $validated = $request->validateWithBag('bookingUpdation', [
            'status' => 'required|in:approved,rejected',
            'reason' => 'nullable|string',
        ]);

        if ($validated['status'] === 'approved') {
            $status = 'phê duyệt';
        } else {
            $status = 'từ chối';
            if ($booking->room->current_occupancy > 0) {
                $booking->room->decrement('current_occupancy');
            }
        }

        $booking->update([
            'status' => $validated['status'],
            'reason' => $validated['reason'],
            'processed_at' => now(),
            'processed_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', __('Đã :status thành công', ['status' => $status]));
    }

    public function terminateBooking(Request $request, Booking $booking)
    {
        $validated = $request->validateWithBag('bookingUpdation', [
            'reason' => 'nullable|string',
        ]);

        if ($booking->status !== 'active') {
            return redirect()->back()
                ->with('error', __('Trạng thái hiện tại không hợp lệ: :status', ['status' => $booking->status]));
        }

        $booking->update([
            'actual_check_out_date' => now(),
            'status' => 'terminated',
            'reason' => $validated['reason'],
            'processed_at' => now(),
            'processed_by' => Auth::id(),
        ]);

        if ($booking->room->current_occupancy > 0) {
            $booking->room->decrement('current_occupancy');
        }

        return redirect()->back()->with('success', __('Đã chấm dứt hợp đồng thành công'));
    }
}
