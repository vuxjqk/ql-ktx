<x-app-layout>
    <x-slot name="header">
        Danh sách hóa đơn
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => url('/')], ['label' => 'Danh sách hóa đơn']]" />

            <!-- Danh sách hóa đơn -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <x-table title="Hóa đơn của sinh viên">
                    <x-thead>
                        <x-tr>
                            <x-th>STT</x-th>
                            <x-th>Mã hóa đơn</x-th>
                            <x-th>Mã phòng</x-th>
                            <x-th>Số tiền</x-th>
                            <x-th>Trạng thái</x-th>
                            <x-th>Ngày đến hạn</x-th>
                            <x-th>Hành động</x-th>
                        </x-tr>
                    </x-thead>
                    <x-tbody>
                        @forelse ($user->bills as $index => $bill)
                            <x-tr>
                                <x-td>#{{ $index + 1 }}</x-td>
                                <x-td>{{ $bill->code }}</x-td>
                                <x-td>{{ $bill->roomAssignment->room->room_code }}</x-td>
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
                                    <x-icon-button :data-items="json_encode($bill->items)" x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'bill-items')" title="Xem chi tiết"
                                        class="bg-blue-500 hover:bg-blue-600 text-white">
                                        <i class="fas fa-eye"></i>
                                    </x-icon-button>
                                    @if ($bill->status === 'paid')
                                        <span title="Đã thanh toán"
                                            class="bg-green-700 text-white p-2 border border-gray-300 rounded-md font-semibold text-xs uppercase tracking-widest shadow-sm">
                                            <i class="fas fa-money-check-alt"></i>
                                        </span>
                                    @else
                                        <x-icon-button :href="route('vnpay.redirect', $bill)" title="Thanh toán"
                                            class="bg-green-600 hover:bg-green-700 text-white">
                                            <i class="fas fa-money-check-alt"></i>
                                        </x-icon-button>
                                    @endif
                                </x-td>
                            </x-tr>
                        @empty
                            <x-tr>
                                <x-td colspan="7" class="text-center text-gray-600">
                                    Không có hóa đơn nào
                                </x-td>
                            </x-tr>
                        @endforelse
                    </x-tbody>
                </x-table>
            </div>
        </div>
    </div>

    <x-modal name="bill-items">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <x-table title="Chi tiết hoá đơn">
                <x-thead>
                    <x-tr>
                        <x-th>STT</x-th>
                        <x-th>Loại hóa đơn</x-th>
                        <x-th>Số tiền</x-th>
                        <x-th>Mô tả</x-th>
                    </x-tr>
                </x-thead>
                <x-tbody id="bill-items"></x-tbody>
            </x-table>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>
            </div>
        </div>
    </x-modal>

    @pushOnce('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const bill_items = document.getElementById('bill-items');

                const formatVND = (amount) => {
                    return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }

                document.querySelectorAll('[data-items]').forEach(button => {
                    button.addEventListener('click', () => {
                        bill_items.innerHTML = '';
                        const items = JSON.parse(button.getAttribute('data-items'));

                        items.forEach((bill, index) => {
                            const tr = document.createElement('tr');
                            tr.className =
                                'hover:bg-gray-50 transition-colors duration-150 ease-in-out';
                            tr.innerHTML = `
                                <td class="px-6 py-4 text-gray-800 whitespace-nowrap">#${index + 1}</td>
                                <td class="px-6 py-4 text-gray-800 whitespace-nowrap">${bill.type}</td>
                                <td class="px-6 py-4 text-gray-800 whitespace-nowrap">${formatVND(bill.amount)} VNĐ</td>
                                <td class="px-6 py-4 text-gray-800 whitespace-nowrap">${bill.description}</td>
                            `;
                            bill_items.appendChild(tr);
                        })
                    });
                });
            });
        </script>
    @endPushOnce
</x-app-layout>
