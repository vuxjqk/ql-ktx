<x-app-layout>
    <x-slot name="header">
        {{ __('Quản lý tiện ích') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => url('/')], ['label' => 'Quản lý tiện ích']]" />

            <div class="flex items-center justify-between mx-6">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-swimming-pool text-blue-600 me-1"></i>
                        {{ __('Quản lý tiện ích') }}
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">{{ __('Quản lý tất cả tiện ích trong hệ thống') }}</p>
                </div>
                <x-secondary-button class="!bg-blue-600 !text-white hover:!bg-blue-700" x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'confirm-creation')">
                    <i class="fas fa-plus"></i>
                    {{ __('Thêm tiện ích mới') }}
                </x-secondary-button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-blue-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-swimming-pool text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Tổng tiện ích') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $totalAmenities }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <x-table :title="__('Danh sách tiện ích')">
                    <x-thead>
                        <x-tr>
                            <x-th>{{ __('STT') }}</x-th>
                            <x-th>
                                <div class="flex items-center gap-6">
                                    {{ __('Tên tiện ích') }}
                                    <x-sortable-column :options="['name_asc', 'name_desc']" />
                                </div>
                            </x-th>
                            <x-th>{{ __('Mô tả') }}</x-th>
                            <x-th>{{ __('Hành động') }}</x-th>
                        </x-tr>
                    </x-thead>
                    <x-tbody>
                        @foreach ($amenities as $index => $amenity)
                            <x-tr>
                                <x-td>#{{ $amenities->firstItem() + $index }}</x-td>
                                <x-td>{{ $amenity->name }}</x-td>
                                <x-td>{{ Str::limit($amenity->description, 50) ?? 'N/A' }}</x-td>
                                <x-td>
                                    <x-icon-button :data-update-url="route('amenities.update', $amenity)" :data-name-value="$amenity->name" :data-description-value="$amenity->description"
                                        icon="fas fa-edit" :title="__('Chỉnh sửa')"
                                        class="!bg-yellow-500 !text-white hover:!bg-yellow-600" x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-updation')" />

                                    <x-icon-button :data-delete-url="route('amenities.destroy', $amenity)" icon="fas fa-trash" :title="__('Xoá')"
                                        class="!bg-red-500 !text-white hover:!bg-red-600" x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-deletion')" />
                                </x-td>
                            </x-tr>
                        @endforeach
                    </x-tbody>
                </x-table>
            </div>

            {{ $amenities->links() }}
        </div>
    </div>

    <x-delete-modal />

    <x-modal name="confirm-creation" :show="$errors->amenityCreation->isNotEmpty()" focusable>
        <form method="post" action="{{ route('amenities.store') }}" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">
                <i class="fas fa-swimming-pool text-blue-600 me-1"></i>
                {{ __('Thông tin tiện ích') }}
            </h2>

            <div class="mt-6">
                <x-input-label for="name-creation" :value="__('Tên tiện ích')" icon="fas fa-swimming-pool" />
                <x-text-input id="name-creation" class="block mt-1 w-full" type="text" name="name"
                    :value="old('name')" required autofocus autocomplete="name" :placeholder="__('Nhập tên tiện ích')" />
                <x-input-error :messages="$errors->amenityCreation->get('name')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="description-creation" :value="__('Mô tả')" icon="fas fa-info-circle" />
                <x-textarea id="description-creation" class="block mt-1 w-full" name="description" :value="old('description')"
                    :placeholder="__('Nhập mô tả tiện ích (tùy chọn)')" />
                <x-input-error :messages="$errors->amenityCreation->get('description')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Huỷ') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Lưu') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="confirm-updation" :show="$errors->amenityUpdation->isNotEmpty()" focusable>
        <form id="update-form" method="post" action="{{ session('update_action', '#') }}" class="p-6">
            @csrf
            @method('put')

            <h2 class="text-lg font-medium text-gray-900">
                <i class="fas fa-swimming-pool text-blue-600 me-1"></i>
                {{ __('Thông tin tiện ích') }}
            </h2>

            <div class="mt-6">
                <x-input-label for="name-updation" :value="__('Tên tiện ích')" icon="fas fa-swimming-pool" />
                <x-text-input id="name-updation" class="block mt-1 w-full" type="text" name="name"
                    :value="old('name')" required autofocus autocomplete="name" :placeholder="__('Nhập tên tiện ích')" />
                <x-input-error :messages="$errors->amenityUpdation->get('name')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="description-updation" :value="__('Mô tả')" icon="fas fa-info-circle" />
                <x-textarea id="description-updation" class="block mt-1 w-full" name="description" :value="old('description')"
                    :placeholder="__('Nhập mô tả tiện ích (tùy chọn)')" />
                <x-input-error :messages="$errors->amenityUpdation->get('description')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Huỷ') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Cập nhật') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    @pushOnce('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('update-form');
                const nameInput = document.getElementById('name-updation');
                const descriptionInput = document.getElementById('description-updation');

                document.querySelectorAll('[data-update-url]').forEach(btn =>
                    btn.addEventListener('click', () => {
                        form.action = btn.dataset.updateUrl;

                        nameInput.value = btn.dataset.nameValue || '';
                        descriptionInput.value = btn.dataset.descriptionValue || '';
                    })
                );
            });
        </script>
    @endPushOnce
</x-app-layout>
