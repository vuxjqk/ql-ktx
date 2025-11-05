<x-app-layout>
    <x-slot name="header">
        {{ __('Chi tiết đặt phòng') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => url('/')],
                ['label' => 'Quản lý đặt phòng', 'url' => route('bookings.index')],
                ['label' => 'Chi tiết đặt phòng'],
            ]" />

            <div class="flex items-center justify-between mx-6">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-calendar-check text-blue-600 me-1"></i>
                        {{ __('Chi tiết đặt phòng') }}
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">{{ __('Xem thông tin chi tiết về đặt phòng') }}</p>
                </div>
                <x-secondary-button :href="route('bookings.index')">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Quay lại') }}
                </x-secondary-button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight mb-6">
                    <i class="fas fa-info-circle text-blue-600 me-1"></i>
                    {{ __('Thông tin đặt phòng') }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label :value="__('Sinh viên')" icon="fas fa-user-graduate" />
                        <div class="flex items-center gap-2 mt-1">
                            @if ($booking->user->avatar)
                                <img src="{{ asset('storage/' . $booking->user->avatar) }}" alt="Avatar"
                                    class="w-8 h-8 rounded-full object-cover">
                            @else
                                <div
                                    class="w-8 h-8 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center">
                                    {{ mb_substr($booking->user->name, 0, 2, 'UTF-8') }}
                                </div>
                            @endif
                            <div class="grid">
                                <span class="font-semibold">{{ $booking->user->name }}</span>
                                <span class="text-sm">
                                    {{ __('MSSV: ') . $booking->user->student->student_code }}
                                </span>
                                <span class="text-sm">
                                    {{ __('Giới tính: ') . ($booking->user->student->gender === 'male' ? 'Nam' : ($booking->user->student->gender === 'female' ? 'Nữ' : 'Khác')) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <x-input-label :value="__('Phòng')" icon="fas fa-door-open" />
                        <div class="grid mt-1">
                            <span class="font-semibold">{{ $booking->room->room_code }}</span>
                            <span class="text-sm">
                                {{ __('Tầng: ') . $booking->room->floor->floor_number }}
                            </span>
                            <span class="text-sm">
                                {{ __('Chi nhánh: ') . $booking->room->floor->branch->name }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <x-input-label :value="__('Hình thức đăng ký')" icon="fas fa-clipboard-list" />
                        <p class="mt-1 text-gray-800">
                            @switch($booking->booking_type)
                                @case('registration')
                                    {{ __('Đăng ký mới') }}
                                @break

                                @case('transfer')
                                    {{ __('Chuyển phòng') }}
                                @break

                                @case('extension')
                                    {{ __('Gia hạn') }}
                                @break
                            @endswitch
                        </p>
                    </div>

                    <div>
                        <x-input-label :value="__('Hình thức thuê')" icon="fas fa-home" />
                        <p class="mt-1 text-gray-800">
                            {{ $booking->rental_type === 'daily' ? __('Theo ngày') : __('Theo tháng') }}
                        </p>
                    </div>

                    <div>
                        <x-input-label :value="__('Ngày nhận phòng')" icon="fas fa-sign-in-alt" />
                        <p class="mt-1 text-gray-800">{{ $booking->check_in_date->format('d/m/Y') }}</p>
                    </div>

                    <div>
                        <x-input-label :value="__('Ngày trả phòng dự kiến')" icon="fas fa-sign-out-alt" />
                        <p class="mt-1 text-gray-800">{{ $booking->expected_check_out_date->format('d/m/Y') }}</p>
                    </div>

                    @if ($booking->actual_check_out_date)
                        <div>
                            <x-input-label :value="__('Ngày trả phòng thực tế')" icon="fas fa-calendar-times" />
                            <p class="mt-1 text-gray-800">{{ $booking->actual_check_out_date->format('d/m/Y') }}</p>
                        </div>
                    @endif

                    <div>
                        <x-input-label :value="__('Trạng thái')" icon="fas fa-info-circle" />
                        <p class="mt-1 text-gray-800">
                            @switch($booking->status)
                                @case('pending')
                                    <span class="text-yellow-600">{{ __('Chờ duyệt') }}</span>
                                @break

                                @case('approved')
                                    <span class="text-blue-600">{{ __('Đã duyệt') }}</span>
                                @break

                                @case('rejected')
                                    <span class="text-red-600">{{ __('Bị từ chối') }}</span>
                                @break

                                @case('active')
                                    <span class="text-green-600">{{ __('Đang hoạt động') }}</span>
                                @break

                                @case('expired')
                                    <span class="text-gray-600">{{ __('Hết hạn') }}</span>
                                @break

                                @case('terminated')
                                    <span class="text-red-600">{{ __('Đã hủy') }}</span>
                                @break
                            @endswitch
                        </p>
                    </div>

                    @if ($booking->reason)
                        <div>
                            <x-input-label :value="__('Lý do')" icon="fas fa-comment-dots" />
                            <p class="mt-1 text-gray-800">{{ $booking->reason }}</p>
                        </div>
                    @endif

                    @if ($booking->processed_at)
                        <div>
                            <x-input-label :value="__('Thời gian xử lý')" icon="fas fa-clock" />
                            <p class="mt-1 text-gray-800">{{ $booking->processed_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif

                    @if ($booking->processedBy)
                        <div>
                            <x-input-label :value="__('Người xử lý')" icon="fas fa-user-check" />
                            <p class="mt-1 text-gray-800">{{ $booking->processedBy->name }}</p>
                        </div>
                    @endif
                </div>
            </div>

            @if ($booking->rental_type === 'monthly' && $booking->contract)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-xl text-gray-800 leading-tight mb-6">
                        <i class="fas fa-file-contract text-blue-600 me-1"></i>
                        {{ __('Thông tin hợp đồng') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label :value="__('Mã hợp đồng')" icon="fas fa-barcode" />
                            <p class="mt-1 text-gray-800">{{ $booking->contract->contract_code }}</p>
                        </div>

                        <div>
                            <x-input-label :value="__('Phí thuê hàng tháng')" icon="fas fa-money-bill-wave" />
                            <p class="mt-1 text-gray-800">
                                {{ number_format($booking->contract->monthly_fee, 0, ',', '.') }} VND</p>
                        </div>

                        <div>
                            <x-input-label :value="__('Tiền đặt cọc')" icon="fas fa-coins" />
                            <p class="mt-1 text-gray-800">{{ number_format($booking->contract->deposit, 0, ',', '.') }}
                                VND</p>
                        </div>

                        @if ($booking->contract->contract_file)
                            <div>
                                <x-input-label :value="__('Tệp hợp đồng')" icon="fas fa-file-pdf" />
                                <p class="mt-1">
                                    <a href="{{ asset('storage/' . $booking->contract->contract_file) }}"
                                        target="_blank" class="text-blue-600 hover:underline">
                                        <i class="fas fa-download"></i> {{ __('Tải xuống') }}
                                    </a>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
