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
                                <x-td>{{ ($item['usage_amount'] ?? 1) . ' ' . ($item['unit'] ?? '') }}</x-td>
                                <x-td>
                                    {{ number_format($item['amount'], 0, ',', '.') }} VND
                                </x-td>
                            </x-tr>
                        @endforeach

                        <x-tr>
                            <x-td colspan="4">
                                <div
                                    class="flex items-center justify-end font-semibold text-xl text-red-600 leading-tight">
                                    {{ __('Tổng tiền: ') . number_format($totalAmount, 0, ',', '.') }} VND
                                </div>
                            </x-td>
                        </x-tr>

                        <x-tr>
                            <x-td colspan="4">
                                <div class="flex items-center justify-end gap-6">
                                    <x-secondary-button :href="route('bills.index', $user)">
                                        <i class="fas fa-arrow-left"></i>
                                        {{ __('Quay lại') }}
                                    </x-secondary-button>

                                    <x-secondary-button class="!bg-blue-600 !text-white hover:!bg-blue-700"
                                        x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-creation')">
                                        <i class="fas fa-plus"></i>
                                        {{ __('Tạo hoá đơn') }}
                                    </x-secondary-button>
                                </div>
                            </x-td>
                        </x-tr>
                    </x-tbody>
                </x-table>
            </div>
        </div>
    </div>

    <x-modal name="confirm-creation" focusable>
        <form method="post" action="{{ route('bills.store', $user) }}" class="p-6">
            @csrf

            <div class="flex items-center gap-6">
                <div class="bg-green-100 shadow-sm sm:rounded-lg p-3">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </div>
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Bạn có chắc chắn muốn tạo hoá đơn không?') }}
                </h2>
            </div>

            <p class="mt-6 text-sm text-gray-600">
                {{ __('Ngay sau khi bạn xác nhận tạo hoá đơn, sinh viên trong phòng sẽ nhận được thông báo từ hệ thống.') }}
            </p>

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
</x-app-layout>
