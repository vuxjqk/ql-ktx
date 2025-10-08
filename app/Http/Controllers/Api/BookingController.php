<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function registration(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'rental_type' => 'required|in:daily,monthly',
            'check_in_date' => 'required|date',
            'expected_check_out_date' => 'required|date|after_or_equal:check_in_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $validated = $validator->validated();

        $room = Room::findOrFail($validated['room_id']);

        if ($room->current_occupancy >= $room->capacity) {
            return response()->json([
                'success' => false,
                'message' => __('Phòng đã đủ số lượng người. Vui lòng chọn phòng khác.'),
            ], 400);
        }

        $floorGender = $room->floor->gender_type;
        if ($floorGender !== 'mixed' && $user->gender !== $floorGender) {
            return response()->json([
                'success' => false,
                'message' => __('Giới tính của bạn không phù hợp với quy định tầng này.'),
            ], 400);
        }

        $hasPending = Booking::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return response()->json([
                'success' => false,
                'message' => __('Bạn đang có một yêu cầu đặt phòng đang chờ phê duyệt. Vui lòng chờ trước khi tạo yêu cầu mới.'),
            ], 400);
        }

        $newBooking = Booking::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'booking_type' => 'registration',
            'rental_type' => $validated['rental_type'],
            'check_in_date' => $validated['check_in_date'],
            'expected_check_out_date' => $validated['expected_check_out_date'],
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Yêu cầu đặt phòng đã được gửi. Vui lòng chờ quản trị viên phê duyệt.'),
            'data' => $newBooking,
        ]);
    }

    public function transfer(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'new_room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'expected_check_out_date' => 'required|date|after_or_equal:check_in_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $validated = $validator->validated();

        $originalBooking = Booking::where('id', $validated['booking_id'])
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$originalBooking) {
            return response()->json([
                'success' => false,
                'message' => __('Không tìm thấy booking hợp lệ để chuyển phòng.'),
            ], 404);
        }

        $hasPending = Booking::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return response()->json([
                'success' => false,
                'message' => __('Bạn đang có một yêu cầu đang chờ duyệt. Vui lòng chờ trước khi chuyển phòng.'),
            ], 400);
        }

        $newRoom = Room::findOrFail($validated['new_room_id']);
        if ($newRoom->current_occupancy >= $newRoom->capacity) {
            return response()->json([
                'success' => false,
                'message' => __('Phòng mới đã đủ người. Vui lòng chọn phòng khác.'),
            ], 400);
        }

        if ($newRoom->floor->gender_type !== 'mixed' && $user->gender !== $newRoom->floor->gender_type) {
            return response()->json([
                'success' => false,
                'message' => __('Giới tính của bạn không phù hợp với phòng mới.'),
            ], 400);
        }

        $transferBooking = Booking::create([
            'user_id' => $user->id,
            'room_id' => $newRoom->id,
            'booking_id' => $originalBooking->id,
            'booking_type' => 'transfer',
            'rental_type' => $originalBooking->rental_type,
            'check_in_date' => $validated['check_in_date'],
            'expected_check_out_date' => $validated['expected_check_out_date'],
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Yêu cầu chuyển phòng đã được gửi. Vui lòng chờ duyệt.'),
            'data' => $transferBooking,
        ]);
    }

    public function extension(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'expected_check_out_date' => 'required|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $validated = $validator->validated();

        $originalBooking = Booking::where('id', $validated['booking_id'])
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$originalBooking) {
            return response()->json([
                'success' => false,
                'message' => __('Không tìm thấy booking hợp lệ để gia hạn.'),
            ], 404);
        }

        if ($originalBooking->expected_check_out_date >= $validated['expected_check_out_date']) {
            return response()->json([
                'success' => false,
                'message' => __('Ngày gia hạn phải sau ngày kết thúc hiện tại.'),
            ], 400);
        }

        $hasPending = Booking::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return response()->json([
                'success' => false,
                'message' => __('Bạn đang có một yêu cầu đang chờ duyệt. Vui lòng đợi xét duyệt trước khi gửi yêu cầu gia hạn.'),
            ], 400);
        }

        $extensionBooking = Booking::create([
            'user_id' => $user->id,
            'room_id' => $originalBooking->room_id,
            'booking_id' => $originalBooking->id,
            'booking_type' => 'extension',
            'rental_type' => $originalBooking->rental_type,
            'check_in_date' => $originalBooking->check_in_date,
            'expected_check_out_date' => $validated['expected_check_out_date'],
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Yêu cầu gia hạn đã được gửi. Vui lòng chờ duyệt.'),
            'data' => $extensionBooking,
        ]);
    }
}
