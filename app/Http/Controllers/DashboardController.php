<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Repair;
use App\Models\Room;
use App\Models\RoomAssignment;
use App\Models\Branch;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $data = [];

        // Dữ liệu cho admin/staff
        if (Auth::user()->role !== 'student') {
            // Tổng doanh thu (tổng amount từ bills với status = 'paid')
            $data['totalRevenue'] = Bill::where('status', 'paid')->sum('amount');

            // Số phòng đang sử dụng
            $data['occupiedRooms'] = Room::where('current_occupancy', '>', 0)->count();

            // Số yêu cầu sửa chữa đang mở
            $data['openRepairs'] = Repair::where('status', 'open')->count();

            // Doanh thu hàng tháng (12 tháng gần nhất)
            $data['monthlyRevenue'] = Bill::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('SUM(amount) as total')
            )
                ->where('status', 'paid')
                ->where('created_at', '>=', now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // Top 5 phòng được thuê nhiều
            $data['popularRooms'] = RoomAssignment::select(
                'rooms.room_code',
                DB::raw('COUNT(room_assignments.id) as count')
            )
                ->join('rooms', 'room_assignments.room_id', '=', 'rooms.id')
                ->groupBy('rooms.id', 'rooms.room_code')
                ->orderBy('count', 'desc')
                ->take(5)
                ->get();

            // Doanh thu theo chi nhánh
            $data['branchRevenue'] = Branch::select(
                'branches.name',
                DB::raw('SUM(bills.amount) as total')
            )
                ->join('rooms', 'branches.id', '=', 'rooms.branch_id')
                ->join('room_assignments', 'rooms.id', '=', 'room_assignments.room_id')
                ->join('bills', 'room_assignments.id', '=', 'bills.room_assignment_id')
                ->where('bills.status', 'paid')
                ->groupBy('branches.id', 'branches.name')
                ->orderBy('total', 'desc')
                ->get();
        }

        // Dữ liệu chung: thông báo chưa đọc
        $data['unreadNotifications'] = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return view('dashboard.index', $data);
    }
}
