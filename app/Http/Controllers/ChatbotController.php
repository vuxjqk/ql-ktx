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

        try {
            $response = Gemini::generativeModel(model: 'gemini-2.0-flash')
                ->generateContent($userMessage);

            $replyText = $response->text() ?? 'Xin lỗi, tôi không hiểu yêu cầu của bạn.';
        } catch (\Throwable $e) {
            Log::error('Gemini chat error: ' . $e->getMessage());
            $replyText = 'Đã có lỗi xảy ra. Vui lòng thử lại sau.';
        }

        return response()->json([
            'reply' => $replyText,
        ]);
    }
}
