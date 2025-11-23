<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Contract;
use App\Models\Payment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function store(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_type' => 'required|in:offline,online',
        ]);

        try {
            DB::transaction(function () use ($bill, $validated) {
                $amount = $validated['amount'];
                $paymentType = $validated['payment_type'];
                $requiredAmount = $bill->total_amount;

                if ($amount < $requiredAmount) {
                    throw new Exception(__('Số tiền thanh toán không đủ'));
                }

                Payment::create([
                    'bill_id' => $bill->id,
                    'payment_type' => $paymentType,
                    'amount' => $amount,
                    'paid_at' => now(),
                    'user_id' => Auth::id(),
                ]);

                $booking = $bill->booking;

                if ($booking->rental_type === 'monthly') {
                    Contract::create([
                        'contract_code' => $this->generateContractCode(),
                        'booking_id' => $booking->id,
                        'monthly_fee' => $booking->room->price_per_month,
                        'deposit' => $requiredAmount,
                    ]);
                }

                $bill->update(['status' => 'paid']);
                $booking->update(['status' => 'active']);
            });

            return redirect()->back()->with('success', __('Đã ghi nhận thanh toán thành công.'));
        } catch (Exception $e) {
            Log::error('Lỗi khi ghi nhận thanh toán booking ID ' . $bill->booking->id . ': ' . $e->getMessage());

            return redirect()->back()->with('error', __('Không thể ghi nhận thanh toán. Vui lòng thử lại.'));
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
