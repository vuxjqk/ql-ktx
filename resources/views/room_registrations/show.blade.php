<x-app-layout>
    <x-slot name="header">
        Chi tiết đăng ký phòng
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => url('/')],
                ['label' => 'Quản lý đăng ký phòng', 'url' => route('room_registrations.index')],
                ['label' => 'Chi tiết đăng ký phòng'],
            ]" />

            <div class="mx-6 flex items-center justify-between">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-clipboard-list text-blue-800"></i>
                        Chi tiết đăng ký phòng
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Xem thông tin chi tiết về yêu cầu đăng ký phòng</p>
                </div>
                <x-secondary-button :href="route('room_registrations.index')">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại
                </x-secondary-button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-info-circle text-blue-800"></i>
                    Thông tin sinh viên
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div class="row-span-3 flex items-center justify-center">
                        @if ($registration->user->avatar)
                            <img src="{{ asset('storage/' . $registration->user->avatar) }}" alt="Avatar"
                                class="w-24 h-24 rounded-full object-cover">
                        @else
                            <div
                                class="w-24 h-24 rounded-full bg-blue-500 flex items-center justify-center font-bold text-4xl text-white">
                                {{ substr($registration->user->name, 0, 2) }}
                            </div>
                        @endif
                    </div>

                    <div>
                        <p class="text-blue-800 leading-tight">
                            <i class="fas fa-user"></i>
                            Tên:
                            <span class="font-semibold">{{ $registration->user->name }}</span>
                        </p>
                    </div>

                    <div>
                        <p class="text-blue-800 leading-tight">
                            <i class="fas fa-id-card"></i>
                            MSSV:
                            <span class="font-semibold">{{ $registration->user->student->student_code }}</span>
                        </p>
                    </div>

                    <div>
                        <p class="text-blue-800 leading-tight">
                            <i class="fas fa-birthday-cake"></i>
                            Ngày sinh:
                            <span class="font-semibold">{{ $registration->user->date_of_birth ?? 'N/A' }}</span>
                        </p>
                    </div>

                    <div>
                        <p class="text-blue-800 leading-tight">
                            <i class="fas fa-graduation-cap"></i>
                            Ngành:
                            <span class="font-semibold">{{ $registration->user->major ?? 'N/A' }}</span>
                        </p>
                    </div>

                    <div>
                        <p class="text-blue-800 leading-tight">
                            <i class="fas fa-venus-mars"></i>
                            Giới tính:
                            <span class="font-semibold">
                                @if ($registration->user->gender == 'male')
                                    Nam
                                @elseif ($registration->user->gender == 'female')
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
                            <span class="font-semibold">{{ $registration->user->class ?? 'N/A' }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-bed text-blue-800"></i>
                    Phòng đã đăng ký
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
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
                            <i class="fas fa-calendar-alt"></i>
                            Ngày yêu cầu:
                            <span class="font-semibold">{{ $registration->requested_at->format('d/m/Y H:i') }}</span>
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
                            <i class="fas fa-money-bill"></i>
                            Giá mỗi tháng:
                            <span class="font-semibold">
                                {{ number_format($registration->room->price_per_month, 0, ',', '.') }} VNĐ
                            </span>
                        </p>
                    </div>

                    <div>
                        <p class="text-blue-800 leading-tight">
                            <i class="fas fa-info-circle"></i>
                            Trạng thái:
                            <span class="font-semibold">
                                @if ($registration->status == 'pending')
                                    <span class="text-yellow-800">Đang chờ xử lý</span>
                                @elseif ($registration->status == 'approved')
                                    <span class="text-green-800">Đã phê duyệt</span>
                                @else
                                    <span class="text-red-800">Đã từ chối</span>
                                @endif
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

            <div class="flex items-center justify-end gap-6">
                <x-secondary-button :href="route('room_registrations.index')">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại
                </x-secondary-button>

                @if ($registration->status === 'pending')
                    <x-secondary-button :data-update-url="route('room_registrations.update', $registration)" data-status-value="approved" data-status-label="phê duyệt"
                        class="bg-green-500 hover:bg-green-600 text-white" x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'confirm-updation')">
                        <i class="fas fa-check-circle"></i>
                        Duyệt
                    </x-secondary-button>
                    <x-secondary-button :data-update-url="route('room_registrations.update', $registration)" data-status-value="rejected" data-status-label="từ chối"
                        class="bg-red-500 hover:bg-red-600 text-white" x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'confirm-updation')">
                        <i class="fas fa-times-circle"></i>
                        Huỷ
                    </x-secondary-button>
                @endif
            </div>
        </div>
    </div>

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
