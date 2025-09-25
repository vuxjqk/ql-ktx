<x-app-layout>
    <x-slot name="header">
        Chi tiết lịch sử nội trú
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[
                ['label' => 'Trang chủ', 'url' => url('/')],
                ['label' => 'Quản lý sinh viên', 'url' => route('students.index')],
                ['label' => 'Chi tiết sinh viên', 'url' => route('students.show', $user)],
                ['label' => 'Lịch sử nội trú', 'url' => route('room_assignments.index', $user)],
                ['label' => 'Chi tiết lịch sử nội trú'],
            ]" />

            <div class="mx-6 flex items-center justify-between">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-history text-blue-600"></i>
                        Chi tiết lịch sử nội trú
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Xem chi tiết lịch sử nội trú của sinh viên</p>
                </div>
                <x-secondary-button :href="route('room_assignments.index', $user)">
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
                    </div>
                </div>
            </div>

            <!-- Chi tiết phân phòng -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-800 leading-tight mb-4">
                    <i class="fas fa-home text-blue-600"></i>
                    Chi tiết phân phòng
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Mã phòng" icon="fas fa-key" />
                        <p class="mt-1 text-gray-600">{{ $assignment->room->room_code }}</p>
                    </div>
                    <div>
                        <x-input-label value="Chi nhánh" icon="fas fa-building" />
                        <p class="mt-1 text-gray-600">{{ $assignment->room->branch->name }}</p>
                    </div>
                    <div>
                        <x-input-label value="Khu nhà" icon="fas fa-building" />
                        <p class="mt-1 text-gray-600">{{ $assignment->room->block }}</p>
                    </div>
                    <div>
                        <x-input-label value="Tầng" icon="fas fa-layer-group" />
                        <p class="mt-1 text-gray-600">{{ $assignment->room->floor }}</p>
                    </div>
                    <div>
                        <x-input-label value="Loại phòng" icon="fas fa-venus-mars" />
                        <p class="mt-1 text-gray-600">
                            @if ($assignment->room->gender_type == 'male')
                                Nam
                            @elseif ($assignment->room->gender_type == 'female')
                                Nữ
                            @else
                                Hỗn hợp
                            @endif
                        </p>
                    </div>
                    <div></div>
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
                </div>
            </div>

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
                                <x-td>{{ $bill->created_at->format('d/m/Y H:i') }}</x-td>
                                <x-td>{{ $bill->due_date->format('d/m/Y') }}</x-td>
                                <x-td>
                                    @if ($bill->status == 'pending' || $bill->status == 'overdue')
                                        <x-icon-button :href="route('bills.pay', $bill)" title="Thanh toán"
                                            class="bg-blue-500 hover:bg-blue-600 text-white">
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
                        @foreach ($assignment->bills->flatMap->transactions as $index => $transaction)
                            <x-tr>
                                <x-td>#{{ $index + 1 }}</x-td>
                                <x-td>{{ $transaction->bill_id }}</x-td>
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
        </div>
    </div>
</x-app-layout>
