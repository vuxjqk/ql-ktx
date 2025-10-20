<x-app-layout>
    <x-slot name="header">
        {{ __('Chỉnh sửa phòng') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => url('/')],
                ['label' => 'Quản lý phòng', 'url' => route('rooms.index')],
                ['label' => 'Chỉnh sửa phòng'],
            ]" />

            <div class="flex items-center justify-between mx-6">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-door-open text-blue-600 me-1"></i>
                        {{ __('Chỉnh sửa phòng') }}
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">{{ __('Cập nhật thông tin phòng trong hệ thống') }}</p>
                </div>
                <x-secondary-button :href="route('rooms.index')">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Quay lại') }}
                </x-secondary-button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-info-circle text-blue-600 me-1"></i>
                    {{ __('Thông tin phòng') }}
                </h3>

                <form action="{{ route('rooms.update', $room) }}" method="post"
                    class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    @csrf
                    @method('put')

                    <div>
                        <x-input-label for="room_code" :value="__('Mã phòng')" icon="fas fa-tag" />
                        <x-text-input id="room_code" class="block mt-1 w-full" type="text" name="room_code"
                            :value="old('room_code', $room->room_code)" required autofocus autocomplete="room_code" :placeholder="__('Nhập mã phòng')" />
                        <x-input-error :messages="$errors->get('room_code')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="floor_id" :value="__('Tầng')" icon="fas fa-layer-group" />
                        <select id="floor_id"
                            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            name="floor_id" required>
                            <option value="">{{ __('Chọn tầng') }}</option>
                            @foreach ($options as $branchName => $floors)
                                <optgroup label="{{ $branchName }}">
                                    @foreach ($floors as $floorId => $floorName)
                                        <option value="{{ $floorId }}" @selected(old('floor_id', $room->floor_id))>
                                            {{ $floorName }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('floor_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="price_per_day" :value="__('Giá theo ngày')" icon="fas fa-calendar-day" />
                        <x-text-input id="price_per_day" class="block mt-1 w-full" type="number" name="price_per_day"
                            :value="old('price_per_day', $room->price_per_day)" required autocomplete="off" :placeholder="__('Nhập giá theo ngày')" />
                        <x-input-error :messages="$errors->get('price_per_day')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="price_per_month" :value="__('Giá theo tháng')" icon="fas fa-calendar-alt" />
                        <x-text-input id="price_per_month" class="block mt-1 w-full" type="number"
                            name="price_per_month" :value="old('price_per_month', $room->price_per_month)" required autocomplete="off" :placeholder="__('Nhập giá theo tháng')" />
                        <x-input-error :messages="$errors->get('price_per_month')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="capacity" :value="__('Sức chứa')" icon="fas fa-users" />
                        <x-text-input id="capacity" class="block mt-1 w-full" type="number" name="capacity"
                            :value="old('capacity', $room->capacity)" required autocomplete="capacity" :placeholder="__('Nhập sức chứa')" />
                        <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="current_occupancy" :value="__('Số người hiện tại')" icon="fas fa-user-friends" />
                        <x-text-input id="current_occupancy" class="block mt-1 w-full" type="number"
                            name="current_occupancy" :value="old('current_occupancy', $room->current_occupancy)" required autocomplete="current_occupancy"
                            :placeholder="__('Nhập số người hiện tại')" />
                        <x-input-error :messages="$errors->get('current_occupancy')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="is_active" :value="__('Trạng thái')" icon="fas fa-toggle-on" />
                        <x-select id="is_active" class="block mt-1 w-full" :options="[
                            '1' => 'Hoạt động',
                            '0' => 'Không hoạt động',
                        ]" name="is_active"
                            :selected="old('is_active', $room->is_active)" required :placeholder="__('Chọn trạng thái')" />
                        <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                    </div>

                    <div class="col-span-2">
                        <x-input-label for="description" :value="__('Mô tả')" icon="fas fa-comment" />
                        <x-textarea id="description" class="block mt-1 w-full" name="description" :value="old('description', $room->description)"
                            :placeholder="__('Nhập mô tả phòng (nếu có)')" />
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="col-span-2 flex items-center justify-end gap-6">
                        <x-secondary-button :href="route('rooms.index')">
                            <i class="fas fa-arrow-left"></i>
                            {{ __('Quay lại') }}
                        </x-secondary-button>

                        <x-primary-button>
                            <i class="fas fa-save"></i>
                            {{ __('Cập nhật') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-image text-blue-600 me-1"></i>
                    {{ __('Hình ảnh phòng') }}
                </h3>

                <form action="{{ route('rooms.storeImages', $room) }}" method="post" enctype="multipart/form-data"
                    class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    @csrf

                    <div>
                        <x-input-label for="images" :value="__('Hình ảnh phòng')" icon="fas fa-image" />
                        <x-file-input id="images" class="block mt-1 w-full" name="images[]" multiple />
                        <x-input-error :messages="$errors->get('images')" class="mt-2" />
                    </div>

                    <div class="flex items-end">
                        <x-primary-button>
                            <i class="fas fa-save"></i>
                            {{ __('Lưu') }}
                        </x-primary-button>
                    </div>

                    <div class="col-span-2 flex flex-wrap gap-6">
                        @foreach ($room->images as $index => $image)
                            <div class="relative group">
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                    alt="Image {{ $index + 1 }}"
                                    class="w-32 h-32 object-cover group-hover:opacity-50 transition-opacity duration-300 ease-out">
                                <x-icon-button :data-delete-url="route('rooms.destroyImage', $image)" icon="fas fa-trash" :title="__('Xoá')"
                                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 !bg-red-500 !text-white !hover:bg-red-600"
                                    x-data=""
                                    x-on:click.prevent="$dispatch('open-modal', 'confirm-deletion')" />
                            </div>
                        @endforeach
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-concierge-bell text-blue-600 me-1"></i>
                    {{ __('Dịch vụ') }}
                </h3>

                <form action="{{ route('rooms.updateServices', $room) }}" method="post"
                    class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    @csrf
                    @method('put')

                    <div class="col-span-2">
                        <x-input-label for="services" :value="__('Dịch vụ')" icon="fas fa-concierge-bell" />
                        <div class="flex flex-wrap gap-6 mt-1">
                            @foreach ($services as $id => $name)
                                <label for="service_{{ $id }}" class="inline-flex items-center">
                                    <input id="service_{{ $id }}" type="checkbox"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                        name="services[]" value="{{ $id }}" @checked(in_array($id, old('services', $room->services->pluck('id')->toArray())))>
                                    <span class="ms-2 text-sm text-gray-600">{{ $name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('services')" class="mt-2" />
                    </div>

                    <div class="col-span-2 flex items-center justify-end gap-6">
                        <x-secondary-button :href="route('rooms.index')">
                            <i class="fas fa-arrow-left"></i>
                            {{ __('Quay lại') }}
                        </x-secondary-button>

                        <x-primary-button>
                            <i class="fas fa-save"></i>
                            {{ __('Cập nhật') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-swimming-pool text-blue-600 me-1"></i>
                    {{ __('Tiện ích') }}
                </h3>

                <form action="{{ route('rooms.updateAmenities', $room) }}" method="post"
                    class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    @csrf
                    @method('put')

                    <div class="col-span-2">
                        <x-input-label for="amenities" :value="__('Tiện ích')" icon="fas fa-swimming-pool" />
                        <div class="flex flex-wrap gap-6 mt-1">
                            @foreach ($amenities as $id => $name)
                                <label for="amenity_{{ $id }}" class="inline-flex items-center">
                                    <input id="amenity_{{ $id }}" type="checkbox"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                        name="amenities[]" value="{{ $id }}" @checked(in_array($id, old('amenities', $room->amenities->pluck('id')->toArray())))>
                                    <span class="ms-2 text-sm text-gray-600">{{ $name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('amenities')" class="mt-2" />
                    </div>

                    <div class="col-span-2 flex items-center justify-end gap-6">
                        <x-secondary-button :href="route('rooms.index')">
                            <i class="fas fa-arrow-left"></i>
                            {{ __('Quay lại') }}
                        </x-secondary-button>

                        <x-primary-button>
                            <i class="fas fa-save"></i>
                            {{ __('Cập nhật') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-delete-modal />
</x-app-layout>
