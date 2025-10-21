<x-app-layout>
    <x-slot name="header">
        {{ __('Ghi nhận sử dụng dịch vụ') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => url('/')],
                ['label' => 'Quản lý phòng', 'url' => route('rooms.index')],
                ['label' => 'Ghi nhận sử dụng dịch vụ'],
            ]" />

            <div class="flex items-center justify-between mx-6">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-concierge-bell text-blue-600 me-1"></i>
                        {{ __('Ghi nhận sử dụng dịch vụ') }}
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">{{ __('Ghi nhận thông tin sử dụng dịch vụ cho phòng') }}</p>
                </div>
                <x-secondary-button :href="route('rooms.index')">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Quay lại') }}
                </x-secondary-button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-info-circle text-blue-600 me-1"></i>
                    {{ __('Thông tin sử dụng dịch vụ') }}
                </h3>

                <form action="{{ route('service-usages.update', $room) }}" method="post"
                    class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    @csrf
                    @method('put')

                    <div>
                        <x-input-label for="room_code" :value="__('Mã phòng')" icon="fas fa-tag" />
                        <x-text-input id="room_code" class="block mt-1 w-full" type="text" :value="$room->room_code"
                            readonly />
                    </div>

                    <div>
                        <x-input-label for="name" :value="__('Tên chi nhánh')" icon="fas fa-user" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" :value="$room->floor->branch->name"
                            readonly />
                    </div>

                    <div>
                        <x-input-label for="updated_at" :value="__('Ngày ghi nhận gần đây')" icon="fas fa-calendar-alt" />
                        <x-text-input id="updated_at" class="block mt-1 w-full" type="date" :value="old(
                            'updated_at',
                            $room->serviceUsages()->latest()->first()?->updated_at->format('Y-m-d'),
                        )"
                            readonly />
                    </div>

                    @foreach ($room->services as $service)
                        <input type="hidden" name="services[{{ $service->id }}][service_id]"
                            value="{{ $service->id }}">

                        <div>
                            <x-input-label :for="'usage_amount_' . $service->id" :value="__('Lượng sử dụng') . ' (' . $service->name . ')'" icon="fas fa-ruler" />
                            <x-text-input :id="'usage_amount_' . $service->id" class="block mt-1 w-full" type="number" step="0.01"
                                name="services[{{ $service->id }}][usage_amount]" :value="old(
                                    'services.' . $service->id . '.usage_amount',
                                    $service->getUsageAmountForRoom($room),
                                )"
                                autocomplete="usage_amount" :placeholder="__('Nhập lượng sử dụng cho ' . $service->name)" />
                            <x-input-error :messages="$errors->get('services.' . $service->id . '.usage_amount')" class="mt-2" />
                        </div>
                    @endforeach

                    <div class="col-span-2 flex items-center justify-end gap-6">
                        <x-secondary-button :href="route('rooms.index')">
                            <i class="fas fa-arrow-left"></i>
                            {{ __('Quay lại') }}
                        </x-secondary-button>

                        <x-primary-button>
                            <i class="fas fa-save"></i>
                            {{ __('Lưu') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <div
                class="bg-white overflow-hidden shadow-sm sm:rounded-lg @if ($room->services->isEmpty()) hidden @endif">
                <x-table :title="__('Chi tiết hoá đơn')">
                    <x-thead>
                        <x-tr>
                            <x-th>{{ __('Tên dịch vụ') }}</x-th>
                            <x-th>{{ __('Đơn giá') }}</x-th>
                            <x-th>{{ __('Lượng sử dụng') }}</x-th>
                            <x-th>{{ __('Đơn vị') }}</x-th>
                            <x-th>{{ __('Thành tiền') }}</x-th>
                        </x-tr>
                    </x-thead>
                    <x-tbody>
                        @foreach ($room->services as $service)
                            <x-tr>
                                <x-td>{{ $service->name }}</x-td>
                                <x-td>{{ number_format($service->unit_price, 0, ',', '.') }} VND</x-td>
                                <x-td>{{ $service->getUsageAmountForRoom($room) }}</x-td>
                                <x-td>{{ $service->unit }}</x-td>
                                <x-td>
                                    {{ number_format($service->unit_price * $service->getUsageAmountForRoom($room), 0, ',', '.') }}
                                    VND
                                </x-td>
                            </x-tr>
                        @endforeach
                    </x-tbody>
                </x-table>

                <div class="flex items-center justify-end gap-6 p-6">
                    <x-secondary-button class="!bg-red-600 !text-white hover:!bg-red-700" x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'confirm-cancelled')">
                        <i class="fas fa-times-circle"></i>
                        {{ __('Huỷ') }}
                    </x-secondary-button>

                    <x-secondary-button class="!bg-blue-600 !text-white hover:!bg-blue-700" x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'confirm-creation')">
                        <i class="fas fa-plus"></i>
                        {{ __('Tạo hoá đơn') }}
                    </x-secondary-button>
                </div>
            </div>
        </div>
    </div>

    <x-modal name="confirm-creation" focusable>
        <form method="post" action="{{ route('bills.store', $room) }}" class="p-6">
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

    <x-modal name="confirm-cancelled" focusable>
        <form method="post" action="{{ route('bills.cancelBills', $room) }}" class="p-6">
            @csrf

            <div class="flex items-center gap-6">
                <div class="bg-red-100 shadow-sm sm:rounded-lg p-3">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Bạn có chắc chắn muốn huỷ hoá đơn không?') }}
                </h2>
            </div>

            <p class="mt-6 text-sm text-gray-600">
                {{ __('Ngay sau khi bạn xác nhận huỷ hoá đơn, toàn bộ hoá đơn trong phòng này sẽ bị huỷ bỏ.') }}
            </p>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Huỷ') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Xác nhận') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
