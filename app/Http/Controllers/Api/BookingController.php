<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        $bookings = $user->bookings()
            ->whereIn('status', ['pending', 'approved', 'active'])
            ->with(['room.floor.branch', 'contract'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'bookings' => $bookings,
        ]);
    }

    public function book(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'rental_type' => 'required|in:daily,monthly',
            'check_in_date' => 'required|date|after_or_equal:' . now()->toDateString(),
            'expected_check_out_date' => 'required|date|after:check_in_date',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->rental_type === 'monthly') {
                $checkIn = Carbon::parse($request->check_in_date);
                $checkOut = Carbon::parse($request->expected_check_out_date);

                if ($checkOut->diffInMonths($checkIn) < 1) {
                    $validator->errors()->add(
                        'expected_check_out_date',
                        'Thời gian thuê theo tháng phải lớn hơn 1 tháng.'
                    );
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $existingBooking = Booking::where('user_id', $request->user()->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingBooking) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã đăng ký phòng trước đó và không thể đăng ký thêm.'
            ], 409);
        }

        $room = Room::with('floor')->find($request->room_id);

        if ($room->current_occupancy >= $room->capacity) {
            return response()->json([
                'success' => false,
                'message' => 'Phòng đã đầy, vui lòng chọn phòng khác.'
            ], 409);
        }

        $userStudent = $request->user()->student;
        $userGender = $userStudent->gender ?? null;
        $floorGender = $room->floor->gender_type;

        if ($floorGender !== 'mixed' && $userGender !== $floorGender) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể đặt phòng này do giới tính không phù hợp.'
            ], 403);
        }

        $activeBooking = $request->user()->activeBooking;
        $bookingType = $activeBooking ? 'transfer' : 'registration';

        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'room_id' => $request->room_id,
            'booking_id' => $activeBooking?->id,
            'booking_type' => $bookingType,
            'rental_type' => $request->rental_type,
            'check_in_date' => $request->check_in_date,
            'expected_check_out_date' => $request->expected_check_out_date,
            'status' => 'pending',
        ]);

        $booking->room->increment('current_occupancy');

        return response()->json(['success' => true, 'booking' => $booking]);
    }

    public function extend(Request $request, Booking $booking)
    {
        $validator = Validator::make($request->all(), [
            'expected_check_out_date' => 'required|date|after:' . $booking->expected_check_out_date,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $existingBooking = Booking::where('user_id', $request->user()->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingBooking) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã đăng ký phòng trước đó và không thể đăng ký thêm.'
            ], 409);
        }

        $extensionBooking = Booking::create([
            'user_id' => $request->user()->id,
            'room_id' => $booking->room_id,
            'booking_type' => 'extension',
            'rental_type' => $booking->rental_type,
            'check_in_date' => $booking->check_in_date,
            'expected_check_out_date' => $request->expected_check_out_date,
            'status' => 'pending',
            'booking_id' => $booking->id,
        ]);

        $booking->room->increment('current_occupancy');

        return response()->json(['success' => true, 'booking' => $extensionBooking]);
    }

    public function terminate(Request $request)
    {
        //
    }

    public function destroy(Request $request, Booking $booking)
    {
        if ($request->user()->id !== $booking->user_id) {
            return response()->json([
                'success' => false,
                'message' => __('Bạn không có quyền xoá booking này')
            ], 403);
        }

        if ($booking->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => __('Chỉ có thể xoá booking đang ở trạng thái chờ duyệt')
            ], 400);
        }

        try {
            if ($booking->room->current_occupancy > 0) {
                $booking->room->decrement('current_occupancy');
            }

            $booking->delete();

            return response()->json([
                'success' => true,
                'message' => __('Đã xoá thành công')
            ]);
        } catch (QueryException $e) {
            $msg = $e->getCode() === '23000'
                ? __('Không thể xóa vì có dữ liệu liên quan')
                : __('Đã xảy ra lỗi khi xoá');

            return response()->json([
                'success' => false,
                'message' => $msg
            ], 500);
        }
    }
}
