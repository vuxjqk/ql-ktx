{{-- resources/views/student/bookings/index.blade.php --}}
@extends('student.layouts.app')

@section('title', 'Quản lý phòng ở')

@pushOnce('styles')
    <style>
        .expandable:hover {
            background-color: #f9fafb;
        }

        .collapse-content {
            transition: all 0.3s ease;
        }
    </style>
@endPushOnce

@section('content')
    <div class="bg-gray-50 py-4 border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-sm">
                <ol class="inline-flex items-center space-x-2">
                    <li><a href="{{ route('student.home') }}" class="text-gray-600 hover:text-blue-600"><i
                                class="fas fa-home"></i></a></li>
                    <li><i class="fas fa-chevron-right text-gray-400 mx-2"></i></li>
                    <li class="text-gray-900 font-medium">Quản lý phòng ở</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Booking mới (pending/approved) --}}
        @if ($newBooking)
            <div class="mb-8 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold">Đăng ký phòng mới đang chờ xử lý</h3>
                        <p class="mt-2">
                            Phòng: <strong>{{ $newBooking->room->room_code }}</strong> •
                            Trạng thái: <span class="status-badge bg-white text-amber-700">
                                {{ $newBooking->status === 'pending' ? 'Đang chờ duyệt' : 'Đã duyệt' }}
                            </span>
                        </p>
                    </div>
                    @if ($newBooking)
                        @if ($newBooking->status === 'pending')
                            <form action="{{ route('student.bookings.cancel', $newBooking) }}" method="POST"
                                class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Bạn có chắc muốn hủy yêu cầu này?')"
                                    class="bg-white text-amber-600 font-bold px-6 py-3 rounded-lg hover:bg-amber-50 transition shadow-lg inline-flex items-center">
                                    <i class="fas fa-times mr-2"></i>Hủy yêu cầu
                                </button>
                            </form>
                        @elseif ($activeBooking)
                            <span class="bg-red-600 px-6 py-3 rounded-lg font-bold text-white">
                                <i class="fas fa-exclamation-circle mr-2"></i>Vui lòng rời phòng cũ trước khi thanh toán
                            </span>
                        @elseif ($newBooking->status === 'approved' && $newBooking->bills->isNotEmpty())
                            <div class="relative inline-block" x-data="{ open: false }">
                                <button @click.prevent="open = !open"
                                    class="bg-white text-amber-600 font-bold px-6 py-3 rounded-lg hover:bg-amber-50 transition shadow-lg inline-flex items-center">
                                    <i class="fas fa-credit-card mr-2"></i>Thanh toán đặt cọc ngay
                                </button>
                                <div x-show="open" @click.away="open = false" x-cloak
                                    class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden">
                                    <div class="py-2">
                                        <a href="{{ route('student.vnpay.redirect', $newBooking->bills->first()) }}"
                                            class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors flex items-center">
                                            VNPAY
                                        </a>
                                        <a href="{{ route('student.zalopay.redirect', $newBooking->bills->first()) }}"
                                            class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors flex items-center">
                                            ZALOPAY
                                        </a>
                                        <a href="{{ route('student.payments.store', $newBooking->bills->first()) }}"
                                            class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors flex items-center">
                                            Test
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <span class="bg-green-600 px-6 py-3 rounded-lg font-bold text-white">
                                <i class="fas fa-check mr-2"></i>Đã thanh toán đặt cọc
                            </span>
                        @endif
                    @endif
                </div>
            </div>
        @endif

        @if (!$activeBooking)
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <i class="fas fa-bed text-gray-300 text-7xl mb-6"></i>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Bạn chưa có phòng đang ở</h2>
                <p class="text-gray-600 mb-8">Hãy chọn phòng phù hợp và đăng ký ngay hôm nay!</p>
                <a href="{{ route('student.rooms.index') }}"
                    class="bg-blue-600 text-white px-8 py-4 rounded-lg font-bold hover:bg-blue-700 transition">
                    <i class="fas fa-search mr-2"></i>Tìm phòng ngay
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Nội dung chính -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- Thông tin phòng hiện tại -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-6">
                            <h2 class="text-2xl font-bold"><i class="fas fa-door-open mr-3"></i>Phòng đang ở</h2>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-sm text-gray-600">Mã phòng</p>
                                    <p class="text-xl font-bold text-gray-900">{{ $room->room_code }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Vị trí</p>
                                    <p class="text-xl font-bold text-gray-900">{{ $room->floor->branch->name }} - Tầng
                                        {{ $room->floor->floor_number }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Loại thuê</p>
                                    <p class="text-xl font-bold text-gray-900">
                                        {{ $activeBooking->rental_type === 'monthly' ? 'Theo tháng' : 'Theo ngày' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Ngày nhận phòng</p>
                                    <p class="text-xl font-bold text-gray-900">
                                        {{ $activeBooking->check_in_date->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Ngày trả phòng</p>
                                    <p class="text-xl font-bold text-gray-900">
                                        {{ $activeBooking->expected_check_out_date->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            @if ($contract)
                                <div class="pt-4 border-t grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <p class="text-sm text-gray-600">Mã hợp đồng</p>
                                        <p class="text-xl font-bold text-gray-900">
                                            {{ $contract->contract_code }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Phí hàng tháng</p>
                                        <p class="text-xl font-bold text-gray-900">
                                            {{ number_format($contract->monthly_fee) }}đ
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Tiền đặt cọc</p>
                                        <p class="text-xl font-bold text-gray-900">
                                            {{ number_format($contract->deposit) }}đ
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">File hợp đồng</p>
                                        <p class="text-xl font-bold text-gray-900">
                                            @if ($contract->contract_file)
                                                <a href="{{ asset('storage/' . $contract->contract_file) }}"
                                                    target="_blank" class="text-blue-600 hover:underline">
                                                    Tải file
                                                </a>
                                            @else
                                                Không có file
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endif
                            <div class="pt-4 border-t flex flex-wrap gap-3">
                                <a href="{{ route('student.rooms.show', $room) }}"
                                    class="text-blue-600 hover:underline font-medium">
                                    <i class="fas fa-eye mr-1"></i>Xem chi tiết phòng
                                </a>

                                @if (is_null($activeBooking->actual_check_out_date))
                                    @php
                                        $daysRemaining = now()->diffInDays($activeBooking->expected_check_out_date);
                                    @endphp

                                    @if ($daysRemaining <= 7 && $daysRemaining >= 0)
                                        <button x-data=""
                                            x-on:click="$dispatch('open-modal', 'extend-booking')"
                                            class="text-green-600 hover:underline font-medium">
                                            <i class="fas fa-clock mr-1"></i>Gia hạn phòng
                                        </button>
                                    @endif

                                    <button x-data=""
                                        x-on:click="$dispatch('open-modal', 'terminate-booking')"
                                        class="text-red-600 hover:underline font-medium">
                                        <i class="fas fa-times-circle mr-1"></i>Yêu cầu rời phòng
                                    </button>
                                @else
                                    <span class="text-gray-500 font-medium">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Đã yêu cầu rời phòng vào
                                        {{ $activeBooking->actual_check_out_date->format('d/m/Y') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Hóa đơn & Thanh toán (có Expand/Collapse) -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 text-white p-6">
                            <h2 class="text-2xl font-bold"><i class="fas fa-receipt mr-3"></i>Hóa đơn & Thanh toán</h2>
                        </div>
                        <div class="p-6 space-y-6">
                            @forelse($bills as $bill)
                                <div class="border border-gray-200 rounded-lg expandable cursor-pointer"
                                    x-data="{ open: false }">
                                    <div class="p-5 flex items-center justify-between" x-on:click="open = !open">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-4">
                                                <h4 class="font-bold text-lg">Hóa đơn #{{ $bill->bill_code }}</h4>
                                                <span
                                                    class="status-badge {{ $bill->status === 'paid' ? 'bg-green-600 text-white' : ($bill->status === 'unpaid' ? 'bg-red-600 text-white' : 'bg-amber-600 text-white') }}">
                                                    {{ $bill->status === 'paid' ? 'Đã thanh toán' : ($bill->status === 'unpaid' ? 'Chưa thanh toán' : 'Chờ xử lý') }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">
                                                Tổng tiền: <strong>{{ number_format($bill->total_amount) }}₫</strong> •
                                                Hạn thanh toán: {{ $bill->due_date?->format('d/m/Y') ?? 'Không giới hạn' }}
                                            </p>
                                        </div>
                                        <i class="fas fa-chevron-down transition" :class="{ 'rotate-180': open }"></i>
                                    </div>

                                    <!-- Nội dung mở rộng -->
                                    <div x-show="open" class="collapse-content border-t border-gray-200 bg-gray-50"
                                        x-collapse>
                                        <div class="p-5 space-y-4">
                                            <!-- Chi tiết hóa đơn -->
                                            <div>
                                                <h5 class="font-semibold text-gray-800 mb-3"><i
                                                        class="fas fa-list mr-2"></i>Chi tiết hóa đơn</h5>
                                                <div class="space-y-2">
                                                    @foreach ($bill->bill_items as $item)
                                                        <div class="flex justify-between text-sm">
                                                            <span>{{ $item->description }}</span>
                                                            <span
                                                                class="font-medium">{{ number_format($item->amount) }}₫</span>
                                                        </div>
                                                    @endforeach
                                                    <div class="border-t pt-2 font-bold text-lg">
                                                        <div class="flex justify-between">
                                                            <span>Tổng cộng</span>
                                                            <span
                                                                class="text-emerald-600">{{ number_format($bill->total_amount) }}₫</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Giao dịch thanh toán -->
                                            @if ($bill->payments->count() > 0)
                                                <div>
                                                    <h5 class="font-semibold text-gray-800 mb-3"><i
                                                            class="fas fa-exchange-alt mr-2"></i>Giao dịch thanh toán</h5>
                                                    <div class="space-y-2">
                                                        @foreach ($bill->payments as $payment)
                                                            <div class="bg-white p-3 rounded border">
                                                                <div class="flex justify-between text-sm">
                                                                    <span>
                                                                        {{ $payment->transaction ? '#' . $payment->transaction->transaction_code : 'Chưa có mã giao dịch' }}
                                                                    </span>
                                                                    <span class="font-medium text-green-600">
                                                                        {{ number_format($payment->transaction ? $payment->transaction->amount : $payment->amount) }}₫
                                                                    </span>
                                                                </div>
                                                                <p class="text-xs text-gray-500 mt-1">
                                                                    {{ $payment->paid_at->format('d/m/Y H:i') }} •
                                                                    {{ $payment->payment_type === 'online' ? 'Trực tuyến' : 'Tiền mặt' }}
                                                                </p>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Nút thanh toán -->
                                            @if ($bill->status === 'unpaid')
                                                <div class="pt-4 border-t text-right">
                                                    <div class="relative inline-block" x-data="{ open: false }">
                                                        <button @click.prevent="open = !open"
                                                            class="inline-flex items-center bg-emerald-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-emerald-700 transition">
                                                            <i class="fas fa-credit-card mr-2"></i>Thanh toán ngay
                                                        </button>
                                                        <div x-show="open" @click.away="open = false" x-cloak
                                                            class="absolute right-0 bottom-full mb-2 w-64 bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden">
                                                            <div class="py-2">
                                                                <a href="{{ route('student.vnpay.redirect', $bill) }}"
                                                                    class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors flex items-center">
                                                                    VNPAY
                                                                </a>
                                                                <a href="{{ route('student.zalopay.redirect', $bill) }}"
                                                                    class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors flex items-center">
                                                                    ZALOPAY
                                                                </a>
                                                                <a href="{{ route('student.payments.store', $bill) }}"
                                                                    class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors flex items-center">
                                                                    Test
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12 text-gray-500">
                                    <i class="fas fa-receipt text-6xl text-gray-300 mb-4"></i>
                                    <p>Chưa có hóa đơn nào</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Sidebar: Báo sửa chữa -->
                <div class="space-y-6">
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold"><i class="fas fa-tools text-blue-600 mr-2"></i>Báo sửa chữa</h3>
                            <button x-data="" x-on:click="$dispatch('open-modal', 'create-repair')"
                                class="bg-green-600 text-white p-3 rounded-lg hover:bg-green-700 transition">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @forelse($repairs as $repair)
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <p class="font-medium text-sm">{{ Str::limit($repair->description, 60) }}</p>
                                    <div class="flex items-center justify-between mt-2 text-xs">
                                        <span class="text-gray-500">{{ $repair->created_at->format('d/m/Y') }}</span>
                                        <span
                                            class="status-badge text-white {{ $repair->status === 'completed' ? 'bg-green-600' : ($repair->status === 'in_progress' ? 'bg-amber-600' : 'bg-red-600') }}">
                                            {{ $repair->status === 'completed' ? 'Hoàn thành' : ($repair->status === 'in_progress' ? 'Đang xử lý' : 'Chờ xử lý') }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-gray-500 py-8">Chưa có báo sửa</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Modal: Yêu cầu rời phòng --}}
    @if ($activeBooking)
        <x-modal name="terminate-booking" focusable>
            <form method="POST" action="{{ route('student.bookings.terminate', $activeBooking) }}" class="p-6">
                @csrf @method('PATCH')
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle text-red-500 text-6xl mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Xác nhận rời phòng?</h3>
                    <p class="text-gray-600 mb-6">Sau khi xác nhận, bạn sẽ không thể ở lại phòng này nữa.</p>
                    <textarea name="reason" rows="4" required placeholder="Lý do rời phòng..."
                        class="w-full border rounded-lg p-3"></textarea>
                    <div class="flex justify-center gap-4 mt-6">
                        <button type="button" x-on:click="$dispatch('close')"
                            class="px-6 py-3 bg-gray-300 rounded-lg">Hủy</button>
                        <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg font-bold">Xác nhận rời
                            phòng</button>
                    </div>
                </div>
            </form>
        </x-modal>
    @endif

    {{-- Modal: Báo sửa chữa --}}
    <x-modal name="create-repair" focusable>
        <form method="POST" action="{{ route('student.repairs.store') }}" enctype="multipart/form-data"
            class="p-6">
            @csrf
            <h3 class="text-xl font-bold mb-6">Báo cáo sửa chữa</h3>
            <textarea name="description" rows="5" required placeholder="Mô tả chi tiết sự cố..."
                class="w-full border rounded-lg p-4"></textarea>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh (nếu có)</label>
                <input type="file" id="image" name="image" accept="image/*"
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
            </div>
            <div class="flex justify-end gap-4 mt-8">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-6 py-3 bg-gray-300 rounded-lg">Hủy</button>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-bold">Gửi báo cáo</button>
            </div>
        </form>
    </x-modal>

    {{-- Modal: Gia hạn phòng --}}
    @if ($activeBooking)
        <x-modal name="extend-booking" focusable>
            <form method="POST" action="{{ route('student.bookings.extend', $activeBooking) }}" class="p-6">
                @csrf

                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                        <i class="fas fa-clock text-green-600 text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">Gia hạn thời gian thuê phòng</h3>
                    <p class="text-gray-600 mt-2">
                        Phòng hiện tại: <strong>{{ $room->room_code }}</strong><br>
                        Ngày rời dự kiến hiện tại:
                        <strong>{{ $activeBooking->expected_check_out_date->format('d/m/Y') }}</strong>
                    </p>
                </div>

                <div class="space-y-6">
                    <!-- Loại thuê -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt text-green-600 mr-2"></i>
                            Loại gia hạn
                        </label>
                        <select name="rental_type" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                            <option value="monthly" {{ $activeBooking->rental_type === 'monthly' ? 'selected' : '' }}>
                                Theo tháng
                            </option>
                            <option value="daily" {{ $activeBooking->rental_type === 'daily' ? 'selected' : '' }}>
                                Theo ngày
                            </option>
                        </select>
                    </div>

                    <!-- Số lượng gia hạn -->
                    <div x-data="{ type: '{{ $activeBooking->rental_type }}' }">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-plus-circle text-green-600 mr-2"></i>
                            <span x-text="type === 'monthly' ? 'Số tháng muốn gia hạn' : 'Số ngày muốn gia hạn'"></span>
                        </label>
                        <input type="number" name="duration" min="1" required
                            x-on:change="type = $event.target.closest('form').querySelector('[name=rental_type]').value"
                            x-init="type = $el.closest('form').querySelector('[name=rental_type]').value"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                            placeholder="Nhập số lượng...">
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end gap-4">
                    <button type="button" x-on:click="$dispatch('close')"
                        class="px-6 py-3 bg-gray-300 text-gray-800 font-medium rounded-lg hover:bg-gray-400 transition">
                        Hủy bỏ
                    </button>
                    <button type="submit"
                        class="px-8 py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition shadow-lg flex items-center">
                        <i class="fas fa-check mr-2"></i>
                        Xác nhận gia hạn
                    </button>
                </div>
            </form>
        </x-modal>
    @endif
@endsection
