<x-app-layout>
    <x-slot name="header">
        Quản lý phòng
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => url('/')], ['label' => 'Quản lý phòng']]" />

            <div class="mx-6 flex items-center justify-between">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-door-open text-blue-800"></i>
                        Quản lý phòng
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Quản lý tất cả phòng trong hệ thống</p>
                </div>
                <x-secondary-button :href="route('rooms.create')" class="bg-blue-600 hover:bg-blue-700 text-white">
                    <i class="fas fa-plus"></i>
                    Thêm phòng
                </x-secondary-button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center">
                    <div class="flex items-center space-x-6">
                        <div class="shadow-sm rounded-lg bg-blue-100 p-3">
                            <i class="fas fa-door-open text-blue-800 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tổng số phòng</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $totalRooms }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center">
                    <div class="flex items-center space-x-6">
                        <div class="shadow-sm rounded-lg bg-green-100 p-3">
                            <i class="fas fa-bed text-green-800 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Phòng đầy</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $fullRooms }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center">
                    <div class="flex items-center space-x-6">
                        <div class="shadow-sm rounded-lg bg-red-100 p-3">
                            <i class="fas fa-bed text-red-800 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Phòng trống</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $emptyRooms }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center">
                    <div class="flex items-center space-x-6">
                        <div class="shadow-sm rounded-lg bg-yellow-100 p-3">
                            <i class="fas fa-bed text-yellow-800 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Phòng còn thiếu</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $missingRooms }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-filter text-blue-800"></i>
                    Tìm kiếm phòng
                </h3>

                <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
                    <div>
                        <x-input-label for="room_code" value="Mã phòng" icon="fas fa-key" />
                        <x-text-input id="room_code" class="block mt-1 w-full" type="search" name="room_code"
                            :value="request('room_code')" autocomplete="room_code" placeholder="Mã phòng..." />
                    </div>
                    <div>
                        <x-input-label for="block" value="Khu nhà" icon="fas fa-building" />
                        <x-select id="block" class="block mt-1 w-full" :options="$blocks" name="block"
                            :selected="request('block')" placeholder="Chọn khu nhà" />
                    </div>
                    <div>
                        <x-input-label for="floor" value="Tầng" icon="fas fa-layer-group" />
                        <x-select id="floor" class="block mt-1 w-full" :options="$floors" name="floor"
                            :selected="request('floor')" placeholder="Chọn tầng" />
                    </div>
                    <div>
                        <x-input-label for="gender_type" value="Loại phòng" icon="fas fa-venus-mars" />
                        <x-select id="gender_type" class="block mt-1 w-full" :options="[
                            'male' => 'Nam',
                            'female' => 'Nữ',
                            'mixed' => 'Hỗn hợp',
                        ]" name="gender_type"
                            :selected="request('gender_type')" placeholder="Chọn loại phòng" />
                    </div>
                    <div class="col-span-2">
                        <x-input-label for="branch_id" value="Chi nhánh" icon="fas fa-building" />
                        <x-select id="branch_id" class="block mt-1 w-full" :options="$branches" name="branch_id"
                            :selected="request('branch_id')" placeholder="Chọn chi nhánh" />
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
                <x-table title="Danh sách phòng">
                    <x-thead>
                        <x-tr>
                            <x-th>STT</x-th>
                            <x-th>Mã phòng</x-th>
                            <x-th>Chi nhánh</x-th>
                            <x-th>Loại phòng</x-th>
                            <x-th>
                                <div class="flex items-center gap-6">
                                    <span>Sức chứa</span>
                                    <x-sortable-column :options="['capacity_asc', 'capacity_desc']" />
                                </div>
                            </x-th>
                            <x-th>
                                <div class="flex items-center gap-6">
                                    <span>Số người hiện tại</span>
                                    <x-sortable-column :options="['current_occupancy_asc', 'current_occupancy_desc']" />
                                </div>
                            </x-th>
                            <x-th>Hành động</x-th>
                        </x-tr>
                    </x-thead>
                    <x-tbody>
                        @foreach ($rooms as $index => $room)
                            <x-tr>
                                <x-td>#{{ $rooms->firstItem() + $index }}</x-td>
                                <x-td>{{ $room->room_code }}</x-td>
                                <x-td>{{ $room->branch->name }}</x-td>
                                <x-td>
                                    @if ($room->gender_type == 'male')
                                        Nam
                                    @elseif ($room->gender_type == 'female')
                                        Nữ
                                    @else
                                        Hỗn hợp
                                    @endif
                                </x-td>
                                <x-td>{{ $room->capacity }}</x-td>
                                <x-td>{{ $room->current_occupancy }}</x-td>
                                <x-td>
                                    <x-icon-button :href="route('rooms.edit', $room)" title="Chỉnh sửa"
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white">
                                        <i class="fas fa-edit"></i>
                                    </x-icon-button>
                                    <x-icon-button :data-delete-url="route('rooms.destroy', $room)" title="Xoá"
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

            {{ $rooms->links() }}
        </div>
    </div>

    <x-delete-modal />
</x-app-layout>
