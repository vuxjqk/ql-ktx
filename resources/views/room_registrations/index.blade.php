<x-app-layout>
    <x-slot name="header">
        Quản lý đăng ký phòng
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => url('/')], ['label' => 'Quản lý đăng ký phòng']]" />

            <div class="mx-6 flex items-center justify-between">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-clipboard-list text-blue-800"></i>
                        Quản lý đăng ký phòng
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Quản lý tất cả yêu cầu đăng ký phòng trong hệ thống</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center">
                    <div class="flex items-center space-x-6">
                        <div class="shadow-sm rounded-lg bg-blue-100 p-3">
                            <i class="fas fa-clipboard-list text-blue-800 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tổng đăng ký</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $totalRegistrations }}
                            </p>
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
                    Tìm kiếm đăng ký
                </h3>

                <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
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
                <x-table title="Danh sách đăng ký phòng">
                    <x-thead>
                        <x-tr>
                            <x-th>STT</x-th>
                            <x-th>MSSV</x-th>
                            <x-th>Sinh viên</x-th>
                            <x-th>Mã phòng</x-th>
                            <x-th>Trạng thái</x-th>
                            <x-th>Ngày yêu cầu</x-th>
                            <x-th>Hành động</x-th>
                        </x-tr>
                    </x-thead>
                    <x-tbody>
                        @foreach ($registrations as $index => $registration)
                            <x-tr>
                                <x-td>#{{ $registrations->firstItem() + $index }}</x-td>
                                <x-td>{{ $registration->user->student->student_code ?? 'N/A' }}</x-td>
                                <x-td>
                                    <div class="flex items-center gap-2">
                                        @if ($registration->user->avatar)
                                            <img src="{{ asset('storage/' . $registration->user->avatar) }}"
                                                alt="Avatar" class="w-8 h-8 rounded-full object-cover">
                                        @else
                                            <div
                                                class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center font-bold text-xs text-white">
                                                {{ substr($registration->user->name, 0, 2) }}
                                            </div>
                                        @endif
                                        {{ $registration->user->name }}
                                    </div>
                                </x-td>
                                <x-td>{{ $registration->room->room_code }}</x-td>
                                <x-td>
                                    @if ($registration->status === 'pending')
                                        <span class="text-yellow-800">Đang chờ xử lý</span>
                                    @elseif ($registration->status === 'approved')
                                        <span class="text-green-800">Đã phê duyệt</span>
                                    @else
                                        <span class="text-red-800">Đã từ chối</span>
                                    @endif
                                </x-td>
                                <x-td>{{ $registration->requested_at->format('d/m/Y H:i') }}</x-td>
                                <x-td>
                                    <x-icon-button :href="route('room_registrations.show', $registration)" title="Chi tiết"
                                        class="bg-blue-500 hover:bg-blue-600 text-white">
                                        <i class="fas fa-eye"></i>
                                    </x-icon-button>
                                    @if ($registration->status === 'pending')
                                        <x-icon-button :data-update-url="route('room_registrations.update', $registration)" data-status-value="approved"
                                            data-status-label="phê duyệt" title="Duyệt"
                                            class="bg-green-500 hover:bg-green-600 text-white" x-data=""
                                            x-on:click.prevent="$dispatch('open-modal', 'confirm-updation')">
                                            <i class="fas fa-check-circle"></i>
                                        </x-icon-button>
                                        <x-icon-button :data-update-url="route('room_registrations.update', $registration)" data-status-value="rejected"
                                            data-status-label="từ chối" title="Huỷ"
                                            class="bg-red-500 hover:bg-red-600 text-white" x-data=""
                                            x-on:click.prevent="$dispatch('open-modal', 'confirm-updation')">
                                            <i class="fas fa-times-circle"></i>
                                        </x-icon-button>
                                    @endif
                                </x-td>
                            </x-tr>
                        @endforeach
                    </x-tbody>
                </x-table>
            </div>

            {{ $registrations->links() }}
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
