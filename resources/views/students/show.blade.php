<x-app-layout>
    <x-slot name="header">
        Chi tiết sinh viên
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => url('/')],
                ['label' => 'Quản lý sinh viên', 'url' => route('students.index')],
                ['label' => 'Chi tiết sinh viên'],
            ]" />

            <div class="mx-6 flex items-center justify-between">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-graduation-cap text-blue-600"></i>
                        Chi tiết sinh viên
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Xem thông tin chi tiết về sinh viên và các thông tin liên quan
                    </p>
                </div>
                <div>
                    <x-secondary-button :href="route('students.index')">
                        <i class="fas fa-arrow-left"></i>
                        Quay lại
                    </x-secondary-button>
                    <x-secondary-button :href="route('room_assignments.index', $user)" class="bg-blue-600 hover:bg-blue-700 text-white">
                        <i class="fas fa-clock-rotate-left"></i>
                        Lịch sử nội trú
                    </x-secondary-button>
                    @if ($registration = $user->roomRegistration)
                        @if ($registration->status === 'pending')
                            <x-icon-button :data-update-url="route('room_registrations.update', $registration)" data-status-value="approved" data-status-label="phê duyệt"
                                title="Phê duyệt" class="bg-green-500 hover:bg-green-600 text-white"
                                x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'confirm-updation')">
                                <i class="fas fa-check-circle"></i>
                            </x-icon-button>
                            <x-icon-button :data-update-url="route('room_registrations.update', $registration)" data-status-value="rejected" data-status-label="từ chối"
                                title="Từ chối" class="bg-red-500 hover:bg-red-600 text-white" x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'confirm-updation')">
                                <i class="fas fa-times-circle"></i>
                            </x-icon-button>
                        @endif
                    @endif
                    <x-icon-button :href="route('students.edit', $user)" title="Chỉnh sửa"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white">
                        <i class="fas fa-edit"></i>
                    </x-icon-button>
                    <x-icon-button :data-delete-url="route('students.destroy', $user)" title="Xoá" class="bg-red-500 hover:bg-red-600 text-white"
                        x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-deletion')">
                        <i class="fas fa-trash"></i>
                    </x-icon-button>
                </div>
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
                                {{ substr($user->name, 0, 2) }}
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
                        <div>
                            <x-input-label value="Ngày sinh" icon="fas fa-calendar-alt" />
                            <p class="mt-1 text-gray-600">
                                {{ $user->date_of_birth ? $user->date_of_birth->format('d/m/Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <x-input-label value="Giới tính" icon="fas fa-venus-mars" />
                            <p class="mt-1 text-gray-600">
                                {{ $user->gender == 'male' ? 'Nam' : ($user->gender == 'female' ? 'Nữ' : 'N/A') }}
                            </p>
                        </div>
                        <div>
                            <x-input-label value="Ngành học" icon="fas fa-graduation-cap" />
                            <p class="mt-1 text-gray-600">{{ $user->student->major ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <x-input-label value="Lớp" icon="fas fa-chalkboard" />
                            <p class="mt-1 text-gray-600">{{ $user->student->class ?? 'N/A' }}</p>
                        </div>
                        <div class="col-span-2">
                            <x-input-label value="Địa chỉ" icon="fas fa-map-marker-alt" />
                            <p class="mt-1 text-gray-600">{{ $user->address ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin đăng ký phòng -->
            @if ($user->roomRegistration)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-lg text-gray-800 leading-tight mb-4">
                        <i class="fas fa-clipboard-list text-blue-600"></i>
                        Thông tin đăng ký phòng
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Mã phòng" icon="fas fa-key" />
                            <p class="mt-1 text-gray-600">{{ $user->roomRegistration->room->room_code }}</p>
                        </div>
                        <div>
                            <x-input-label value="Chi nhánh" icon="fas fa-building" />
                            <p class="mt-1 text-gray-600">{{ $user->roomRegistration->room->branch->name }}</p>
                        </div>
                        <div>
                            <x-input-label value="Khu nhà" icon="fas fa-building" />
                            <p class="mt-1 text-gray-600">{{ $user->roomRegistration->room->block }}</p>
                        </div>
                        <div>
                            <x-input-label value="Tầng" icon="fas fa-layer-group" />
                            <p class="mt-1 text-gray-600">{{ $user->roomRegistration->room->floor }}</p>
                        </div>
                        <div>
                            <x-input-label value="Loại phòng" icon="fas fa-venus-mars" />
                            <p class="mt-1 text-gray-600">
                                @if ($user->roomRegistration->room->gender_type == 'male')
                                    Nam
                                @elseif ($user->roomRegistration->room->gender_type == 'female')
                                    Nữ
                                @else
                                    Hỗn hợp
                                @endif
                            </p>
                        </div>
                        <div>
                            <x-input-label value="Trạng thái" icon="fas fa-info-circle" />
                            <p class="mt-1">
                                @if ($user->roomRegistration->status == 'pending')
                                    <span class="text-yellow-600">Đang chờ</span>
                                @elseif ($user->roomRegistration->status == 'approved')
                                    <span class="text-green-600">Đã duyệt</span>
                                @else
                                    <span class="text-red-600">Đã từ chối</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <x-input-label value="Ngày yêu cầu" icon="fas fa-calendar-alt" />
                            <p class="mt-1 text-gray-600">
                                {{ $user->roomRegistration->requested_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <x-input-label value="Ghi chú" icon="fas fa-align-left" />
                            <p class="mt-1 text-gray-600">{{ $user->roomRegistration->notes ?? 'Không có ghi chú' }}
                            </p>
                        </div>
                        <div>
                            <x-input-label value="Ngày xử lý" icon="fas fa-calendar-check" />
                            <p class="mt-1 text-gray-600">
                                {{ $user->roomRegistration->processed_at ? $user->roomRegistration->processed_at->format('d/m/Y H:i') : 'Chưa xử lý' }}
                            </p>
                        </div>
                        <div>
                            <x-input-label value="Người xử lý" icon="fas fa-user-check" />
                            <p class="mt-1 text-gray-600">
                                {{ $user->roomRegistration->processed_by ? $user->roomRegistration->processor->name : 'Chưa có' }}
                            </p>
                        </div>
                        <div>
                            <x-input-label value="Ngày nhận phòng" icon="fas fa-calendar-check" />
                            <p class="mt-1 text-gray-600">
                                {{ $user->roomAssignment?->checked_in_at?->format('d/m/Y H:i') ?? 'Chưa nhận phòng' }}
                            </p>
                        </div>
                        <div>
                            <x-input-label value="Ngày trả phòng" icon="fas fa-calendar-times" />
                            <p class="mt-1 text-gray-600">
                                {{ $user->roomAssignment?->checked_out_at?->format('d/m/Y H:i') ?? 'Chưa trả phòng' }}
                            </p>
                        </div>
                        @if ($assignment = $user->roomAssignment)
                            <div>
                                <x-secondary-button :data-delete-url="route('room_assignments.destroy', $assignment)" class="bg-red-500 hover:bg-red-600 text-white"
                                    x-data=""
                                    x-on:click.prevent="$dispatch('open-modal', 'confirm-deletion')">
                                    <i class="fas fa-trash"></i>
                                    Huỷ đăng ký
                                </x-secondary-button>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if ($user->roomRegistration && $user->roomRegistration->status == 'approved' && $user->roomAssignment)
                <!-- Danh sách hóa đơn -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <x-table title="Hóa đơn liên quan">
                        <x-thead>
                            <x-tr>
                                <x-th>STT</x-th>
                                <x-th>Số tiền</x-th>
                                <x-th>Trạng thái</x-th>
                                <x-th>Ngày tạo</x-th>
                                <x-th>Ngày đến hạn</x-th>
                                <x-th>Hành động</x-th>
                            </x-tr>
                        </x-thead>
                        <x-tbody>
                            @foreach ($user->roomAssignment->bills as $index => $bill)
                                <x-tr>
                                    <x-td>#{{ $index + 1 }}</x-td>
                                    <x-td>{{ number_format($bill->amount, 0, ',', '.') }} VNĐ</x-td>
                                    <x-td>
                                        @if ($bill->status == 'pending')
                                            <span class="text-yellow-600">Đang chờ</span>
                                        @elseif ($bill->status == 'paid')
                                            <span class="text-green-600">Đã thanh toán</span>
                                        @elseif ($bill->status == 'failed')
                                            <span class="text-red-600">Thất bại</span>
                                        @else
                                            <span class="text-red-600">Quá hạn</span>
                                        @endif
                                    </x-td>
                                    <x-td>{{ $bill->created_at->format('d/m/Y H:i') }}</x-td>
                                    <x-td>{{ $bill->due_date->format('d/m/Y') }}</x-td>
                                    <x-td>
                                        @if ($bill->status == 'pending' || $bill->status == 'overdue')
                                            <x-icon-button :data-bill-update-url="route('bills.update', $bill)" title="Thanh toán"
                                                class="bg-blue-500 hover:bg-blue-600 text-white"
                                                x-data=""
                                                x-on:click.prevent="$dispatch('open-modal', 'confirm-bill-updation')">
                                                <i class="fas fa-money-check-alt"></i>
                                            </x-icon-button>
                                        @endif
                                    </x-td>
                                </x-tr>
                            @endforeach
                        </x-tbody>
                    </x-table>
                </div>

                <!-- Danh sách giao dịch -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <x-table title="Giao dịch liên quan">
                        <x-thead>
                            <x-tr>
                                <x-th>STT</x-th>
                                <x-th>Mã hoá đơn</x-th>
                                <x-th>Số tiền</x-th>
                                <x-th>Ngân hàng</x-th>
                                <x-th>Trạng thái</x-th>
                                <x-th>Ngày giao dịch</x-th>
                            </x-tr>
                        </x-thead>
                        <x-tbody>
                            @foreach ($user->roomAssignment->bills->flatMap->transactions as $index => $transaction)
                                <x-tr>
                                    <x-td>#{{ $index + 1 }}</x-td>
                                    <x-td>{{ $transaction->bill->code }}</x-td>
                                    <x-td>{{ number_format($transaction->vnp_amount, 0, ',', '.') }} VNĐ</x-td>
                                    <x-td>{{ $transaction->vnp_bank_code ?? 'N/A' }}</x-td>
                                    <x-td>
                                        @if ($transaction->vnp_transaction_status == '00')
                                            <span class="text-green-600">Thành công</span>
                                        @else
                                            <span class="text-red-600">Thất bại</span>
                                        @endif
                                    </x-td>
                                    <x-td>{{ $transaction->vnp_pay_date ? \Carbon\Carbon::parse($transaction->vnp_pay_date)->format('d/m/Y H:i') : 'N/A' }}</x-td>
                                </x-tr>
                            @endforeach
                        </x-tbody>
                    </x-table>
                </div>
            @endif
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

    <x-modal name="confirm-bill-updation" focusable>
        <form id="update-bill-form" method="post" action="#" class="p-6">
            @csrf
            @method('put')

            <h2 class="text-lg font-medium text-gray-900">
                Sinh viên đã thanh toán trực tiếp hoá đơn này rồi?
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

                const bill_form = document.getElementById('update-bill-form');

                document.querySelectorAll('[data-bill-update-url]').forEach(button => {
                    button.addEventListener('click', () => {
                        bill_form.action = button.getAttribute('data-bill-update-url');
                    });
                });
            });
        </script>
    @endPushOnce
</x-app-layout>
