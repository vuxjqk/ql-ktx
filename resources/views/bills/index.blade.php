<x-app-layout>
    <x-slot name="header">
        {{ __('Quản lý hóa đơn') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => url('/')],
                ['label' => 'Quản lý sinh viên', 'url' => route('students.index')],
                ['label' => 'Thông tin sinh viên', 'url' => route('students.show', $user)],
                ['label' => 'Quản lý hóa đơn'],
            ]" />

            <div class="flex items-center justify-between mx-6">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight inline-flex items-center">
                        <span class="me-1">
                            @if ($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar"
                                    class="w-8 h-8 rounded-full object-cover">
                            @else
                                <div
                                    class="w-8 h-8 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center">
                                    {{ mb_substr($user->name, 0, 2, 'UTF-8') }}
                                </div>
                            @endif
                        </span>
                        {{ __('Quản lý hóa đơn') }}
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ __('Quản lý tất cả hóa đơn của :name trong hệ thống', ['name' => $user->name]) }}
                    </p>
                </div>
                <x-secondary-button :href="route('students.show', $user)">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Quay lại') }}
                </x-secondary-button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-blue-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-file-invoice-dollar text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Tổng hóa đơn') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $totalBills }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-yellow-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-hourglass-start text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Chưa thanh toán') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $unpaidBills }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-green-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Đã thanh toán') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $paidBills }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-red-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-times-circle text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Đã hủy') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $cancelledBills }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-filter text-blue-600 me-1"></i>
                    {{ __('Tìm kiếm hóa đơn') }}
                </h3>

                <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
                    <div>
                        <x-input-label for="bill_code" :value="__('Mã hoá đơn')" icon="fas fa-tag" />
                        <x-text-input id="bill_code" class="block mt-1 w-full" type="search" name="bill_code"
                            :value="request('bill_code')" autocomplete="bill_code" :placeholder="__('Mã hóa đơn...')" />
                    </div>

                    <div>
                        <x-input-label for="status" :value="__('Trạng thái')" icon="fas fa-info-circle" />
                        <x-select id="status" class="block mt-1 w-full" :options="[
                            'unpaid' => 'Chưa thanh toán',
                            'paid' => 'Đã thanh toán',
                            'partial' => 'Thanh toán một phần',
                            'cancelled' => 'Đã hủy',
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

            <div x-data="{ openRow: null, openTab: false }" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <x-table :title="__('Danh sách hóa đơn')">
                    <x-thead>
                        <x-tr>
                            <x-th>{{ __('STT') }}</x-th>
                            <x-th>{{ __('Mã hóa đơn') }}</x-th>
                            <x-th>{{ __('Phòng') }}</x-th>
                            <x-th>{{ __('Thời gian cư trú') }}</x-th>
                            <x-th>{{ __('Tổng tiền') }}</x-th>
                            <x-th>{{ __('Trạng thái') }}</x-th>
                            <x-th>{{ __('Hạn thanh toán') }}</x-th>
                            <x-th>{{ __('Người tạo') }}</x-th>
                            <x-th>{{ __('Hành động') }}</x-th>
                        </x-tr>
                    </x-thead>
                    <x-tbody>
                        @foreach ($bills as $index => $bill)
                            <x-tr>
                                <x-td>#{{ $bills->firstItem() + $index }}</x-td>
                                <x-td>{{ $bill->bill_code }}</x-td>
                                <x-td>
                                    <div class="grid">
                                        <span class="font-semibold">{{ $bill->booking->room->room_code }}</span>
                                        <span class="text-sm">
                                            {{ __('Tầng: ') . $bill->booking->room->floor->floor_number }}
                                        </span>
                                        <span class="text-sm">
                                            {{ __('Chi nhánh: ') . $bill->booking->room->floor->branch->name }}
                                        </span>
                                    </div>
                                </x-td>
                                <x-td>
                                    {{ $bill->booking->check_in_date->format('d/m/Y') }} -
                                    {{ $bill->booking->expected_check_out_date->format('d/m/Y') }}
                                </x-td>
                                <x-td>{{ number_format($bill->total_amount, 0, ',', '.') }} VND</x-td>
                                <x-td>
                                    @switch($bill->status)
                                        @case('unpaid')
                                            <span class="text-yellow-600">{{ __('Chưa thanh toán') }}</span>
                                        @break

                                        @case('paid')
                                            <span class="text-green-600">{{ __('Đã thanh toán') }}</span>
                                        @break

                                        @case('partial')
                                            <span class="text-blue-600">{{ __('Thanh toán một phần') }}</span>
                                        @break

                                        @case('cancelled')
                                            <span class="text-red-600">{{ __('Đã hủy') }}</span>
                                        @break
                                    @endswitch
                                </x-td>
                                <x-td>{{ $bill->due_date ? $bill->due_date->format('d/m/Y') : 'N/A' }}</x-td>
                                <x-td>{{ $bill->creator->name }}</x-td>
                                <x-td>
                                    <x-icon-button
                                        @click="openRow = openRow === {{ $index }} ? null : {{ $index }}"
                                        class="!bg-green-500 !text-white hover:!bg-green-600">
                                        <span
                                            :class="{
                                                'hidden': openRow === {{ $index }},
                                                'inline-flex': openRow !== {{ $index }}
                                            }">
                                            <i class="fas fa-chevron-down"></i>
                                        </span>
                                        <span
                                            :class="{
                                                'hidden': openRow !== {{ $index }},
                                                'inline-flex': openRow === {{ $index }}
                                            }">
                                            <i class="fas fa-chevron-up"></i>
                                        </span>
                                    </x-icon-button>

                                    @php
                                        $remainingAmount = $bill->total_amount - $bill->payments->sum('amount');
                                    @endphp

                                    <x-icon-button :data-pay-url="route('bills.pay', $bill)" :data-amount="$remainingAmount" icon="fas fa-money-check-alt"
                                        :title="__('Thanh toán')" class="!bg-blue-500 !text-white hover:!bg-blue-600"
                                        x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-pay')" />

                                    <x-icon-button :href="route('bills.export', $bill)" target="_blank" icon="fas fa-file-pdf"
                                        :title="__('Xuất hoá đơn')" class="!bg-purple-500 !text-white hover:!bg-purple-600" />
                                </x-td>
                            </x-tr>

                            <tr x-show="openRow === {{ $index }}">
                                <td colspan="9">
                                    <div class="bg-blue-50 px-12">
                                        <div class="flex items-center justify-end py-3">
                                            <x-secondary-button @click="openTab = !openTab"
                                                class="!bg-blue-500 !text-white hover:!bg-blue-600">
                                                <span x-show="!openTab" class="flex items-center gap-2">
                                                    <i class="fas fa-arrow-right"></i>
                                                    {{ __('Chi tiết hoá đơn') }}
                                                </span>
                                                <span x-show="openTab" class="flex items-center gap-2">
                                                    <i class="fas fa-arrow-left"></i>
                                                    {{ __('Thanh toán') }}
                                                </span>
                                            </x-secondary-button>
                                        </div>

                                        <table x-show="!openTab" class="w-full table-auto">
                                            <thead class="bg-blue-100">
                                                <tr>
                                                    <th
                                                        class="px-6 py-3 text-left font-medium text-sm text-gray-600 leading-tight uppercase tracking-wide whitespace-nowrap">
                                                        {{ __('STT') }}</th>
                                                    <th
                                                        class="px-6 py-3 text-left font-medium text-sm text-gray-600 leading-tight uppercase tracking-wide whitespace-nowrap">
                                                        {{ __('Mô tả') }}</th>
                                                    <th
                                                        class="px-6 py-3 text-left font-medium text-sm text-gray-600 leading-tight uppercase tracking-wide whitespace-nowrap">
                                                        {{ __('Số tiền') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-blue-150">
                                                @foreach ($bill->bill_items as $itemIndex => $item)
                                                    <tr
                                                        class="hover:bg-blue-100 transition-colors duration-150 ease-in-out">
                                                        <td class="px-6 py-4 text-gray-800 whitespace-nowrap">
                                                            #{{ $itemIndex + 1 }}</td>
                                                        <td class="px-6 py-4 text-gray-800 whitespace-nowrap">
                                                            {{ $item->description }}</td>
                                                        <td class="px-6 py-4 text-gray-800 whitespace-nowrap">
                                                            {{ number_format($item->amount, 0, ',', '.') }} VND</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        <table x-show="openTab" class="w-full table-auto">
                                            <thead class="bg-blue-100">
                                                <tr>
                                                    <th
                                                        class="px-6 py-3 text-left font-medium text-sm text-gray-600 leading-tight uppercase tracking-wide whitespace-nowrap">
                                                        {{ __('STT') }}</th>
                                                    <th
                                                        class="px-6 py-3 text-left font-medium text-sm text-gray-600 leading-tight uppercase tracking-wide whitespace-nowrap">
                                                        {{ __('Loại thanh toán') }}</th>
                                                    <th
                                                        class="px-6 py-3 text-left font-medium text-sm text-gray-600 leading-tight uppercase tracking-wide whitespace-nowrap">
                                                        {{ __('Số tiền') }}</th>
                                                    <th
                                                        class="px-6 py-3 text-left font-medium text-sm text-gray-600 leading-tight uppercase tracking-wide whitespace-nowrap">
                                                        {{ __('Thời gian thanh toán') }}</th>
                                                    <th
                                                        class="px-6 py-3 text-left font-medium text-sm text-gray-600 leading-tight uppercase tracking-wide whitespace-nowrap">
                                                        {{ __('Người thanh toán') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-blue-150">
                                                @foreach ($bill->payments as $paymentIndex => $payment)
                                                    <tr
                                                        class="hover:bg-blue-100 transition-colors duration-150 ease-in-out">
                                                        <td class="px-6 py-4 text-gray-800 whitespace-nowrap">
                                                            #{{ $paymentIndex + 1 }}</td>
                                                        <td class="px-6 py-4 text-gray-800 whitespace-nowrap">
                                                            {{ $payment->payment_type === 'online' ? 'Trực tuyến' : 'Tiền mặt' }}
                                                        </td>
                                                        <td class="px-6 py-4 text-gray-800 whitespace-nowrap">
                                                            {{ number_format($payment->amount, 0, ',', '.') }} VND</td>
                                                        <td class="px-6 py-4 text-gray-800 whitespace-nowrap">
                                                            {{ $payment->paid_at?->format('d/m/Y H:i') }}</td>
                                                        <td class="px-6 py-4 text-gray-800 whitespace-nowrap">
                                                            {{ $payment->user->name ?? 'N/A' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-tbody>
                </x-table>
            </div>

            {{ $bills->links() }}
        </div>
    </div>

    <x-modal name="confirm-pay" focusable>
        <form id="pay-form" method="post" action="#" class="p-6">
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

    @pushOnce('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('pay-form');
                const billAmountText = document.getElementById('bill-amount');
                document.querySelectorAll('[data-pay-url]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        form.action = btn.dataset.payUrl;
                        billAmountText.textContent =
                            `Số tiền cần thanh toán: ${Number(btn.dataset.amount).toLocaleString()}₫`;
                    });
                });
            });
        </script>
    @endPushOnce
</x-app-layout>
