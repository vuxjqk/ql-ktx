<x-app-layout>
    <x-slot name="header">
        Dashboard
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => url('/')], ['label' => 'Dashboard']]" />

            <div class="mx-6 flex items-center justify-between">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-chart-line text-blue-600"></i>
                        Dashboard Quản lý Ký túc xá
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Tổng quan về doanh thu, phòng và chi nhánh</p>
                </div>
            </div>

            <!-- Tổng quan (chỉ dành cho admin/staff) -->
            @cannot('is-student')
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800">Tổng doanh thu</h3>
                        <p class="mt-2 text-2xl font-bold text-blue-600">{{ number_format($totalRevenue, 0, ',', '.') }} VNĐ
                        </p>
                    </div>
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800">Phòng đang sử dụng</h3>
                        <p class="mt-2 text-2xl font-bold text-blue-600">{{ $occupiedRooms }}</p>
                    </div>
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800">Yêu cầu sửa chữa mở</h3>
                        <p class="mt-2 text-2xl font-bold text-blue-600">{{ $openRepairs }}</p>
                    </div>
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800">Thông báo chưa đọc</h3>
                        <p class="mt-2 text-2xl font-bold text-blue-600">{{ $unreadNotifications }}</p>
                    </div>
                </div>
            @endcan

            <!-- Thông báo dành cho sinh viên -->
            @can('is-student')
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-bell text-blue-600"></i>
                        Thông báo của bạn
                    </h3>
                    @if ($unreadNotifications > 0)
                        <p class="text-gray-600">Bạn có <span
                                class="font-bold text-blue-600">{{ $unreadNotifications }}</span> thông báo chưa đọc.</p>
                        <x-primary-button :href="route('notifications.index')" class="mt-4">
                            Xem thông báo
                        </x-primary-button>
                    @else
                        <p class="text-gray-600">Không có thông báo mới.</p>
                    @endif
                </div>
            @endcan

            <!-- Biểu đồ -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Biểu đồ doanh thu hàng tháng -->
                @cannot('is-student')
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-chart-line text-blue-600"></i>
                            Doanh thu hàng tháng
                        </h3>
                        <canvas id="revenueChart" height="200"></canvas>
                    </div>
                @endcan

                <!-- Biểu đồ phòng được thuê nhiều -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-bed text-blue-600"></i>
                        Top 5 phòng được thuê nhiều
                    </h3>
                    <canvas id="popularRoomsChart" height="200"></canvas>
                </div>

                <!-- Biểu đồ chi nhánh có doanh thu cao -->
                @cannot('is-student')
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-building text-blue-600"></i>
                            Doanh thu theo chi nhánh
                        </h3>
                        <canvas id="branchRevenueChart" height="200"></canvas>
                    </div>
                @endcan
            </div>
        </div>
    </div>

    <!-- Script Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Biểu đồ doanh thu hàng tháng
        @cannot('is-student')
            new Chart(document.getElementById('revenueChart'), {
                type: 'line',
                data: {
                    labels: @json($monthlyRevenue->pluck('month')),
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: @json($monthlyRevenue->pluck('total')),
                        borderColor: 'rgba(59, 130, 246, 1)',
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('vi-VN') + ' VNĐ';
                                }
                            }
                        }
                    }
                }
            });
        @endcan

        @cannot('is-student')
            // Biểu đồ phòng được thuê nhiều
            new Chart(document.getElementById('popularRoomsChart'), {
                type: 'bar',
                data: {
                    labels: @json($popularRooms->pluck('room_code')),
                    datasets: [{
                        label: 'Số lần thuê',
                        data: @json($popularRooms->pluck('count')),
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        @endcan

        // Biểu đồ doanh thu theo chi nhánh
        @cannot('is-student')
            new Chart(document.getElementById('branchRevenueChart'), {
                type: 'bar',
                data: {
                    labels: @json($branchRevenue->pluck('name')),
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: @json($branchRevenue->pluck('total')),
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('vi-VN') + ' VNĐ';
                                }
                            }
                        }
                    }
                }
            });
        @endcan
    </script>
</x-app-layout>
