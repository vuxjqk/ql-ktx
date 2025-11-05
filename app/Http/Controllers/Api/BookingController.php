<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // staff/admin duyệt/quan sát
    public function index(Request $request)
    {
        $bookings = Booking::with(['user', 'room'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('id')->paginate(10);
        return response()->json($bookings);
    }

    // student tự tạo booking
    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'booking_type' => 'required|in:registration,transfer,extension',
            'rental_type' => 'required|in:daily,monthly',
            'check_in_date' => 'required|date',
            'expected_check_out_date' => 'required|date|after:check_in_date',
            'reason' => 'nullable|string',
        ]);

        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'room_id' => $data['room_id'],
            'booking_type' => $data['booking_type'],
            'rental_type' => $data['rental_type'],
            'check_in_date' => $data['check_in_date'],
            'expected_check_out_date' => $data['expected_check_out_date'],
            'status' => 'pending',
            'reason' => $data['reason'] ?? null,
        ]);

        return response()->json($booking->load(['user', 'room']), 201);
    }

    // staff/admin cập nhật trạng thái
    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,approved,rejected,active,expired,terminated',
        ]);
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => $data['status'], 'processed_by' => $request->user()->id, 'processed_at' => now()]);
        return response()->json($booking);
    }

    // student xem booking của mình
    public function myBookings(Request $request)
    {
        $list = Booking::with('room')->where('user_id', $request->user()->id)->orderByDesc('id')->paginate(10);
        return response()->json($list);
    }
}
