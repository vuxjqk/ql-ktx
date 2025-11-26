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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <x-table :title="__('Danh sách đặt phòng')">
                    <x-thead>
                        <x-tr>
                            <x-th>{{ __('STT') }}</x-th>
                            <x-th>{{ __('Người đặt phòng') }}</x-th>
                            <x-th>{{ __('Phòng') }}</x-th>
                            <x-th>{{ __('Hình thức') }}</x-th>
                            <x-th>{{ __('Thời gian cư trú') }}</x-th>
                            <x-th>{{ __('Trạng thái') }}</x-th>
                            <x-th>{{ __('Hành động') }}</x-th>
                        </x-tr>
                    </x-thead>
                    <x-tbody>
                        @foreach ($bookings as $index => $booking)
                            <x-tr>
                                <x-td>#{{ $bookings->firstItem() + $index }}</x-td>
                                <x-td>
                                    <div class="flex items-center gap-2">
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
                                                {{ __('MSSV: ') . $booking->user->student?->student_code }}
                                            </span>
                                            <span class="text-sm">
                                                {{ __('Giới tính: ') . ($booking->user->student?->gender === 'male' ? 'Nam' : ($booking->user->student?->gender === 'female' ? 'Nữ' : 'Khác')) }}
                                            </span>
                                        </div>
                                    </div>
                                </x-td>
                                <x-td>
                                    <div class="grid">
                                        <span class="font-semibold">{{ $booking->room->room_code }}</span>
                                        <span class="text-sm">
                                            {{ __('Tầng: ') . $booking->room->floor->floor_number }}
                                        </span>
                                        <span class="text-sm">
                                            {{ __('Chi nhánh: ') . $booking->room->floor->branch->name }}
                                        </span>
                                    </div>
                                </x-td>
                                <x-td>
                                    <div class="grid">
                                        <span>{{ __('Hình thức đăng ký: ') . ($booking->booking_type === 'registration' ? 'Đăng ký mới' : ($booking->booking_type === 'transfer' ? 'Chuyển phòng' : 'Gia hạn')) }}</span>
                                        <span>{{ __('Hình thức thuê: ') . ($booking->rental_type === 'daily' ? 'Theo ngày' : 'Theo tháng') }}</span>
                                    </div>
                                </x-td>
                                <x-td>
                                    {{ $booking->check_in_date->format('d/m/Y') }} -
                                    {{ $booking->expected_check_out_date->format('d/m/Y') }}
                                </x-td>
                                <x-td>
                                    @switch($booking->status)
                                        @case('pending')
                                            <div class="grid">
                                                <span class="text-yellow-600">{{ __('Chờ duyệt') }}</span>

                                                <div>
                                                    <x-icon-button :data-update-url="route('bookings.update', $booking)" data-status-value="approved"
                                                        data-status-label="phê duyệt" title="Phê duyệt"
                                                        class="!bg-green-500 !text-white hover:!bg-green-600"
                                                        x-data=""
                                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-updation')">
                                                        <i class="fas fa-check-circle"></i>
                                                    </x-icon-button>
                                                    <x-icon-button :data-update-url="route('bookings.update', $booking)" data-status-value="rejected"
                                                        data-status-label="từ chối" title="Từ chối"
                                                        class="!bg-orange-500 !text-white hover:!bg-orange-600"
                                                        x-data=""
                                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-updation')">
                                                        <i class="fas fa-times-circle"></i>
                                                    </x-icon-button>
                                                </div>
                                            </div>
                                        @break

                                        @case('approved')
                                            <div class="grid">
                                                <span class="text-blue-600">{{ __('Đã duyệt') }}</span>

                                                <div>
                                                    @php
                                                        if ($booking->rental_type === 'daily') {
                                                            $days =
                                                                $booking->check_in_date->diffInDays(
                                                                    $booking->expected_check_out_date,
                                                                ) + 1;
                                                            $requiredAmount = $days * $booking->room->price_per_day;
                                                        } else {
                                                            $requiredAmount = $booking->room->price_per_month;
                                                        }

                                                        $bill = $booking->bills()->oldest()->first();
                                                    @endphp

                                                    @if ($bill)
                                                        <x-icon-button :data-bill-create-url="route('payments.store', $bill)" :data-amount="$requiredAmount" title="Thanh toán"
                                                            class="!bg-blue-500 !text-white hover:!bg-blue-600"
                                                            x-data=""
                                                            x-on:click.prevent="$dispatch('open-modal', 'confirm-bill-creation')">
                                                            <i class="fas fa-money-check-alt"></i>
                                                        </x-icon-button>
                                                    @endif
                                                </div>
                                            </div>
                                        @break

                                        @case('rejected')
                                            <div class="grid">
                                                <span class="text-red-600">{{ __('Bị từ chối') }}</span>
                                                <span class="text-sm">
                                                    {{ __('Lý do:') }}
                                                    <br>
                                                    {{ $booking->reason ?? __('Không có lý do') }}
                                                </span>
                                            </div>
                                        @break

                                        @case('active')
                                            <div class="grid">
                                                <span class="text-green-600">{{ __('Đang hoạt động') }}</span>

                                                <div>
                                                    <x-icon-button :data-update-url="route('bookings.terminateBooking', $booking)" data-status-value="terminate"
                                                        data-status-label="chấm dứt hợp đồng" title="Chấm dứt"
                                                        class="!bg-red-500 !text-white hover:!bg-red-600"
                                                        x-data=""
                                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-updation')">
                                                        <i class="fas fa-times"></i>
                                                    </x-icon-button>
                                                </div>
                                            </div>
                                        @break

                                        @case('expired')
                                            <div class="grid">
                                                <span class="text-gray-600">{{ __('Hết hạn') }}</span>
                                                <span class="text-sm">
                                                    {{ __('Ngày trả phòng:') }}
                                                    <br>
                                                    {{ $booking->actual_check_out_date->format('d/m/Y') }}
                                                </span>
                                            </div>
                                        @break

                                        @case('terminated')
                                            <div class="grid">
                                                <span class="text-red-600">{{ __('Đã chấm dứt') }}</span>
                                                <span class="text-sm">
                                                    {{ __('Ngày trả phòng:') }}
                                                    <br>
                                                    {{ $booking->actual_check_out_date->format('d/m/Y') }}
                                                </span>
                                            </div>
                                        @break
                                    @endswitch
                                </x-td>
                                <x-td>
                                    <x-icon-button :href="route('bookings.show', $booking)" icon="fas fa-eye" :title="__('Xem chi tiết')"
                                        class="!bg-blue-500 !text-white hover:!bg-blue-600" />

                                    <x-icon-button :data-delete-url="route('bookings.destroy', $booking)" icon="fas fa-trash" :title="__('Xoá')"
                                        class="!bg-red-500 !text-white hover:!bg-red-600" x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-deletion')" />
                                </x-td>
                            </x-tr>
                        @endforeach
                    </x-tbody>
                </x-table>
            </div>

            {{ $bookings->links() }}
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
