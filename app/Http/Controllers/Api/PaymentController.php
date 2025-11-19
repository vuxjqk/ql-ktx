<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceMail;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function redirect(Request $request, Bill $bill)
    {
        if (!in_array($bill->status, ['unpaid', 'partial'])) {
            return response()->json([
                'success' => false,
                'message' => __('Hóa đơn này không thể thanh toán.')
            ], 400);
        }

        $alreadyPaid = $bill->payments()->sum('amount');
        $remaining = $bill->total_amount - $alreadyPaid;

        if ($remaining <= 0) {
            return response()->json([
                'success' => false,
                'message' => __('Hóa đơn này đã được thanh toán đầy đủ.')
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1|max:' . $remaining,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $vnp_TmnCode = config('services.vnpay.tmn_code');
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $vnp_Url = config('services.vnpay.url');
        $vnp_ReturnUrl = config('services.vnpay.return_url');

        $vnp_TxnRef = $bill->bill_code . '_' . uniqid();
        $vnp_OrderInfo = "Thanh toan hoa don #" . $bill->bill_code;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $request->amount * 100;
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

        return response()->json([
            'success' => true,
            'payment_url' => $paymentUrl
        ]);
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
            return response()->json([
                'success' => false,
                'message' => __('Dữ liệu không hợp lệ (hash không khớp).')
            ], 400);
        }

        if ($responseCode !== '00' || $transactionStatus !== '00') {
            return response()->json([
                'success' => false,
                'message' => __('Thanh toán không thành công.')
            ], 400);
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
                    'transaction_code' => $vnp_TxnRef,
                    'amount' => $amount,
                ]);

                $paidAmount = $bill->payments()->sum('amount');

                $status = $paidAmount >= $bill->total_amount ? 'paid' : 'partial';
                $bill->update(['status' => $status]);

                $this->generateAndSendInvoice($bill);
            });

            return response()->json([
                'success' => true,
                'message' => __('Thanh toán thành công.')
            ]);
        } catch (Exception $e) {
            Log::error('Lỗi khi thanh toán hóa đơn ' . $bill->bill_code . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Không thể ghi nhận thanh toán. Vui lòng thử lại.')
            ], 500);
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
}
