<x-app-layout>
    <x-slot name="header">
        Chỉnh sửa phòng
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => url('/')],
                ['label' => 'Quản lý phòng', 'url' => route('rooms.index')],
                ['label' => 'Chỉnh sửa phòng'],
            ]" />

            <div class="mx-6 flex items-center justify-between">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-door-open text-blue-600"></i>
                        Chỉnh sửa phòng
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Cập nhật thông tin phòng trong hệ thống</p>
                </div>
                <x-secondary-button :href="route('rooms.index')">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại
                </x-secondary-button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    Thông tin phòng
                </h3>

                <form action="{{ route('rooms.update', $room) }}" method="post"
                    class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="room_code" value="Mã phòng" icon="fas fa-key" />
                        <x-text-input id="room_code" class="block mt-1 w-full" type="text" name="room_code"
                            :value="old('room_code', $room->room_code)" required autofocus autocomplete="room_code" placeholder="Nhập mã phòng" />
                        <x-input-error :messages="$errors->get('room_code')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="branch_id" value="Chi nhánh" icon="fas fa-building" />
                        <x-select id="branch_id" class="block mt-1 w-full" :options="$branches" name="branch_id"
                            :selected="old('branch_id', $room->branch_id)" required placeholder="Chọn chi nhánh" />
                        <x-input-error :messages="$errors->get('branch_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="block" value="Khu nhà" icon="fas fa-building" />
                        <x-text-input list="block-list" id="block" class="block mt-1 w-full" type="text"
                            name="block" :value="old('block', $room->block)" required autocomplete="block"
                            placeholder="Nhập khu nhà (1 ký tự)" />
                        <datalist id="block-list">
                            <option value="A">
                            <option value="B">
                            <option value="C">
                        </datalist>

                        <x-input-error :messages="$errors->get('block')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="floor" value="Tầng" icon="fas fa-layer-group" />
                        <x-text-input id="floor" class="block mt-1 w-full" type="number" name="floor"
                            :value="old('floor', $room->floor)" required autocomplete="floor" placeholder="Nhập số tầng" />
                        <x-input-error :messages="$errors->get('floor')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="gender_type" value="Loại phòng" icon="fas fa-venus-mars" />
                        <x-select id="gender_type" class="block mt-1 w-full" :options="[
                            'male' => 'Nam',
                            'female' => 'Nữ',
                            'mixed' => 'Hỗn hợp',
                        ]" name="gender_type"
                            :selected="old('gender_type', $room->gender_type)" required placeholder="Chọn loại phòng" />
                        <x-input-error :messages="$errors->get('gender_type')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="price" value="Giá mỗi tháng" icon="fas fa-money-bill" />
                        <x-text-input id="price" class="block mt-1 w-full" type="number"
                            name="price" :value="old('price', $room->price)" required autocomplete="price"
                            placeholder="Nhập giá mỗi tháng" />
                        <x-input-error :messages="$errors->get('price')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="capacity" value="Sức chứa" icon="fas fa-users" />
                        <x-text-input id="capacity" class="block mt-1 w-full" type="number" name="capacity"
                            :value="old('capacity', $room->capacity)" required autocomplete="capacity" placeholder="Nhập sức chứa" />
                        <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="current_occupancy" value="Số người hiện tại" icon="fas fa-user-friends" />
                        <x-text-input id="current_occupancy" class="block mt-1 w-full" type="number"
                            name="current_occupancy" :value="old('current_occupancy', $room->current_occupancy)" required autocomplete="current_occupancy"
                            placeholder="Nhập số người hiện tại" />
                        <x-input-error :messages="$errors->get('current_occupancy')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="is_active" value="Trạng thái" icon="fas fa-power-off" />
                        <x-select id="is_active" class="block mt-1 w-full" :options="[
                            1 => 'Đang hoạt động',
                            0 => 'Đang bảo trì',
                        ]" name="is_active"
                            :selected="old('is_active', $room->is_active)" />
                        <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                    </div>

                    <div class="col-span-2">
                        <x-input-label for="description" value="Mô tả" icon="fas fa-align-left" />
                        <x-textarea id="description" class="block mt-1 w-full" name="description" :value="old('description', $room->description)"
                            placeholder="Nhập mô tả phòng (không bắt buộc)" />
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="col-span-2 flex items-center justify-end gap-6">
                        <x-secondary-button :href="route('rooms.index')">
                            <i class="fas fa-arrow-left"></i>
                            Quay lại
                        </x-secondary-button>

                        <x-primary-button>
                            <i class="fas fa-save"></i>
                            Cập nhật
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
