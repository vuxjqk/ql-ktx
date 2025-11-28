<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceUsageShare;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceCostController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Lấy khoảng thời gian từ request hoặc mặc định tháng hiện tại
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        // Tính toán các chỉ số
        $currentMonthTotal = $this->getCurrentMonthTotal($userId);
        $lastMonthTotal = $this->getLastMonthTotal($userId);
        $percentageChange = $this->calculatePercentageChange($currentMonthTotal, $lastMonthTotal);
        $forecast = $this->calculateForecast($userId);

        // Dữ liệu cho biểu đồ Line Chart - Chi phí theo ngày/tháng
        $lineChartData = $this->getLineChartData($userId, $startDate, $endDate);

        // Dữ liệu cho Stacked Column Chart - Chi phí theo dịch vụ theo tháng
        $stackedChartData = $this->getStackedChartData($userId, $startDate, $endDate);

        // Dữ liệu cho Pie Chart - Tỷ lệ chi phí các dịch vụ tháng hiện tại
        $pieChartData = $this->getPieChartData($userId);

        // Bảng chi tiết
        $detailsData = $this->getDetailsData($userId, $startDate, $endDate);

        // Danh sách dịch vụ
        $services = Service::all();

        return view('student.service-costs.index', compact(
            'currentMonthTotal',
            'lastMonthTotal',
            'percentageChange',
            'forecast',
            'lineChartData',
            'stackedChartData',
            'pieChartData',
            'detailsData',
            'services',
            'startDate',
            'endDate'
        ));
    }

    private function getCurrentMonthTotal($userId)
    {
        return ServiceUsageShare::whereHas('serviceUsage', function ($q) {
            $q->whereMonth('usage_date', now()->month)
                ->whereYear('usage_date', now()->year);
        })
            ->where('user_id', $userId)
            ->sum('share_amount');
    }

    private function getLastMonthTotal($userId)
    {
        return ServiceUsageShare::whereHas('serviceUsage', function ($q) {
            $q->whereMonth('usage_date', now()->subMonth()->month)
                ->whereYear('usage_date', now()->subMonth()->year);
        })
            ->where('user_id', $userId)
            ->sum('share_amount');
    }

    private function calculatePercentageChange($current, $last)
    {
        if ($last == 0) return $current > 0 ? 100 : 0;
        return (($current - $last) / $last) * 100;
    }

    private function calculateForecast($userId)
    {
        $daysPassed = now()->day;
        $totalDaysInMonth = now()->daysInMonth;

        $currentTotal = $this->getCurrentMonthTotal($userId);

        if ($daysPassed == 0) return 0;

        // Dự báo = (tổng hiện tại / số ngày đã qua) * tổng số ngày trong tháng
        return ($currentTotal / $daysPassed) * $totalDaysInMonth;
    }

    private function getLineChartData($userId, $startDate, $endDate)
    {
        $diffInDays = $startDate->diffInDays($endDate);

        // Nếu khoảng thời gian > 60 ngày, group theo tháng, ngược lại theo ngày
        if ($diffInDays > 60) {
            return $this->getLineChartDataByMonth($userId, $startDate, $endDate);
        } else {
            return $this->getLineChartDataByDay($userId, $startDate, $endDate);
        }
    }

    private function getLineChartDataByDay($userId, $startDate, $endDate)
    {
        $data = ServiceUsageShare::with(['serviceUsage.service'])
            ->whereHas('serviceUsage', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('usage_date', [$startDate, $endDate]);
            })
            ->where('user_id', $userId)
            ->get()
            ->groupBy(function ($item) {
                return $item->serviceUsage->usage_date->format('Y-m-d');
            })
            ->map(function ($group) {
                return [
                    'total' => $group->sum('share_amount'),
                    'services' => $group->groupBy('serviceUsage.service.name')->map(function ($serviceGroup) {
                        return $serviceGroup->sum('share_amount');
                    })
                ];
            });

        return $data;
    }

    private function getLineChartDataByMonth($userId, $startDate, $endDate)
    {
        $data = ServiceUsageShare::with(['serviceUsage.service'])
            ->whereHas('serviceUsage', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('usage_date', [$startDate, $endDate]);
            })
            ->where('user_id', $userId)
            ->get()
            ->groupBy(function ($item) {
                return $item->serviceUsage->usage_date->format('Y-m');
            })
            ->map(function ($group) {
                return [
                    'total' => $group->sum('share_amount'),
                    'services' => $group->groupBy('serviceUsage.service.name')->map(function ($serviceGroup) {
                        return $serviceGroup->sum('share_amount');
                    })
                ];
            });

        return $data;
    }

    private function getStackedChartData($userId, $startDate, $endDate)
    {
        $data = ServiceUsageShare::with(['serviceUsage.service'])
            ->whereHas('serviceUsage', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('usage_date', [$startDate, $endDate]);
            })
            ->where('user_id', $userId)
            ->get()
            ->groupBy(function ($item) {
                return $item->serviceUsage->usage_date->format('Y-m');
            })
            ->map(function ($group) {
                return $group->groupBy('serviceUsage.service.name')->map(function ($serviceGroup) {
                    return $serviceGroup->sum('share_amount');
                });
            });

        return $data;
    }

    private function getPieChartData($userId)
    {
        $data = ServiceUsageShare::with(['serviceUsage.service'])
            ->whereHas('serviceUsage', function ($q) {
                $q->whereMonth('usage_date', now()->month)
                    ->whereYear('usage_date', now()->year);
            })
            ->where('user_id', $userId)
            ->get()
            ->groupBy('serviceUsage.service.name')
            ->map(function ($group) {
                return [
                    'total' => $group->sum('share_amount'),
                    'count' => $group->count()
                ];
            });

        return $data;
    }

    private function getDetailsData($userId, $startDate, $endDate)
    {
        return ServiceUsageShare::with(['serviceUsage.service', 'serviceUsage.room'])
            ->whereHas('serviceUsage', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('usage_date', [$startDate, $endDate]);
            })
            ->where('user_id', $userId)
            ->get()
            ->map(function ($share) {
                $usage = $share->serviceUsage;
                $service = $usage->service;

                // Kiểm tra có vượt định mức miễn phí không
                $exceedsFreeQuota = $service->free_quota > 0 && $usage->usage_amount > $service->free_quota;

                return [
                    'date' => $usage->usage_date,
                    'service_name' => $service->name,
                    'usage_amount' => $usage->usage_amount,
                    'unit' => $service->unit,
                    'subtotal' => $usage->subtotal,
                    'share_amount' => $share->share_amount,
                    'exceeds_quota' => $exceedsFreeQuota,
                    'free_quota' => $service->free_quota,
                    'unit_price' => $usage->unit_price,
                ];
            })
            ->sortByDesc('date')
            ->values();
    }
}
