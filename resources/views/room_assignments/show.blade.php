<x-app-layout>
    <x-slot name="header">
        Chi tiết phân phòng
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => url('/')],
                ['label' => 'Quản lý phân phòng', 'url' => route('room_assignments.index')],
                ['label' => 'Chi tiết phân phòng'],
            ]" />

            <div class="mx-6 flex items-center justify-between">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-home text-blue-600"></i>
                        Chi tiết phân phòng
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Xem thông tin chi tiết về phân phòng
                    </p>
                </div>
                <x-secondary-button :href="route('room_assignments.index')">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại
                </x-secondary-button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Thông tin chính -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-lg text-gray-800 leading-tight mb-4">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        Thông tin phân phòng
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Mã sinh viên" icon="fas fa-id-card" />
                            <p class="mt-1 text-gray-600">{{ $assignment->user->student->student_code ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <x-input-label value="Họ tên" icon="fas fa-user" />
                            <div class="flex items-center gap-2 mt-1">
                                @if ($assignment->user->avatar)
                                    <img src="{{ asset('storage/' . $assignment->user->avatar) }}" alt="Avatar"
                                        class="w-8 h-8 rounded-full object-cover">
                                @else
                                    <div
                                        class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center font-bold text-xs text-white">
                                        {{ substr($assignment->user->name, 0, 2) }}
                                    </div>
                                @endif
                                <p class="text-gray-600">{{ $assignment->user->name }}</p>
                            </div>
                        </div>

                        <div>
                            <x-input-label value="Mã phòng" icon="fas fa-key" />
                            <p class="mt-1 text-gray-600">{{ $assignment->room->room_code }}</p>
                        </div>

                        <div>
                            <x-input-label value="Ngày nhận phòng" icon="fas fa-calendar-check" />
                            <p class="mt-1 text-gray-600">
                                {{ $assignment->checked_in_at ? $assignment->checked_in_at->format('d/m/Y H:i') : 'Chưa nhận phòng' }}
                            </p>
                        </div>

                        <div>
                            <x-input-label value="Ngày trả phòng" icon="fas fa-calendar-times" />
                            <p class="mt-1 text-gray-600">
                                {{ $assignment->checked_out_at ? $assignment->checked_out_at->format('d/m/Y H:i') : 'Chưa trả phòng' }}
                            </p>
                        </div>

                        <div>
                            <x-input-label value="Mã đăng ký" icon="fas fa-clipboard-list" />
                            <div class="mt-1">
                                @if ($assignment->registration_id)
                                    <a href="{{ route('room_registrations.show', $assignment->registration) }}"
                                        class="underline text-sm text-blue-600 hover:text-blue-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        #{{ $assignment->registration_id }}
                                    </a>
                                @else
                                    <span class="text-sm text-gray-600">N/A</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hành động -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-lg text-gray-800 leading-tight mb-4">
                        <i class="fas fa-cog text-blue-600"></i>
                        Hành động
                    </h3>

                    <div class="flex flex-col gap-4">
                        @if (!$assignment->checked_in_at)
                            {{-- <x-primary-button :href="route('room_assignments.checkin', $assignment)">
                                <i class="fas fa-sign-in-alt"></i>
                                Nhận phòng
                            </x-primary-button> --}}
                        @endif

                        @if ($assignment->checked_in_at && !$assignment->checked_out_at)
                            {{-- <x-primary-button :href="route('room_assignments.checkout', $assignment)">
                                <i class="fas fa-sign-out-alt"></i>
                                Trả phòng
                            </x-primary-button> --}}
                        @endif

                        {{-- <x-danger-button :data-delete-url="route('room_assignments.destroy', $assignment)" x-data=""
                            x-on:click.prevent="$dispatch('open-modal', 'confirm-deletion')">
                            <i class="fas fa-trash"></i>
                            Xoá phân phòng
                        </x-danger-button> --}}
                    </div>
                </div>
            </div>

            <!-- Danh sách hóa đơn -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-800 leading-tight mb-4">
                    <i class="fas fa-file-invoice-dollar text-blue-600"></i>
                    Danh sách hóa đơn
                </h3>

                <x-table title="Hóa đơn liên quan">
                    <x-thead>
                        <x-tr>
                            <x-th>STT</x-th>
                            <x-th>Số tiền</x-th>
                            <x-th>Trạng thái</x-th>
                            <x-th>Ngày đến hạn</x-th>
                            <x-th>Hành động</x-th>
                        </x-tr>
                    </x-thead>
                    <x-tbody>
                        @foreach ($assignment->bills as $index => $bill)
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
                                <x-td>{{ $bill->due_date->format('d/m/Y') }}</x-td>
                                <x-td>
                                    @if ($bill->status == 'pending' || $bill->status == 'overdue')
                                        <x-icon-button :data-update-url="route('bills.update', $bill)" title="Thanh toán"
                                            class="bg-blue-500 hover:bg-blue-600 text-white" x-data=""
                                            x-on:click.prevent="$dispatch('open-modal', 'confirm-updation')">
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-800 leading-tight mb-4">
                    <i class="fas fa-exchange-alt text-blue-600"></i>
                    Danh sách giao dịch
                </h3>

                <x-table title="Giao dịch liên quan">
                    <x-thead>
                        <x-tr>
                            <x-th>STT</x-th>
                            <x-th>Mã giao dịch</x-th>
                            <x-th>Số tiền</x-th>
                            <x-th>Ngân hàng</x-th>
                            <x-th>Loại thẻ</x-th>
                            <x-th>Trạng thái</x-th>
                            <x-th>Ngày giao dịch</x-th>
                        </x-tr>
                    </x-thead>
                    <x-tbody>
                        @foreach ($assignment->bills->flatMap->transactions as $index => $transaction)
                            <x-tr>
                                <x-td>#{{ $index + 1 }}</x-td>
                                <x-td>{{ $transaction->vnp_txn_ref }}</x-td>
                                <x-td>{{ number_format($transaction->vnp_amount, 0, ',', '.') }} VNĐ</x-td>
                                <x-td>{{ $transaction->vnp_bank_code ?? 'N/A' }}</x-td>
                                <x-td>{{ $transaction->vnp_card_type ?? 'N/A' }}</x-td>
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
        </div>
    </div>

    <x-modal name="confirm-updation" focusable>
        <form id="update-form" method="post" action="#" class="p-6">
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

    <x-delete-modal />

    @pushOnce('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('update-form');

                document.querySelectorAll('[data-update-url]').forEach(button => {
                    button.addEventListener('click', () => {
                        form.action = button.getAttribute('data-update-url');
                    });
                });
            });
        </script>
    @endPushOnce
</x-app-layout>
