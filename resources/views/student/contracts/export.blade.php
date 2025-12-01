<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>HỢP ĐỒNG THUÊ PHÒNG KÝ TÚC XÁ</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm 1.8cm;
        }

        body {
            font-family: 'DejaVu Sans', 'Times New Roman', serif;
            font-size: 13px;
            line-height: 1.6;
            color: #000;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-bold {
            font-weight: bold;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .mt-30 {
            margin-top: 30px;
        }

        .underline {
            text-decoration: underline;
        }

        .header-logo {
            text-align: center;
            margin-bottom: 15px;
        }

        .header-logo img {
            height: 80px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            color: #0066cc;
        }

        .contract-code {
            text-align: center;
            font-size: 15px;
            margin-bottom: 30px;
            color: #0066cc;
        }

        .parties table {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }

        .parties td {
            padding: 8px 0;
            vertical-align: top;
        }

        .party-label {
            width: 120px;
            font-weight: bold;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            color: #0066cc;
            border-bottom: 2px solid #0066cc;
            padding-bottom: 5px;
            margin: 25px 0 15px 0;
        }

        .article {
            margin-bottom: 15px;
        }

        .article-title {
            font-weight: bold;
            margin-bottom: 8px;
        }

        ul {
            padding-left: 20px;
            margin: 8px 0;
        }

        li {
            margin-bottom: 6px;
        }

        .signature-table {
            width: 100%;
            margin-top: 50px;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-box {
            height: 100px;
            border-bottom: 1px solid #000;
            margin-bottom: 10px;
            position: relative;
        }

        .signature-name {
            font-weight: bold;
            font-size: 14px;
        }

        .footer-note {
            margin-top: 40px;
            font-size: 11px;
            text-align: center;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="container">

        <!-- Logo & Header -->
        <div class="header-logo">
            <img src="{{ public_path('storage/logo/huit.jpg') }}" alt="HUIT Logo">
        </div>

        <div class="text-center mb-20">
            <strong>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</strong><br>
            <strong>Độc lập - Tự do - Hạnh phúc</strong>
            <div style="border-top: 1px solid #000; width: 150px; margin: 8px auto;"></div>
        </div>

        <div class="title uppercase">HỢP ĐỒNG THUÊ PHÒNG KÝ TÚC XÁ</div>

        <div class="contract-code">
            Số: <strong>{{ $contract->contract_code }}</strong><br>
            Ngày lập: <strong>{{ $contract->created_at->format('d/m/Y') }}</strong>
        </div>

        <!-- Các bên tham gia -->
        <div class="section-title">I. CÁC BÊN THAM GIA HỢP ĐỒNG</div>
        <table class="parties">
            <tr>
                <td class="party-label">BÊN A<br>(Bên cho thuê):</td>
                <td>
                    <strong>PHÒNG QUẢN LÝ KÝ TÚC XÁ - TRƯỜNG ĐẠI HỌC CÔNG THƯƠNG TP.HCM (HUIT)</strong><br>
                    Địa chỉ: 140 Lê Trọng Tấn, Phường Tây Thạnh, Quận Tân Phú, TP. Hồ Chí Minh<br>
                    Điện thoại: 0283 8163 318 | Email: ktx@huit.edu.vn<br>
                    Đại diện: Ban Quản lý Ký túc xá
                </td>
            </tr>
            <tr>
                <td class="party-label">BÊN B<br>(Bên thuê):</td>
                <td>
                    <strong>{{ $contract->booking->user->name }}</strong><br>

                    <span style="display:inline-block; margin-right:50px;">
                        Mã sinh viên: <strong>{{ $contract->booking->user->student?->student_code ?? 'N/A' }}</strong>
                    </span>
                    <span style="display:inline-block;">
                        Lớp: {{ $contract->booking->user->student?->class ?? '..................' }}
                    </span>
                    <br>

                    <span style="display:inline-block; margin-right:50px;">
                        Ngày sinh:
                        {{ $contract->booking->user->student?->date_of_birth?->format('d/m/Y') ?? '..................' }}
                    </span>
                    @php
                        $gender = $contract->booking->user->student?->gender;
                        $genderLabel = match ($gender) {
                            'male' => 'Nam',
                            'female' => 'Nữ',
                            default => 'Khác',
                        };
                    @endphp
                    <span style="display:inline-block;">
                        Giới tính: {{ $genderLabel }}
                    </span>
                    <br>

                    Số điện thoại: {{ $contract->booking->user->student?->phone ?? '..................' }}<br>
                    Địa chỉ thường trú:
                    {{ $contract->booking->user->student?->address ?? '........................................................................' }}
                </td>
            </tr>
        </table>

        <div class="section-title">II. NỘI DUNG HỢP ĐỒNG</div>

        <div class="article">
            <div class="article-title">Điều 1: Đối tượng hợp đồng</div>
            Bên A đồng ý cho Bên B thuê phòng ký túc xá với thông tin như sau:<br>
            • Phòng số: <strong>{{ $contract->booking->room->room_code }}</strong><br>
            • Tầng: {{ $contract->booking->room->floor->floor_number ?? '' }} -
            Chi nhánh: <strong>{{ $contract->booking->room->floor->branch->name }}</strong><br>
            • Loại thuê:
            <strong>{{ $contract->booking->rental_type == 'monthly' ? 'Theo tháng' : 'Theo ngày' }}</strong><br>
            • Thời gian thuê: Từ ngày <strong>{{ $contract->booking->check_in_date->format('d/m/Y') }}</strong>
            đến ngày <strong>{{ $contract->booking->expected_check_out_date->format('d/m/Y') }}</strong>
        </div>

        <div class="article">
            <div class="article-title">Điều 2: Giá thuê và phương thức thanh toán</div>
            • Giá thuê phòng hàng tháng: <strong>{{ number_format($contract->monthly_fee, 0, ',', '.') }}
                VND/tháng</strong><br>
            • Tiền đặt cọc: <strong>{{ number_format($contract->deposit, 0, ',', '.') }} VND</strong>
            (Bằng {{ $contract->deposit > 0 ? 'một' : 'không' }} tháng tiền phòng, được hoàn lại khi trả phòng đúng quy
            định)<br>
            • Phương thức thanh toán: Chuyển khoản hoặc nộp tiền mặt tại Phòng Quản lý KTX hàng tháng.
        </div>

        <div class="article">
            <div class="article-title">Điều 3: Quyền và nghĩa vụ các bên</div>
            <strong>Bên A có quyền:</strong>
            <ul>
                <li>Thu tiền phòng và tiền đặt cọc đúng hạn.</li>
                <li>Thu hồi phòng nếu Bên B vi phạm nội quy hoặc không thanh toán đúng hạn quá 15 ngày.</li>
            </ul>

            <strong>Bên B có nghĩa vụ:</strong>
            <ul>
                <li>Thanh toán đầy đủ và đúng hạn tiền phòng, điện nước, dịch vụ.</li>
                <li>Tuân thủ nội quy ký túc xá, giữ gìn tài sản chung.</li>
                <li>Giữ gìn vệ sinh, không tự ý sửa chữa, cải tạo phòng.</li>
                <li>Báo hỏng hóc thiết bị trong vòng 24h.</li>
                <li>Trả phòng đúng hạn, bàn giao đầy đủ tài sản khi kết thúc hợp đồng.</li>
            </ul>
        </div>

        <div class="article">
            <div class="article-title">Điều 4: Hiệu lực hợp đồng</div>
            Hợp đồng có hiệu lực kể từ ngày ký và kết thúc khi Bên B trả phòng hợp lệ hoặc bị chấm dứt theo quy định.
        </div>

        <div class="article">
            <div class="article-title">Điều 5: Cam kết chung</div>
            Hai bên đã đọc kỹ, hiểu rõ và tự nguyện ký kết hợp đồng này. Hợp đồng được lập thành 02 bản có giá trị pháp
            lý ngang nhau, mỗi bên giữ 01 bản.
        </div>

        <!-- Chữ ký -->
        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-name">ĐẠI DIỆN BÊN A</div>
                    <div class="signature-box"></div>
                    <div>(Ký, ghi rõ họ tên & đóng dấu)</div>
                </td>
                <td>
                    <div class="signature-name">ĐẠI DIỆN BÊN B</div>
                    <div class="signature-box"></div>
                    <div>(Ký và ghi rõ họ tên)</div>
                    <br>
                    <strong>{{ $contract->booking->user->name }}</strong>
                </td>
            </tr>
        </table>

        <div class="footer-note mt-30">
            <em>Hợp đồng được in từ Hệ thống Quản lý Ký túc xá HUIT -
                Ngày in: {{ now()->format('d/m/Y H:i') }}</em>
        </div>
    </div>
</body>

</html>
