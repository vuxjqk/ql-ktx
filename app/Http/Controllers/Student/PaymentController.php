<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceMail;
use App\Models\Bill;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function store(Bill $bill)
    {
        if (!in_array($bill->status, ['unpaid', 'partial'])) {
            return redirect()->back()->with('error', __('Hóa đơn này không thể thanh toán.'));
        }

        $alreadyPaid = $bill->payments()->sum('amount');
        $remaining = $bill->total_amount - $alreadyPaid;

        if ($remaining <= 0) {
            return redirect()->back()->with('error', __('Hóa đơn này đã được thanh toán đầy đủ.'));
        }

        $amount = $remaining;

        try {
            DB::transaction(function () use ($bill, $amount) {
                Payment::create([
                    'bill_id' => $bill->id,
                    'payment_type' => 'online',
                    'amount' => $amount,
                    'paid_at' => now(),
                    'user_id' => $bill->user_id,
                ]);

                $paidAmount = $bill->payments()->sum('amount');

                $status = $paidAmount >= $bill->total_amount ? 'paid' : 'partial';
                $bill->update(['status' => $status]);

                $booking = $bill->booking;
                if ($booking->status === 'approved') {
                    $booking->update(['status' => 'active']);
                } else if ($booking->status === 'active' && $booking->actual_check_out_date) {
                    $booking->update(['status' => 'terminated']);

                    if ($booking->room->current_occupancy > 0) {
                        $booking->room->decrement('current_occupancy');
                    }
                }
            });

            return redirect()->route('student.bookings.index')->with('success', __('Thanh toán thành công.'));
        } catch (Exception $e) {
            Log::error('Lỗi khi thanh toán hóa đơn ' . $bill->bill_code . ': ' . $e->getMessage());
            return redirect()->route('student.bookings.index')->with('error', __('Không thể thanh toán. Vui lòng thử lại.'));
        }
    }

    public function redirect(Request $request, Bill $bill)
    {
        if (!in_array($bill->status, ['unpaid', 'partial'])) {
            return redirect()->back()->with('error', __('Hóa đơn này không thể thanh toán.'));
        }

        $alreadyPaid = $bill->payments()->sum('amount');
        $remaining = $bill->total_amount - $alreadyPaid;

        if ($remaining <= 0) {
            return redirect()->back()->with('error', __('Hóa đơn này đã được thanh toán đầy đủ.'));
        }

        $amount = $remaining;

        $vnp_TmnCode = config('services.vnpay.tmn_code');
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $vnp_Url = config('services.vnpay.url');
        $vnp_ReturnUrl = config('services.vnpay.return_url');

        $vnp_TxnRef = $bill->bill_code . '_' . uniqid();
        $vnp_OrderInfo = "Thanh toan hoa don #" . $bill->bill_code;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $amount * 100;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $request->ip();

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => now()->format('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        ksort($inputData);
        $query = "";
        $hashdata = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_SecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $paymentUrl = $vnp_Url . "?" . $query . "vnp_SecureHash=" . $vnp_SecureHash;

        return redirect()->away($paymentUrl);
    }

    public function callback(Request $request)
    {
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHashType']);
        unset($inputData['vnp_SecureHash']);

        ksort($inputData);
        $hashdata = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $responseCode = $inputData['vnp_ResponseCode'] ?? null;
        $transactionStatus = $inputData['vnp_TransactionStatus'] ?? null;
        $amount = ($inputData['vnp_Amount'] ?? 0) / 100;
        $vnp_TxnRef = $inputData['vnp_TxnRef'] ?? null;

        $billCode = explode('_', $vnp_TxnRef)[0];
        $bill = Bill::where('bill_code', $billCode)->firstOrFail();

        if ($secureHash !== $vnp_SecureHash) {
            return redirect()->route('student.bookings.index')->with('error', __('Dữ liệu không hợp lệ (hash không khớp).'));
        }

        if ($responseCode !== '00' || $transactionStatus !== '00') {
            return redirect()->route('student.bookings.index')->with('error', __('Thanh toán không thành công.'));
        }

        try {
            DB::transaction(function () use ($bill, $vnp_TxnRef, $amount) {
                $payment = Payment::create([
                    'bill_id' => $bill->id,
                    'payment_type' => 'online',
                    'amount' => $amount,
                    'paid_at' => now(),
                    'user_id' => $bill->user_id,
                ]);

                Transaction::create([
                    'payment_id' => $payment->id,
                    'gateway' => 'vnpay',
                    'transaction_code' => $vnp_TxnRef,
                    'amount' => $amount,
                ]);

                $paidAmount = $bill->payments()->sum('amount');

                $status = $paidAmount >= $bill->total_amount ? 'paid' : 'partial';
                $bill->update(['status' => $status]);

                $booking = $bill->booking;
                if ($booking->status === 'approved') {
                    $booking->update(['status' => 'active']);
                } else if ($booking->status === 'active' && $booking->actual_check_out_date) {
                    $booking->update(['status' => 'terminated']);

                    if ($booking->room->current_occupancy > 0) {
                        $booking->room->decrement('current_occupancy');
                    }
                }

                $this->generateAndSendInvoice($bill);
            });

            return redirect()->route('student.bookings.index')->with('success', __('Thanh toán thành công.'));
        } catch (Exception $e) {
            Log::error('Lỗi khi thanh toán hóa đơn ' . $bill->bill_code . ': ' . $e->getMessage());
            return redirect()->route('student.bookings.index')->with('error', __('Không thể thanh toán. Vui lòng thử lại.'));
        }
    }

    public function redirectZaloPay(Bill $bill)
    {
        if (!in_array($bill->status, ['unpaid', 'partial'])) {
            return redirect()->back()->with('error', __('Hóa đơn này không thể thanh toán.'));
        }

        $alreadyPaid = $bill->payments()->sum('amount');
        $remaining = $bill->total_amount - $alreadyPaid;

        if ($remaining <= 0) {
            return redirect()->back()->with('error', __('Hóa đơn này đã được thanh toán đầy đủ.'));
        }

        $amount = $remaining;

        $zalopay = config('services.zalopay');

        $appId       = $zalopay['app_id'];
        $key1        = $zalopay['key1'];
        $endpoint    = $zalopay['endpoint'];
        $callbackUrl = $zalopay['callback_url'];
        $redirectUrl = $zalopay['redirect_url'];

        $appTransId = date('ymd') . '_' . $bill->bill_code . '_' . time();

        $order = [
            'app_id'       => $appId,
            'app_trans_id' => $appTransId,
            'app_time'     => round(microtime(true) * 1000),
            'app_user'     => (string) $bill->user_id,
            'item'         => json_encode([
                [
                    'userId' => $bill->user_id,
                    'amount' => (int) $amount
                ]
            ]),
            'embed_data'   => json_encode([
                'redirecturl' => $redirectUrl
            ]),
            'amount'       => (int) $amount,
            'description'  => 'Thanh toán hóa đơn #' . $bill->bill_code,
            'callback_url' => $callbackUrl,
        ];

        $dataToSign = $order['app_id'] . '|' . $order['app_trans_id'] . '|' . $order['app_user'] . '|' . $order['amount'] . '|' . $order['app_time'] . '|' . $order['embed_data'] . '|' . $order['item'];
        $order['mac'] = hash_hmac('sha256', $dataToSign, $key1);

        $client = new \GuzzleHttp\Client();
        $response = $client->post($endpoint, ['form_params' => $order]);
        $result = json_decode($response->getBody()->getContents(), true);

        if (!isset($result['order_url'])) {
            return redirect()->back()->with('error', __('Không thể tạo yêu cầu thanh toán ZaloPay.'));
        }

        return redirect()->away($result['order_url']);
    }

    public function returnZaloPay(Request $request)
    {
        $appTransId = $request->input('apptransid');
        $amount     = $request->input('amount', 0);
        $status     = $request->input('status');

        if (!$appTransId) {
            return redirect()->route('student.bookings.index')
                ->with('error', __('Dữ liệu trả về không hợp lệ.'));
        }

        if ($status != 1) {
            return redirect()->route('student.bookings.index')
                ->with('error', __('Thanh toán không thành công.'));
        }

        // Tạm thời bỏ kiểm tra hash

        $billCode = explode('_', $appTransId)[1] ?? null;
        $bill = Bill::where('bill_code', $billCode)->firstOrFail();
        $amount = (int)$amount;

        try {
            DB::transaction(function () use ($bill, $appTransId, $amount) {
                $payment = Payment::create([
                    'bill_id' => $bill->id,
                    'payment_type' => 'online',
                    'amount' => $amount,
                    'paid_at' => now(),
                    'user_id' => $bill->user_id,
                ]);

                Transaction::create([
                    'payment_id' => $payment->id,
                    'gateway' => 'zalopay',
                    'transaction_code' => $appTransId,
                    'amount' => $amount,
                ]);

                $paidAmount = $bill->payments()->sum('amount');
                $status = $paidAmount >= $bill->total_amount ? 'paid' : 'partial';
                $bill->update(['status' => $status]);

                $booking = $bill->booking;
                if ($booking->status === 'approved') {
                    $booking->update(['status' => 'active']);
                } else if ($booking->status === 'active' && $booking->actual_check_out_date) {
                    $booking->update(['status' => 'terminated']);
                    if ($booking->room->current_occupancy > 0) {
                        $booking->room->decrement('current_occupancy');
                    }
                }

                $this->generateAndSendInvoice($bill);
            });

            return redirect()->route('student.bookings.index')
                ->with('success', __('Thanh toán thành công.'));
        } catch (Exception $e) {
            Log::error('Lỗi khi thanh toán hóa đơn ' . $bill->bill_code . ': ' . $e->getMessage());
            return redirect()->route('student.bookings.index')
                ->with('error', __('Không thể thanh toán. Vui lòng thử lại.'));
        }
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

    protected function generateContractCode(): string
    {
        $date = now()->format('ymdHi');
        $countToday = Contract::whereDate('created_at', today())->count() + 1;
        return 'CONT-' . $date . str_pad($countToday, 4, '0', STR_PAD_LEFT);
    }
}
