<x-mail::message>
    # Hóa Đơn Ký Túc Xá

    Kính gửi {{ $bill->user->name }},

    Chúng tôi gửi bạn thông tin hóa đơn từ Hệ thống Ký túc xá Trường Đại học Công Thương TP. HCM:

    **Thông tin hóa đơn**
    - **Mã hóa đơn**: {{ $bill->bill_code }}
    - **Ngày lập**: {{ $bill->created_at->format('d/m/Y H:i') }}
    - **Hạn thanh toán**: {{ $bill->due_date ? $bill->due_date->format('d/m/Y') : 'N/A' }}
    - **Phòng**: {{ $bill->booking->room->room_code }}
    - **Thời gian cư trú**: {{ $bill->booking->check_in_date->format('d/m/Y') }} -
    {{ $bill->booking->expected_check_out_date->format('d/m/Y') }}
    - **Trạng thái**:
    {{ $bill->status == 'unpaid' ? 'Chưa thanh toán' : ($bill->status == 'paid' ? 'Đã thanh toán' : ($bill->status == 'partial' ? 'Thanh toán một phần' : 'Đã hủy')) }}

    **Chi tiết thanh toán**
    @forelse ($bill->bill_items as $index => $item)
        - {{ $item->description }}: {{ number_format($item->amount, 0, ',', '.') }} VND (Số lượng:
        {{ $item->quantity ?? 1 }})
    @empty
        Không có dữ liệu hóa đơn.
    @endforelse

    **Tổng tiền**: {{ number_format($bill->total_amount, 0, ',', '.') }} VND
    **Đã thanh toán**: {{ number_format($bill->paid_amount ?? 0, 0, ',', '.') }} VND
    **Còn phải thanh toán**: {{ number_format($bill->total_amount - ($bill->paid_amount ?? 0), 0, ',', '.') }} VND

    **Ghi chú**:
    - Vui lòng thanh toán đầy đủ trước hạn thanh toán.
    - Mọi thắc mắc vui lòng liên hệ qua email: info@huit.edu.vn hoặc số điện thoại: 0283 8163 318.

    Trân trọng,
    Hệ thống Ký túc xá - Trường Đại học Công Thương TP. HCM
</x-mail::message>
