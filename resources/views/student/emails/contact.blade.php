{{-- resources/views/emails/contact.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tin nhắn mới từ sinh viên KTX</title>
</head>

<body style="margin:0; padding:0; background:#f4f6f9; font-family: 'Segoe UI', Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9; padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:white; border-radius:16px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td
                            style="background: linear-gradient(135deg, #3B82F6, #8B5CF6); padding:40px 30px; text-align:center;">
                            <h1 style="color:white; margin:0; font-size:28px; font-weight:bold;">
                                Hệ thống KTX HUIT
                            </h1>
                            <p style="color:#e0e7ff; margin:10px 0 0; font-size:16px;">
                                Tin nhắn mới từ sinh viên
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:40px 30px;">
                            <h2 style="color:#1e293b; margin-top:0;">
                                Có tin nhắn mới từ sinh viên!
                            </h2>

                            <div
                                style="background:#f8fafc; border-left:5px solid #3B82F6; padding:20px; margin:25px 0; border-radius:0 8px 8px 0;">
                                <p style="margin:0; color:#475569; line-height:1.7;">
                                    <strong>Họ tên:</strong> {{ $name }}<br>
                                    <strong>Email:</strong> <a href="mailto:{{ $email }}"
                                        style="color:#3B82F6;">{{ $email }}</a><br>
                                    <strong>Chủ đề:</strong> {{ $subject }}<br>
                                    <strong>Thời gian gửi:</strong> {{ $sent_at }}
                                </p>
                            </div>

                            <h3 style="color:#1e293b; margin-bottom:15px;">Nội dung tin nhắn:</h3>
                            <div
                                style="background:#f1f5f9; padding:20px; border-radius:12px; color:#334155; line-height:1.8; white-space: pre-wrap;">
                                {{ $messageText }}
                            </div>

                            <hr style="border:none; border-top:1px solid #e2e8f0; margin:40px 0;">

                            <p style="color:#64748b; font-size:14px; text-align:center;">
                                Đây là email tự động từ hệ thống Quản lý Ký túc xá HUIT<br>
                                Vui lòng trả lời trực tiếp email này để phản hồi sinh viên.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#1e293b; color:#94a3b8; padding:25px; text-align:center; font-size:13px;">
                            © 2025 Hệ thống Quản lý Ký túc xá Sinh viên HUIT<br>
                            Phát triển bởi Nhóm: KLCN_TH138: Trần Anh Vũ • Vũ Đình Ân • Trần Huỳnh Đức Anh
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        </tr>
    </table>
</body>

</html>
