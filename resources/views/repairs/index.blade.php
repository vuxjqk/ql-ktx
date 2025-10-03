<x-app-layout>
    <x-slot name="header">
        Lịch sử báo cáo sửa chữa
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => url('/')], ['label' => 'Lịch sử báo cáo sửa chữa']]" />

            <div class="mx-6 flex items-center justify-between">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-history text-blue-600"></i>
                        Lịch sử báo cáo sửa chữa
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Xem và quản lý các yêu cầu sửa chữa</p>
                </div>
                @can('is-student')
                    <x-primary-button :href="route('repairs.create')">
                        <i class="fas fa-plus"></i>
                        Tạo yêu cầu mới
                    </x-primary-button>
                @endcan
            </div>

            <!-- Bộ lọc -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="get" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="status" value="Trạng thái" icon="fas fa-filter" />
                        <x-select id="status" class="block mt-1 w-full" name="status" :options="[
                            '' => 'Tất cả',
                            'open' => 'Mở',
                            'in_progress' => 'Đang xử lý',
                            'resolved' => 'Đã giải quyết',
                            'closed' => 'Đã đóng',
                        ]"
                            :selected="request('status')" />
                    </div>
                    <div>
                        <x-input-label for="type" value="Loại sửa chữa" icon="fas fa-wrench" />
                        <x-select id="type" class="block mt-1 w-full" name="type" :options="[
                            '' => 'Tất cả',
                            'electric' => 'Điện',
                            'water' => 'Nước',
                            'furniture' => 'Đồ nội thất',
                            'other' => 'Khác',
                        ]"
                            :selected="request('type')" />
                    </div>
                    <div class="flex items-end">
                        <x-primary-button type="submit">
                            <i class="fas fa-search"></i>
                            Lọc
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Danh sách yêu cầu sửa chữa -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-800 leading-tight mb-4">
                    <i class="fas fa-list text-blue-600"></i>
                    Danh sách yêu cầu sửa chữa
                </h3>

                @if ($repairs->isEmpty())
                    <p class="text-gray-600">Không có yêu cầu sửa chữa nào.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Phòng</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Loại</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Mô tả</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Trạng thái</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ngày báo</th>
                                    @cannot('is-student')
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Phân công</th>
                                    @endcannot
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($repairs as $repair)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $repair->room->room_code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ [
                                                'electric' => 'Điện',
                                                'water' => 'Nước',
                                                'furniture' => 'Đồ nội thất',
                                                'other' => 'Khác',
                                            ][$repair->type] }}
                                        </td>
                                        <td class="px-6 py-4">{{ Str::limit($repair->description, 50) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex px-2 text-xs leading-5 font-semibold rounded-full {{ [
                                                    'open' => 'bg-yellow-100 text-yellow-800',
                                                    'in_progress' => 'bg-blue-100 text-blue-800',
                                                    'resolved' => 'bg-green-100 text-green-800',
                                                    'closed' => 'bg-gray-100 text-gray-800',
                                                ][$repair->status] }}">
                                                {{ [
                                                    'open' => 'Mở',
                                                    'in_progress' => 'Đang xử lý',
                                                    'resolved' => 'Đã giải quyết',
                                                    'closed' => 'Đã đóng',
                                                ][$repair->status] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $repair->reported_at->format('d/m/Y H:i') }}</td>
                                        @cannot('is-student')
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $repair->assignedTo ? $repair->assignedTo->name : 'Chưa phân công' }}
                                            </td>
                                        @endcannot
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            {{-- <a href="{{ route('repairs.show', $repair) }}"
                                                class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i>
                                            </a> --}}
                                            @if ($repair->status === 'open')
                                                @can('is-student')
                                                    <a href="{{ route('repairs.edit', $repair) }}"
                                                        class="ml-4 text-green-600 hover:text-green-900">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('repairs.destroy', $repair) }}" method="post"
                                                        class="inline-block ml-4">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                                            onclick="return confirm('Bạn có chắc muốn xóa yêu cầu này?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                                @cannot('is-student')
                                                    <a href="{{ route('repairs.edit', $repair) }}"
                                                        class="ml-4 text-green-600 hover:text-green-900">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcannot
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $repairs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
