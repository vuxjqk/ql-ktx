<x-app-layout>
    <x-slot name="header">
        Quản lý phân phòng
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => url('/')], ['label' => 'Quản lý phân phòng']]" />

            <div class="mx-6 flex items-center justify-between">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-home text-blue-800"></i>
                        Quản lý phân phòng
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Quản lý tất cả phân phòng trong hệ thống</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center">
                    <div class="flex items-center space-x-6">
                        <div class="shadow-sm rounded-lg bg-blue-100 p-3">
                            <i class="fas fa-home text-blue-800 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tổng phân phòng</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $totalAssignments }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <x-table title="Danh sách phân phòng">
                    <x-thead>
                        <x-tr>
                            <x-th>STT</x-th>
                            <x-th>MSSV</x-th>
                            <x-th>Sinh viên</x-th>
                            <x-th>Mã phòng</x-th>
                            <x-th>Ngày nhận phòng</x-th>
                            <x-th>Ngày trả phòng</x-th>
                            <x-th>Mã đăng ký</x-th>
                            <x-th>Hành động</x-th>
                        </x-tr>
                    </x-thead>
                    <x-tbody>
                        @foreach ($assignments as $index => $assignment)
                            <x-tr>
                                <x-td>#{{ $assignments->firstItem() + $index }}</x-td>
                                <x-td>{{ $assignment->user->student->student_code ?? 'N/A' }}</x-td>
                                <x-td>
                                    <div class="flex items-center gap-2">
                                        @if ($assignment->user->avatar)
                                            <img src="{{ asset('storage/' . $assignment->user->avatar) }}"
                                                alt="Avatar" class="w-8 h-8 rounded-full object-cover">
                                        @else
                                            <div
                                                class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center font-bold text-xs text-white">
                                                {{ substr($assignment->user->name, 0, 2) }}
                                            </div>
                                        @endif
                                        {{ $assignment->user->name }}
                                    </div>
                                </x-td>
                                <x-td>{{ $assignment->room->room_code }}</x-td>
                                <x-td>{{ $assignment->checked_in_at ? $assignment->checked_in_at->format('d/m/Y H:i') : 'Chưa nhận phòng' }}</x-td>
                                <x-td>{{ $assignment->checked_out_at ? $assignment->checked_out_at->format('d/m/Y H:i') : 'Chưa trả phòng' }}</x-td>
                                <x-td>
                                    @if ($assignment->registration_id)
                                        <a href="{{ route('room_registrations.show', $assignment->registration) }}"
                                            class="underline text-sm text-blue-600 hover:text-blue-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            #{{ $assignment->registration_id }}
                                        </a>
                                    @else
                                        <span class="text-sm text-gray-600">N/A</span>
                                    @endif
                                </x-td>
                                <x-td>
                                    <x-icon-button :href="route('room_assignments.show', $assignment)" title="Chi tiết"
                                        class="bg-blue-500 hover:bg-blue-600 text-white">
                                        <i class="fas fa-eye"></i>
                                    </x-icon-button>
                                    <x-icon-button :data-delete-url="route('room_assignments.destroy', $assignment)" title="Xoá"
                                        class="bg-red-500 hover:bg-red-600 text-white" x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-deletion')">
                                        <i class="fas fa-trash"></i>
                                    </x-icon-button>
                                </x-td>
                            </x-tr>
                        @endforeach
                    </x-tbody>
                </x-table>
            </div>

            {{ $assignments->links() }}
        </div>
    </div>

    <x-delete-modal />
</x-app-layout>
