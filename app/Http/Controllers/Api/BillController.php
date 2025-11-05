<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    // student xem bill của mình
    public function myBills(Request $request)
    {
        $bills = Bill::with(['bill_items', 'payments', 'booking'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')->paginate(10);
        return response()->json($bills);
    }

    // staff/admin tạo bill
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'booking_id' => 'required|exists:bookings,id',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'is_monthly_bill' => 'boolean',
        ]);

        return DB::transaction(function () use ($data, $request) {
            $total = collect($data['items'])->sum('amount');
            $bill = Bill::create([
                'bill_code' => 'B' . now()->format('YmdHis') . rand(100, 999),
                'user_id' => $data['user_id'],
                'booking_id' => $data['booking_id'],
                'total_amount' => $total,
                'status' => 'unpaid',
                'due_date' => $data['due_date'] ?? null,
                'created_by' => $request->user()->id,
                'is_monthly_bill' => $data['is_monthly_bill'] ?? false,
            ]);
            foreach ($data['items'] as $it) {
                BillItem::create([
                    'bill_id' => $bill->id,
                    'description' => $it['description'],
                    'amount' => $it['amount'],
                ]);
            }
            return response()->json($bill->load('bill_items'), 201);
        });
    }

    // staff/admin cập nhật trạng thái (paid/partial/cancelled)
    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|in:unpaid,paid,partial,cancelled',
        ]);
        $bill = Bill::findOrFail($id);
        $bill->update(['status' => $data['status']]);
        return response()->json($bill);
    }
}
