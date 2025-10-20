<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => url('/')], ['label' => 'Dashboard']]" />

            <div class="flex items-center justify-between mx-6">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-tachometer-alt text-blue-600 me-1"></i>
                        {{ __('Dashboard') }}
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">{{ __('Tổng quan tình trạng ký túc xá trong hệ thống') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-blue-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Tổng doanh thu') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">
                                {{ number_format($totalRevenue, 0, ',', '.') }}M VND</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-green-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-percentage text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Tỷ lệ tăng trưởng') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">
                                {{ number_format($revenueGrowthRate, 2) }}%</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-yellow-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-calendar-check text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Tổng đặt phòng') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $totalBookings }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex items-center p-6">
                    <div class="flex items-center gap-6">
                        <div class="bg-red-100 shadow-sm sm:rounded-lg p-3">
                            <i class="fas fa-user-graduate text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Sinh viên mới') }}</p>
                            <p class="font-semibold text-xl text-gray-800 leading-tight">{{ $newStudents }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-xl text-gray-800 leading-tight mb-6">
                        <i class="fas fa-chart-bar text-blue-600 me-1"></i>
                        {{ __('Doanh thu theo tháng') }}
                    </h3>
                    <div>
                        <canvas id="monthlyRevenueChart" height="100"></canvas>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-xl text-gray-800 leading-tight mb-6">
                        <i class="fas fa-building text-blue-600 me-1"></i>
                        {{ __('Đặt phòng theo chi nhánh') }}
                    </h3>
                    <div>
                        <canvas id="bookingsByBranchChart" height="100"></canvas>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-xl text-gray-800 leading-tight mb-6">
                        <i class="fas fa-exchange-alt text-blue-600 me-1"></i>
                        {{ __('Tỷ lệ ở lại/rời đi') }}
                    </h3>
                    <div>
                        <canvas id="stayLeaveRatioChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Management Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Booking Management -->
                <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-lg transition">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900">{{ __('Quản Lý Đặt Phòng') }}</h3>
                        <i class="fas fa-calendar-check text-blue-600 text-2xl"></i>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                            <span class="text-gray-700">{{ __('Chờ duyệt') }}</span>
                            <span
                                class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-semibold">{{ $pendingBookings }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                            <span class="text-gray-700">{{ __('Đã duyệt') }}</span>
                            <span
                                class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">{{ $approvedBookings }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                            <span class="text-gray-700">{{ __('Bị từ chối') }}</span>
                            <span
                                class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-semibold">{{ $rejectedBookings }}</span>
                        </div>
                        <a href="{{ route('bookings.index') }}"
                            class="inline-block text-center w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium transition">
                            <i class="fas fa-arrow-right mr-2"></i>{{ __('Xem Chi Tiết') }}
                        </a>
                    </div>
                </div>

                <!-- Maintenance -->
                <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-lg transition">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900">{{ __('Quản Lý Sửa Chữa') }}</h3>
                        <i class="fas fa-wrench text-orange-600 text-2xl"></i>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                            <span class="text-gray-700">{{ __('Chờ xử lý') }}</span>
                            <span
                                class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-semibold">{{ $pendingRepairs }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                            <span class="text-gray-700">{{ __('Đang xử lý') }}</span>
                            <span
                                class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm font-semibold">{{ $inProgressRepairs }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                            <span class="text-gray-700">{{ __('Đã hoàn thành') }}</span>
                            <span
                                class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">{{ $completedRepairs }}</span>
                        </div>
                        <a href="{{ route('repairs.index') }}"
                            class="inline-block text-center w-full mt-4 bg-orange-600 hover:bg-orange-700 text-white py-2 rounded-lg font-medium transition">
                            <i class="fas fa-arrow-right mr-2"></i>{{ __('Xem Chi Tiết') }}
                        </a>
                    </div>
                </div>

                <!-- Room Status -->
                <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-lg transition">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900">{{ __('Tình Trạng Phòng') }}</h3>
                        <i class="fas fa-chart-pie text-green-600 text-2xl"></i>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                            <span class="text-gray-700">{{ __('Phòng đầy') }}</span>
                            <span
                                class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">{{ $fullRooms }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                            <span class="text-gray-700">{{ __('Phòng trống') }}</span>
                            <span
                                class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">{{ $emptyRooms }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                            <span class="text-gray-700">{{ __('Phòng chưa đầy') }}</span>
                            <span
                                class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-semibold">{{ $missingRooms }}</span>
                        </div>
                        <a href="{{ route('rooms.index') }}"
                            class="inline-block text-center w-full mt-4 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-medium transition">
                            <i class="fas fa-arrow-right mr-2"></i>{{ __('Xem Chi Tiết') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Management Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Student Management -->
                <a href="{{ route('students.index') }}"
                    class="block bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-sm p-6 hover:shadow-lg transition cursor-pointer border border-blue-200">
                    <div class="w-14 h-14 bg-blue-600 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-graduation-cap text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('Quản Lý Sinh Viên') }}</h3>
                    <p class="text-sm text-gray-700 mb-4">{{ __('Thêm, sửa, xóa thông tin sinh viên') }}</p>
                    <div class="flex items-center text-blue-600 font-semibold text-sm">
                        <span>{{ number_format($totalStudents) }} {{ __('sinh viên') }}</span>
                        <i class="fas fa-arrow-right ml-2"></i>
                    </div>
                </a>

                <!-- Staff Management -->
                <a href="{{ route('users.index') }}"
                    class="block bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg shadow-sm p-6 hover:shadow-lg transition cursor-pointer border border-indigo-200">
                    <div class="w-14 h-14 bg-indigo-600 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-people-group text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('Quản Lý Nhân Viên') }}</h3>
                    <p class="text-sm text-gray-700 mb-4">{{ __('Quản lý đội ngũ nhân sự và phân công') }}</p>
                    <div class="flex items-center text-indigo-600 font-semibold text-sm">
                        <span>{{ number_format($totalStaffs) }} {{ __('nhân viên') }}</span>
                        <i class="fas fa-arrow-right ml-2"></i>
                    </div>
                </a>

                <!-- Branch Management -->
                <a href="{{ route('branches.index') }}"
                    class="block bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-lg shadow-sm p-6 hover:shadow-lg transition cursor-pointer border border-cyan-200">
                    <div class="w-14 h-14 bg-cyan-600 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-sitemap text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('Quản Lý Chi Nhánh') }}</h3>
                    <p class="text-sm text-gray-700 mb-4">{{ __('Quản lý thông tin các chi nhánh ký túc xá') }}</p>
                    <div class="flex items-center text-cyan-600 font-semibold text-sm">
                        <span>{{ number_format($totalBranches) }} {{ __('chi nhánh') }}</span>
                        <i class="fas fa-arrow-right ml-2"></i>
                    </div>
                </a>

                <!-- Services Management -->
                <a href="{{ route('services.index') }}"
                    class="block bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg shadow-sm p-6 hover:shadow-lg transition cursor-pointer border border-orange-200">
                    <div class="w-14 h-14 bg-orange-600 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-concierge-bell text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('Quản Lý Dịch Vụ') }}</h3>
                    <p class="text-sm text-gray-700 mb-4">{{ __('Quản lý nước, điện, internet') }}</p>
                    <div class="flex items-center text-orange-600 font-semibold text-sm">
                        <span>{{ number_format($totalServices) }} {{ __('dịch vụ') }}</span>
                        <i class="fas fa-arrow-right ml-2"></i>
                    </div>
                </a>

                <!-- Amenities Management -->
                <a href="{{ route('amenities.index') }}"
                    class="block bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow-sm p-6 hover:shadow-lg transition cursor-pointer border border-green-200">
                    <div class="w-14 h-14 bg-green-600 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-swimming-pool text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('Quản Lý Tiện Ích') }}</h3>
                    <p class="text-sm text-gray-700 mb-4">{{ __('Quản lý các tiện ích có trong ký túc xá') }}</p>
                    <div class="flex items-center text-green-600 font-semibold text-sm">
                        <span>{{ number_format($totalAmenities) }} {{ __('tiện ích') }}</span>
                        <i class="fas fa-arrow-right ml-2"></i>
                    </div>
                </a>

                <!-- Statistics -->
                <a href="{{ route('statistics') }}"
                    class="block bg-gradient-to-br from-red-50 to-red-100 rounded-lg shadow-sm p-6 hover:shadow-lg transition cursor-pointer border border-red-200">
                    <div class="w-14 h-14 bg-red-600 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-chart-line text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('Báo Cáo & Thống Kê') }}</h3>
                    <p class="text-sm text-gray-700 mb-4">{{ __('Xem báo cáo chi tiết và xu hướng') }}</p>
                    <div class="flex items-center text-red-600 font-semibold text-sm">
                        <span>{{ __('Tạo báo cáo') }}</span>
                        <i class="fas fa-arrow-right ml-2"></i>
                    </div>
                </a>

                <!-- Settings -->
                <a href="#"
                    class="block bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow-sm p-6 hover:shadow-lg transition cursor-pointer border border-purple-200">
                    <div class="w-14 h-14 bg-purple-600 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-sliders text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('Cài Đặt Hệ Thống') }}</h3>
                    <p class="text-sm text-gray-700 mb-4">{{ __('Cấu hình các thông số hệ thống') }}</p>
                    <div class="flex items-center text-purple-600 font-semibold text-sm">
                        <span>{{ __('Cấu hình') }}</span>
                        <i class="fas fa-arrow-right ml-2"></i>
                    </div>
                </a>

                <!-- Archive -->
                <a href="{{ route('backup') }}"
                    class="block bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg shadow-sm p-6 hover:shadow-lg transition cursor-pointer border border-gray-200">
                    <div class="w-14 h-14 bg-gray-600 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-box-archive text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('Lưu Trữ') }}</h3>
                    <p class="text-sm text-gray-700 mb-4">{{ __('Quản lý dữ liệu lưu trữ') }}</p>
                    <div class="flex items-center text-gray-600 font-semibold text-sm">
                        <span>{{ __('Xem kho lưu trữ') }}</span>
                        <i class="fas fa-arrow-right ml-2"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>

    @pushOnce('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Monthly Revenue Chart
                const monthlyRevenue = @json($monthlyRevenue);
                const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
                new Chart(monthlyRevenueCtx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(monthlyRevenue),
                        datasets: [{
                            label: '{{ __('Doanh thu (Triệu VND)') }}',
                            data: Object.values(monthlyRevenue),
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString('vi-VN') + ' Triệu VND';
                                    }
                                }
                            }
                        }
                    }
                });

                // Bookings by Branch Chart
                const bookingsByBranch = @json($bookingsByBranch);
                const bookingsByBranchCtx = document.getElementById('bookingsByBranchChart').getContext('2d');
                new Chart(bookingsByBranchCtx, {
                    type: 'pie',
                    data: {
                        labels: Object.keys(bookingsByBranch),
                        datasets: [{
                            label: '{{ __('Số lượng đặt phòng') }}',
                            data: Object.values(bookingsByBranch),
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.6)',
                                'rgba(255, 99, 132, 0.6)',
                                'rgba(75, 192, 192, 0.6)',
                                'rgba(255, 205, 86, 0.6)',
                                'rgba(153, 102, 255, 0.6)',
                                'rgba(255, 159, 64, 0.6)'
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 99, 132, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 205, 86, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                position: 'right'
                            }
                        }
                    }
                });

                // Stay/Leave Ratio Chart
                const stayLeaveRatio = @json($stayLeaveRatio);
                const stayLeaveRatioCtx = document.getElementById('stayLeaveRatioChart').getContext('2d');
                new Chart(stayLeaveRatioCtx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(stayLeaveRatio),
                        datasets: [{
                            label: '{{ __('Tỷ lệ') }}',
                            data: Object.values(stayLeaveRatio),
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.6)',
                                'rgba(255, 99, 132, 0.6)',
                                'rgba(255, 206, 86, 0.6)'
                            ],
                            borderColor: [
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 99, 132, 1)',
                                'rgba(255, 206, 86, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                position: 'right'
                            }
                        }
                    }
                });
            });
        </script>
    @endPushOnce
</x-app-layout>
