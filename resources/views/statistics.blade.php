<x-app-layout>
    <x-slot name="header">
        {{ __('Thống kê') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6">
            <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => url('/')], ['label' => 'Thống kê']]" />

            <div class="flex items-center justify-between mx-6">
                <div>
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-chart-line text-blue-600 me-1"></i>
                        {{ __('Thống kê') }}
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">{{ __('Xem thống kê doanh thu và hoạt động ký túc xá') }}</p>
                </div>
                <x-secondary-button :href="route('reports')" class="!bg-blue-600 !text-white !hover:bg-blue-700">
                    <i class="fas fa-file-excel"></i>
                    {{ __('Xuất báo cáo') }}
                </x-secondary-button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form class="flex items-end gap-6 mb-6">
                    <div>
                        <x-input-label for="year" :value="__('Chọn năm')" icon="fas fa-calendar-alt" />
                        <x-select id="year" class="block mt-1 w-48" name="year" :options="array_reverse(
                            array_combine(range(2020, now()->year + 1), range(2020, now()->year + 1)),
                            true,
                        )"
                            :selected="$year" />
                    </div>
                    <x-primary-button>
                        <i class="fas fa-filter"></i>
                        {{ __('Lọc') }}
                    </x-primary-button>
                </form>
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
