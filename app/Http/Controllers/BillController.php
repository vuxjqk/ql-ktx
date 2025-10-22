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

        $totalBills = $bills->count();
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
        $bookingIds = $room->activeBookings()
            ->where('rental_type', 'monthly')
            ->pluck('id');

        $hasBillThisMonth = Bill::whereIn('booking_id', $bookingIds)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->whereHas('bill_items', function ($query) {
                $query->where('description', 'like', 'Tiền Ký túc xá%');
            })
            ->exists();

        if ($hasBillThisMonth) {
            return redirect()->back()->with('warning', __('Phòng này đã được tạo hóa đơn trong tháng này.'));
        }

        try {
            $bill_code = $this->generateBillCode();

            $activeBookings = $room->activeBookings()
                ->where('rental_type', 'monthly')
                ->with('contract')
                ->get();

            $count = $activeBookings->count();

            if ($count === 0) {
                return redirect()->back()->with('info', __('Phòng này không có sinh viên nào đang ở.'));
            }

            DB::transaction(function () use ($bill_code, $room, $activeBookings, $count) {
                foreach ($activeBookings as $index => $booking) {
                    $bill = Bill::create([
                        'bill_code'    => $bill_code,
                        'user_id'      => $booking->user_id,
                        'booking_id'   => $booking->id,
                        'total_amount' => 0,
                        'status'       => 'unpaid',
                        'due_date'     => now()->endOfMonth()->addDays(7),
                        'created_by'   => Auth::id(),
                    ]);

                    $totalAmount = $booking->contract->monthly_fee;

                    BillItem::create([
                        'bill_id'     => $bill->id,
                        'description' => 'Tiền Ký túc xá tháng ' . now()->format('m'),
                        'amount'      => $totalAmount,
                    ]);

                    foreach ($room->services as $service) {
                        $usageAmount = $service->getUsageAmountForRoom($room);

                        $subtotal = $service->unit_price * max(0, $usageAmount - $service->free_quota);
                        $baseShare = intdiv($subtotal, $count);
                        $remainder = $subtotal % $count;

                        $amount = $baseShare;
                        if ($index < $remainder) {
                            $amount += 1;
                        }

                        BillItem::create([
                            'bill_id'     => $bill->id,
                            'description' => 'Tiền ' . $service->name . " ($usageAmount / $service->unit chia đều cho $count người) tháng " . now()->format('m'),
                            'amount'      => $amount,
                        ]);

                        $totalAmount += $amount;
                    }

                    $bill->update(['total_amount' => $totalAmount]);

                    $bill->load(['user.student', 'booking.room.floor.branch', 'bill_items', 'creator']);

                    $pdfPath = public_path("storage/invoices/invoice_{$bill->bill_code}.pdf");

                    File::ensureDirectoryExists(dirname($pdfPath));

                    Pdf::loadView('bills.export', compact('bill'))->save($pdfPath);

                    if ($email = $bill->user->email) {
                        Mail::to($email)->send(new InvoiceMail($bill, $pdfPath));

                        File::exists($pdfPath) && File::delete($pdfPath);
                    }
                }
            });

            return redirect()->back()->with('success', __('Đã tạo hoá đơn thành công.'));
        } catch (Exception $e) {
            Log::error('Lỗi khi tạo hoá đơn cho phòng ' . $room->room_code . ': ' . $e->getMessage());

            return redirect()->back()->with('error', __('Không thể tạo hoá đơn. Vui lòng thử lại.'));
        }
    }

    public function cancelBills(Room $room)
    {
        try {
            $bookingIds = $room->activeBookings()
                ->where('rental_type', 'monthly')
                ->pluck('id');

            $bills = Bill::whereIn('booking_id', $bookingIds)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->where('status', 'unpaid')
                ->whereHas('bill_items', function ($query) {
                    $query->where('description', 'like', 'Tiền Ký túc xá%');
                })
                ->get();

            if ($bills->isEmpty()) {
                return redirect()->back()->with('info', __('Không có hoá đơn nào cần huỷ.'));
            }

            DB::transaction(function () use ($bills) {
                foreach ($bills as $bill) {
                    $bill->update([
                        'status' => 'cancelled',
                    ]);

                    $bill->load(['user.student', 'booking.room.floor.branch', 'bill_items', 'creator']);

                    $pdfPath = public_path("storage/invoices/invoice_{$bill->bill_code}.pdf");

                    File::ensureDirectoryExists(dirname($pdfPath));

                    Pdf::loadView('bills.export', compact('bill'))->save($pdfPath);

                    if ($email = $bill->user->email) {
                        Mail::to($email)->send(new InvoiceMail($bill, $pdfPath));

                        File::exists($pdfPath) && File::delete($pdfPath);
                    }
                }
            });

            return redirect()->back()->with('success', __('Đã huỷ hoá đơn thành công.'));
        } catch (Exception $e) {
            Log::error('Lỗi khi huỷ hoá đơn cho phòng ' . $room->room_code . ': ' . $e->getMessage());

            return redirect()->back()->with('error', __('Không thể huỷ hoá đơn. Vui lòng thử lại.'));
        }
    }

    public function payBill(Bill $bill)
    {
        if ($bill->status !== 'unpaid') {
            return redirect()->back()->with('error', __('Không thể ghi nhận thanh toán cho hoá đơn này.'));
        }

        Payment::create([
            'bill_id' => $bill->id,
            'payment_type' => 'offline',
            'amount' => $bill->total_amount,
            'paid_at' => now(),
            'user_id' => Auth::id(),
        ]);

        $bill->update(['status' => 'paid']);

        $bill->load(['user.student', 'booking.room.floor.branch', 'bill_items', 'creator']);

        $pdfPath = public_path("storage/invoices/invoice_{$bill->bill_code}.pdf");

        File::ensureDirectoryExists(dirname($pdfPath));

        Pdf::loadView('bills.export', compact('bill'))->save($pdfPath);

        if ($email = $bill->user->email) {
            Mail::to($email)->send(new InvoiceMail($bill, $pdfPath));

            File::exists($pdfPath) && File::delete($pdfPath);
        }

        return redirect()->back()->with('success', __('Đã ghi nhận thanh toán thành công.'));
    }

    public function export(Bill $bill)
    {
        $bill->load(['user.student', 'booking.room.floor.branch', 'bill_items', 'creator']);
        $pdf = Pdf::loadView('bills.export', compact('bill'));
        return $pdf->stream('bills.pdf');
    }

    protected function generateBillCode(): string
    {
        $date = now()->format('ymdHis');
        $countToday = Bill::whereDate('created_at', today())->count();
        return 'HD' . $date . str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);
    }
}
