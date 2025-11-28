@extends('student.layouts.app')

@section('title', 'Dashboard Chi Phí Dịch Vụ')

@section('content')
    <!-- Breadcrumb -->
    <div class="bg-gray-50 py-4 border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-sm">
                <ol class="inline-flex items-center space-x-2">
                    <li>
                        <a href="{{ route('student.home') }}" class="text-gray-600 hover:text-blue-600">
                            <i class="fas fa-home"></i>
                        </a>
                    </li>
                    <li><i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i></li>
                    <li class="text-gray-900 font-medium">Chi Phí Dịch Vụ</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header & Filters -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        <i class="fas fa-chart-line text-blue-600 mr-2"></i>Dashboard Chi Phí Dịch Vụ
                    </h1>
                    <p class="text-gray-600">Theo dõi và phân tích chi phí sử dụng dịch vụ của bạn</p>
                </div>

                <!-- Date Filter -->
                <form method="GET" action="{{ route('student.service-costs.index') }}"
                    class="flex flex-wrap gap-3 items-end bg-white p-4 rounded-lg shadow-md">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
                        <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
                        <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                        <i class="fas fa-filter mr-2"></i>Lọc
                    </button>
                </form>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Tổng chi phí tháng này -->
            <div class="stat-card bg-gradient-to-br from-blue-500 to-blue-600 text-white">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm opacity-90">Tháng này</span>
                    <i class="fas fa-wallet text-2xl opacity-80"></i>
                </div>
                <div class="text-3xl font-bold mb-1">{{ number_format($currentMonthTotal) }}₫</div>
                <div class="text-sm opacity-90">Tổng chi phí</div>
            </div>

            <!-- So sánh tháng trước -->
            <div class="stat-card">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600">So với tháng trước</span>
                    <i class="fas fa-chart-bar text-2xl text-gray-400"></i>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-3xl font-bold text-gray-900">
                        {{ number_format(abs($percentageChange), 1) }}%
                    </span>
                    @if ($percentageChange > 0)
                        <span class="trend-up">
                            <i class="fas fa-arrow-up"></i> Tăng
                        </span>
                    @elseif($percentageChange < 0)
                        <span class="trend-down">
                            <i class="fas fa-arrow-down"></i> Giảm
                        </span>
                    @else
                        <span class="text-gray-500">
                            <i class="fas fa-minus"></i> Không đổi
                        </span>
                    @endif
                </div>
                <div class="text-sm text-gray-500 mt-1">
                    Tháng trước: {{ number_format($lastMonthTotal) }}₫
                </div>
            </div>

            <!-- Dự báo cuối tháng -->
            <div class="stat-card bg-gradient-to-br from-purple-500 to-purple-600 text-white">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm opacity-90">Dự báo cuối tháng</span>
                    <i class="fas fa-crystal-ball text-2xl opacity-80"></i>
                </div>
                <div class="text-3xl font-bold mb-1">{{ number_format($forecast) }}₫</div>
                <div class="text-sm opacity-90">
                    Ước tính ({{ now()->day }}/{{ now()->daysInMonth }} ngày)
                </div>
            </div>

            <!-- Trung bình ngày -->
            <div class="stat-card bg-gradient-to-br from-green-500 to-green-600 text-white">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm opacity-90">Trung bình/ngày</span>
                    <i class="fas fa-calendar-day text-2xl opacity-80"></i>
                </div>
                <div class="text-3xl font-bold mb-1">
                    {{ number_format(now()->day > 0 ? $currentMonthTotal / now()->day : 0) }}₫
                </div>
                <div class="text-sm opacity-90">Tháng hiện tại</div>
            </div>
        </div>

        <!-- Line Chart - Chi phí theo thời gian -->
        <div class="chart-container">
            <h3 class="text-xl font-bold text-gray-900 mb-4">
                <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                Chi Phí Theo Thời Gian
            </h3>
            <div class="relative w-full h-[400px]">
                <canvas id="lineChart" class="absolute inset-0 w-full h-full"></canvas>
            </div>
        </div>

        <!-- Pie Chart - Tỷ lệ chi phí -->
        <div class="chart-container">
            <h3 class="text-xl font-bold text-gray-900 mb-4">
                <i class="fas fa-chart-pie text-green-600 mr-2"></i>
                Tỷ Lệ Chi Phí Tháng Này
            </h3>
            <div class="relative w-full h-[400px]">
                <canvas id="pieChart" class="absolute inset-0 w-full h-full"></canvas>
            </div>
        </div>

        <!-- Stacked Column Chart -->
        <div class="chart-container">
            <h3 class="text-xl font-bold text-gray-900 mb-4">
                <i class="fas fa-chart-bar text-purple-600 mr-2"></i>
                Chi Phí Theo Dịch Vụ (Theo Tháng)
            </h3>
            <div class="relative w-full h-[400px]">
                <canvas id="stackedChart" class="absolute inset-0 w-full h-full"></canvas>
            </div>
        </div>

        <!-- Details Table -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                <h3 class="text-2xl font-bold">
                    <i class="fas fa-table mr-2"></i>Chi Tiết Sử Dụng Dịch Vụ
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Ngày
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Dịch Vụ
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Sử Dụng
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Tổng Phòng
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Phần Bạn Trả
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Ghi Chú
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($detailsData as $detail)
                            <tr class="hover:bg-gray-50 transition {{ $detail['exceeds_quota'] ? 'exceed-quota' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $detail['date']->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                            <i class="fas fa-bolt text-blue-600 text-sm"></i>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ $detail['service_name'] }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="font-semibold">{{ number_format($detail['usage_amount'], 2) }}</span>
                                    <span class="text-gray-500 ml-1">{{ $detail['unit'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                                    {{ number_format($detail['subtotal']) }}₫
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                    <span class="font-bold text-blue-600">
                                        {{ number_format($detail['share_amount']) }}₫
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if ($detail['exceeds_quota'])
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Vượt định mức {{ number_format($detail['free_quota']) }} {{ $detail['unit'] }}
                                        </span>
                                    @else
                                        <span class="text-gray-500">Trong định mức</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                                    <p class="text-gray-500">Không có dữ liệu trong khoảng thời gian này</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($detailsData->count() > 0)
                        <tfoot class="bg-gray-50 font-bold">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-right text-gray-900">
                                    Tổng cộng:
                                </td>
                                <td class="px-6 py-4 text-right text-blue-600 text-lg">
                                    {{ number_format($detailsData->sum('share_amount')) }}₫
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
@endsection

@pushOnce('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Chuẩn bị dữ liệu
            const lineChartData = @json($lineChartData);
            const stackedChartData = @json($stackedChartData);
            const pieChartData = @json($pieChartData);
            const services = @json($services);

            // Màu sắc cho các dịch vụ
            const serviceColors = {
                'Điện': {
                    bg: 'rgba(255, 206, 86, 0.8)',
                    border: 'rgb(255, 206, 86)'
                },
                'Nước': {
                    bg: 'rgba(54, 162, 235, 0.8)',
                    border: 'rgb(54, 162, 235)'
                },
                'Internet': {
                    bg: 'rgba(153, 102, 255, 0.8)',
                    border: 'rgb(153, 102, 255)'
                },
                'Vệ sinh': {
                    bg: 'rgba(75, 192, 192, 0.8)',
                    border: 'rgb(75, 192, 192)'
                },
            };

            // Helper function để lấy màu
            function getColor(serviceName, index) {
                if (serviceColors[serviceName]) {
                    return serviceColors[serviceName];
                }
                const colors = [{
                        bg: 'rgba(255, 99, 132, 0.8)',
                        border: 'rgb(255, 99, 132)'
                    },
                    {
                        bg: 'rgba(255, 159, 64, 0.8)',
                        border: 'rgb(255, 159, 64)'
                    },
                    {
                        bg: 'rgba(201, 203, 207, 0.8)',
                        border: 'rgb(201, 203, 207)'
                    },
                ];
                return colors[index % colors.length];
            }

            // 1. LINE CHART
            const lineCtx = document.getElementById('lineChart').getContext('2d');
            const lineLabels = generateSmartLabels(lineChartData, 60);
            const lineTotalData = lineLabels.map(label => lineChartData[label].total);

            // Tạo datasets cho từng dịch vụ
            const serviceNames = new Set();
            Object.values(lineChartData).forEach(day => {
                Object.keys(day.services || {}).forEach(name => serviceNames.add(name));
            });

            const lineDatasets = [{
                label: 'Tổng chi phí',
                data: lineTotalData,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true
            }];

            Array.from(serviceNames).forEach((serviceName, index) => {
                const color = getColor(serviceName, index);
                lineDatasets.push({
                    label: serviceName,
                    data: lineLabels.map(label => lineChartData[label].services[serviceName] || 0),
                    borderColor: color.border,
                    backgroundColor: color.bg,
                    borderWidth: 2,
                    tension: 0.4,
                    hidden: false // Ẩn mặc định, chỉ hiện tổng
                });
            });

            new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: lineLabels,
                    datasets: lineDatasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' +
                                        new Intl.NumberFormat('vi-VN').format(context.parsed.y) + '₫';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('vi-VN').format(value) + '₫';
                                }
                            }
                        }
                    }
                }
            });

            // 2. PIE CHART
            const pieCtx = document.getElementById('pieChart').getContext('2d');
            const pieLabels = Object.keys(pieChartData);
            const pieValues = pieLabels.map(label => pieChartData[label].total);
            const pieColors = pieLabels.map((label, index) => getColor(label, index));

            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: pieLabels,
                    datasets: [{
                        data: pieValues,
                        backgroundColor: pieColors.map(c => c.bg),
                        borderColor: pieColors.map(c => c.border),
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return context.label + ': ' +
                                        new Intl.NumberFormat('vi-VN').format(context.parsed) +
                                        '₫ (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });

            // 3. STACKED COLUMN CHART
            const stackedCtx = document.getElementById('stackedChart').getContext('2d');
            const stackedLabels = generateSmartLabels(stackedChartData, 36);

            // Lấy tất cả tên dịch vụ
            const allServices = new Set();
            Object.values(stackedChartData).forEach(month => {
                Object.keys(month).forEach(service => allServices.add(service));
            });

            const stackedDatasets = Array.from(allServices).map((serviceName, index) => {
                const color = getColor(serviceName, index);
                return {
                    label: serviceName,
                    data: stackedLabels.map(month => stackedChartData[month][serviceName] || 0),
                    backgroundColor: color.bg,
                    borderColor: color.border,
                    borderWidth: 1
                };
            });

            new Chart(stackedCtx, {
                type: 'bar',
                data: {
                    labels: stackedLabels,
                    datasets: stackedDatasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            mode: 'index',
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' +
                                        new Intl.NumberFormat('vi-VN').format(context.parsed.y) + '₫';
                                },
                                footer: function(tooltipItems) {
                                    let total = 0;
                                    tooltipItems.forEach(item => total += item.parsed.y);
                                    return 'Tổng: ' + new Intl.NumberFormat('vi-VN').format(total) +
                                        '₫';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('vi-VN').format(value) + '₫';
                                }
                            }
                        }
                    }
                }
            });

            // Helper: Giới hạn số label, nếu quá nhiều thì group
            function generateSmartLabels(data, maxPoints = 60) {
                const keys = Object.keys(data).sort();
                if (keys.length <= maxPoints) return keys;

                // Nếu quá nhiều → group theo tuần (nếu là ngày) hoặc theo quý (nếu là tháng)
                const isMonthly = keys[0].length === 7; // Y-m
                const result = [];

                if (isMonthly) {
                    // Group theo quý (3 tháng 1 điểm)
                    for (let i = 0; i < keys.length; i += 3) {
                        const chunk = keys.slice(i, i + 3);
                        if (chunk.length === 1) {
                            result.push(chunk[0]);
                        } else {
                            result.push(`${chunk[0]} → ${chunk[chunk.length - 1]}`);
                        }
                    }
                } else {
                    // Group theo tuần
                    const dates = keys.map(k => new Date(k));
                    const grouped = [];
                    let currentWeek = null;

                    dates.forEach((date, i) => {
                        const weekStart = new Date(date);
                        weekStart.setDate(date.getDate() - date.getDay()); // Chủ nhật

                        const weekKey = weekStart.toISOString().slice(0, 10);
                        if (weekKey !== currentWeek) {
                            currentWeek = weekKey;
                            grouped.push(keys[i]);
                        }
                    });

                    // Nếu vẫn quá nhiều, lấy đều khoảng cách
                    if (grouped.length > maxPoints) {
                        const step = Math.ceil(grouped.length / maxPoints);
                        const final = [];
                        for (let i = 0; i < grouped.length; i += step) {
                            final.push(grouped[i]);
                        }
                        return final;
                    }
                    return grouped;
                }

                return result.slice(0, maxPoints);
            }
        });
    </script>
@endPushOnce
