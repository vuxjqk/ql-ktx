<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
    public function store(Request $request, Bill $bill)
    {
        if (!in_array($bill->status, ['paid', 'partial'])) {
            return redirect()->back()->with('error', __('Hóa đơn này không thể hoàn tiền.'));
        }

        if ($bill->refunds()->exists()) {
            return redirect()->back()->with('error', __('Hóa đơn này đã được hoàn tiền trước đó.'));
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0|lte:' . $bill->total_amount,
            'reason' => 'nullable|string',
        ]);

        $refund = Refund::create([
            'bill_id' => $bill->id,
            'user_id' => $bill->user->id,
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
            'refund_date' => now(),
            'processed_by' => Auth::id(),
        ]);

        $refund->bill->update(['status' => 'refunded']);

        return redirect()->back()->with('success', __('Đã hoàn tiền thành công.'));
    }
}
