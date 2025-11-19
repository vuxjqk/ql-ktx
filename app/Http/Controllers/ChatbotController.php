<?php

namespace App\Http\Controllers;

use Gemini\Laravel\Facades\Gemini;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function handleChat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $userMessage = $request->input('message');

<<<<<<< HEAD
        try {
            $response = Gemini::generativeModel(model: 'gemini-2.0-flash')
                ->generateContent($userMessage);

            $replyText = $response->text() ?? 'Xin lỗi, tôi không hiểu yêu cầu của bạn.';
=======
        $systemPrompt = "Bạn là trợ lý AI cho hệ thống quản lý ký túc xá sinh viên. Hãy trả lời các câu hỏi một cách hữu ích, chính xác và liên quan đến các chủ đề như: đăng ký phòng ở, thông tin phòng, thanh toán phí, báo cáo sự cố bảo trì, quy định ký túc xá, lịch trình sự kiện, và các vấn đề liên quan đến sinh viên ở ký túc xá. Nếu câu hỏi không liên quan, hãy lịch sự hướng dẫn người dùng quay lại chủ đề chính. Tin nhắn từ người dùng: ";

        $fullPrompt = $systemPrompt . $userMessage;

        try {
            $response = Gemini::generativeModel(model: 'gemini-2.0-flash')
                ->generateContent($fullPrompt);

            $replyText = $response->text() ?? 'Xin lỗi, tôi không hiểu yêu cầu của bạn. Bạn có thể hỏi về ký túc xá sinh viên không?';
>>>>>>> upstream-main
        } catch (\Throwable $e) {
            Log::error('Gemini chat error: ' . $e->getMessage());
            $replyText = 'Đã có lỗi xảy ra. Vui lòng thử lại sau.';
        }

        return response()->json([
            'reply' => $replyText,
        ]);
    }
}
