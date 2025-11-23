<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Hóa đơn') }}</title>
    <style>
        @page {
            size: A4;
            margin: 0.8cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            background: white;
        }

        .invoice {
            max-width: 210mm;
            margin: 0 auto;
            padding: 15px;
        }

        .header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #0066cc;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
            padding: 0;
        }

        .logo-cell {
            width: 50%;
            padding-right: 10px;
        }

        .logo-wrapper {
            width: 100%;
        }

        .logo-table {
            width: 100%;
        }

        .logo-table td {
            vertical-align: middle;
        }

        .logo-img-cell {
            width: 70px;
            padding-right: 15px;
        }

        .logo {
            width: 70px;
            height: 70px;
        }

        .logo img {
            width: 100%;
            height: 100%;
        }

        .company-info {
            font-size: 11px;
            line-height: 1.6;
            margin-left: 16px;
        }

        .company-info h3 {
            font-size: 15px;
            margin-bottom: 5px;
            font-weight: bold;
            color: #0066cc;
        }

        .company-info div {
            margin-bottom: 2px;
        }

        .invoice-title-cell {
            width: 50%;
            text-align: center;
            padding-left: 10px;
        }

        .invoice-title h1 {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 8px;
            letter-spacing: 2px;
            color: #0066cc;
        }

        .invoice-meta {
            font-size: 13px;
            font-weight: bold;
        }

        .invoice-meta .code {
            color: #0066cc;
        }

        .customer-info {
            margin-bottom: 20px;
        }

        .customer-info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .customer-info-table td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }

        .customer-info-table td:first-child {
            padding-right: 10px;
        }

        .customer-info-table td:last-child {
            padding-left: 10px;
        }

        .customer-details,
        .order-details {
            height: 255px;
            border: 1px solid #0066cc;
            padding: 12px;
            background-color: #f0f4ff;
        }

        .customer-details h3,
        .order-details h3 {
            font-size: 13px;
            margin-bottom: 10px;
            border-bottom: 2px solid #0066cc;
            padding-bottom: 5px;
            font-weight: bold;
            color: #0066cc;
        }

        .info-row {
            margin-bottom: 6px;
            font-size: 11px;
        }

        .info-row table {
            width: 100%;
        }

        .info-label {
            font-weight: bold;
            width: 90px;
            color: #333;
        }

        .info-value {
            word-break: break-word;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .products-table th {
            background-color: #0066cc;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #0066cc;
            font-size: 12px;
        }

        .products-table td {
            padding: 9px 8px;
            border: 1px solid #ddd;
            vertical-align: middle;
            font-size: 11px;
        }

        .products-table tr:nth-child(even) {
            background-color: #f9fbff;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-number {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }

        .summary {
            margin-bottom: 20px;
            text-align: right;
        }

        .summary-table {
            display: inline-table;
            border-collapse: collapse;
            border: 1px solid #0066cc;
        }

        .summary-table tr {
            border-bottom: 1px solid #0066cc;
        }

        .summary-table tr:last-child {
            border-bottom: none;
        }

        .summary-table td {
            padding: 10px 12px;
            font-size: 12px;
        }

        .summary-table .label {
            font-weight: bold;
            background-color: #f0f4ff;
            width: 50%;
            color: #0066cc;
        }

        .summary-table .value {
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        .summary-table .total-row .label,
        .summary-table .total-row .value {
            background-color: #0066cc;
            color: white;
            font-weight: bold;
            font-size: 13px;
        }

        .payment-status {
            display: inline-block;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 11px;
        }

        .status-unpaid {
            background-color: #ffcccc;
            color: #cc0000;
        }

        .status-paid {
            background-color: #ccffcc;
            color: #00cc00;
        }

        .status-partial {
            background-color: #ffffcc;
            color: #cc6600;
        }

        .status-cancelled {
            background-color: #e6e6e6;
            color: #666666;
        }

        .footer {
            margin-top: 25px;
            clear: both;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }

        .footer-table td:first-child {
            padding-right: 7px;
        }

        .footer-table td:last-child {
            padding-left: 7px;
        }

        .notes h4,
        .signature h4 {
            font-size: 12px;
            margin-bottom: 8px;
            border-bottom: 2px solid #0066cc;
            padding-bottom: 3px;
            font-weight: bold;
            color: #0066cc;
        }

        .notes p {
            font-size: 10px;
            line-height: 1.4;
            margin-bottom: 3px;
        }

        .signature-box {
            border: 1px dashed #0066cc;
            height: 70px;
            margin-top: 8px;
            text-align: center;
            padding-top: 28px;
            font-size: 10px;
            color: #999;
        }

        .signature-name {
            text-align: center;
            margin-top: 8px;
            font-size: 11px;
        }

        .signature-name strong {
            display: block;
            margin-bottom: 2px;
        }

        .thank-you {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            font-weight: bold;
            color: #0066cc;
            padding-top: 10px;
            border-top: 2px solid #0066cc;
        }

        .footer-info {
            text-align: center;
            font-size: 9px;
            color: #999;
            margin-top: 10px;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="invoice">
        <!-- Header -->
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="logo-cell">
                        <table class="logo-table">
                            <tr>
                                <td class="logo-img-cell">
                                    <div class="logo">
                                        <img src="{{ public_path('storage/logo/huit.jpg') }}" alt="HUIT">
                                    </div>
                                </td>
                                <td>
                                    <div class="company-info">
                                        <h3>{{ __('HỆ THỐNG KÝ TÚC XÁ SINH VIÊN') }}</h3>
                                        <div><strong>{{ __('Trường Đại học Công Thương TP. HCM') }}</strong></div>
                                        <div>{{ __('Địa chỉ') }}:
                                            {{ __('140 Lê Trọng Tấn, Phường Tây Thạnh, TP. HCM') }}</div>
                                        <div>{{ __('Điện thoại') }}: {{ __('0283 8163 318') }}</div>
                                        <div>{{ __('Email') }}: {{ __('info@huit.edu.vn') }}</div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="invoice-title-cell">
                        <div class="invoice-title">
                            <h1>{{ __('HÓA ĐƠN') }}</h1>
                            <div class="invoice-meta">
                                <span>{{ __('Số') }}:</span>
                                <span class="code">{{ $bill->bill_code }}</span>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Customer Information -->
        <div class="customer-info">
            <table class="customer-info-table">
                <tr>
                    <td>
                        <div class="customer-details">
                            <h3>{{ __('THÔNG TIN SINH VIÊN') }}</h3>
                            <div class="info-row">
                                <table>
                                    <tr>
                                        <td class="info-label">{{ __('Họ tên') }}:</td>
                                        <td class="info-value"><strong>{{ $bill->user->name }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="info-row">
                                <table>
                                    <tr>
                                        <td class="info-label">{{ __('Mã sinh viên') }}:</td>
                                        <td class="info-value">{{ $bill->user->student?->student_code }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="info-row">
                                <table>
                                    <tr>
                                        <td class="info-label">{{ __('Email') }}:</td>
                                        <td class="info-value">{{ $bill->user->email ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="info-row">
                                <table>
                                    <tr>
                                        <td class="info-label">{{ __('Địa chỉ') }}:</td>
                                        <td class="info-value">{{ $bill->user->student?->address ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="info-row">
                                <table>
                                    <tr>
                                        <td class="info-label">{{ __('Số điện thoại') }}:</td>
                                        <td class="info-value">{{ $bill->user->phone ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="order-details">
                            <h3>{{ __('THÔNG TIN HÓA ĐƠN') }}</h3>
                            <div class="info-row">
                                <table>
                                    <tr>
                                        <td class="info-label">{{ __('Mã hóa đơn') }}:</td>
                                        <td class="info-value"><strong>{{ $bill->bill_code }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="info-row">
                                <table>
                                    <tr>
                                        <td class="info-label">{{ __('Ngày lập') }}:</td>
                                        <td class="info-value">{{ $bill->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="info-row">
                                <table>
                                    <tr>
                                        <td class="info-label">{{ __('Hạn thanh toán') }}:</td>
                                        <td class="info-value">
                                            {{ $bill->due_date ? $bill->due_date->format('d/m/Y') : 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="info-row">
                                <table>
                                    <tr>
                                        <td class="info-label">{{ __('Phòng') }}:</td>
                                        <td class="info-value">{{ $bill->booking->room->room_code }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="info-row">
                                <table>
                                    <tr>
                                        <td class="info-label">{{ __('Chi nhánh') }}:</td>
                                        <td class="info-value">{{ $bill->booking->room->floor->branch->name }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="info-row">
                                <table>
                                    <tr>
                                        <td class="info-label">{{ __('Thời gian cư trú') }}:</td>
                                        <td class="info-value">
                                            {{ $bill->booking->check_in_date->format('d/m/Y') }} -
                                            {{ $bill->booking->expected_check_out_date->format('d/m/Y') }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="info-row">
                                <table>
                                    <tr>
                                        <td class="info-label">{{ __('Trạng thái') }}:</td>
                                        <td class="info-value">
                                            @switch($bill->status)
                                                @case('unpaid')
                                                    <span
                                                        class="payment-status status-unpaid">{{ __('Chưa thanh toán') }}</span>
                                                @break

                                                @case('paid')
                                                    <span class="payment-status status-paid">{{ __('Đã thanh toán') }}</span>
                                                @break

                                                @case('partial')
                                                    <span
                                                        class="payment-status status-partial">{{ __('Thanh toán một phần') }}</span>
                                                @break

                                                @case('cancelled')
                                                    <span class="payment-status status-cancelled">{{ __('Đã hủy') }}</span>
                                                @break

                                                @default
                                                    {{ 'N/A' }}
                                            @endswitch
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Products Table -->
        <table class="products-table">
            <thead>
                <tr>
                    <th style="width: 40px;">{{ __('STT') }}</th>
                    <th style="width: 280px;">{{ __('Nội dung') }}</th>
                    <th style="width: 100px;" class="text-right">{{ __('Số lượng') }}</th>
                    <th style="width: 100px;" class="text-right">{{ __('Đơn giá') }}</th>
                    <th style="width: 120px;" class="text-right">{{ __('Thành tiền') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bill->bill_items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->description }}</td>
                        <td class="text-center">{{ $item->quantity ?? 1 }}</td>
                        <td class="text-right text-number">
                            {{ number_format($item->unit_price ?? $item->amount, 0, ',', '.') }}</td>
                        <td class="text-right text-number">{{ number_format($item->amount, 0, ',', '.') }} VND</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 20px; color: #999;">
                            {{ __('Không có dữ liệu hóa đơn') }}
                        </td>
                    </tr>
                @endforelse
                @if ($bill->bill_items->count() < 5)
                    @for ($i = $bill->bill_items->count(); $i < 5; $i++)
                        <tr>
                            <td style="height: 25px; border: 1px solid #ddd;"></td>
                            <td style="border: 1px solid #ddd;"></td>
                            <td style="border: 1px solid #ddd;"></td>
                            <td style="border: 1px solid #ddd;"></td>
                            <td style="border: 1px solid #ddd;"></td>
                        </tr>
                    @endfor
                @endif
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary">
            <table class="summary-table">
                <tr>
                    <td class="label">{{ __('Tổng cộng') }}:</td>
                    <td class="value text-number">{{ number_format($bill->total_amount, 0, ',', '.') }} VND</td>
                </tr>
                <tr>
                    <td class="label">{{ __('Đã thanh toán') }}:</td>
                    <td class="value text-number">{{ number_format($bill->paid_amount ?? 0, 0, ',', '.') }} VND</td>
                </tr>
                <tr>
                    <td class="label">{{ __('Còn phải thanh toán') }}:</td>
                    <td class="value text-number">
                        {{ number_format($bill->total_amount - ($bill->paid_amount ?? 0), 0, ',', '.') }} VND</td>
                </tr>
                <tr class="total-row">
                    <td class="label">{{ __('TỔNG TIỀN PHẢI THANH TOÁN') }}:</td>
                    <td class="value">{{ number_format($bill->total_amount, 0, ',', '.') }} VND</td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <table class="footer-table">
                <tr>
                    <td>
                        <div class="notes">
                            <h4>{{ __('GHI CHÚ') }}</h4>
                            <p>{{ __('• Hóa đơn này là chứng từ thanh toán hợp lệ theo quy định.') }}</p>
                            <p>{{ __('• Vui lòng thanh toán đầy đủ theo hạn thời gian quy định.') }}</p>
                            <p>{{ __('• Không giải quyết khiếu nại sau 7 ngày từ ngày phát hành.') }}</p>
                            <p>{{ __('• Cảm ơn bạn đã tin tưởng sử dụng dịch vụ của chúng tôi!') }}</p>
                        </div>
                    </td>
                    <td>
                        <div class="signature">
                            <h4>{{ __('CHỮ KÝ NGƯỜI LẬP') }}</h4>
                            <div class="signature-box">
                                {{ __('Ký tên và đóng dấu') }}
                            </div>
                            <div class="signature-name">
                                <strong>{{ __('Phòng Quản Lý Ký Túc Xá') }}</strong>
                                <small>{{ __('Ngày lập: ') }}{{ $bill->created_at->format('d/m/Y') }}</small>
                                <br>
                                <small>{{ __('Người lập: ') }}{{ $bill->creator->name }}</small>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="thank-you">
            {{ __('✓ Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi! Hẹn gặp lại!') }}
        </div>

        <div class="footer-info">
            {{ __('Được in từ hệ thống quản lý ký túc xá - In lúc: ') }}{{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>

</html>
