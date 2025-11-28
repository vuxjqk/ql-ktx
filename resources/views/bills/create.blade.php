<x-app-layout>
    <x-slot name="header">
        {{ __('Tạo hoá đơn') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => url('/')],
                ['label' => 'Quản lý sinh viên', 'url' => route('students.index')],
                ['label' => $user->name, 'url' => route('students.show', $user)],
                ['label' => 'Quản lý hóa đơn', 'url' => route('bills.index', $user)],
                ['label' => 'Tạo hoá đơn'],
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
                        {{ __('Tạo hoá đơn') }}
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ __('Tạo hoá đơn phí ký túc xá và dịch vụ cho sinh viên :name', ['name' => $user->name]) }}
                    </p>
                </div>
                <x-secondary-button :href="route('bills.index', $user)">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Quay lại') }}
                </x-secondary-button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @php
                    $isCheckoutRequested = $booking->status === 'active' && $booking->actual_check_out_date !== null;
                    $hasNoBill = $totalAmount < 1000;
                @endphp

                @if ($hasNoBill)
                    <!-- Trường hợp không có hóa đơn cần tạo -->
                    <div class="p-8 text-center">
                        <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-info-circle text-3xl text-gray-500"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            {{ __('Không có hóa đơn cần tạo trong kỳ này') }}
                        </h3>
                        <p class="text-sm text-gray-600 max-w-md mx-auto">
                            @if ($isCheckoutRequested)
                                Sinh viên đã yêu cầu trả phòng và không phát sinh thêm chi phí nào.
                            @else
                                Tổng tiền dịch vụ và tiền thuê trong kỳ nhỏ hơn 1.000 VND.
                            @endif
                        </p>
                    </div>

                    <!-- Nút xác nhận trả phòng nếu đang yêu cầu trả phòng + không có hóa đơn -->
                    @if ($isCheckoutRequested)
                        <div class="px-8 pb-8 text-center">
                            <x-secondary-button class="!bg-red-600 !text-white hover:!bg-red-700"
                                x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'confirm-terminate')">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ __('Xác nhận trả phòng cho sinh viên') }}
                            </x-secondary-button>
                        </div>
                    @endif

                    <!-- Nút quay lại -->
                    <div class="px-8 pb-8 text-center border-t border-gray-200 pt-6">
                        <x-secondary-button :href="route('bills.index', $user)">
                            <i class="fas fa-arrow-left me-2"></i>
                            {{ __('Quay lại danh sách hóa đơn') }}
                        </x-secondary-button>
                    </div>
                @else
                    <!-- Có hóa đơn cần tạo -->
                    <x-table :title="__('Chi tiết hóa đơn')">
                        <x-thead>
                            <x-tr>
                                <x-th>{{ __('STT') }}</x-th>
                                <x-th>{{ __('Tên dịch vụ') }}</x-th>
                                <x-th>{{ __('Lượng sử dụng') }}</x-th>
                                <x-th>{{ __('Thành tiền') }}</x-th>
                            </x-tr>
                        </x-thead>
                        <x-tbody>
                            @foreach ($billItems as $index => $item)
                                <x-tr>
                                    <x-td>#{{ $index + 1 }}</x-td>
                                    <x-td>{{ $item['description'] }}</x-td>
                                    <x-td>
                                        @if (isset($item['usage_amount']))
                                            {{ number_format($item['usage_amount'], 2) }} {{ $item['unit'] ?? '' }}
                                        @else
                                            1 kỳ
                                        @endif
                                    </x-td>
                                    <x-td class="font-medium">
                                        {{ number_format($item['amount'], 0, ',', '.') }} VND
                                    </x-td>
                                </x-tr>
                            @endforeach

                            <x-tr class="bg-gray-50">
                                <x-td colspan="3" class="text-right font-bold text-lg">
                                    {{ __('Tổng cộng') }}
                                </x-td>
                                <x-td class="font-bold text-xl text-red-600">
                                    {{ number_format($totalAmount, 0, ',', '.') }} VND
                                </x-td>
                            </x-tr>

                            <x-tr>
                                <x-td colspan="4">
                                    <div class="flex items-center justify-end gap-6 py-4">
                                        <x-secondary-button :href="route('bills.index', $user)">
                                            <i class="fas fa-arrow-left"></i>
                                            {{ __('Quay lại') }}
                                        </x-secondary-button>

                                        <x-primary-button x-data=""
                                            x-on:click.prevent="$dispatch('open-modal', 'confirm-creation')">
                                            <i class="fas fa-plus"></i>
                                            {{ __('Tạo hoá đơn') }}
                                        </x-primary-button>
                                    </div>
                                </x-td>
                            </x-tr>
                        </x-tbody>
                    </x-table>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal xác nhận tạo hóa đơn -->
    <x-modal name="confirm-creation" focusable>
        <form method="post" action="{{ route('bills.store', $user) }}" class="p-6">
            @csrf

            <div class="flex items-center gap-6">
                <div class="bg-green-100 shadow-sm sm:rounded-lg p-3">
                    <i class="fas fa-file-invoice-dollar text-green-600 text-xl"></i>
                </div>
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Xác nhận tạo hóa đơn') }}
                </h2>
            </div>

            <p class="mt-4 text-sm text-gray-600">
                Tổng tiền: <strong class="text-red-600">{{ number_format($totalAmount, 0, ',', '.') }} VND</strong>
            </p>

            <p class="mt-4 text-sm text-gray-600">
                {{ __('Ngay sau khi bạn xác nhận, hóa đơn sẽ được tạo và sinh viên sẽ nhận được thông báo.') }}
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Huỷ') }}
                </x-secondary-button>

                <x-primary-button type="submit">
                    {{ __('Tạo hóa đơn') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Modal xác nhận trả phòng (khi không có hóa đơn + đang yêu cầu trả phòng) -->
    @if ($isCheckoutRequested && $hasNoBill)
        <x-modal name="confirm-terminate" focusable>
            <form method="post" action="{{ route('bookings.terminateBooking', $booking) }}" class="p-6">
                @csrf
                @method('PUT')

                <div class="flex items-center gap-6">
                    <div class="bg-orange-100 shadow-sm sm:rounded-lg p-3">
                        <i class="fas fa-home text-orange-600 text-xl"></i>
                    </div>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Xác nhận trả phòng cho sinh viên') }}
                    </h2>
                </div>

                <p class="mt-4 text-sm text-gray-600">
                    Sinh viên: <strong>{{ $user->name }}</strong><br>
                    Phòng: <strong>{{ $booking->room->room_code }}</strong>
                </p>

                <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-800 font-medium">
                        Không phát sinh thêm chi phí nào trong kỳ trả phòng.
                    </p>
                </div>

                <p class="mt-4 text-sm text-gray-600">
                    {{ __('Sau khi xác nhận, hợp đồng sẽ được chấm dứt và phòng sẽ được giải phóng.') }}
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Huỷ') }}
                    </x-secondary-button>

                    <x-danger-button type="submit">
                        <i class="fas fa-check me-2"></i>
                        {{ __('Xác nhận trả phòng') }}
                    </x-danger-button>
                </div>
            </form>
        </x-modal>
    @endif
</x-app-layout>
