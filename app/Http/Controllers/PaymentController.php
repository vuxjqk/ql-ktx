<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Booking;
use App\Models\Contract;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function store(Booking $booking)
    {
        if ($booking->status !== 'approved') {
            return redirect()->back()->with('error', __('Không thể ghi nhận thanh toán vì trạng thái đặt phòng không phù hợp'));
        }

        try {
            $bill_code = $this->generateBillCode();

            $amount = $booking->rental_type === 'daily'
                ? ($booking->check_in_date->diffInDays($booking->expected_check_out_date) + 1) * $booking->room->price_per_day
                : $booking->room->price_per_month;

            DB::transaction(function () use ($bill_code, $booking, $amount) {
                $bill = Bill::create([
                    'bill_code'    => $bill_code,
                    'user_id'      => $booking->user_id,
                    'booking_id'   => $booking->id,
                    'total_amount' => $amount,
                    'status'       => 'paid',
                    'created_by'   => Auth::id(),
                ]);

                BillItem::create([
                    'bill_id'      => $bill->id,
                    'description'  => 'Thanh toán đợt đầu',
                    'amount'       => $amount,
                ]);

                Payment::create([
                    'bill_id'      => $bill->id,
                    'payment_type' => 'offline',
                    'amount'       => $amount,
                    'paid_at'      => now(),
                    'user_id'      => Auth::id(),
                ]);

                Contract::create([
                    'contract_code' => $this->generateContractCode(),
                    'booking_id' => $booking->id,
                    'monthly_fee' => $amount,
                    'deposit' => $amount,
                ]);

                $booking->update(['status' => 'active']);
            });

            return redirect()->back()->with('success', __('Đã ghi nhận thanh toán đợt đầu thành công.'));
        } catch (Exception $e) {
            Log::error('Lỗi khi ghi nhận thanh toán booking ID ' . $booking->id . ': ' . $e->getMessage());

            return redirect()->back()->with('error', __('Không thể ghi nhận thanh toán. Vui lòng thử lại.'));
        }
    }

    protected function generateBillCode(): string
    {
        $date = now()->format('ymdHi');
        $countToday = Bill::whereDate('created_at', today())->count();
        return 'Bill-' . $date . str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);
    }

    protected function generateContractCode()
    {
        $date = now()->format('ymdHi');
        $countToday = Contract::whereDate('created_at', today())->count();
        return 'Cont-' . $date . str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);
    }
}
