<x-app-layout>
    <x-slot name="header">
        Thêm bản ghi tiện ích
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => url('/')],
                ['label' => 'Quản lý phòng', 'url' => route('rooms.index')],
                ['label' => 'Thêm bản ghi tiện ích'],
            ]" />

            <div class="mx-6 flex items-center justify-between">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-plug text-blue-800"></i>
                        Thêm bản ghi tiện ích
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Thêm bản ghi tiện ích cho phòng {{ $room->room_code }}</p>
                </div>
                <x-secondary-button :href="route('rooms.index')">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại
                </x-secondary-button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-info-circle text-blue-800"></i>
                    Thông tin tiện ích
                </h3>

                <form action="{{ route('utilities.store', $room) }}" method="post"
                    class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    @csrf

                    <div>
                        <x-input-label for="month" value="Tháng" icon="fas fa-calendar-alt" />
                        <x-text-input id="month" class="block mt-1 w-full" type="month" name="month"
                            :value="old('month')" required autofocus placeholder="Chọn tháng" />
                        <x-input-error :messages="$errors->get('month')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="electric_usage" value="Lượng điện tiêu thụ (kWh)" icon="fas fa-bolt" />
                        <x-text-input id="electric_usage" class="block mt-1 w-full" type="number" step="0.01"
                            name="electric_usage" :value="old('electric_usage')" required placeholder="Nhập lượng điện tiêu thụ" />
                        <x-input-error :messages="$errors->get('electric_usage')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="water_usage" value="Lượng nước tiêu thụ (m³)" icon="fas fa-tint" />
                        <x-text-input id="water_usage" class="block mt-1 w-full" type="number" step="0.01"
                            name="water_usage" :value="old('water_usage')" required placeholder="Nhập lượng nước tiêu thụ" />
                        <x-input-error :messages="$errors->get('water_usage')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="electric_cost" value="Chi phí điện (VNĐ)" icon="fas fa-money-bill" />
                        <x-text-input id="electric_cost" class="block mt-1 w-full" type="number" name="electric_cost"
                            :value="old('electric_cost')" required placeholder="Nhập chi phí điện" />
                        <x-input-error :messages="$errors->get('electric_cost')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="water_cost" value="Chi phí nước (VNĐ)" icon="fas fa-money-bill" />
                        <x-text-input id="water_cost" class="block mt-1 w-full" type="number" name="water_cost"
                            :value="old('water_cost')" required placeholder="Nhập chi phí nước" />
                        <x-input-error :messages="$errors->get('water_cost')" class="mt-2" />
                    </div>

                    <div class="col-span-2">
                        <x-input-label for="notes" value="Ghi chú" icon="fas fa-align-left" />
                        <x-textarea id="notes" class="block mt-1 w-full" name="notes" :value="old('notes')"
                            placeholder="Nhập ghi chú (không bắt buộc)" />
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <div class="col-span-2 flex items-center justify-end gap-6">
                        <x-secondary-button :href="route('rooms.index')">
                            <i class="fas fa-arrow-left"></i>
                            Quay lại
                        </x-secondary-button>

                        <x-primary-button>
                            <i class="fas fa-save"></i>
                            Lưu
                        </x-primary-button>
                    </div>
                </form>
            </div>

            @if ($lastUtility)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-info-circle text-blue-800"></i>
                        Bản ghi tiện ích gần đây nhất
                    </h3>

                    <form action="{{ route('utilities.update', $lastUtility) }}" method="post"
                        class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="last-month" value="Tháng" icon="fas fa-calendar-alt" />
                            <x-text-input id="last-month" class="block mt-1 w-full" type="month" name="last_month"
                                :value="old('last_month', $lastUtility->month->format('Y-m'))" required autofocus placeholder="Chọn tháng" />
                            <x-input-error :messages="$errors->get('last_month')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="last-electric_usage" value="Lượng điện tiêu thụ (kWh)"
                                icon="fas fa-bolt" />
                            <x-text-input id="last-electric_usage" class="block mt-1 w-full" type="number"
                                step="0.01" name="last_electric_usage" :value="old('last_electric_usage', $lastUtility->electric_usage)" required
                                placeholder="Nhập lượng điện tiêu thụ" />
                            <x-input-error :messages="$errors->get('last_electric_usage')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="last-water_usage" value="Lượng nước tiêu thụ (m³)"
                                icon="fas fa-tint" />
                            <x-text-input id="last-water_usage" class="block mt-1 w-full" type="number"
                                step="0.01" name="last_water_usage" :value="old('last_water_usage', $lastUtility->water_usage)" required
                                placeholder="Nhập lượng nước tiêu thụ" />
                            <x-input-error :messages="$errors->get('last_water_usage')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="last-electric_cost" value="Chi phí điện (VNĐ)"
                                icon="fas fa-money-bill" />
                            <x-text-input id="last-electric_cost" class="block mt-1 w-full" type="number"
                                name="last_electric_cost" :value="old('last_electric_cost', $lastUtility->electric_cost)" required
                                placeholder="Nhập chi phí điện" />
                            <x-input-error :messages="$errors->get('last_electric_cost')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="last-water_cost" value="Chi phí nước (VNĐ)"
                                icon="fas fa-money-bill" />
                            <x-text-input id="last-water_cost" class="block mt-1 w-full" type="number"
                                name="last_water_cost" :value="old('last_water_cost', $lastUtility->water_cost)" required placeholder="Nhập chi phí nước" />
                            <x-input-error :messages="$errors->get('last_water_cost')" class="mt-2" />
                        </div>

                        <div class="col-span-2">
                            <x-input-label for="last-notes" value="Ghi chú" icon="fas fa-align-left" />
                            <x-textarea id="last-notes" class="block mt-1 w-full" name="last_notes"
                                :value="old('last_notes', $lastUtility->notes)" placeholder="Nhập ghi chú (không bắt buộc)" />
                            <x-input-error :messages="$errors->get('last_notes')" class="mt-2" />
                        </div>

                        <div class="col-span-2 flex items-center justify-end gap-6">
                            <x-secondary-button :href="route('rooms.index')">
                                <i class="fas fa-arrow-left"></i>
                                Quay lại
                            </x-secondary-button>

                            <x-danger-button :data-delete-url="route('utilities.destroy', $lastUtility)" x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'confirm-deletion')">
                                <i class="fas fa-trash"></i>
                                Xoá
                            </x-danger-button>

                            <x-primary-button>
                                <i class="fas fa-save"></i>
                                Cập nhật
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <x-delete-modal />
</x-app-layout>
