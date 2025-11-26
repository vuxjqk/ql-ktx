<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceMail;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Payment;
use App\Models\ServiceUsageShare;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BillController extends Controller
{
    public function index(Request $request, User $user)
    {
        $bills = $user->bills()
            ->with(['booking.room.floor.branch', 'bill_items', 'payments.transaction', 'payments.user', 'creator'])
            ->filter($request->all())
            ->paginate(10)
            ->appends($request->query());

        $totalBills = $bills->total();
        $unpaidBills = $bills->where('status', 'unpaid')->count();
        $paidBills = $bills->where('status', 'paid')->count();
        $cancelledBills = $bills->where('status', 'cancelled')->count();

        return view('bills.index', compact(
            'user',
            'bills',
            'totalBills',
            'unpaidBills',
            'paidBills',
            'cancelledBills'
        ));
    }

    public function create(User $user)
    {
        $booking = $user->activeBooking;

        if (!$booking) {
            return redirect()->route('bills.index', $user)
                ->with('info', __('Sinh viên này hiện tại không cư trú.'));
        }

        $now = now();
        $periodStart = optional(Bill::where('booking_id', $booking->id)
            ->where('is_monthly_bill', true)
            ->latest()
            ->first())
            ->created_at?->addDay() ?? $booking->check_in_date;

        $periodEnd = $booking->actual_check_out_date ?? $now;

        $totalAmount = 0;
        $billItems = [];

        if ($booking->rental_type === 'monthly' && $booking->contract) {
            $daysStayed = $periodStart->diffInDays($periodEnd) + 1;
            $dailyTotal = $booking->room->price_per_day * $daysStayed;
            $rentAmount = min($booking->contract->monthly_fee, $dailyTotal);

            $billItems[] = [
                'description' => 'Tiền ký túc xá',
                'amount' => $rentAmount,
            ];

            $totalAmount += $rentAmount;
        }

        $services = ServiceUsageShare::with('serviceUsage.service')
            ->where('user_id', $user->id)
            ->whereHas('serviceUsage', function ($q) use ($booking, $periodStart, $periodEnd) {
                $q->where('room_id', $booking->room_id)
                    ->whereBetween('usage_date', [$periodStart, $periodEnd]);
            })
            ->get()
            ->groupBy('serviceUsage.service_id')
            ->map(function ($group) {
                $service = $group->first()->serviceUsage->service;

                return [
                    'description' => $service->name,
                    'unit' => $service->unit,
                    'usage_amount' => $group->sum(fn($item) => $item->serviceUsage->usage_amount),
                    'amount' => $group->sum('share_amount')
                ];
            });

        $totalAmount += $services->sum('amount');

        $billItems = array_merge($billItems, $services->toArray());

        return view('bills.create', compact('user', 'booking', 'billItems', 'totalAmount'));
    }

    public function store(User $user)
    {
        return $this->createBill($user, true);
    }

    public function cancelBills(Bill $bill)
    {
        if ($bill->status !== 'unpaid') {
            return redirect()->back()->with('error', __('Hóa đơn này không thể hủy.'));
        }

        $bill->update(['status' => 'cancelled']);
        return redirect()->back()->with('success', __('Đã hủy hóa đơn thành công.'));
    }

    public function payBill(Request $request, Bill $bill)
    {
        if (!in_array($bill->status, ['unpaid', 'partial'])) {
            return redirect()->back()->with('error', __('Hóa đơn này không thể thanh toán.'));
        }

        $alreadyPaid = $bill->payments()->sum('amount');
        $remaining = $bill->total_amount - $alreadyPaid;

        if ($remaining <= 0) {
            return redirect()->back()->with('error', __('Hóa đơn này đã được thanh toán đầy đủ.'));
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $remaining,
            'payment_type' => 'required|in:offline,online',
        ]);

        try {
            DB::transaction(function () use ($bill, $validated) {
                Payment::create([
                    'bill_id' => $bill->id,
                    'payment_type' => $validated['payment_type'],
                    'amount' => $validated['amount'],
                    'paid_at' => now(),
                    'user_id' => Auth::id(),
                ]);

                $paidAmount = $bill->payments->sum('amount');

                $status = $paidAmount >= $bill->total_amount ? 'paid' : 'partial';
                $bill->update(['status' => $status]);

                $this->generateAndSendInvoice($bill);
            });

            return redirect()->back()->with('success', __('Đã ghi nhận thanh toán thành công.'));
        } catch (Exception $e) {
            Log::error('Lỗi khi thanh toán hóa đơn ' . $bill->bill_code . ': ' . $e->getMessage());
            return redirect()->back()->with('error', __('Không thể ghi nhận thanh toán. Vui lòng thử lại.'));
        }
    }

    public function export(Bill $bill)
    {
        $bill->load(['user.student', 'booking.room.floor.branch', 'bill_items', 'creator']);
        $pdf = Pdf::loadView('bills.export', compact('bill'));
        return $pdf->stream("invoice_{$bill->bill_code}.pdf");
    }

    protected function generateBillCode(): string
    {
        $date = now()->format('ymdHi');
        $countToday = Bill::whereDate('created_at', today())->count() + 1;
        return 'BILL-' . $date . str_pad($countToday, 4, '0', STR_PAD_LEFT);
    }

    protected function generateAndSendInvoice(Bill $bill)
    {
        $bill->load(['user.student', 'booking.room.floor.branch', 'bill_items', 'creator']);

        $pdfPath = storage_path("app/public/invoices/invoice_{$bill->bill_code}.pdf");
        File::ensureDirectoryExists(dirname($pdfPath));

        Pdf::loadView('bills.export', compact('bill'))->save($pdfPath);

        if ($email = $bill->user->email) {
            Mail::to($email)->queue(new InvoiceMail($bill, $pdfPath));
        }

        File::delete($pdfPath);
    }

    protected function createBill(User $user, bool $isMonthlyBill = false)
    {
        $booking = $user->activeBooking;

        if (!$booking) {
            return redirect()->back()->with('info', __('Sinh viên này hiện tại không cư trú.'));
        }

        if ($booking->rental_type === 'daily' && $isMonthlyBill) {
            return redirect()->back()->with('warning', __('Sinh viên thuê theo ngày không tạo hóa đơn hàng tháng.'));
        }

        if ($isMonthlyBill) {
            $exists = Bill::where('booking_id', $booking->id)
                ->where('is_monthly_bill', true)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->where('status', '!=', 'cancelled')
                ->exists();

            if ($exists) {
                return redirect()->back()->with('warning', __('Hóa đơn tháng này đã tồn tại và chưa bị hủy.'));
            }
        }

        try {
            $bill = DB::transaction(function () use ($user, $booking, $isMonthlyBill) {
                $now = now();
                $checkInDate = $booking->check_in_date;
                $checkOutDate = $booking->actual_check_out_date;

                $lastMonthlyBill = Bill::where('booking_id', $booking->id)
                    ->where('is_monthly_bill', true)
                    ->latest()
                    ->first();

                $periodStart = $lastMonthlyBill?->created_at->addDay() ?? $checkInDate;
                $periodEnd = $checkOutDate ?? $now;

                if ($periodStart->gt($periodEnd)) {
                    throw new Exception(__('Không có dữ liệu để lập hóa đơn trong khoảng thời gian này.'));
                }

                $totalAmount = 0;
                $billItems = [];

                if ($booking->rental_type === 'monthly' && $booking->contract) {
                    $monthlyFee = $booking->contract->monthly_fee;
                    $dailyPrice = $booking->room->price_per_day;

                    if ($isMonthlyBill) {
                        $rentAmount = $monthlyFee;
                        $description = "Tiền ký túc xá tháng " . $periodEnd->format('m/Y');
                    } else {
                        $daysStayed = $periodStart->diffInDays($periodEnd) + 1;
                        $dailyTotal = $dailyPrice * $daysStayed;
                        $rentAmount = min($monthlyFee, $dailyTotal);
                        $description = "Tiền ký túc xá phát sinh ({$periodStart->format('d/m')} - {$periodEnd->format('d/m/Y')})";
                    }

                    if ($rentAmount > 0) {
                        $billItems[] = compact('description', 'rentAmount');
                        $totalAmount += $rentAmount;
                    }
                }

                $services = ServiceUsageShare::with('serviceUsage.service')
                    ->where('user_id', $user->id)
                    ->whereHas('serviceUsage', function ($q) use ($booking, $periodStart, $periodEnd) {
                        $q->where('room_id', $booking->room_id)
                            ->whereBetween('usage_date', [$periodStart, $periodEnd]);
                    })
                    ->get()
                    ->groupBy('serviceUsage.service_id')
                    ->map(function ($group) {
                        return [
                            'name' => $group->first()->serviceUsage->service->name,
                            'amount' => $group->sum('share_amount')
                        ];
                    });

                foreach ($services as $service) {
                    if ($service['amount'] > 0) {
                        $desc = "Tiền {$service['name']} ({$periodStart->format('d/m')} - {$periodEnd->format('d/m/Y')})";

                        $billItems[] = [
                            'description' => $desc,
                            'amount' => $service['amount']
                        ];
                        $totalAmount += $service['amount'];
                    }
                }

                if ($totalAmount <= 0) {
                    throw new Exception(__('Không có khoản phí nào để lập hóa đơn.'));
                }

                $bill = Bill::create([
                    'bill_code'       => $this->generateBillCode(),
                    'user_id'         => $user->id,
                    'booking_id'      => $booking->id,
                    'total_amount'    => $totalAmount,
                    'status'          => 'unpaid',
                    'due_date'        => $now->addDays(7),
                    'created_by'      => Auth::id(),
                    'is_monthly_bill' => $isMonthlyBill,
                ]);

                foreach ($billItems as $item) {
                    BillItem::create([
                        'bill_id'     => $bill->id,
                        'description' => $item['description'],
                        'amount'      => $item['amount'] ?? $item['rentAmount'],
                    ]);
                }

                return $bill;
            });

            $this->generateAndSendInvoice($bill);

            return redirect()->back()->with('success', __('Đã tạo hóa đơn thành công.'));
        } catch (Exception $e) {
            Log::error('Lỗi khi tạo hóa đơn cho sinh viên ' . $user->student?->student_code . ': ' . $e->getMessage());
            return redirect()->back()->with('error', __('Không thể tạo hóa đơn. Vui lòng thử lại.'));
        }
    }
}
