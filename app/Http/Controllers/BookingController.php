<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Booking;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with(['user.student', 'room.floor.branch'])
            ->filter($request->all())
            ->latest('updated_at')
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

            if ($booking->rental_type === 'daily') {
                $days = $booking->check_in_date->diffInDays($booking->expected_check_out_date) + 1;
                $requiredAmount = $days * $booking->room->price_per_day;
            } else {
                $requiredAmount = $booking->room->price_per_month;
            }

            $bill = Bill::create([
                'bill_code' => $this->generateBillCode(),
                'user_id' => $booking->user_id,
                'booking_id' => $booking->id,
                'total_amount' => $requiredAmount,
                'status' => 'unpaid',
                'created_by' => Auth::id(),
            ]);

            BillItem::create([
                'bill_id' => $bill->id,
                'description' => $booking->rental_type === 'daily'
                    ? __('Thanh toán thuê theo ngày')
                    : __('Đặt cọc thuê theo tháng'),
                'amount' => $requiredAmount,
            ]);
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

        $data = [
            'actual_check_out_date' => now(),
            'status' => 'terminated',
            'processed_at' => now(),
            'processed_by' => Auth::id(),
        ];

        if (!is_null($validated['reason'] ?? null)) {
            $data['reason'] = $validated['reason'];
        }

        $booking->update($data);

        if ($booking->room->current_occupancy > 0) {
            $booking->room->decrement('current_occupancy');
        }

        return redirect()->route('bookings.show', $booking)->with('success', __('Đã chấm dứt thành công'));
    }

    public function destroy(Booking $booking)
    {
        if ($booking->status === 'active') {
            return redirect()->back()->with('error', __('Không thể xoá booking đang hoạt động'));
        }

        try {
            if ($booking->room->current_occupancy > 0) {
                $booking->room->decrement('current_occupancy');
            }

            $contract = $booking->contract;
            if ($contract) {
                if ($contract->contract_file && Storage::disk('public')->exists($contract->contract_file)) {
                    Storage::disk('public')->delete($contract->contract_file);
                }
                $contract->delete();
            }

            if ($booking->bills && $booking->bills->count()) {
                $booking->bills->each->delete();
            }

            $booking->delete();

            return redirect()->route('bookings.index')->with('success', __('Đã xoá thành công'));
        } catch (QueryException $e) {
            $msg = $e->getCode() === '23000'
                ? __('Không thể xóa vì có dữ liệu liên quan')
                : __('Đã xảy ra lỗi khi xoá');

            return redirect()->back()->with('error', $msg);
        }
    }

    protected function generateBillCode(): string
    {
        $date = now()->format('ymdHi');
        $countToday = Bill::whereDate('created_at', today())->count() + 1;
        return 'BILL-' . $date . str_pad($countToday, 4, '0', STR_PAD_LEFT);
    }
}
