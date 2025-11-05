<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Booking;
use App\Models\Contract;
use App\Models\Payment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        if ($booking->status !== 'approved') {
            return redirect()->back()->with('error', __('Không thể ghi nhận thanh toán vì trạng thái đặt phòng không phù hợp'));
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_type' => 'required|in:offline,online',
        ]);

        try {
            DB::transaction(function () use ($booking, $validated) {
                $amount = $validated['amount'];
                $paymentType = $validated['payment_type'];

                if ($booking->rental_type === 'daily') {
                    $days = $booking->check_in_date->diffInDays($booking->expected_check_out_date) + 1;
                    $requiredAmount = $days * $booking->room->price_per_day;
                } else {
                    $requiredAmount = $booking->room->price_per_month;
                }

                if ($amount < $requiredAmount) {
                    throw new Exception(__('Số tiền thanh toán không đủ'));
                }

                $billCode = $this->generateBillCode();

                $bill = Bill::create([
                    'bill_code' => $billCode,
                    'user_id' => $booking->user_id,
                    'booking_id' => $booking->id,
                    'total_amount' => $requiredAmount,
                    'status' => 'paid',
                    'created_by' => Auth::id(),
                ]);

                BillItem::create([
                    'bill_id' => $bill->id,
                    'description' => $booking->rental_type === 'daily'
                        ? __('Thanh toán thuê theo ngày')
                        : __('Đặt cọc thuê theo tháng'),
                    'amount' => $requiredAmount,
                ]);

                Payment::create([
                    'bill_id' => $bill->id,
                    'payment_type' => $paymentType,
                    'amount' => $amount,
                    'paid_at' => now(),
                    'user_id' => Auth::id(),
                ]);

                if ($booking->rental_type === 'monthly') {
                    Contract::create([
                        'contract_code' => $this->generateContractCode(),
                        'booking_id' => $booking->id,
                        'monthly_fee' => $booking->room->price_per_month,
                        'deposit' => $requiredAmount,
                    ]);
                }

                $booking->update(['status' => 'active']);
            });

            return redirect()->back()->with('success', __('Đã ghi nhận thanh toán thành công.'));
        } catch (Exception $e) {
            Log::error('Lỗi khi ghi nhận thanh toán booking ID ' . $booking->id . ': ' . $e->getMessage());
            return redirect()->back()->with('error', __('Không thể ghi nhận thanh toán: ') . $e->getMessage());
        }
    }

    protected function generateBillCode(): string
    {
        $date = now()->format('ymdHi');
        $countToday = Bill::whereDate('created_at', today())->count() + 1;
        return 'BILL-' . $date . str_pad($countToday, 4, '0', STR_PAD_LEFT);
    }

    protected function generateContractCode(): string
    {
        $date = now()->format('ymdHi');
        $countToday = Contract::whereDate('created_at', today())->count() + 1;
        return 'CONT-' . $date . str_pad($countToday, 4, '0', STR_PAD_LEFT);
    }
}
