@extends('student.layouts.app')

@section('title', 'Quản lý phòng ở')

@push('styles')
    <style>
        .status-badge {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
        }

        .card-hover:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
    </style>
@endpush

@section('content')
    <!-- Breadcrumb -->
    <div class="bg-gray-50 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-2 text-sm">
                    <li>
                        <a href="#" class="text-gray-600 hover:text-blue-600">
                            <i class="fas fa-home"></i>
                        </a>
                    </li>
                    <li><i class="fas fa-chevron-right text-gray-400 text-xs"></i></li>
                    <li class="text-gray-900 font-medium">Quản lý phòng ở</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (!$currentBooking)
            <!-- No Booking Section -->
            <div class="bg-white rounded-xl shadow-md p-8 text-center">
                <i class="fas fa-exclamation-circle text-red-500 text-6xl mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Chưa đăng ký phòng</h2>
                <p class="text-gray-600 mb-6">Bạn chưa có phòng đang ở hoặc đăng ký. Hãy chọn phòng phù hợp và đăng ký ngay!
                </p>
                <a href="#"
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200 font-semibold">
                    <i class="fas fa-search mr-2"></i>Đăng ký phòng mới
                </a>
            </div>
        @else
            <!-- Has Booking Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Room Information -->
                    <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">
                            <i class="fas fa-door-open mr-2 text-blue-600"></i>Thông tin phòng hiện tại
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-600">Mã phòng</p>
                                <p class="text-lg font-bold text-gray-900">{{ $room->room_code }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Chi nhánh - Tầng</p>
                                <p class="text-lg font-bold text-gray-900">{{ $room->floor->branch->name ?? 'N/A' }} - Tầng
                                    {{ $room->floor->floor_number ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Loại thuê</p>
                                <p class="text-lg font-bold text-gray-900">{{ ucfirst($currentBooking->rental_type) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Ngày nhận phòng</p>
                                <p class="text-lg font-bold text-gray-900">
                                    {{ $currentBooking->check_in_date->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Ngày dự kiến rời phòng</p>
                                <p class="text-lg font-bold text-gray-900">
                                    {{ $currentBooking->expected_check_out_date->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Trạng thái</p>
                                <span
                                    class="status-badge {{ $currentBooking->status === 'active' ? 'bg-green-500 text-white' : 'bg-yellow-500 text-white' }}">
                                    {{ ucfirst($currentBooking->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-6 border-t border-gray-200 pt-4 flex space-x-4">
                            <a href="#" class="text-blue-600 hover:text-blue-700 font-semibold">
                                <i class="fas fa-eye mr-1"></i>Xem chi tiết phòng
                            </a>
                            @if ($currentBooking->rental_type === 'monthly')
                                <x-secondary-button x-data=""
                                    x-on:click.prevent="$dispatch('open-modal', 'extend-booking-{{ $currentBooking->id }}')"
                                    class="text-sm">
                                    <i class="fas fa-sync-alt mr-1"></i>Gia hạn phòng
                                </x-secondary-button>
                            @endif
                            @php
                                $activeBooking = $currentBooking?->status === 'active';
                            @endphp

                            @if ($activeBooking)
                                @if (is_null($currentBooking->actual_check_out_date))
                                    {{-- Hiển thị nút --}}
                                    <x-danger-button x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'terminate-booking-{{ $currentBooking->id }}')"
                                        class="text-sm">
                                        <i class="fas fa-times-circle mr-1"></i>Yêu cầu chấm dứt hợp đồng
                                    </x-danger-button>
                                @else
                                    {{-- Hiển thị thông báo --}}
                                    <p class="text-sm text-green-600 font-semibold">
                                        Đã yêu cầu chấm dứt hợp đồng vào
                                        {{ $currentBooking->actual_check_out_date->format('d/m/Y') }}
                                    </p>
                                @endif

                            @endif
                        </div>
                    </div>

                    <!-- Contract Section (if monthly) -->
                    @if ($currentBooking->rental_type === 'monthly' && $contract)
                        <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                                <i class="fas fa-file-contract mr-2 text-blue-600"></i>Hợp đồng thuê phòng
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <p class="text-sm text-gray-600">Mã hợp đồng</p>
                                    <p class="text-lg font-bold text-gray-900">{{ $contract->contract_code }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Phí hàng tháng</p>
                                    <p class="text-lg font-bold text-gray-900">{{ number_format($contract->monthly_fee) }}đ
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Tiền đặt cọc</p>
                                    <p class="text-lg font-bold text-gray-900">{{ number_format($contract->deposit) }}đ</p>
                                </div>
                            </div>
                            @if ($contract->contract_file)
                                <a href="{{ asset('storage/' . $contract->contract_file) }}" target="_blank"
                                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200 font-semibold">
                                    <i class="fas fa-download mr-2"></i>Tải hợp đồng
                                </a>
                            @endif
                        </div>
                    @endif

                    <!-- Repairs Section -->
                    <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-900">
                                <i class="fas fa-tools mr-2 text-blue-600"></i>Báo cáo sửa chữa
                            </h2>
                            <x-secondary-button x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'create-repair')"
                                class="!bg-green-600 hover:!bg-green-700 !text-white">
                                <i class="fas fa-plus mr-1"></i>Báo sửa mới
                            </x-secondary-button>
                        </div>
                        <div class="space-y-4">
                            @forelse($repairs as $repair)
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="font-semibold text-gray-900">{{ Str::limit($repair->description, 50) }}
                                        </p>
                                        <span
                                            class="status-badge {{ $repair->status === 'completed' ? 'bg-green-500 text-white' : ($repair->status === 'in_progress' ? 'bg-yellow-500 text-white' : 'bg-red-500 text-white') }}">
                                            {{ ucfirst($repair->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600">Ngày báo: {{ $repair->created_at->format('d/m/Y') }}
                                    </p>
                                    @if ($repair->image_path)
                                        <a href="{{ asset('storage/' . $repair->image_path) }}" target="_blank"
                                            class="text-blue-600 hover:text-blue-700 text-sm">
                                            <i class="fas fa-image mr-1"></i>Xem ảnh
                                        </a>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <i class="fas fa-tools text-gray-300 text-5xl mb-4"></i>
                                    <p class="text-gray-500">Chưa có báo cáo sửa chữa nào</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Bills and Payments Section -->
                    <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">
                            <i class="fas fa-receipt mr-2 text-blue-600"></i>Hóa đơn và thanh toán
                        </h2>
                        <div class="space-y-4">
                            @forelse($bills as $bill)
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="font-semibold text-gray-900">Hóa đơn #{{ $bill->bill_code }}</p>
                                        <span
                                            class="status-badge {{ $bill->status === 'paid' ? 'bg-green-500 text-white' : ($bill->status === 'unpaid' ? 'bg-red-500 text-white' : 'bg-yellow-500 text-white') }}">
                                            {{ ucfirst($bill->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600">Tổng tiền: {{ number_format($bill->total_amount) }}đ
                                    </p>
                                    <p class="text-sm text-gray-600">Hạn thanh toán:
                                        {{ $bill->due_date ? $bill->due_date->format('d/m/Y') : 'N/A' }}</p>
                                    @if ($bill->status !== 'paid')
                                        <x-secondary-button data-payment-url="{{ route('student.vnpay.redirect', $bill) }}"
                                            x-data=""
                                            x-on:click.prevent="$dispatch('open-modal', 'confirm-payment')">
                                            <i class="fas fa-credit-card mr-1"></i>Thanh toán ngay
                                        </x-secondary-button>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <i class="fas fa-receipt text-gray-300 text-5xl mb-4"></i>
                                    <p class="text-gray-500">Chưa có hóa đơn nào</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-md p-6 sticky top-24 card-hover">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">
                            <i class="fas fa-history mr-2 text-blue-600"></i>Lịch sử giao dịch
                        </h3>
                        <div class="space-y-4 max-h-96 overflow-y-auto">
                            @forelse($transactions as $transaction)
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="font-semibold text-gray-900">#{{ $transaction->transaction_code }}</p>
                                    <p class="text-sm text-gray-600">Số tiền: {{ number_format($transaction->amount) }}đ
                                    </p>
                                    <p class="text-sm text-gray-600">Ngày:
                                        {{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            @empty
                                <div class="text-center py-6">
                                    <i class="fas fa-history text-gray-300 text-4xl mb-4"></i>
                                    <p class="text-gray-500">Chưa có giao dịch nào</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <x-modal name="confirm-payment" focusable>
        <form id="payment-form" method="post" action="#" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">
                Thanh toán hóa đơn # <span></span>
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Tổng tiền cần thanh toán: <span></span>đ
            </p>

            <div class="mt-6">
                <x-input-label for="amount" value="{{ __('Số tiền') }}" />

                <x-text-input id="amount" name="amount" type="number" class="mt-1 block w-full"
                    placeholder="{{ __('Nhập số tiền') }}" min="1" />

                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Hủy') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Thanh toán') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Modal báo sửa chữa -->
    <x-modal name="create-repair" focusable>
        <form method="post" action="{{ route('student.repairs.store') }}" class="p-6"
            enctype="multipart/form-data">
            @csrf

            <!-- Ẩn room_id (phòng hiện tại) -->
            <input type="hidden" name="room_id" value="{{ $room?->id }}">

            <h2 class="text-lg font-medium text-gray-900 mb-4">
                Báo cáo sửa chữa mới
            </h2>

            <div class="mt-4">
                <x-input-label for="description" value="Mô tả sự cố" />
                <textarea id="description" name="description" rows="5"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Mô tả chi tiết vấn đề cần sửa chữa..." required>{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="image" value="Hình ảnh (nếu có)" />
                <input type="file" id="image" name="image" accept="image/*"
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                <x-input-error :messages="$errors->get('image')" class="mt-2" />
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Hủy
                </x-secondary-button>

                <x-primary-button type="submit">
                    Gửi báo cáo
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Gia hạn phòng -->
    <x-modal name="extend-booking-{{ $currentBooking?->id }}" focusable>
        <form method="post" action="#" class="p-6">
            @csrf
            @method('PATCH')

            <h2 class="text-lg font-medium text-gray-900 mb-4">
                Gia hạn thời gian thuê phòng
            </h2>

            <p class="text-sm text-gray-600 mb-6">
                Phòng hiện tại: <span class="font-semibold">{{ $room?->room_code }}</span><br>
                Ngày dự kiến rời hiện tại: <span class="font-semibold">
                    {{ $currentBooking?->expected_check_out_date->format('d/m/Y') }}
                </span>
            </p>

            <div x-data="{ rentalType: '{{ $currentBooking?->rental_type }}' }">
                <!-- Loại thuê -->
                <div class="mb-5">
                    <x-input-label for="rental_type" value="Loại thuê" />
                    <select x-model="rentalType" name="rental_type" id="rental_type"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="monthly">Theo tháng</option>
                        <option value="daily">Theo ngày</option>
                    </select>
                    <x-input-error :messages="$errors->get('rental_type')" class="mt-2" />
                </div>

                <!-- Số tháng / Số ngày -->
                <div class="mb-6">
                    <x-input-label for="duration">
                        <span x-text="rentalType === 'monthly' ? 'Số tháng gia hạn' : 'Số ngày gia hạn'"></span>
                    </x-input-label>
                    <x-text-input type="number" name="duration" id="duration" min="1" required
                        class="mt-1 block w-full"
                        x-effect="$el.placeholder = rentalType === 'monthly'
                            ? 'Nhập số tháng'
                            : 'Nhập số ngày'" />
                    <x-input-error :messages="$errors->get('duration')" class="mt-2" />
                </div>

                <div class="flex justify-end space-x-3">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Hủy
                    </x-secondary-button>

                    <x-primary-button type="submit">
                        Xác nhận gia hạn
                    </x-primary-button>
                </div>
            </div>
        </form>
    </x-modal>

    <x-modal name="terminate-booking-{{ $currentBooking->id }}" focusable>
        <form method="post" action="{{ route('student.bookings.terminate', $currentBooking) }}" class="p-6">
            @csrf
            @method('PATCH')

            <div class="flex items-center mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-3xl mr-4"></i>
                <div>
                    <h2 class="text-lg font-medium text-gray-900">
                        Chấm dứt hợp đồng thuê phòng
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Phòng: <span class="font-bold">{{ $room->room_code }}</span>
                    </p>
                </div>
            </div>

            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-red-800">
                    <strong>Lưu ý:</strong> Sau khi xác nhận, bạn sẽ không thể ở lại phòng này nữa.<br>
                    Ngày rời phòng thực tế sẽ được ghi nhận là <strong>hôm nay</strong> ({{ now()->format('d/m/Y') }}).
                </p>
            </div>

            <div class="mb-6">
                <x-input-label for="reason" value="Lý do chấm dứt (bắt buộc)" class="text-red-700" />
                <textarea id="reason" name="reason" rows="4" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                    placeholder="Ví dụ: Chuyển ra ngoài, tốt nghiệp, lý do cá nhân...">{{ old('reason') }}</textarea>
                <x-input-error :messages="$errors->get('reason')" class="mt-2" />
            </div>

            <div class="flex justify-end space-x-3">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Hủy bỏ
                </x-secondary-button>

                <x-danger-button type="submit">
                    <i class="fas fa-check mr-1"></i>Xác nhận chấm dứt
                </x-danger-button>
            </div>
        </form>
    </x-modal>
@endsection

@pushOnce('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('payment-form');
            document.querySelectorAll('[data-payment-url]').forEach(btn =>
                btn.addEventListener('click', () => form.action = btn.dataset.paymentUrl)
            );
        });
    </script>
@endPushOnce
