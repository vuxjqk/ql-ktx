<x-app-layout>
    <x-slot name="header">
        {{ __('Ghi nhận sử dụng dịch vụ') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => url('/')],
                ['label' => 'Quản lý phòng', 'url' => route('rooms.index')],
                [
                    'label' => 'Phòng ' . $room->room_code . ' | ' . $room->floor->branch->name,
                    'url' => route('rooms.show', $room),
                ],
                ['label' => 'Ghi nhận sử dụng dịch vụ'],
            ]" />

            <div class="flex items-center justify-between mx-6">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-concierge-bell text-blue-600 me-1"></i>
                        {{ __('Ghi nhận sử dụng dịch vụ') }}
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ __('Ghi nhận lại việc sử dụng dịch vụ trong phòng ' . $room->room_code . ' | ' . $room->floor->branch->name) }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-blue-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-concierge-bell text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Tổng dịch vụ') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $totalServices }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-yellow-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-user-friends text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Đã chia sẻ cho') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">
                                {{ $usages->first()->shares_count ?? 0 }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-green-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-home text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Đang cư trú') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $activeBookings }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-filter text-blue-600 me-1"></i>
                    {{ __('Lọc theo ngày ghi nhận') }}
                </h3>

                <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
                    <div>
                        <x-input-label for="usage_date" :value="__('Ngày ghi nhận')" icon="fas fa-calendar-alt" />
                        <x-text-input id="usage_date" class="block mt-1 w-full" type="date" name="usage_date"
                            :value="request('usage_date', today()->format('Y-m-d'))" />
                    </div>

                    <div class="flex items-end">
                        <x-primary-button>
                            <i class="fas fa-search"></i>
                            {{ __('Tìm kiếm') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <form action="{{ route('service-usages.update', $room) }}" method="post"
                class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @csrf
                @method('put')

                <input type="hidden" name="usage_date" value="{{ $usageDate }}">

                <x-table :title="__('Ghi nhận cho ngày ') . \Carbon\Carbon::parse($usageDate)->format('d/m/Y')">
                    <x-thead>
                        <x-tr>
                            <x-th>{{ __('STT') }}</x-th>
                            <x-th>
                                <div class="flex items-center gap-6">
                                    {{ __('Tên dịch vụ') }}
                                    <x-sortable-column :options="['name_asc', 'name_desc']" />
                                </div>
                            </x-th>
                            <x-th>{{ __('Lượng sử dụng') }}</x-th>
                            <x-th>{{ __('Đơn giá') }}</x-th>
                            <x-th>{{ __('Hạn mức miễn phí') }}</x-th>
                            <x-th>{{ __('Thành tiền cũ') }}</x-th>
                        </x-tr>
                    </x-thead>
                    <x-tbody>
                        @foreach ($services as $index => $service)
                            <x-tr>
                                <x-td>#{{ $index + 1 }}</x-td>
                                <x-td>
                                    {{ $service->name }}
                                    @if ($service->is_mandatory)
                                        <span class="text-red-600" title="{{ __('Bắt buộc') }}">*</span>
                                    @endif
                                </x-td>
                                <x-td>
                                    <input type="hidden" name="services[{{ $service->id }}][service_id]"
                                        value="{{ $service->id }}">

                                    @php
                                        $usage = $usages->get($service->id);
                                        $amount = $usage ? $usage->usage_amount : 0;
                                    @endphp

                                    <div>
                                        <x-input-label :for="'usage_amount_' . $service->id" :value="__('Lượng sử dụng') . ' (' . $service->name . ')'" icon="fas fa-ruler" />
                                        <x-text-input :id="'usage_amount_' . $service->id" class="block mt-1 w-full" type="number"
                                            step="0.01" name="services[{{ $service->id }}][usage_amount]"
                                            :value="old('services.' . $service->id . '.usage_amount', $amount)" autocomplete="usage_amount" :placeholder="__('Nhập lượng sử dụng cho ' . $service->name)" />
                                        <x-input-error :messages="$errors->get('services.' . $service->id . '.usage_amount')" class="mt-2" />
                                    </div>
                                </x-td>
                                <x-td>{{ number_format($service->unit_price, 0, ',', '.') }} /
                                    {{ $service->unit }}</x-td>
                                <x-td>{{ $service->free_quota }}</x-td>
                                <x-td>
                                    {{ number_format($usage->subtotal ?? 0, 0, ',', '.') }} VND
                                </x-td>
                            </x-tr>
                        @endforeach

                        <x-tr>
                            <x-td colspan="6">
                                <div class="flex items-center justify-end gap-6">
                                    <x-input-error :messages="$errors->get('usage_date')" class="mt-2" />

                                    <x-secondary-button :href="route('rooms.show', $room)">
                                        <i class="fas fa-arrow-left"></i>
                                        {{ __('Quay lại') }}
                                    </x-secondary-button>

                                    <x-primary-button>
                                        <i class="fas fa-save"></i>
                                        {{ __('Ghi nhận') }}
                                    </x-primary-button>
                                </div>
                            </x-td>
                        </x-tr>
                    </x-tbody>
                </x-table>
            </form>

            {{ $services->links() }}
        </div>
    </div>
</x-app-layout>
