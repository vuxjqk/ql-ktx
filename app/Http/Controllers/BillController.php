<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceMail;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Payment;
use App\Models\Room;
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

    public function store(Room $room)
    {
        $activeMonthlyBookings = $room->activeBookings()
            ->where('rental_type', 'monthly')
            ->with('contract')
            ->get();

        if ($activeMonthlyBookings->isEmpty()) {
            return redirect()->back()->with('info', __('Phòng này không có sinh viên thuê theo tháng.'));
        }

        $monthlyBillsQuery = Bill::whereIn('booking_id', $activeMonthlyBookings->pluck('id'))
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('is_monthly_bill', true);

        $hasBillThisMonth = $monthlyBillsQuery->exists();
        $hasCancelledBill = (clone $monthlyBillsQuery)->where('status', 'cancelled')->exists();

        if ($hasBillThisMonth && !$hasCancelledBill) {
            return redirect()->back()->with('warning', __('Hóa đơn tháng này đã được tạo hoặc đang tồn tại.'));
        }

        try {
            DB::transaction(function () use ($room, $activeMonthlyBookings) {
                $occupantsCount = $activeMonthlyBookings->count();
                $monthDescription = __('Tiền ký túc xá tháng ') . now()->format('m/Y');

                foreach ($activeMonthlyBookings as $booking) {
                    $billCode = $this->generateBillCode();

                    $bill = Bill::create([
                        'bill_code' => $billCode,
                        'user_id' => $booking->user_id,
                        'booking_id' => $booking->id,
                        'total_amount' => 0,
                        'status' => 'unpaid',
                        'due_date' => now()->endOfMonth()->addDays(7),
                        'created_by' => Auth::id(),
                        'is_monthly_bill' => true,
                    ]);

                    $rentAmount = $booking->contract->monthly_fee;
                    BillItem::create([
                        'bill_id' => $bill->id,
                        'description' => $monthDescription,
                        'amount' => $rentAmount,
                    ]);
                    $totalAmount = $rentAmount;

                    foreach ($room->services as $service) {
                        $usageAmount = $service->getUsageAmountForRoom($room, now()->month, now()->year);
                        $excessUsage = max(0, $usageAmount - $service->free_quota);
                        $serviceCost = $excessUsage * $service->unit_price;

                        $baseShare = (int) ($serviceCost / $occupantsCount);
                        $remainder = $serviceCost % $occupantsCount;
                        $shareAmount = $baseShare + ($remainder > 0 ? 1 : 0);

                        BillItem::create([
                            'bill_id' => $bill->id,
                            'description' => __('Tiền ') . $service->name . " ($usageAmount $service->unit, chia đều $occupantsCount người) tháng " . now()->format('m/Y'),
                            'amount' => $shareAmount,
                        ]);

                        $totalAmount += $shareAmount;
                    }

                    $bill->update(['total_amount' => $totalAmount]);

                    $this->generateAndSendInvoice($bill);
                }
            });

            return redirect()->back()->with('success', __('Đã tạo hóa đơn hàng tháng thành công.'));
        } catch (Exception $e) {
            Log::error('Lỗi khi tạo hóa đơn cho phòng ' . $room->room_code . ': ' . $e->getMessage());
            return redirect()->back()->with('error', __('Không thể tạo hóa đơn. Vui lòng thử lại.'));
        }
    }

    public function cancelBills(Room $room)
    {
        $activeMonthlyBookings = $room->activeBookings()
            ->where('rental_type', 'monthly')
            ->pluck('id');

        $bills = Bill::whereIn('booking_id', $activeMonthlyBookings)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('status', 'unpaid')
            ->where('is_monthly_bill', true)
            ->get();

        if ($bills->isEmpty()) {
            return redirect()->back()->with('info', __('Không có hóa đơn nào để hủy trong tháng này.'));
        }

        try {
            DB::transaction(function () use ($bills) {
                foreach ($bills as $bill) {
                    $bill->update(['status' => 'cancelled']);
                    $this->generateAndSendInvoice($bill);
                }
            });

            return redirect()->back()->with('success', __('Đã hủy hóa đơn thành công.'));
        } catch (Exception $e) {
            Log::error('Lỗi khi hủy hóa đơn cho phòng ' . $room->room_code . ': ' . $e->getMessage());
            return redirect()->back()->with('error', __('Không thể hủy hóa đơn. Vui lòng thử lại.'));
        }
    }

    public function payBill(Request $request, Bill $bill)
    {
        if (!in_array($bill->status, ['unpaid', 'partial'])) {
            return redirect()->back()->with('error', __('Hóa đơn này không thể thanh toán.'));
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $bill->total_amount,
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
            Mail::to($email)->send(new InvoiceMail($bill, $pdfPath));
        }

        File::delete($pdfPath);
    }
}
