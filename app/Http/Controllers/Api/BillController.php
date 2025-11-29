<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    // student: xem bill của mình
    public function myBills(Request $request)
    {
        $perPage = min($request->integer('per_page', 10) ?? 10, 50);

        $bills = Bill::with([
            'bill_items',
            'payments.transaction',
            'refunds',
            'booking.room.floor.branch',
        ])
            ->where('user_id', $request->user()->id)
            ->when($request->filled('status'), fn($q, $status) => $q->where('status', $status))
            ->when($request->filled('bill_code'), fn($q, $code) => $q->where('bill_code', 'like', "%{$code}%"))
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json($bills);
    }

    public function show(Request $request, Bill $bill)
    {
        $user = $request->user();

        if ($user->role === 'student' && $bill->user_id !== $user->id) {
            abort(403, __('Bạn không có quyền xem hoá đơn này.'));
        }

        return response()->json(
            $bill->load([
                'bill_items',
                'payments.transaction',
                'refunds',
                'booking.room.floor.branch',
                'creator',
            ])
        );
    }

    // staff/admin: tạo bill
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

            return response()->json(
                $bill->load(['bill_items', 'booking.room.floor.branch']),
                201
            );
        });
    }

    // staff/admin: cập nhật trạng thái
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
