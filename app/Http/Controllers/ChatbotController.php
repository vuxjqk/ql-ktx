<?php

namespace App\Http\Controllers;

use Gemini\Laravel\Facades\Gemini;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function handleChat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $userMessage = $request->input('message');

        // Prompt để Gemini generate SQL query từ câu hỏi người dùng, chỉ liên quan đến các bảng branches, floors, rooms
        $sqlPrompt = "Bạn là trợ lý AI chuyên generate SQL query. Dựa trên câu hỏi tiếng Việt của người dùng: '{$userMessage}', hãy tạo một câu SQL SELECT chính xác và sử dụng cú pháp chuẩn SQL (ANSI), có thể chạy được trên cả MySQL và PostgreSQL, để truy vấn thông tin từ các bảng branches, floors và rooms. Các bảng có schema sau:
- branches: id, name, address, created_at, updated_at.
- floors: id, floor_number, branch_id (foreign to branches.id), gender_type (male/female/mixed), created_at, updated_at.
- rooms: id, room_code, floor_id (foreign to floors.id), price_per_day, price_per_month, capacity, current_occupancy, is_active, description, created_at, updated_at.
Bạn có thể sử dụng JOIN nếu cần để kết nối các bảng. Chỉ generate SQL nếu câu hỏi liên quan đến thông tin về chi nhánh (branches), tầng (floors), hoặc phòng (rooms) như tên, địa chỉ, số tầng, loại giới tính, giá, dung lượng, mô tả, v.v. Nếu không liên quan, trả về chuỗi rỗng ''. Không thêm giải thích, chỉ trả về SQL query hoặc ''. Đừng dùng markdown như ```sql, chỉ trả về query thuần.";

        try {
            $sqlResponse = Gemini::generativeModel(model: 'gemini-2.0-flash')
                ->generateContent($sqlPrompt);

            $generatedSql = trim($sqlResponse->text() ?? '');

            // Xử lý nếu Gemini trả về với markdown ```sql ... ```
            if (strpos($generatedSql, '```sql') === 0) {
                $generatedSql = trim(substr($generatedSql, 6)); // Bỏ ```sql
                $generatedSql = trim(str_replace('```', '', $generatedSql)); // Bỏ ``` cuối nếu có
            }

            $queryResults = [];
            if (!empty($generatedSql)) {
                // Chạy SQL query (chỉ SELECT để an toàn)
                if (stripos($generatedSql, 'SELECT') === 0) {
                    $queryResults = DB::select($generatedSql);
                } else {
                    throw new \Exception('Invalid SQL query generated.');
                }
            }

            // Chuyển kết quả thành JSON để đưa vào prompt
            $jsonResults = json_encode($queryResults);

            // System prompt chính cho Gemini để diễn giải kết quả hoặc trả lời nếu không có query
            $systemPrompt = "Bạn là trợ lý AI cho hệ thống quản lý ký túc xá sinh viên. Hãy trả lời các câu hỏi một cách hữu ích, chính xác và liên quan đến các chủ đề như: đăng ký phòng ở, thông tin phòng, thanh toán phí, báo cáo sự cố bảo trì, quy định ký túc xá, lịch trình sự kiện, và các vấn đề liên quan đến sinh viên ở ký túc xá. Nếu câu hỏi không liên quan, hãy lịch sự hướng dẫn người dùng quay lại chủ đề chính. Dựa trên kết quả truy vấn từ các bảng branches, floors, rooms (nếu có): {$jsonResults}, hãy diễn giải thành tiếng Việt tự nhiên để trả lời câu hỏi của người dùng. Tin nhắn từ người dùng: ";

            $fullPrompt = $systemPrompt . $userMessage;

            $response = Gemini::generativeModel(model: 'gemini-2.0-flash')
                ->generateContent($fullPrompt);

            $replyText = $response->text() ?? 'Xin lỗi, tôi không hiểu yêu cầu của bạn. Bạn có thể hỏi về ký túc xá sinh viên không?';
        } catch (\Throwable $e) {
            Log::error('Gemini chat or SQL error: ' . $e->getMessage());
            $replyText = 'Đã có lỗi xảy ra. Vui lòng thử lại sau.';
        }

        return response()->json([
            'reply' => $replyText,
        ]);
    }
}
