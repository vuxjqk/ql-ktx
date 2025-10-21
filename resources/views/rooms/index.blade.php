<x-app-layout>
    <x-slot name="header">
        {{ __('Quản lý phòng') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => url('/')], ['label' => 'Quản lý phòng']]" />

            <div class="flex items-center justify-between mx-6">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-door-open text-blue-600 me-1"></i>
                        {{ __('Quản lý phòng') }}
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">{{ __('Quản lý tất cả phòng trong hệ thống') }}</p>
                </div>
                <x-secondary-button :href="route('rooms.create')" class="!bg-blue-600 !text-white hover:!bg-blue-700">
                    <i class="fas fa-plus"></i>
                    {{ __('Thêm phòng mới') }}
                </x-secondary-button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-blue-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-door-open text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Tổng số phòng') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $totalRooms }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-green-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Phòng trống') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $emptyRooms }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-yellow-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-exclamation-circle text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Phòng chưa đầy') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $missingRooms }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-red-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-times-circle text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Phòng đầy') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $fullRooms }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div x-data="roomFilter('{{ url('/floors-by-branch') }}')" x-init="init()"
                class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-filter text-blue-600 me-1"></i>
                    {{ __('Tìm kiếm phòng') }}
                </h3>

                <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
                    <div>
                        <x-input-label for="room_code" :value="__('Mã phòng')" icon="fas fa-tag" />
                        <x-text-input id="room_code" class="block mt-1 w-full" type="search" name="room_code"
                            :value="request('room_code')" autocomplete="room_code" :placeholder="__('Mã phòng...')" />
                    </div>

                    <div>
                        <x-input-label for="branch_id" :value="__('Chi nhánh')" icon="fas fa-building" />
                        <x-select x-model="branchId" @change="loadFloors" id="branch_id" class="block mt-1 w-full"
                            :options="$branches" name="branch_id" :placeholder="__('Chọn chi nhánh')" />
                    </div>

                    <div>
                        <x-input-label for="floor_id" :value="__('Tầng')" icon="fas fa-layer-group" />
                        <select id="floor_id"
                            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            name="floor_id">
                            <option value="">{{ __('Chọn tầng') }}</option>
                            <template x-for="floor in floors" :key="floor.id">
                                <option :value="floor.id" x-text="'Tầng ' + floor.floor_number"
                                    @selected(request('floor_id'))></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="gender_type" :value="__('Loại tầng')" icon="fas fa-venus-mars" />
                        <x-select id="gender_type" class="block mt-1 w-full" :options="[
                            'male' => 'Nam',
                            'female' => 'Nữ',
                            'mixed' => 'Hỗn hợp',
                        ]" name="gender_type"
                            :selected="request('gender_type')" :placeholder="__('Chọn loại tầng')" />
                    </div>

                    <div>
                        <x-input-label for="is_active" :value="__('Trạng thái')" icon="fas fa-toggle-on" />
                        <x-select id="is_active" class="block mt-1 w-full" :options="[
                            '1' => 'Hoạt động',
                            '0' => 'Không hoạt động',
                        ]" name="is_active"
                            :selected="request('is_active')" :placeholder="__('Chọn trạng thái')" />
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
                <x-table :title="__('Danh sách phòng')">
                    <x-thead>
                        <x-tr>
                            <x-th>{{ __('STT') }}</x-th>
                            <x-th>
                                <div class="flex items-center gap-6">
                                    {{ __('Phòng') }}
                                    <x-sortable-column :options="['room_code_asc', 'room_code_desc']" />
                                </div>
                            </x-th>
                            <x-th>{{ __('Loại tầng') }}</x-th>
                            <x-th>{{ __('Giá') }}</x-th>
                            <x-th>
                                <div class="flex items-center gap-6">
                                    {{ __('Sức chứa') }}
                                    <x-sortable-column :options="['capacity_asc', 'capacity_desc']" />
                                </div>
                            </x-th>
                            <x-th>
                                <div class="flex items-center gap-6">
                                    {{ __('Số người hiện tại') }}
                                    <x-sortable-column :options="['current_occupancy_asc', 'current_occupancy_desc']" />
                                </div>
                            </x-th>
                            <x-th>{{ __('Trạng thái') }}</x-th>
                            <x-th>{{ __('Hành động') }}</x-th>
                        </x-tr>
                    </x-thead>
                    <x-tbody>
                        @foreach ($rooms as $index => $room)
                            <x-tr>
                                <x-td>#{{ $rooms->firstItem() + $index }}</x-td>
                                <x-td>
                                    <div class="grid">
                                        <span class="font-semibold">{{ $room->room_code }}</span>
                                        <span class="text-sm">
                                            {{ __('Tầng: ') . $room->floor->floor_number }}
                                        </span>
                                        <span class="text-sm">
                                            {{ __('Chi nhánh: ') . $room->floor->branch->name }}
                                        </span>
                                    </div>
                                </x-td>
                                <x-td>
                                    {{ $room->floor->gender_type === 'male' ? 'Nam' : ($room->floor->gender_type === 'female' ? 'Nữ' : 'Hỗn hợp') }}
                                </x-td>
                                <x-td>
                                    <div class="grid">
                                        <span class="text-green-600">
                                            {{ __('Theo ngày: ') . number_format($room->price_per_day, 0, ',', '.') }}
                                            VND
                                        </span>
                                        <span class="text-blue-600">
                                            {{ __('Theo tháng: ') . number_format($room->price_per_month, 0, ',', '.') }}
                                            VND
                                        </span>
                                    </div>
                                </x-td>
                                <x-td>{{ $room->capacity }}</x-td>
                                <x-td>
                                    <div class="flex items-center gap-2">
                                        {{ $room->current_occupancy }}

                                        <a class="text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                            href="{{ route('service-usages.edit', $room) }}">
                                            {{ __('(Ghi nhận sử dụng dịch vụ)') }}
                                        </a>
                                    </div>
                                </x-td>
                                <x-td>
                                    @if ($room->is_active)
                                        <span class="text-green-600">{{ __('Hoạt động') }}</span>
                                    @else
                                        <span class="text-red-600">{{ __('Không hoạt động') }}</span>
                                    @endif
                                </x-td>
                                <x-td>
                                    <x-icon-button :href="route('rooms.show', $room)" icon="fas fa-eye" :title="__('Xem chi tiết')"
                                        class="!bg-blue-500 !text-white hover:!bg-blue-600" />

                                    <x-icon-button :href="route('rooms.edit', $room)" icon="fas fa-edit" :title="__('Chỉnh sửa')"
                                        class="!bg-yellow-500 !text-white hover:!bg-yellow-600" />

                                    <x-icon-button :data-delete-url="route('rooms.destroy', $room)" icon="fas fa-trash" :title="__('Xoá')"
                                        class="!bg-red-500 !text-white hover:!bg-red-600" x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-deletion')" />
                                </x-td>
                            </x-tr>
                        @endforeach
                    </x-tbody>
                </x-table>
            </div>

            {{ $rooms->links() }}
        </div>
    </div>

    <x-delete-modal />

    @pushOnce('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                function roomFilter(baseUrl) {
                    return {
                        branchId: '',
                        floors: [],
                        init() {
                            this.branchId = '{{ request('branch_id') ?? '' }}';
                            if (this.branchId) {
                                this.loadFloors();
                            }
                        },
                        loadFloors() {
                            if (!this.branchId) {
                                this.floors = [];
                                return;
                            }

                            fetch(`${baseUrl}/${this.branchId}`)
                                .then(res => res.json())
                                .then(data => {
                                    this.floors = Object.entries(data).map(([id, floor_number]) => ({
                                        id: id,
                                        floor_number: floor_number
                                    }));
                                })
                                .catch(err => console.error('Lỗi khi load tầng:', err));
                        }
                    }
                }
            });
        </script>
    @endPushOnce
</x-app-layout>
