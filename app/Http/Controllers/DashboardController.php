<?php

namespace App\Http\Controllers;

use App\Exports\StatisticsExport;
use App\Models\Amenity;
use App\Models\Booking;
use App\Models\Branch;
use App\Models\Repair;
use App\Models\Room;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function index()
    {
        $year = now()->year;

        $totalRevenue = $this->getTotalRevenue($year);
        $totalBookings = $this->getTotalBookings($year);
        $newStudents = $this->getNewStudents($year);
        $growthRate = $this->getRevenueGrowthRate($year, $totalRevenue);
        $monthlyRevenue = $this->getMonthlyRevenue($year);
        $bookingsByBranch = $this->getBookingsByBranch($year);
        $stayLeaveRatio = $this->getStayLeaveRatio($year);

        $pendingBookings = Booking::where('status', 'pending')->count();
        $approvedBookings = Booking::where('status', 'approved')->count();
        $rejectedBookings = Booking::where('status', 'rejected')->count();

        $pendingRepairs = Repair::where('status', 'pending')->count();
        $inProgressRepairs = Repair::where('status', 'in_progress')->count();
        $completedRepairs = Repair::where('status', 'completed')->count();

        $fullRooms = Room::whereRaw('current_occupancy >= capacity')->count();
        $emptyRooms = Room::whereRaw('current_occupancy = 0')->count();
        $missingRooms = Room::whereRaw('current_occupancy < capacity AND current_occupancy > 0')->count();

        $totalStudents = User::where('role', 'student')->count();
        $totalStaffs = User::whereIn('role', ['admin', 'staff'])->count();
        $totalBranches = Branch::count();
        $totalServices = Service::count();
        $totalAmenities = Amenity::count();

        return view('dashboard', [
            'year' => $year,
            'totalRevenue' => $totalRevenue,
            'revenueGrowthRate' => $growthRate,
            'totalBookings' => $totalBookings,
            'newStudents' => $newStudents,
            'monthlyRevenue' => $monthlyRevenue,
            'bookingsByBranch' => $bookingsByBranch,
            'stayLeaveRatio' => $stayLeaveRatio,
            'pendingBookings' => $pendingBookings,
            'approvedBookings' => $approvedBookings,
            'rejectedBookings' => $rejectedBookings,
            'pendingRepairs' => $pendingRepairs,
            'inProgressRepairs' => $inProgressRepairs,
            'completedRepairs' => $completedRepairs,
            'fullRooms' => $fullRooms,
            'emptyRooms' => $emptyRooms,
            'missingRooms' => $missingRooms,
            'totalStudents' => $totalStudents,
            'totalStaffs' => $totalStaffs,
            'totalBranches' => $totalBranches,
            'totalServices' => $totalServices,
            'totalAmenities' => $totalAmenities,
        ]);
    }

    public function statistics(Request $request)
    {
        $year = $request->year ?? now()->year;

        $totalRevenue = $this->getTotalRevenue($year);
        $totalBookings = $this->getTotalBookings($year);
        $newStudents = $this->getNewStudents($year);
        $growthRate = $this->getRevenueGrowthRate($year, $totalRevenue);
        $monthlyRevenue = $this->getMonthlyRevenue($year);
        $bookingsByBranch = $this->getBookingsByBranch($year);
        $stayLeaveRatio = $this->getStayLeaveRatio($year);

        return view('statistics', [
            'year' => $year,
            'totalRevenue' => $totalRevenue,
            'revenueGrowthRate' => $growthRate,
            'totalBookings' => $totalBookings,
            'newStudents' => $newStudents,
            'monthlyRevenue' => $monthlyRevenue,
            'bookingsByBranch' => $bookingsByBranch,
            'stayLeaveRatio' => $stayLeaveRatio,
        ]);
    }

    public function reports()
    {
        return Excel::download(new StatisticsExport(request()->input('year', now()->year)), 'reports.xlsx');
    }

    // Tính tổng doanh thu trong năm
    public function getTotalRevenue($year)
    {
        return round(DB::table('bills')
            ->whereIn('status', ['paid', 'partial'])
            ->whereYear('created_at', $year)
            ->sum('total_amount') / 1_000_000, 2);
    }

    // Tính tổng số lượt đặt trong năm
    public function getTotalBookings($year)
    {
        return DB::table('bookings')
            ->whereIn('status', ['approved', 'active', 'expired', 'terminated'])
            ->whereYear('created_at', $year)
            ->count();
    }

    // Đếm số học viên mới trong năm
    public function getNewStudents($year)
    {
        return DB::table('bookings as b')
            ->select('b.user_id')
            ->whereYear('b.created_at', $year)
            ->whereRaw('b.id = (SELECT MIN(id) FROM bookings WHERE user_id = b.user_id)')
            ->distinct()
            ->count('b.user_id');
    }

    // Tính tỷ lệ tăng trưởng doanh thu theo năm
    public function getRevenueGrowthRate($year, $currentRevenue)
    {
        $previousRevenue = round(DB::table('bills')
            ->whereYear('created_at', $year - 1)
            ->whereIn('status', ['paid', 'partial'])
            ->sum('total_amount') / 1_000_000, 2);

        if ($previousRevenue == 0) {
            return null;
        }

        return round((($currentRevenue - $previousRevenue) / $previousRevenue) * 100, 2);
    }

    // Lấy dữ liệu doanh thu theo từng tháng
    public function getMonthlyRevenue($year)
    {
        $revenues = DB::table('bills')
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->whereYear('created_at', $year)
            ->whereIn('status', ['paid', 'partial'])
            ->groupByRaw('MONTH(created_at)')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $result = [];
        for ($i = 1; $i <= 12; $i++) {
            $result["Tháng $i"] = isset($revenues[$i])
                ? round($revenues[$i] / 1_000_000, 2)
                : 0;
        }

        return $result;
    }

    // Lấy số lượt đặt theo từng chi nhánh
    public function getBookingsByBranch($year)
    {
        $bookings = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->join('floors', 'rooms.floor_id', '=', 'floors.id')
            ->join('branches', 'floors.branch_id', '=', 'branches.id')
            ->selectRaw('branches.name as branch_name, COUNT(bookings.id) as total_bookings')
            ->whereYear('bookings.created_at', $year)
            ->groupBy('branches.name')
            ->orderByDesc('total_bookings')
            ->pluck('total_bookings', 'branch_name')
            ->toArray();

        return $bookings;
    }

    // Tính tỷ lệ học viên ở lại và rời đi
    public function getStayLeaveRatio($year)
    {
        $newStudents = DB::table('bookings')
            ->where('booking_type', 'registration')
            ->whereYear('created_at', $year)
            ->distinct('user_id')
            ->count('user_id');

        $stayedStudents = DB::table('bookings')
            ->whereIn('booking_type', ['extension', 'transfer'])
            ->whereYear('created_at', $year)
            ->distinct('user_id')
            ->count('user_id');

        $leftStudents = DB::table('bookings')
            ->whereYear('actual_check_out_date', $year)
            ->whereNotNull('actual_check_out_date')
            ->distinct('user_id')
            ->count('user_id');

        return [
            'Sinh viên mới' => $newStudents,
            'Sinh viên ở lại' => $stayedStudents,
            'Sinh viên rời đi' => $leftStudents,
        ];
    }
}
