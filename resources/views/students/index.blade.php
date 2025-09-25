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

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center">
                    <div class="flex items-center space-x-6">
                        <div class="shadow-sm rounded-lg bg-yellow-100 p-3">
                            <i class="fas fa-hourglass-half text-yellow-800 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Đang chờ xử lý</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">
                                {{ $statusCounts['pending'] ?? 0 }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center">
                    <div class="flex items-center space-x-6">
                        <div class="shadow-sm rounded-lg bg-green-100 p-3">
                            <i class="fas fa-check-circle text-green-800 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Đã phê duyệt</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">
                                {{ $statusCounts['approved'] ?? 0 }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center">
                    <div class="flex items-center space-x-6">
                        <div class="shadow-sm rounded-lg bg-red-100 p-3">
                            <i class="fas fa-times-circle text-red-800 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Đã từ chối</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">
                                {{ $statusCounts['rejected'] ?? 0 }}
                            </p>
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
                    <div>
                        <x-input-label for="status" value="Trạng thái" icon="fas fa-info-circle" />
                        <x-select id="status" class="block mt-1 w-full" :options="[
                            'pending' => 'Đang chờ xử lý',
                            'approved' => 'Đã phê duyệt',
                            'rejected' => 'Đã từ chối',
                        ]" name="status"
                            :selected="request('status')" placeholder="Chọn trạng thái" />
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
                            <x-th>
                                <div class="flex items-center gap-6">
                                    <span>Sinh viên</span>
                                    <x-sortable-column :options="['a_to_z', 'z_to_a']" />
                                </div>
                            </x-th>
                            <x-th>Liên hệ</x-th>
                            <x-th>Phòng</x-th>
                            <x-th>Hành động</x-th>
                        </x-tr>
                    </x-thead>
                    <x-tbody>
                        @foreach ($users as $index => $user)
                            @php
                                $registration = $user->roomRegistration;
                            @endphp

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
                                                {{ substr($user->name, 0, 2) }}
                                            </div>
                                        @endif
                                        <div class="flex flex-col">
                                            <span class="font-semibold">{{ $user->name }}</span>
                                            <span class="text-sm">MSSV: {{ $user->student->student_code }}</span>
                                            <span class="text-sm">Lớp: {{ $user->student->class ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </x-td>
                                <x-td>
                                    <div class="flex flex-col text-sm">
                                        <span>Email: {{ $user->email ?? 'N/A' }}</span>
                                        <span>Sđt: {{ $user->phone ?? 'N/A' }}</span>
                                        <span>Địa chỉ: {{ $user->address ?? 'N/A' }}</span>
                                    </div>
                                </x-td>
                                <x-td>
                                    <div class="flex flex-col">
                                        @if ($registration)
                                            <span class="font-semibold">
                                                <span class="text-sm">Phòng:</span>
                                                {{ $registration->room->room_code }}
                                            </span>

                                            <span class="text-sm">
                                                <span class="text-xs">Trạng thái:</span>
                                                @if ($registration->status === 'pending')
                                                    <span class="text-yellow-800">Đang chờ xử lý</span>
                                                @elseif ($registration->status === 'approved')
                                                    <span class="text-green-800">Đã phê duyệt</span>
                                                @else
                                                    <span class="text-red-800">Đã từ chối</span>
                                                @endif
                                            </span>

                                            <span class="text-sm">
                                                <span class="text-xs">Ngày yêu cầu:</span>
                                                {{ $registration->requested_at->format('d/m/Y H:i') }}
                                            </span>
                                        @else
                                            <span class="text-sm">Không đăng ký nội trú</span>
                                        @endif
                                    </div>
                                </x-td>
                                <x-td>
                                    <x-icon-button :href="route('students.show', $user)" title="Chi tiết"
                                        class="bg-blue-500 hover:bg-blue-600 text-white">
                                        <i class="fas fa-eye"></i>
                                    </x-icon-button>

                                    @if ($registration)
                                        @if ($registration->status === 'pending')
                                            <x-icon-button :data-update-url="route('room_registrations.update', $registration)" data-status-value="approved"
                                                data-status-label="phê duyệt" title="Phê duyệt"
                                                class="bg-green-500 hover:bg-green-600 text-white"
                                                x-data=""
                                                x-on:click.prevent="$dispatch('open-modal', 'confirm-updation')">
                                                <i class="fas fa-check-circle"></i>
                                            </x-icon-button>
                                            <x-icon-button :data-update-url="route('room_registrations.update', $registration)" data-status-value="rejected"
                                                data-status-label="từ chối" title="Từ chối"
                                                class="bg-red-500 hover:bg-red-600 text-white" x-data=""
                                                x-on:click.prevent="$dispatch('open-modal', 'confirm-updation')">
                                                <i class="fas fa-times-circle"></i>
                                            </x-icon-button>
                                        @endif
                                    @endif

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

    <x-modal name="confirm-updation" :show="$errors->registrationUpdation->isNotEmpty()" focusable>
        <form id="update-form" method="post" action="#" class="p-6">
            @csrf
            @method('put')

            <h2 class="text-lg font-medium text-gray-900">
                Bạn có chắc chắn muốn
                <span id="status-label" class="font-semibold text-blue-500"></span>
                không?
            </h2>

            <input id="status-input" type="hidden" name="status">
            <x-input-error :messages="$errors->registrationUpdation->get('status')" class="mt-2" />

            <div id="notes-container" class="mt-6">
                <x-input-label for="notes" value="Ghi chú" icon="fas fa-align-left" />
                <x-textarea id="notes" class="block mt-1 w-full" name="notes" :value="old('notes')"
                    placeholder="Lý do từ chối (không bắt buộc)" />
                <x-input-error :messages="$errors->registrationUpdation->get('notes')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Huỷ
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    Xác nhận
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    @pushOnce('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('update-form');
                const input = document.getElementById('status-input');
                const label = document.getElementById('status-label');
                const container = document.getElementById('notes-container');

                document.querySelectorAll('[data-update-url]').forEach(button => {
                    button.addEventListener('click', () => {
                        form.action = button.getAttribute('data-update-url');
                        const status = button.getAttribute('data-status-value');
                        input.value = status;
                        label.textContent = button.getAttribute('data-status-label');
                        if (status === 'approved') {
                            container.classList.add('hidden');
                        } else {
                            container.classList.remove('hidden');
                        }
                    });
                });
            });
        </script>
    @endPushOnce
</x-app-layout>
