<x-app-layout>
    <x-slot name="header">
        Quản lý sinh viên
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => url('/')], ['label' => 'Quản lý sinh viên']]" />

            <div class="mx-6 flex items-center justify-between">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-graduation-cap text-blue-800"></i>
                        Quản lý sinh viên
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Quản lý tất cả sinh viên trong hệ thống</p>
                </div>
                <x-secondary-button :href="route('students.create')" class="bg-blue-600 hover:bg-blue-700 text-white">
                    <i class="fas fa-plus"></i>
                    Thêm sinh viên
                </x-secondary-button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center">
                    <div class="flex items-center space-x-6">
                        <div class="shadow-sm rounded-lg bg-blue-100 p-3">
                            <i class="fas fa-graduation-cap text-blue-800 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tổng sinh viên</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $totalStudents }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-filter text-blue-800"></i>
                    Tìm kiếm sinh viên
                </h3>

                <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
                    <div class="col-span-2">
                        <x-input-label for="search" value="Tìm kiếm" icon="fas fa-search" />
                        <x-text-input id="search" class="block mt-1 w-full" type="search" name="search"
                            :value="request('search')" autocomplete="search" placeholder="Tìm kiếm theo tên hoặc email..." />
                    </div>
                    <div>
                        <x-input-label for="student_code" value="MSSV" icon="fas fa-id-card" />
                        <x-text-input id="student_code" class="block mt-1 w-full" type="search" name="student_code"
                            :value="request('student_code')" autocomplete="student_code" placeholder="Tìm kiếm theo MSSV..." />
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
                <x-table title="Danh sách sinh viên">
                    <x-thead>
                        <x-tr>
                            <x-th>STT</x-th>
                            <x-th>MSSV</x-th>
                            <x-th>
                                <div class="flex items-center gap-6">
                                    <span>Tên</span>
                                    <x-sortable-column :options="['a_to_z', 'z_to_a']" />
                                </div>
                            </x-th>
                            <x-th>Lớp</x-th>
                            <x-th>Email</x-th>
                            <x-th>Số điện thoại</x-th>
                            <x-th>Hành động</x-th>
                        </x-tr>
                    </x-thead>
                    <x-tbody>
                        @foreach ($users as $index => $user)
                            <x-tr>
                                <x-td>#{{ $users->firstItem() + $index }}</x-td>
                                <x-td>{{ $user->student->student_code }}</x-td>
                                <x-td>
                                    <div class="flex items-center gap-2">
                                        @if ($user->avatar)
                                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar"
                                                class="w-8 h-8 rounded-full object-cover">
                                        @else
                                            <div
                                                class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center font-bold text-xs text-white">
                                                {{ substr($user->name, 0, 2) }}
                                            </div>
                                        @endif
                                        {{ $user->name }}
                                    </div>
                                </x-td>
                                <x-td>{{ $user->student->class ?? 'N/A' }}</x-td>
                                <x-td>{{ $user->email }}</x-td>
                                <x-td>{{ $user->phone ?? 'N/A' }}</x-td>
                                <x-td>
                                    @if ($user->trashed())
                                        <form action="{{ route('students.restore', $user->id) }}" method="post">
                                            @csrf
                                            <x-icon-button type="submit" title="Khôi phục"
                                                class="bg-green-500 hover:bg-green-600 text-white">
                                                <i class="fas fa-undo-alt"></i>
                                            </x-icon-button>
                                        </form>
                                    @else
                                        <x-icon-button :href="route('students.edit', $user)" title="Chỉnh sửa"
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white">
                                            <i class="fas fa-edit"></i>
                                        </x-icon-button>
                                        <x-icon-button :data-delete-url="route('students.destroy', $user)" title="Xoá"
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
