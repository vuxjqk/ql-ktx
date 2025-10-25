<?php

namespace App\Http\Controllers;

use Gemini\Laravel\Facades\Gemini;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function handleChat(Request $request)
    {
        // $userMessage = trim($request->input('message', ''));

        // Log::info('Chatbot called', ['message' => $userMessage]);

        // // ✅ CHATBOT THÔNG MINH KHÔNG DÙNG API
        // $reply = $this->getSmartReply($userMessage);

        // return response()->json(['reply' => $reply]);

        // Validate input
        $request->validate([
            'message' => 'required|string',
        ]);

        $userMessage = $request->input('message');

        try {
            // Gửi yêu cầu đến Gemini: dùng model generativeModel theo doc v2.x :contentReference[oaicite:2]{index=2}
            $response = Gemini::generativeModel(model: 'gemini-2.0-flash')
                ->generateContent($userMessage);

            $replyText = $response->text() ?? 'Xin lỗi, tôi không hiểu yêu cầu của bạn.';
        } catch (\Throwable $e) {
            // Log lỗi nếu cần
            Log::error('Gemini chat error: ' . $e->getMessage());

            $replyText = 'Đã có lỗi xảy ra. Vui lòng thử lại sau.';
        }

        return response()->json([
            'reply' => $replyText,
        ]);
    }

    private function getSmartReply($message)
    {
        $message = strtolower($message);

        // Quy tắc trả lời thông minh cho ký túc xá
        if (strpos($message, 'phòng') !== false || strpos($message, 'room') !== false) {
            return "📍 **Phòng trống hiện tại:**
                • Phòng 101, 102: Còn chỗ (2.5tr/tháng)
                • Phòng 201: Đầy
                • Phòng 301: Còn 1 chỗ
                Hỏi 'tra phòng 101' để xem chi tiết!";
        }

        if (strpos($message, 'tiền') !== false || strpos($message, 'thanh toán') !== false || strpos($message, 'học phí') !== false) {
            return "💰 **Hạn đóng tiền:**
                • Tháng 10: 25/10/2025
                • Cách thanh toán: 
                1. Chuyển khoản Vietcombank 123456789
                2. QR code tại quầy lễ tân
                Ghi rõ: 'Họ tên + Phòng số'";
        }

        if (strpos($message, 'sửa') !== false || strpos($message, 'hỏng') !== false || strpos($message, 'điện') !== false) {
            return "🔧 **Báo sửa chữa:**
                1. Gửi ảnh + mô tả vào đây
                2. Nhân viên sẽ đến trong 24h
                3. Hotline: 1900-xxx-xxx";
        }

        if (strpos($message, 'wifi') !== false) {
            return "📶 **WiFi ký túc xá:**
                • Tên: KTX_WiFi_2025
                • Pass: ktx2025@123
                • Tốc độ: 100Mbps
                Liên hệ admin nếu yếu!";
        }

        if (strpos($message, 'giờ') !== false || strpos($message, 'cửa') !== false) {
            return "⏰ **Giờ giấc:**
                • Cửa chính: 6h-22h
                • Sau 22h: Cổng phụ (thẻ từ)
                • Cuối tuần: 24/7";
        }

        // Trả lời chung
        $replies = [
            "Tôi có thể giúp bạn về phòng ở, thanh toán, sửa chữa, wifi, giờ giấc ký túc xá!",
            "Hỏi tôi: 'phòng trống', 'đóng tiền', 'báo sửa', 'wifi pass' nhé!",
            "Tôi sẵn sàng hỗ trợ bạn 24/7! 😊",
            "Bạn cần giúp gì về ký túc xá ạ?"
        ];

        return $replies[array_rand($replies)];
    }
}
