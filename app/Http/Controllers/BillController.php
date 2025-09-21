<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Transaction;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function redirect(Bill $bill)
    {
        // Lấy cấu hình từ config
        $vnp_TmnCode = config('services.vnpay.tmn_code');
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $vnp_Url = config('services.vnpay.url');
        $vnp_ReturnUrl = config('services.vnpay.return_url');

        // Chuẩn bị dữ liệu
        $vnp_TxnRef = $bill->id;
        $vnp_OrderInfo = "Thanh toan hoa don #" . $bill->id;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $bill->amount * 100;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = request()->ip();

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

        // Sắp xếp mảng theo key
        ksort($inputData);

        // Tạo query và hashdata
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

        // Tạo SecureHash
        $vnp_SecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

        // Tạo URL thanh toán
        $paymentUrl = $vnp_Url . "?" . $query . "vnp_SecureHash=" . $vnp_SecureHash;

        // Redirect đến VNPay
        return redirect()->away($paymentUrl);
    }

    public function callback(Request $request)
    {
        // Lấy hash secret từ config
        $vnp_HashSecret = config('services.vnpay.hash_secret');

        // Lấy tất cả tham số từ query string
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';

        // Loại bỏ các tham số liên quan đến chữ ký
        unset($inputData['vnp_SecureHashType']);
        unset($inputData['vnp_SecureHash']);

        // Sắp xếp mảng theo key
        ksort($inputData);

        // Tạo chuỗi hashdata
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

        // Tạo chữ ký để kiểm tra
        $secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

        // Lấy thông tin giao dịch
        $billId = $inputData['vnp_TxnRef'] ?? null;
        $responseCode = $inputData['vnp_ResponseCode'] ?? null;
        $transactionStatus = $inputData['vnp_TransactionStatus'] ?? null;
        $amount = ($inputData['vnp_Amount'] ?? 0); // Lưu nguyên giá trị VNPay (đã nhân 100)

        // Kiểm tra chữ ký
        if ($secureHash === $vnp_SecureHash) {
            // Tìm hóa đơn
            $bill = Bill::find($billId);

            if (!$bill) {
                return redirect()->route('room_registrations.create')->with('error', 'Hóa đơn không tồn tại!');
            }

            // Kiểm tra số tiền
            if ($bill->amount * 100 != $amount) {
                return redirect()->route('room_registrations.create')->with('error', 'Số tiền không khớp!');
            }

            // Lưu thông tin giao dịch vào bảng vnpay_transactions
            $transaction = Transaction::create([
                'bill_id' => $billId,
                'vnp_transaction_no' => $inputData['vnp_TransactionNo'] ?? null,
                'vnp_amount' => $amount,
                'vnp_bank_code' => $inputData['vnp_BankCode'] ?? null,
                'vnp_bank_tran_no' => $inputData['vnp_BankTranNo'] ?? null,
                'vnp_card_type' => $inputData['vnp_CardType'] ?? null,
                'vnp_order_info' => $inputData['vnp_OrderInfo'] ?? null,
                'vnp_response_code' => $responseCode,
                'vnp_transaction_status' => $transactionStatus,
                'vnp_pay_date' => $inputData['vnp_PayDate'] ?? null,
                'vnp_txn_ref' => $inputData['vnp_TxnRef'] ?? null,
                'vnp_secure_hash' => $vnp_SecureHash,
            ]);

            // Cập nhật trạng thái hóa đơn
            if ($responseCode == '00' && $transactionStatus == '00') {
                $bill->update(['status' => 'paid']);
                return redirect()->route('room_registrations.create')->with('success', 'Thanh toán hóa đơn #' . $billId . ' thành công!');
            } else {
                $bill->update(['status' => 'failed']);
                return redirect()->route('room_registrations.create')->with('error', 'Thanh toán hóa đơn #' . $billId . ' thất bại! Mã lỗi: ' . $responseCode);
            }
        } else {
            // Chữ ký không hợp lệ
            return redirect()->route('room_registrations.create')->with('error', 'Chữ ký không hợp lệ, giao dịch có thể bị giả mạo!');
        }
    }
}
