<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * Staff/Admin: browse bookings with filters
     */
    public function index(Request $request)
    {
        $perPage = min($request->integer('per_page', 10) ?? 10, 50);

        $bookings = Booking::with(['user.student', 'room.floor.branch'])
            ->when($request->filled('status'), fn($q, $status) => $q->where('status', $status))
            ->when($request->filled('booking_type'), fn($q, $type) => $q->where('booking_type', $type))
            ->when($request->filled('user_id'), fn($q, $userId) => $q->where('user_id', $userId))
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json($bookings);
    }

    /**
     * Student: create booking by type (registration/extension/transfer)
     */
    public function store(Request $request)
    {
        Log::info('DEBUG BOOKING:', [
            'check_in_raw' => $request->input('check_in_date'),
            'check_out_raw' => $request->input('expected_check_out_date'),
            'parsed_check_in' => Carbon::parse($request->input('check_in_date'))->toDateTimeString(),
            'parsed_check_out' => Carbon::parse($request->input('expected_check_out_date'))->toDateTimeString(),
            'diff' => Carbon::parse($request->input('expected_check_out_date'))->diffInMonths(Carbon::parse($request->input('check_in_date')))
        ]);

        $data = $request->validate([
            'room_id' => 'required_if:booking_type,registration,transfer|nullable|exists:rooms,id',
            'booking_type' => 'required|in:registration,transfer,extension',
            'rental_type' => 'required_if:booking_type,registration|in:daily,monthly',
            'check_in_date' => 'required|date',
            'expected_check_out_date' => 'required|date|after:check_in_date',
            'reason' => 'nullable|string|max:1000',
        ]);

        $user = $request->user()->loadMissing('activeBooking');
        $checkIn = Carbon::parse($data['check_in_date'])->startOfDay();
        $expected = Carbon::parse($data['expected_check_out_date'])->startOfDay();

        if (
            ($data['booking_type'] === 'registration')
            && ($data['rental_type'] ?? null) === 'monthly'
            && $checkIn->diffInMonths($expected) < 1
        ) {
            throw ValidationException::withMessages([
                'expected_check_out_date' => __('Thời gian thuê theo tháng phải lớn hơn 1 tháng.'),
            ]);
        }

        return match ($data['booking_type']) {
            'registration' => $this->handleRegistration($user, $data, $checkIn, $expected),
            'extension'    => $this->handleExtension($user, $data, $checkIn, $expected),
            'transfer'     => $this->handleTransfer($user, $data, $checkIn, $expected),
        };
    }

    /**
     * Staff/Admin: update booking status
     */
    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,approved,rejected,active,expired,terminated',
        ]);

        $booking = Booking::findOrFail($id);
        $booking->update([
            'status'       => $data['status'],
            'processed_by' => $request->user()->id,
            'processed_at' => now(),
        ]);

        return response()->json($booking->load(['user', 'room']));
    }

    /**
     * Student: list own bookings
     */
    public function myBookings(Request $request)
    {
        $perPage = min($request->integer('per_page', 10) ?? 10, 50);

        $list = Booking::with(['room.floor.branch'])
            ->where('user_id', $request->user()->id)
            ->when($request->filled('status'), fn($q, $status) => $q->where('status', $status))
            ->when($request->filled('booking_type'), fn($q, $type) => $q->where('booking_type', $type))
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json($list);
    }

    protected function handleRegistration($user, array $data, Carbon $checkIn, Carbon $expected)
    {
        if ($user->activeBooking) {
            throw ValidationException::withMessages([
                'booking_type' => __('Bạn đang có hợp đồng hoạt động. Không thể tạo đăng ký mới.'),
            ]);
        }

        $this->guardPendingRequest($user->id, 'registration');

        $room = $this->ensureRoomAvailable($data['room_id'], $user);

        $booking = Booking::create([
            'user_id'                 => $user->id,
            'room_id'                 => $room->id,
            'booking_type'            => 'registration',
            'rental_type'             => $data['rental_type'],
            'check_in_date'           => $checkIn->toDateString(),
            'expected_check_out_date' => $expected->toDateString(),
            'status'                  => 'pending',
            'reason'                  => $data['reason'] ?? null,
        ]);

        return response()->json($booking->load(['room.floor.branch']), 201);
    }

    protected function handleExtension($user, array $data, Carbon $checkIn, Carbon $expected)
    {
        $active = $this->requireActiveBooking($user);
        $this->guardPendingRequest($user->id, 'extension');

        if ($checkIn->lt(Carbon::parse($active->expected_check_out_date))) {
            throw ValidationException::withMessages([
                'check_in_date' => __('Ngày bắt đầu gia hạn phải sau thời gian kết thúc hợp đồng hiện tại.'),
            ]);
        }

        $booking = Booking::create([
            'user_id'                 => $user->id,
            'room_id'                 => $active->room_id,
            'booking_id'              => $active->id,
            'booking_type'            => 'extension',
            'rental_type'             => $active->rental_type,
            'check_in_date'           => $checkIn->toDateString(),
            'expected_check_out_date' => $expected->toDateString(),
            'status'                  => 'pending',
            'reason'                  => $data['reason'] ?? null,
        ]);

        return response()->json($booking->load(['room.floor.branch']), 201);
    }

    protected function handleTransfer($user, array $data, Carbon $checkIn, Carbon $expected)
    {
        $active = $this->requireActiveBooking($user);
        $this->guardPendingRequest($user->id, 'transfer');

        $room = $this->ensureRoomAvailable($data['room_id'], $user);

        if ($room->id === $active->room_id) {
            throw ValidationException::withMessages([
                'room_id' => __('Vui lòng chọn phòng khác với phòng hiện tại.'),
            ]);
        }

        $booking = Booking::create([
            'user_id'                 => $user->id,
            'room_id'                 => $room->id,
            'booking_id'              => $active->id,
            'booking_type'            => 'transfer',
            'rental_type'             => $active->rental_type,
            'check_in_date'           => $checkIn->toDateString(),
            'expected_check_out_date' => $expected->toDateString(),
            'status'                  => 'pending',
            'reason'                  => $data['reason'] ?? null,
        ]);

        return response()->json($booking->load(['room.floor.branch']), 201);
    }

    protected function ensureRoomAvailable(?int $roomId, $user = null): Room
    {
        $room = Room::with('floor')
            ->where('id', $roomId ?? 0)
            ->whereRaw('is_active = true')
            ->first();

        if (!$room) {
            throw ValidationException::withMessages([
                'room_id' => __('Phòng không khả dụng hoặc đã bị khóa.'),
            ]);
        }

        if ($room->current_occupancy >= $room->capacity) {
            throw ValidationException::withMessages([
                'room_id' => __('Phòng đã đầy, vui lòng chọn phòng khác.'),
            ]);
        }

        if ($user && $room->relationLoaded('floor') && $room->floor) {
            $userStudent = $user->student ?? null;
            $userGender  = $userStudent->gender ?? null;
            $floorGender = $room->floor->gender_type ?? 'mixed';

            if ($floorGender !== 'mixed' && $userGender !== $floorGender) {
                throw ValidationException::withMessages([
                    'room_id' => __('Bạn không thể đặt phòng này do giới tính không phù hợp.'),
                ]);
            }
        }

        return $room;
    }

    public function requestReturn(Request $request)
    {
        $user = $request->user()->loadMissing('activeBooking');
        $active = $this->requireActiveBooking($user);

        // Nếu đã gửi yêu cầu trả phòng rồi (actual_check_out_date != null) thì không cho gửi nữa
        if (!is_null($active->actual_check_out_date)) {
            throw ValidationException::withMessages([
                'booking' => __('Bạn đã gửi yêu cầu trả phòng. Vui lòng chờ quản lý xử lý.'),
            ]);
        }

        $data = $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        // Ở phiên bản đơn giản: dùng ngày hôm nay làm ngày yêu cầu trả phòng
        $checkoutDate = now()->toDateString();

        $active->update([
            'actual_check_out_date' => $checkoutDate,
            // ghi đè hoặc giữ reason cũ tuỳ bạn, ở đây mình cho ghi đè
            'reason'                => $data['reason'] ?? $active->reason,
        ]);

        return response()->json($active->load(['room.floor.branch']), 200);
    }

    public function destroy(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Bạn không có quyền thao tác với yêu cầu này.',
            ], 403);
        }

        if ($booking->status !== 'pending') {
            return response()->json([
                'message' => 'Yêu cầu này đã được xử lý. Không thể xoá.',
            ], 422);
        }

        // Chỉ cho xoá các loại yêu cầu: đăng ký / gia hạn / chuyển phòng
        if (!in_array($booking->booking_type, ['registration', 'extension', 'transfer'])) {
            return response()->json([
                'message' => 'Không thể xoá loại yêu cầu này.',
            ], 422);
        }

        try {
            $booking->delete();

            return response()->json([
                'message' => 'Xoá yêu cầu thành công.',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Không thể xoá yêu cầu.',
            ], 500);
        }
    }

    protected function guardPendingRequest(int $userId, string $type): void
    {
        $exists = Booking::where('user_id', $userId)
            ->where('booking_type', $type)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            $typeName = match ($type) {
                'registration' => 'đăng ký',
                'extension'    => 'gia hạn',
                'transfer'     => 'chuyển phòng',
                default        => $type
            };

            throw ValidationException::withMessages([
                'booking_type' => __("Bạn đang có yêu cầu :type đang chờ xử lý.", ['type' => $typeName]),
            ]);
        }
    }

    protected function requireActiveBooking($user)
    {
        $active = $user->activeBooking;

        if (!$active) {
            throw ValidationException::withMessages([
                'booking' => __('Bạn chưa có hợp đồng hoạt động nào.'),
            ]);
        }

        return $active;
    }
}
