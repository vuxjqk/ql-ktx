<x-app-layout>
    <x-slot name="header">
        Quản lý nhân sự
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => url('/')], ['label' => 'Quản lý nhân sự']]" />

            <div class="mx-6 flex items-center justify-between">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-user-tie text-blue-600"></i>
                        Quản lý nhân sự
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Quản lý tất cả nhân sự trong hệ thống</p>
                </div>
                <x-secondary-button :href="route('users.create')" class="bg-blue-600 hover:bg-blue-700 text-white">
                    <i class="fas fa-plus"></i>
                    Thêm nhân sự
                </x-secondary-button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center">
                    <div class="flex items-center space-x-6">
                        <div class="shadow-sm rounded-lg bg-blue-100 p-3">
                            <i class="fas fa-user text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tổng nhân viên</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $totalStaffs }}</p>
                        </div>
                    </div>
                </div>

                @if (isset($totalAdmins))
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center">
                        <div class="flex items-center space-x-6">
                            <div class="shadow-sm rounded-lg bg-green-100 p-3">
                                <i class="fas fa-user-shield text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Tổng quản trị viên</p>
                                <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $totalAdmins }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-filter text-blue-600"></i>
                    Tìm kiếm nhân sự
                </h3>

                <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
                    <div class="col-span-2">
                        <x-input-label for="search" value="Tìm kiếm" icon="fas fa-search" />
                        <x-text-input id="search" class="block mt-1 w-full" type="search" name="search"
                            :value="request('search')" autocomplete="search" placeholder="Tìm kiếm theo tên hoặc email..." />
                    </div>
                    <div>
                        <x-input-label for="role" value="Vai trò" icon="fas fa-user-tag" />
                        <x-select id="role" class="block mt-1 w-full" :options="[
                            'admin' => 'Quản trị viên',
                            'staff' => 'Nhân viên',
                        ]" name="role"
                            :selected="request('role')" placeholder="Chọn vai trò" />
                    </div>
                    <div class="flex items-end">
                        <x-primary-button>
                            <i class="fas fa-search"></i>
                            Tìm kiếm
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <x-table title="Danh sách nhân sự">
                    <x-thead>
                        <x-tr>
                            <x-th>STT</x-th>
                            <x-th>
                                <div class="flex items-center gap-6">
                                    <span>Nhân sự</span>
                                    <x-sortable-column :options="['a_to_z', 'z_to_a']" />
                                </div>
                            </x-th>
                            <x-th>Liên hệ</x-th>
                            <x-th>Vai trò</x-th>
                            <x-th>Hành động</x-th>
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
                                                class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center font-bold text-xs text-white">
                                                {{ mb_substr($user->name, 0, 2, 'UTF-8') }}
                                            </div>
                                        @endif
                                        {{ $user->name }}
                                    </div>
                                </x-td>
                                <x-td>
                                    <div class="flex flex-col text-sm">
                                        <span>Email: {{ $user->email ?? 'N/A' }}</span>
                                        <span>Sđt: {{ $user->phone ?? 'N/A' }}</span>
                                        <span>Địa chỉ: {{ $user->address ?? 'N/A' }}</span>
                                    </div>
                                </x-td>
                                <x-td>{{ $user->role == 'admin' ? 'Quản trị viên' : 'Nhân viên' }}</x-td>
                                <x-td>
                                    @if ($user->trashed())
                                        <form action="{{ route('users.restore', $user->id) }}" method="post">
                                            @csrf
                                            <x-icon-button type="submit" title="Khôi phục"
                                                class="bg-green-500 hover:bg-green-600 text-white">
                                                <i class="fas fa-undo-alt"></i>
                                            </x-icon-button>
                                        </form>
                                    @else
                                        <x-icon-button :href="route('users.edit', $user)" title="Chỉnh sửa"
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white">
                                            <i class="fas fa-edit"></i>
                                        </x-icon-button>
                                        <x-icon-button :data-delete-url="route('users.destroy', $user)" title="Xoá"
                                            class="bg-red-500 hover:bg-red-600 text-white" x-data=""
                                            x-on:click.prevent="$dispatch('open-modal', 'confirm-deletion')">
                                            <i class="fas fa-trash"></i>
                                        </x-icon-button>
                                    @endif
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
