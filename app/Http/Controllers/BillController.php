<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Bạn chưa đăng nhập.');
        }

        $user = User::with([
            'student',
            'bills.items',
            'bills.roomAssignment.room'
        ])->find($user->id);

        return view('bills.index', compact('user'));
    }

    public function create()
    {
        return view('bills.create');
    }

    public function redirect(Bill $bill)
    {
        // Lấy cấu hình từ config
        $TmnCode = config('services.vnpay.tmn_code');
        $HashSecret = config('services.vnpay.hash_secret');
        $Url = config('services.vnpay.url');
        $ReturnUrl = config('services.vnpay.return_url');

        // Chuẩn bị dữ liệu
        $TxnRef = $bill->code;
        $OrderInfo = "Thanh toan hoa don #" . $bill->code;
        $OrderType = 'billpayment';
        $Amount = $bill->amount * 100;
        $Locale = 'vn';
        $IpAddr = request()->ip();

        $inputData = [
            "Version" => "2.1.0",
            "TmnCode" => $TmnCode,
            "Amount" => $Amount,
            "Command" => "pay",
            "CreateDate" => now()->format('YmdHis'),
            "CurrCode" => "VND",
            "IpAddr" => $IpAddr,
            "Locale" => $Locale,
            "OrderInfo" => $OrderInfo,
            "OrderType" => $OrderType,
            "ReturnUrl" => $ReturnUrl,
            "TxnRef" => $TxnRef,
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
        $SecureHash = hash_hmac('sha512', $hashdata, $HashSecret);

        // Tạo URL thanh toán
        $paymentUrl = $Url . "?" . $query . "SecureHash=" . $SecureHash;

        // Redirect đến VNPay
        return redirect()->away($paymentUrl);
    }

    public function callback(Request $request)
    {
        // Lấy hash secret từ config
        $HashSecret = config('services.vnpay.hash_secret');

        // Lấy tất cả tham số từ query string
        $inputData = $request->all();
        $SecureHash = $inputData['SecureHash'] ?? '';

        // Loại bỏ các tham số liên quan đến chữ ký
        unset($inputData['SecureHashType']);
        unset($inputData['SecureHash']);

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
        $secureHash = hash_hmac('sha512', $hashdata, $HashSecret);

        // Lấy thông tin giao dịch
        $billCode = $inputData['TxnRef'] ?? null;
        $responseCode = $inputData['ResponseCode'] ?? null;
        $transactionStatus = $inputData['TransactionStatus'] ?? null;
        $amount = ($inputData['Amount'] ?? 0) / 100;

        // Kiểm tra chữ ký
        if ($secureHash === $SecureHash) {
            // Tìm hóa đơn
            $bill = Bill::where('code', $billCode)->first();

            if (!$bill) {
                return redirect()->route('room_registrations.create')->with('error', 'Hóa đơn không tồn tại!');
            }

            // Kiểm tra số tiền
            if ($bill->amount != $amount) {
                return redirect()->route('room_registrations.create')->with('error', 'Số tiền không khớp!');
            }

            $existing = Transaction::where('txn_ref', $inputData['TxnRef'] ?? null)
                ->where('transaction_no', $inputData['TransactionNo'] ?? null)
                ->first();

            if (!$existing) {
                // Lưu thông tin giao dịch vào bảng vnpay_transactions
                Transaction::create([
                    'bill_id' => $bill->id,
                    'transaction_no' => $inputData['TransactionNo'] ?? null,
                    'amount' => $amount,
                    'bank_code' => $inputData['BankCode'] ?? null,
                    'bank_tran_no' => $inputData['BankTranNo'] ?? null,
                    'card_type' => $inputData['CardType'] ?? null,
                    'order_info' => $inputData['OrderInfo'] ?? null,
                    'response_code' => $responseCode,
                    'transaction_status' => $transactionStatus,
                    'pay_date' => $inputData['PayDate'] ?? null,
                    'txn_ref' => $inputData['TxnRef'] ?? null,
                    'secure_hash' => $SecureHash,
                ]);
            }

            // Cập nhật trạng thái hóa đơn
            if ($responseCode == '00' && $transactionStatus == '00') {
                $bill->update(['status' => 'paid']);
                return redirect()->route('room_registrations.create')->with('success', 'Thanh toán hóa đơn #' . $bill->id . ' thành công!');
            } else {
                $bill->update(['status' => 'failed']);
                return redirect()->route('room_registrations.create')->with('error', 'Thanh toán hóa đơn #' . $bill->id . ' thất bại! Mã lỗi: ' . $responseCode);
            }
        } else {
            // Chữ ký không hợp lệ
            return redirect()->route('room_registrations.create')->with('error', 'Chữ ký không hợp lệ, giao dịch có thể bị giả mạo!');
        }
    }

    public function update(Bill $bill)
    {
        if ($bill->status === 'paid') {
            return redirect()->back()->with('error', 'Hoá đơn này đã được thanh toán rồi');
        }

        $bill->update(['status' => 'paid']);
        return redirect()->back()->with('success', 'Thanh toán hóa đơn #' . $bill->code . ' thành công!');
    }
}
