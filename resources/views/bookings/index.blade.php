<x-app-layout>
    <x-slot name="header">
        {{ __('Quản lý đặt phòng') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => url('/')], ['label' => 'Quản lý đặt phòng']]" />

            <div class="flex items-center justify-between mx-6">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-calendar-check text-blue-600 me-1"></i>
                        {{ __('Quản lý đặt phòng') }}
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">{{ __('Quản lý tất cả đặt phòng trong hệ thống') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-blue-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Tổng số lượng') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $totalBookings }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-yellow-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-hourglass-start text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Chờ duyệt') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $pendingBookings }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-green-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Đang hoạt động') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $activeBookings }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-red-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-times-circle text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Hết hạn/đã hủy') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $expiredBookings }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-filter text-blue-600 me-1"></i>
                    {{ __('Tìm kiếm đặt phòng') }}
                </h3>

                <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
                    <div class="col-span-2">
                        <x-input-label for="search" :value="__('Tìm kiếm')" icon="fas fa-search" />
                        <x-text-input id="search" class="block mt-1 w-full" type="search" name="search"
                            :value="request('search')" autocomplete="search" :placeholder="__('Tìm kiếm theo tên sinh viên hoặc mã phòng...')" />
                    </div>

                    <div>
                        <x-input-label for="booking_type" :value="__('Hình thức đăng ký')" icon="fas fa-list" />
                        <x-select id="booking_type" class="block mt-1 w-full" :options="[
                            'registration' => 'Đăng ký mới',
                            'extension' => 'Gia hạn',
                            'transfer' => 'Chuyển phòng',
                        ]" name="booking_type"
                            :selected="request('booking_type')" :placeholder="__('Chọn hình thức đăng ký')" />
                    </div>

                    <div>
                        <x-input-label for="rental_type" :value="__('Hình thức thuê')" icon="fas fa-clock" />
                        <x-select id="rental_type" class="block mt-1 w-full" :options="[
                            'daily' => 'Theo ngày',
                            'monthly' => 'Theo tháng',
                        ]" name="rental_type"
                            :selected="request('rental_type')" :placeholder="__('Chọn hình thức thuê')" />
                    </div>

                    <div>
                        <x-input-label for="status" :value="__('Trạng thái')" icon="fas fa-info-circle" />
                        <x-select id="status" class="block mt-1 w-full" :options="[
                            'pending' => 'Chờ duyệt',
                            'approved' => 'Đã duyệt',
                            'rejected' => 'Bị từ chối',
                            'active' => 'Đang hoạt động',
                            'expired' => 'Hết hạn',
                            'terminated' => 'Đã hủy',
                        ]" name="status"
                            :selected="request('status')" :placeholder="__('Chọn trạng thái')" />
                    </div>

                    <div class="flex items-end">
                        <x-primary-button>
                            <i class="fas fa-search"></i>
                            {{ __('Tìm kiếm') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-800">Danh sách đặt phòng</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    STT</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Sinh viên</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Phòng</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Hình thức</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Thời gian</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Trạng thái</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($bookings as $index => $booking)
                                @php
                                    $isCheckoutRequested =
                                        $booking->status === 'active' && $booking->actual_check_out_date !== null;
                                    $statusClass = match ($booking->status) {
                                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                        'approved' => 'bg-blue-100 text-blue-800 border-blue-300',
                                        'rejected' => 'bg-red-100 text-red-800 border-red-300',
                                        'active' => $isCheckoutRequested
                                            ? 'bg-orange-100 text-orange-800 border-orange-300'
                                            : 'bg-green-100 text-green-800 border-green-300',
                                        'expired', 'terminated' => 'bg-gray-100 text-gray-700 border-gray-300',
                                        default => 'bg-gray-100 text-gray-600',
                                    };

                                    $statusText = match (true) {
                                        $booking->status === 'pending' => 'Chờ duyệt',
                                        $booking->status === 'approved' => 'Đã duyệt',
                                        $booking->status === 'rejected' => 'Bị từ chối',
                                        $isCheckoutRequested => 'Yêu cầu trả phòng',
                                        $booking->status === 'active' => 'Đang hoạt động',
                                        $booking->status === 'expired' => 'Hết hạn',
                                        $booking->status === 'terminated' => 'Đã chấm dứt',
                                        default => 'Không xác định',
                                    };
                                @endphp

                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4 text-sm text-gray-600">#{{ $bookings->firstItem() + $index }}
                                    </td>

                                    <!-- Sinh viên -->
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            @if ($booking->user->avatar)
                                                <img src="{{ asset('storage/' . $booking->user->avatar) }}"
                                                    alt="Avatar" class="w-10 h-10 rounded-full object-cover border">
                                            @else
                                                <div
                                                    class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                                                    {{ mb_substr($booking->user->name, 0, 2, 'UTF-8') }}
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $booking->user->name }}</p>
                                                <p class="text-xs text-gray-500">MSSV:
                                                    {{ $booking->user->student?->student_code }}</p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $booking->user->student?->gender === 'male' ? 'Nam' : ($booking->user->student?->gender === 'female' ? 'Nữ' : 'Khác') }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Phòng -->
                                    <td class="px-4 py-4 text-sm">
                                        <p class="font-medium">{{ $booking->room->room_code }}</p>
                                        <p class="text-xs text-gray-500">Tầng {{ $booking->room->floor->floor_number }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $booking->room->floor->branch->name }}</p>
                                    </td>

                                    <!-- Hình thức -->
                                    <td class="px-4 py-4 text-sm">
                                        <div class="space-y-1">
                                            <span class="block text-xs">
                                                {{ $booking->booking_type === 'registration' ? 'Đăng ký mới' : ($booking->booking_type === 'transfer' ? 'Chuyển phòng' : 'Gia hạn') }}
                                            </span>
                                            <span
                                                class="inline-block px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                                {{ $booking->rental_type === 'daily' ? 'Theo ngày' : 'Theo tháng' }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Thời gian -->
                                    <td class="px-4 py-4 text-sm text-gray-600">
                                        {{ $booking->check_in_date->format('d/m/Y') }}
                                        <span class="text-gray-400">→</span>
                                        {{ $booking->expected_check_out_date->format('d/m/Y') }}
                                        @if ($booking->actual_check_out_date)
                                            <br><span class="text-xs text-red-600">Trả thực tế:
                                                {{ $booking->actual_check_out_date->format('d/m/Y') }}</span>
                                        @endif
                                    </td>

                                    <!-- Trạng thái + Hành động theo trạng thái -->
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <span
                                                class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium border {{ $statusClass }}">
                                                <i class="fas fa-circle text-[8px] mr-1"></i>
                                                {{ $statusText }}
                                            </span>

                                            <!-- Hành động nhỏ ngay tại cột trạng thái -->
                                            <div class="flex items-center gap-1">
                                                @if ($booking->status === 'pending')
                                                    <button title="Phê duyệt"
                                                        data-update-url="{{ route('bookings.update', $booking) }}"
                                                        data-status-value="approved" data-status-label="phê duyệt"
                                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-updation')"
                                                        class="p-1.5 text-green-600 hover:bg-green-100 rounded-lg transition">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button title="Từ chối"
                                                        data-update-url="{{ route('bookings.update', $booking) }}"
                                                        data-status-value="rejected" data-status-label="từ chối"
                                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-updation')"
                                                        class="p-1.5 text-red-600 hover:bg-red-100 rounded-lg transition">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @elseif($booking->status === 'approved')
                                                    @php
                                                        $amount =
                                                            $booking->rental_type === 'daily'
                                                                ? ($booking->check_in_date->diffInDays(
                                                                        $booking->expected_check_out_date,
                                                                    ) +
                                                                        1) *
                                                                    $booking->room->price_per_day
                                                                : $booking->room->price_per_month;
                                                        $bill = $booking->bills()->oldest()->first();
                                                    @endphp
                                                    @if ($bill)
                                                        <button title="Ghi nhận thanh toán trực tiếp"
                                                            data-bill-create-url="{{ route('payments.store', $bill) }}"
                                                            data-amount="{{ $amount }}"
                                                            x-on:click.prevent="$dispatch('open-modal', 'confirm-bill-creation')"
                                                            class="p-1.5 text-blue-600 hover:bg-blue-100 rounded-lg transition">
                                                            <i class="fas fa-money-bill-wave"></i>
                                                        </button>
                                                    @endif
                                                @elseif($booking->status === 'active')
                                                    @if (!$isCheckoutRequested)
                                                        <button title="Chấm dứt sớm"
                                                            data-update-url="{{ route('bookings.terminateBooking', $booking) }}"
                                                            data-status-value="terminate"
                                                            x-on:click.prevent="$dispatch('open-modal', 'confirm-updation')"
                                                            class="p-1.5 text-red-600 hover:bg-red-100 rounded-lg transition">
                                                            <i class="fas fa-stop"></i>
                                                        </button>
                                                    @endif
                                                @elseif(in_array($booking->status, ['rejected', 'expired', 'terminated']))
                                                    <span class="text-xs text-gray-500">Hoàn tất</span>
                                                @endif
                                            </div>
                                        </div>

                                        @if ($booking->reason)
                                            <p class="text-xs text-red-600 mt-1">Lý do:
                                                {{ Str::limit($booking->reason, 50) }}</p>
                                        @endif
                                    </td>

                                    <!-- Cột hành động chính -->
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- Xem chi tiết -->
                                            <a href="{{ route('bookings.show', $booking) }}"
                                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                                title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <!-- Tạo hóa đơn (hàng tháng hoặc kết thúc) -->
                                            @if ($booking->status === 'active' && ($booking->rental_type === 'monthly' || $isCheckoutRequested))
                                                <a href="{{ route('bills.create', $booking->user) }}"
                                                    class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition"
                                                    title="{{ $isCheckoutRequested ? 'Tạo hóa đơn kết thúc' : 'Tạo hóa đơn hàng tháng' }}">
                                                    <i class="fas fa-file-invoice-dollar"></i>
                                                </a>
                                            @endif

                                            <!-- Xóa -->
                                            @if ($booking->status === 'active')
                                                <button data-delete-url="{{ route('bookings.destroy', $booking) }}"
                                                    x-on:click.prevent="$dispatch('open-modal', 'confirm-deletion')"
                                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                                    title="Xóa vĩnh viễn">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>
    </div>

    <x-modal name="confirm-updation" :show="$errors->bookingUpdation->isNotEmpty()" focusable>
        <form id="update-form" method="post" action="#" class="p-6">
            @csrf
            @method('put')

            <h2 class="text-lg font-medium text-gray-900">
                {!! __('Bạn có chắc chắn muốn :action không?', [
                    'action' => '<span id="status-label" class="font-semibold text-blue-500"></span>',
                ]) !!}
            </h2>

            <input id="status-input" type="hidden" name="status">
            <x-input-error :messages="$errors->bookingUpdation->get('status')" class="mt-2" />

            <div id="reason-container" class="mt-6">
                <x-input-label for="reason" value="Lý do" icon="fas fa-align-left" />
                <x-textarea id="reason" class="block mt-1 w-full" name="reason" :value="old('reason')"
                    placeholder="Lý do (không bắt buộc)" />
                <x-input-error :messages="$errors->bookingUpdation->get('reason')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Huỷ') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Xác nhận') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="confirm-bill-creation" focusable>
        <form id="create-bill-form" method="post" action="#" class="p-6">
            @csrf

            <div class="flex items-center gap-6">
                <div class="bg-blue-100 shadow-sm sm:rounded-lg p-3">
                    <i class="fas fa-money-check-alt text-blue-600 text-xl"></i>
                </div>
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Ghi nhận thanh toán cho sinh viên này?') }}
                </h2>
            </div>

            <div class="mt-6">
                <p id="bill-amount" class="font-medium text-sm text-gray-700"></p>
            </div>

            <div class="mt-6">
                <x-input-label for="amount" :value="__('Số tiền')" icon="fas fa-money-bill" />
                <x-text-input id="amount" class="block mt-1 w-full" type="number" name="amount"
                    :value="old('amount')" required autocomplete="off" :placeholder="__('Nhập số tiền')" />
                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="payment_type" :value="__('Loại thanh toán')" icon="fas fa-money-check-alt" />
                <x-select id="payment_type" class="block mt-1 w-full" :options="[
                    'online' => 'Trực tuyến',
                    'offline' => 'Tiền mặt',
                ]" name="payment_type"
                    :selected="old('payment_type')" required :placeholder="__('Chọn loại thanh toán')" />
                <x-input-error :messages="$errors->get('payment_type')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Huỷ') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Xác nhận') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <x-delete-modal />

    @pushOnce('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('update-form');
                const input = document.getElementById('status-input');
                const label = document.getElementById('status-label');
                const container = document.getElementById('reason-container');

                document.querySelectorAll('[data-update-url]').forEach(button => {
                    button.addEventListener('click', () => {
                        form.action = button.getAttribute('data-update-url');
                        const status = button.getAttribute('data-status-value');
                        input.value = status;
                        label.textContent = button.getAttribute('data-status-label');
                        if (status === 'approved') {
                            container.classList.add('hidden');
                        } else {
                            container.classList.remove('hidden');
                        }
                    });
                });

                const billForm = document.getElementById('create-bill-form');
                const billAmountText = document.getElementById('bill-amount');
                document.querySelectorAll('[data-bill-create-url]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        billForm.action = btn.dataset.billCreateUrl;
                        billAmountText.textContent =
                            `Số tiền cần thanh toán: ${Number(btn.dataset.amount).toLocaleString()}₫`;
                    });
                });
            });
        </script>
    @endPushOnce
</x-app-layout>
