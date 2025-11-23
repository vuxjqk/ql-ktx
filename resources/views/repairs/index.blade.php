<x-app-layout>
    <x-slot name="header">
        {{ __('Quản lý sửa chữa') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => url('/')], ['label' => 'Quản lý sửa chữa']]" />

            <div class="flex items-center justify-between mx-6">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-tools text-blue-600 me-1"></i>
                        {{ __('Quản lý sửa chữa') }}
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">{{ __('Quản lý tất cả yêu cầu sửa chữa trong hệ thống') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-blue-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-tools text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Tổng yêu cầu') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $totalRepairs }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-yellow-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-hourglass-start text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Chờ xử lý') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $pendingRepairs }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-blue-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-wrench text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Đang xử lý') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $inProgressRepairs }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-green-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Đã hoàn thành') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $completedRepairs }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-filter text-blue-600 me-1"></i>
                    {{ __('Tìm kiếm yêu cầu sửa chữa') }}
                </h3>

                <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
                    <div class="col-span-2">
                        <x-input-label for="search" :value="__('Tìm kiếm')" icon="fas fa-search" />
                        <x-text-input id="search" class="block mt-1 w-full" type="search" name="search"
                            :value="request('search')" autocomplete="search" :placeholder="__('Tìm kiếm theo tên sinh viên hoặc mã phòng...')" />
                    </div>

                    <div>
                        <x-input-label for="status" :value="__('Trạng thái')" icon="fas fa-info-circle" />
                        <x-select id="status" class="block mt-1 w-full" :options="[
                            'pending' => 'Chờ xử lý',
                            'in_progress' => 'Đang xử lý',
                            'completed' => 'Hoàn thành',
                            'cancelled' => 'Đã hủy',
                        ]" name="status"
                            :selected="request('status')" :placeholder="__('Chọn trạng thái')" />
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
                <x-table :title="__('Danh sách yêu cầu sửa chữa')">
                    <x-thead>
                        <x-tr>
                            <x-th>{{ __('STT') }}</x-th>
                            <x-th>{{ __('Người yêu cầu') }}</x-th>
                            <x-th>{{ __('Phòng') }}</x-th>
                            <x-th>{{ __('Mô tả') }}</x-th>
                            <x-th>{{ __('Hình ảnh') }}</x-th>
                            <x-th>{{ __('Trạng thái') }}</x-th>
                        </x-tr>
                    </x-thead>
                    <x-tbody>
                        @foreach ($repairs as $index => $repair)
                            <x-tr>
                                <x-td>#{{ $repairs->firstItem() + $index }}</x-td>
                                <x-td>
                                    <div class="flex items-center gap-2">
                                        @if ($repair->user->avatar)
                                            <img src="{{ asset('storage/' . $repair->user->avatar) }}" alt="Avatar"
                                                class="w-8 h-8 rounded-full object-cover">
                                        @else
                                            <div
                                                class="w-8 h-8 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center">
                                                {{ mb_substr($repair->user->name, 0, 2, 'UTF-8') }}
                                            </div>
                                        @endif
                                        <div class="grid">
                                            <span class="font-semibold">{{ $repair->user->name }}</span>
                                            <span class="text-sm">
                                                {{ __('MSSV: ') . $repair->user->student?->student_code }}
                                            </span>
                                            <span class="text-sm">
                                                {{ __('Giới tính: ') . ($repair->user->student?->gender === 'male' ? 'Nam' : ($repair->user->student?->gender === 'female' ? 'Nữ' : 'Khác')) }}
                                            </span>
                                        </div>
                                    </div>
                                </x-td>
                                <x-td>
                                    <div class="grid">
                                        <span class="font-semibold">{{ $repair->room->room_code }}</span>
                                        <span class="text-sm">
                                            {{ __('Tầng: ') . $repair->room->floor->floor_number }}
                                        </span>
                                        <span class="text-sm">
                                            {{ __('Chi nhánh: ') . $repair->room->floor->branch->name }}
                                        </span>
                                    </div>
                                </x-td>
                                <x-td>{{ Str::limit($repair->description, 50) }}</x-td>
                                <x-td>
                                    @if ($repair->image_path)
                                        <a href="{{ asset('storage/' . $repair->image_path) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $repair->image_path) }}" alt="Repair Image"
                                                class="w-16 h-16 object-cover rounded">
                                        </a>
                                    @else
                                        {{ __('Không có') }}
                                    @endif
                                </x-td>
                                <x-td>
                                    <div class="grid">
                                        @switch($repair->status)
                                            @case('pending')
                                                <span class="text-yellow-600">{{ __('Chờ xử lý') }}</span>
                                            @break

                                            @case('in_progress')
                                                <span class="text-blue-600">{{ __('Đang xử lý') }}</span>
                                            @break

                                            @case('completed')
                                                <span class="text-green-600">{{ __('Hoàn thành') }}</span>
                                            @break

                                            @case('cancelled')
                                                <span class="text-red-600">{{ __('Đã hủy') }}</span>
                                            @break
                                        @endswitch

                                        <div>
                                            <x-icon-button :data-status-update-url="route('repairs.update', $repair)" :data-status-value="$repair->status" title="Cập nhật"
                                                class="!bg-yellow-500 !text-white hover:!bg-yellow-600"
                                                x-data=""
                                                x-on:click.prevent="$dispatch('open-modal', 'confirm-status-updation')">
                                                <i class="fas fa-edit"></i>
                                            </x-icon-button>

                                            @if ($repair->status === 'pending')
                                                <x-icon-button :data-update-url="route('repairs.update', $repair)" data-status-value="cancelled"
                                                    data-status-label="huỷ bỏ" title="Huỷ bỏ"
                                                    class="!bg-red-500 !text-white hover:!bg-red-600"
                                                    x-data=""
                                                    x-on:click.prevent="$dispatch('open-modal', 'confirm-updation')">
                                                    <i class="fas fa-times-circle"></i>
                                                </x-icon-button>
                                            @endif
                                        </div>
                                    </div>
                                </x-td>
                            </x-tr>
                        @endforeach
                    </x-tbody>
                </x-table>
            </div>

            {{ $repairs->links() }}
        </div>
    </div>

    <x-status-update-modal :statuses="collect([
        'pending' => __('Chờ xử lý'),
        'in_progress' => __('Đang xử lý'),
        'completed' => __('Hoàn thành'),
    ])" />

    <x-modal name="confirm-updation" focusable>
        <form id="update-form" method="post" action="#" class="p-6">
            @csrf
            @method('put')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Bạn có chắc chắn muốn huỷ bỏ yêu cầu sửa chữa này không?') }}
            </h2>

            <input type="hidden" name="status" value="cancelled">

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Huỷ') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Xác nhận') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>

    @pushOnce('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('update-form');
                document.querySelectorAll('[data-update-url]').forEach(btn =>
                    btn.addEventListener('click', () => form.action = btn.dataset.updateUrl)
                );
            });
        </script>
    @endPushOnce
</x-app-layout>
