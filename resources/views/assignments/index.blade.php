<x-app-layout>
    <x-slot name="header">
        Lịch sử nội trú
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => url('/')],
                ['label' => 'Quản lý sinh viên', 'url' => route('students.index')],
                ['label' => 'Chi tiết sinh viên', 'url' => route('students.show', $user)],
                ['label' => 'Lịch sử nội trú'],
            ]" />

            <div class="mx-6 flex items-center justify-between">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-history text-blue-600"></i>
                        Lịch sử nội trú
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Xem lịch sử nội trú của sinh viên</p>
                </div>
                <x-secondary-button :href="route('students.show', $user)">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại
                </x-secondary-button>
            </div>

            <!-- Thông tin sinh viên -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-800 leading-tight mb-4">
                    <i class="fas fa-user-graduate text-blue-600"></i>
                    Thông tin sinh viên
                </h3>

                <div class="flex flex-col md:flex-row gap-6">
                    <div class="flex-shrink-0">
                        @if ($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar"
                                class="w-32 h-32 rounded-lg object-cover shadow-sm">
                        @else
                            <div
                                class="w-32 h-32 rounded-lg bg-blue-500 flex items-center justify-center font-bold text-2xl text-white">
                                {{ mb_substr($user->name, 0, 2, 'UTF-8') }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Mã sinh viên" icon="fas fa-id-card" />
                            <p class="mt-1 text-gray-600">{{ $user->student->student_code ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <x-input-label value="Họ tên" icon="fas fa-user" />
                            <p class="mt-1 text-gray-600">{{ $user->name }}</p>
                        </div>
                        <div>
                            <x-input-label value="Email" icon="fas fa-envelope" />
                            <p class="mt-1 text-gray-600">{{ $user->email }}</p>
                        </div>
                        <div>
                            <x-input-label value="Số điện thoại" icon="fas fa-phone" />
                            <p class="mt-1 text-gray-600">{{ $user->phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lịch sử nội trú -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <x-table title="Lịch sử nội trú">
                    <x-thead>
                        <x-tr>
                            <x-th>STT</x-th>
                            <x-th>Mã phòng</x-th>
                            <x-th>Chi nhánh</x-th>
                            <x-th>Ngày nhận phòng</x-th>
                            <x-th>Ngày trả phòng</x-th>
                            <x-th>Hành động</x-th>
                        </x-tr>
                    </x-thead>
                    <x-tbody>
                        @forelse ($user->assignments as $index => $assignment)
                            <x-tr>
                                <x-td>#{{ $index + 1 }}</x-td>
                                <x-td>{{ $assignment->room->room_code }}</x-td>
                                <x-td>{{ $assignment->room->branch->name }}</x-td>
                                <x-td>
                                    {{ $assignment->checked_in_at ? $assignment->checked_in_at->format('d/m/Y H:i') : 'Chưa nhận phòng' }}
                                </x-td>
                                <x-td>
                                    {{ $assignment->checked_out_at ? $assignment->checked_out_at->format('d/m/Y H:i') : 'Chưa trả phòng' }}
                                </x-td>
                                <x-td>
                                    <x-icon-button :href="route('assignments.show', [$user, $assignment])" title="Chi tiết"
                                        class="bg-blue-500 hover:bg-blue-600 text-white">
                                        <i class="fas fa-eye"></i>
                                    </x-icon-button>
                                </x-td>
                            </x-tr>
                        @empty
                            <x-tr>
                                <x-td colspan="8">
                                    Không có lịch sử nội trú
                                </x-td>
                            </x-tr>
                        @endforelse
                    </x-tbody>
                </x-table>
            </div>
        </div>
    </div>
</x-app-layout>
