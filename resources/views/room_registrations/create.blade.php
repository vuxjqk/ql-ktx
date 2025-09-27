<x-app-layout>
    <x-slot name="header">
        Đăng ký phòng
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => url('/')], ['label' => 'Đăng ký phòng']]" />

            <div class="mx-6 flex items-center justify-between">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-bed text-blue-600"></i>
                        Đăng ký phòng
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Tiến hành đăng ký phòng nội trú</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    Thông tin sinh viên
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div class="row-span-3 flex items-center justify-center">
                        @if ($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar"
                                class="w-24 h-24 rounded-full object-cover">
                        @else
                            <div
                                class="w-24 h-24 rounded-full bg-blue-500 flex items-center justify-center font-bold text-4xl text-white">
                                {{ mb_substr($user->name, 0, 2, 'UTF-8') }}
                            </div>
                        @endif
                    </div>

                    <div>
                        <p class="text-blue-800 leading-tight">
                            <i class="fas fa-user"></i>
                            Tên:
                            <span class="font-semibold">{{ $user->name }}</span>
                        </p>
                    </div>

                    <div>
                        <p class="text-blue-800 leading-tight">
                            <i class="fas fa-id-card"></i>
                            MSSV:
                            <span class="font-semibold">{{ $user->student->student_code ?? 'N/A' }}</span>
                        </p>
                    </div>

                    <div>
                        <p class="text-blue-800 leading-tight">
                            <i class="fas fa-birthday-cake"></i>
                            Ngày sinh:
                            <span class="font-semibold">{{ $user->date_of_birth ?? 'N/A' }}</span>
                        </p>
                    </div>

                    <div>
                        <p class="text-blue-800 leading-tight">
                            <i class="fas fa-graduation-cap"></i>
                            Ngành:
                            <span class="font-semibold">{{ $user->major ?? 'N/A' }}</span>
                        </p>
                    </div>

                    <div>
                        <p class="text-blue-800 leading-tight">
                            <i class="fas fa-venus-mars"></i>
                            Giới tính:
                            <span class="font-semibold">
                                @if ($user->gender == 'male')
                                    Nam
                                @elseif ($user->gender == 'female')
                                    Nữ
                                @else
                                    N/A
                                @endif
                            </span>
                        </p>
                    </div>

                    <div>
                        <p class="text-blue-800 leading-tight">
                            <i class="fas fa-users"></i>
                            Lớp:
                            <span class="font-semibold">{{ $user->class ?? 'N/A' }}</span>
                        </p>
                    </div>
                </div>
            </div>

            @if ($registration)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-bed text-blue-600"></i>
                        Phòng đã đăng ký
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div class="row-span-2 flex items-center justify-center border-2 border-blue-800">
                            <div class="flex items-center justify-center font-bold text-xl text-blue-800">
                                @if ($registration->status == 'pending')
                                    Đang chờ xử lý
                                @elseif ($registration->status == 'approved')
                                    Đã phê duyệt
                                @else
                                    Đã từ chối
                                @endif
                            </div>
                        </div>

                        <div>
                            <p class="text-blue-800 leading-tight">
                                <i class="fas fa-key"></i>
                                Mã phòng:
                                <span class="font-semibold">{{ $registration->room->room_code }}</span>
                            </p>
                        </div>

                        <div>
                            <p class="text-blue-800 leading-tight">
                                <i class="fas fa-users"></i>
                                Sức chứa:
                                <span class="font-semibold">{{ $registration->room->capacity }}</span>
                            </p>
                        </div>

                        <div>
                            <p class="text-blue-800 leading-tight">
                                <i class="fas fa-venus-mars"></i>
                                Loại phòng:
                                <span class="font-semibold">
                                    @if ($registration->room->gender_type == 'male')
                                        Nam
                                    @elseif ($registration->room->gender_type == 'female')
                                        Nữ
                                    @else
                                        Hỗn hợp
                                    @endif
                                </span>
                            </p>
                        </div>

                        <div>
                            <p class="text-blue-800 leading-tight">
                                <i class="fas fa-user-friends"></i>
                                Số người hiện tại:
                                <span class="font-semibold">{{ $registration->room->current_occupancy }}</span>
                            </p>
                        </div>

                        <div class="flex items-center justify-center">
                            @if ($registration->status !== 'approved')
                                <x-danger-button :data-delete-url="route('room_registrations.destroy', $registration)" x-data=""
                                    x-on:click.prevent="$dispatch('open-modal', 'confirm-deletion')">
                                    Xoá
                                </x-danger-button>
                            @else
                                @php
                                    $assignment = $registration->assignment;
                                @endphp

                                @if ($assignment->checked_in_at)
                                    @php
                                        $bill = $assignment->bills()->orderBy('created_at', 'asc')->first();
                                    @endphp

                                    @if ($bill->status === 'paid')
                                        <span
                                            class="bg-green-700 text-white px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs uppercase tracking-widest shadow-sm">
                                            Đã thanh toán
                                        </span>
                                    @else
                                        <x-secondary-button :href="route('vnpay.redirect', $bill)"
                                            class="bg-green-600 hover:bg-green-700 text-white">
                                            Thanh toán
                                        </x-secondary-button>
                                    @endif
                                @else
                                    <x-secondary-button :href="route('assignments.edit', $assignment)"
                                        class="bg-blue-600 hover:bg-blue-700 text-white">
                                        Tiếp tục đến hợp đồng
                                    </x-secondary-button>
                                @endif
                            @endif
                        </div>

                        <div>
                            <p class="text-blue-800 leading-tight">
                                <i class="fas fa-money-bill"></i>
                                Giá mỗi tháng:
                                <span class="font-semibold">
                                    {{ number_format($registration->room->price_per_month, 0, ',', '.') }} VNĐ
                                </span>
                            </p>
                        </div>

                        <div></div>

                        <div>
                            <p class="text-blue-800 leading-tight">
                                <i class="fas fa-calendar-check"></i>
                                Ngày xử lý:
                                <span class="font-semibold">
                                    {{ $registration->processed_at ? $registration->processed_at->format('d/m/Y H:i') : 'Chưa xử lý' }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <p class="text-blue-800 leading-tight">
                                <i class="fas fa-user-check"></i>
                                Người xử lý:
                                <span class="font-semibold">
                                    {{ $registration->processed_by ? $registration->processor->name : 'Chưa có' }}
                                </span>
                            </p>
                        </div>

                        <div class="md:col-span-3">
                            <p class="text-blue-800 leading-tight">
                                <i class="fas fa-align-left"></i>
                                Ghi chú:
                                <span class="font-semibold">{{ $registration->notes ?? 'Không có ghi chú' }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-filter text-blue-600"></i>
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
                    <div>
                        <x-input-label for="is_active" value="Trạng thái" icon="fas fa-power-off" />
                        <x-select id="is_active" class="block mt-1 w-full" :options="[
                            1 => 'Đang hoạt động',
                            0 => 'Đang bảo trì',
                        ]" name="is_active"
                            :selected="request('is_active')" placeholder="Chọn trạng thái" />
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
                <x-table title="Chọn phòng đăng ký">
                    <x-thead>
                        <x-tr>
                            <x-th>STT</x-th>
                            <x-th>Mã phòng</x-th>
                            <x-th>Loại phòng</x-th>
                            <x-th>Giá mỗi tháng</x-th>
                            <x-th>Sức chứa</x-th>
                            <x-th>Số người hiện tại</x-th>
                        </x-tr>
                    </x-thead>
                    <x-tbody>
                        @foreach ($rooms as $index => $room)
                            <x-tr :data-room-id="$room->id" class="cursor-pointer" x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'confirm-registration')">
                                <x-td>#{{ $loop->iteration }}</x-td>
                                <x-td>{{ $room->room_code }}</x-td>
                                <x-td>
                                    @if ($room->gender_type == 'male')
                                        Nam
                                    @elseif ($room->gender_type == 'female')
                                        Nữ
                                    @else
                                        Hỗn hợp
                                    @endif
                                </x-td>
                                <x-td>{{ number_format($room->price_per_month, 0, ',', '.') }} VNĐ</x-td>
                                <x-td>{{ $room->capacity }}</x-td>
                                <x-td>{{ $room->current_occupancy }}</x-td>
                            </x-tr>
                            <x-tr>
                                <x-td colspan="6">Mô tả: {{ $room->description ?? 'N/A' }}</x-td>
                            </x-tr>
                        @endforeach
                    </x-tbody>
                </x-table>
            </div>

            {{ $rooms->links() }}
        </div>
    </div>

    <x-delete-modal />

    <x-modal name="confirm-registration" focusable>
        <form method="post" action="{{ route('room_registrations.store') }}" class="p-6">
            @csrf

            <input id="room_id" type="hidden" name="room_id">

            <h2 class="text-lg font-medium text-gray-900">
                Bạn có chắc chắn muốn đăng ký
                <span class="font-semibold text-green-500">phòng</span>
                này không?
            </h2>

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
                const room_id = document.getElementById('room_id');

                document.querySelectorAll('[data-room-id]').forEach(button => {
                    button.addEventListener('click', () => {
                        room_id.value = button.getAttribute('data-room-id');
                    });
                });
            });
        </script>
    @endPushOnce
</x-app-layout>
