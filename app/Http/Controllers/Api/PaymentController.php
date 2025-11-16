<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function redirect(Request $request, Bill $bill)
    {
        if (!in_array($bill->status, ['unpaid', 'partial'])) {
            return response()->json([
                'success' => false,
                'message' => 'Hóa đơn này không thể thanh toán.'
            ], 400);
        }

        $alreadyPaid = $bill->payments()->sum('amount');
        $remaining = $bill->total_amount - $alreadyPaid;

        if ($remaining <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Hóa đơn này đã được thanh toán đầy đủ.'
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

        $vnp_TxnRef = time() . '_' . uniqid();
        $vnp_OrderInfo = "Thanh toan hoa don #" . $vnp_TxnRef;
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
        // Lấy toàn bộ dữ liệu VNPAY trả về
        $data = $request->all();

        // Ghi log để kiểm tra callback có chạy hay không
        Log::info('VNPAY CALLBACK TEST', $data);

        // Trả JSON đơn giản
        return response()->json([
            'success' => true,
            'message' => 'Callback nhận thành công!',
            'data' => $data
        ]);
    }
}
