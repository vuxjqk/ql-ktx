<x-app-layout>
    <x-slot name="header">
        {{ __('Quản lý nhân viên') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => url('/')], ['label' => 'Quản lý nhân viên']]" />

            <div class="flex items-center justify-between mx-6">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-user-tie text-blue-600 me-1"></i>
                        {{ __('Quản lý nhân viên') }}
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">{{ __('Quản lý tất cả nhân viên trong hệ thống') }}</p>
                </div>
                <x-secondary-button :href="route('users.create')" class="!bg-blue-600 !text-white !hover:bg-blue-700">
                    <i class="fas fa-plus"></i>
                    {{ __('Thêm nhân viên mới') }}
                </x-secondary-button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-blue-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-user-tie text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Tổng nhân viên') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $totalStaffs }}</p>
                        </div>
                    </div>
                </div>

                @if ($totalAdmins)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                        <div class="flex items-center gap-6">
                            <div class="bg-green-100 shadow-sm sm:rounded-lg p-3">
                                <i class="fas fa-user-shield text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">{{ __('Tổng quản trị viên') }}</p>
                                <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $totalAdmins }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-filter text-blue-600 me-1"></i>
                    {{ __('Tìm kiếm nhân sự') }}
                </h3>

                <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
                    <div class="col-span-2">
                        <x-input-label for="search" :value="__('Tìm kiếm')" icon="fas fa-search" />
                        <x-text-input id="search" class="block mt-1 w-full" type="search" name="search"
                            :value="request('search')" autocomplete="search" :placeholder="__('Tìm kiếm theo tên hoặc email...')" />
                    </div>

                    <div>
                        <x-input-label for="role" :value="__('Vai trò')" icon="fas fa-user-tag" />
                        <x-select id="role" class="block mt-1 w-full" :options="[
                            'admin' => 'Quản trị viên',
                            'staff' => 'Nhân viên',
                        ]" name="role"
                            :selected="request('role')" :placeholder="__('Chọn vai trò')" />
                    </div>

                    <div class="flex items-end">
                        <x-primary-button>
                            <i class="fas fa-search"></i>
                            {{ __('Tìm kiếm') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <x-table :title="__('Danh sách nhân viên')">
                    <x-thead>
                        <x-tr>
                            <x-th>{{ __('STT') }}</x-th>
                            <x-th>
                                <div class="flex items-center gap-6">
                                    {{ __('Họ tên') }}
                                    <x-sortable-column :options="['name_asc', 'name_desc']" />
                                </div>
                            </x-th>
                            <x-th>{{ __('Email') }}</x-th>
                            <x-th>{{ __('Vai trò') }}</x-th>
                            <x-th>{{ __('Hành động') }}</x-th>
                        </x-tr>
                    </x-thead>
                    <x-tbody>
                        @foreach ($users as $index => $user)
                            <x-tr>
                                <x-td>#{{ $users->firstItem() + $index }}</x-td>
                                <x-td>
                                    <div class="flex items-center gap-2">
                                        @if ($user->avatar)
                                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar"
                                                class="w-8 h-8 rounded-full object-cover">
                                        @else
                                            <div
                                                class="w-8 h-8 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center">
                                                {{ mb_substr($user->name, 0, 2, 'UTF-8') }}
                                            </div>
                                        @endif
                                        {{ $user->name }}
                                    </div>
                                </x-td>
                                <x-td>{{ $user->email ?? 'N/A' }}</x-td>
                                <x-td>{{ $user->role === 'admin' ? 'Quản trị viên' : 'Nhân viên' }}</x-td>
                                <x-td>
                                    <x-icon-button :href="route('users.edit', $user)" icon="fas fa-edit" :title="__('Chỉnh sửa')"
                                        class="!bg-yellow-500 !text-white !hover:bg-yellow-600" />

                                    <x-icon-button :data-delete-url="route('users.destroy', $user)" icon="fas fa-trash" :title="__('Xoá')"
                                        class="!bg-red-500 !text-white !hover:bg-red-600" x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-deletion')" />
                                </x-td>
                            </x-tr>
                        @endforeach
                    </x-tbody>
                </x-table>
            </div>

            {{ $users->links() }}
        </div>
    </div>

    <x-delete-modal />
</x-app-layout>
