<x-app-layout>
    <x-slot name="header">
        {{ __('Quản lý chi nhánh') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => url('/')], ['label' => 'Quản lý chi nhánh']]" />

            <div class="flex items-center justify-between mx-6">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-building text-blue-600 me-1"></i>
                        {{ __('Quản lý chi nhánh') }}
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">{{ __('Quản lý tất cả chi nhánh trong hệ thống') }}</p>
                </div>
                <x-secondary-button class="!bg-blue-600 !text-white !hover:bg-blue-700" x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'confirm-creation')">
                    <i class="fas fa-plus"></i>
                    {{ __('Thêm chi nhánh mới') }}
                </x-secondary-button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-blue-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-building text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Tổng chi nhánh') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $totalBranches }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div x-data="{ openRow: null }" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <x-table :title="__('Danh sách chi nhánh')">
                    <x-thead>
                        <x-tr>
                            <x-th>{{ __('STT') }}</x-th>
                            <x-th>
                                <div class="flex items-center gap-6">
                                    {{ __('Tên chi nhánh') }}
                                    <x-sortable-column :options="['name_asc', 'name_desc']" />
                                </div>
                            </x-th>
                            <x-th>{{ __('Địa chỉ') }}</x-th>
                            <x-th>{{ __('Hành động') }}</x-th>
                        </x-tr>
                    </x-thead>
                    <x-tbody>
                        @foreach ($branches as $index => $branch)
                            <x-tr>
                                <x-td>#{{ $branches->firstItem() + $index }}</x-td>
                                <x-td>{{ $branch->name }}</x-td>
                                <x-td>{{ $branch->address ?? 'N/A' }}</x-td>
                                <x-td>
                                    <x-icon-button
                                        @click="openRow = openRow === {{ $index }} ? null : {{ $index }}"
                                        class="!bg-green-500 !text-white !hover:bg-green-600">
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

                                    <x-icon-button :data-create-floor-url="route('floors.store', $branch)" icon="fas fa-plus" :title="__('Thêm')"
                                        class="!bg-blue-500 !text-white !hover:bg-blue-600" x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-floor-creation')" />

                                    <x-icon-button :data-update-url="route('branches.update', $branch)" :data-name-value="$branch->name" :data-address-value="$branch->address"
                                        icon="fas fa-edit" :title="__('Chỉnh sửa')"
                                        class="!bg-yellow-500 !text-white !hover:bg-yellow-600" x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-updation')" />

                                    <x-icon-button :data-delete-url="route('branches.destroy', $branch)" icon="fas fa-trash" :title="__('Xoá')"
                                        class="!bg-red-500 !text-white !hover:bg-red-600" x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-deletion')" />
                                </x-td>
                            </x-tr>

                            <tr x-show="openRow === {{ $index }}">
                                <td colspan="4">
                                    <div class="bg-blue-50 px-12">
                                        <table class="w-full table-auto">
                                            <thead class="bg-blue-100">
                                                <tr>
                                                    <th
                                                        class="px-6 py-3 text-left font-medium text-sm text-gray-600 leading-tight uppercase tracking-wide whitespace-nowrap">
                                                        {{ __('STT') }}</th>
                                                    <th
                                                        class="px-6 py-3 text-left font-medium text-sm text-gray-600 leading-tight uppercase tracking-wide whitespace-nowrap">
                                                        {{ __('Số tầng') }}</th>
                                                    <th
                                                        class="px-6 py-3 text-left font-medium text-sm text-gray-600 leading-tight uppercase tracking-wide whitespace-nowrap">
                                                        {{ __('Loại tầng') }}</th>
                                                    <th
                                                        class="px-6 py-3 text-left font-medium text-sm text-gray-600 leading-tight uppercase tracking-wide whitespace-nowrap">
                                                        {{ __('Hành động') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-blue-150">
                                                @foreach ($branch->floors as $index => $floor)
                                                    <tr
                                                        class="hover:bg-blue-100 transition-colors duration-150 ease-in-out">
                                                        <td class="px-6 py-4 text-gray-800 whitespace-nowrap">
                                                            #{{ $index + 1 }}</td>
                                                        <td class="px-6 py-4 text-gray-800 whitespace-nowrap">
                                                            {{ $floor->floor_number }}</td>
                                                        <td class="px-6 py-4 text-gray-800 whitespace-nowrap">
                                                            {{ $floor->gender_type === 'male' ? 'Nam' : ($floor->gender_type === 'female' ? 'Nữ' : 'Hỗn hợp') }}
                                                        </td>
                                                        <td class="px-6 py-4 text-gray-800 whitespace-nowrap">
                                                            <x-icon-button :data-update-floor-url="route('floors.update', $floor)" :data-floor-number-value="$floor->floor_number"
                                                                :data-gender-type-value="$floor->gender_type" icon="fas fa-edit" :title="__('Chỉnh sửa')"
                                                                class="!bg-yellow-500 !text-white !hover:bg-yellow-600"
                                                                x-data=""
                                                                x-on:click.prevent="$dispatch('open-modal', 'confirm-floor-updation')" />

                                                            <x-icon-button :data-delete-url="route('floors.destroy', $floor)" icon="fas fa-trash"
                                                                :title="__('Xoá')"
                                                                class="!bg-red-500 !text-white !hover:bg-red-600"
                                                                x-data=""
                                                                x-on:click.prevent="$dispatch('open-modal', 'confirm-deletion')" />
                                                        </td>
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

            {{ $branches->links() }}
        </div>
    </div>

    <x-delete-modal />

    <x-modal name="confirm-creation" :show="$errors->branchCreation->isNotEmpty()" focusable>
        <form method="post" action="{{ route('branches.store') }}" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">
                <i class="fas fa-building text-blue-600 me-1"></i>
                {{ __('Thông tin chi nhánh') }}
            </h2>

            <div class="mt-6">
                <x-input-label for="name-creation" :value="__('Tên chi nhánh')" icon="fas fa-user" />
                <x-text-input id="name-creation" class="block mt-1 w-full" type="text" name="name"
                    :value="old('name')" required autofocus autocomplete="name" :placeholder="__('Nhập tên chi nhánh')" />
                <x-input-error :messages="$errors->branchCreation->get('name')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="address-creation" :value="__('Địa chỉ')" icon="fas fa-map-marker-alt" />
                <x-text-input id="address-creation" class="block mt-1 w-full" type="text" name="address"
                    :value="old('address')" autocomplete="address" :placeholder="__('Nhập tên địa chỉ')" />
                <x-input-error :messages="$errors->branchCreation->get('address')" class="mt-2" />
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

    <x-modal name="confirm-floor-creation" :show="$errors->floorCreation->isNotEmpty()" focusable>
        <form id="create-floor-form" method="post" action="{{ session('create_action', '#') }}" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">
                <i class="fas fa-layer-group text-blue-600 me-1"></i>
                {{ __('Thông tin tầng') }}
            </h2>

            <div class="mt-6">
                <x-input-label for="floor_number-creation" :value="__('Số tầng')" icon="fas fa-sort-numeric-up-alt" />
                <x-text-input id="floor_number-creation" class="block mt-1 w-full" type="number" name="floor_number"
                    :value="old('floor_number')" required autofocus autocomplete="floor_number" :placeholder="__('Nhập số tầng')" />
                <x-input-error :messages="$errors->floorCreation->get('floor_number')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="gender_type-creation" :value="__('Loại tầng')" icon="fas fa-venus-mars" />
                <x-select id="gender_type-creation" class="block mt-1 w-full" :options="[
                    'male' => 'Nam',
                    'female' => 'Nữ',
                    'mixed' => 'Hỗn hợp',
                ]" name="gender_type"
                    :selected="old('gender_type')" required :placeholder="__('Chọn loại tầng')" />
                <x-input-error :messages="$errors->floorCreation->get('gender_type')" class="mt-2" />
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

    <x-modal name="confirm-updation" :show="$errors->branchUpdation->isNotEmpty()" focusable>
        <form id="update-form" method="post" action="{{ session('update_action', '#') }}" class="p-6">
            @csrf
            @method('put')

            <h2 class="text-lg font-medium text-gray-900">
                <i class="fas fa-building text-blue-600 me-1"></i>
                {{ __('Thông tin chi nhánh') }}
            </h2>

            <div class="mt-6">
                <x-input-label for="name-updation" :value="__('Tên chi nhánh')" icon="fas fa-user" />
                <x-text-input id="name-updation" class="block mt-1 w-full" type="text" name="name"
                    :value="old('name')" required autofocus autocomplete="name" :placeholder="__('Nhập tên chi nhánh')" />
                <x-input-error :messages="$errors->branchUpdation->get('name')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="address-updation" :value="__('Địa chỉ')" icon="fas fa-map-marker-alt" />
                <x-text-input id="address-updation" class="block mt-1 w-full" type="text" name="address"
                    :value="old('address')" autocomplete="address" :placeholder="__('Nhập tên địa chỉ')" />
                <x-input-error :messages="$errors->branchUpdation->get('address')" class="mt-2" />
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

    <x-modal name="confirm-floor-updation" :show="$errors->floorUpdation->isNotEmpty()" focusable>
        <form id="update-floor-form" method="post" action="{{ session('update_action', '#') }}" class="p-6">
            @csrf
            @method('put')

            <h2 class="text-lg font-medium text-gray-900">
                <i class="fas fa-layer-group text-blue-600 me-1"></i>
                {{ __('Thông tin tầng') }}
            </h2>

            <div class="mt-6">
                <x-input-label for="floor_number-updation" :value="__('Số tầng')" icon="fas fa-sort-numeric-up-alt" />
                <x-text-input id="floor_number-updation" class="block mt-1 w-full" type="number"
                    name="floor_number" :value="old('floor_number')" required autofocus autocomplete="floor_number"
                    :placeholder="__('Nhập số tầng')" />
                <x-input-error :messages="$errors->floorUpdation->get('floor_number')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="gender_type-updation" :value="__('Loại tầng')" icon="fas fa-venus-mars" />
                <x-select id="gender_type-updation" class="block mt-1 w-full" :options="[
                    'male' => 'Nam',
                    'female' => 'Nữ',
                    'mixed' => 'Hỗn hợp',
                ]" name="gender_type"
                    :selected="old('gender_type')" required :placeholder="__('Chọn loại tầng')" />
                <x-input-error :messages="$errors->floorUpdation->get('gender_type')" class="mt-2" />
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
                const createFloorForm = document.getElementById('create-floor-form');

                document.querySelectorAll('[data-create-floor-url]').forEach(btn =>
                    btn.addEventListener('click', () => {
                        createFloorForm.action = btn.dataset.createFloorUrl;
                    })
                );

                const form = document.getElementById('update-form');
                const nameInput = document.getElementById('name-updation');
                const addressInput = document.getElementById('address-updation');

                document.querySelectorAll('[data-update-url]').forEach(btn =>
                    btn.addEventListener('click', () => {
                        form.action = btn.dataset.updateUrl;

                        nameInput.value = btn.dataset.nameValue || '';
                        addressInput.value = btn.dataset.addressValue || '';
                    })
                );

                const updateFloorForm = document.getElementById('update-floor-form');
                const floorNumberInput = document.getElementById('floor_number-updation');
                const genderTypeInput = document.getElementById('gender_type-updation');

                document.querySelectorAll('[data-update-floor-url]').forEach(btn =>
                    btn.addEventListener('click', () => {
                        updateFloorForm.action = btn.dataset.updateFloorUrl;

                        floorNumberInput.value = btn.dataset.floorNumberValue || '';
                        genderTypeInput.value = btn.dataset.genderTypeValue || '';
                    })
                );
            });
        </script>
    @endPushOnce
</x-app-layout>
