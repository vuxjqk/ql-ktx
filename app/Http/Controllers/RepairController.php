<?php

namespace App\Http\Controllers;

use App\Models\Repair;
use Illuminate\Http\Request;

class RepairController extends Controller
{
    public function index(Request $request)
    {
        $repairs = Repair::with(['user.student', 'room.floor.branch'])
            ->filter($request->all())
            ->paginate(10)
            ->appends($request->query());

        $totalRepairs = Repair::count();
        $pendingRepairs = Repair::where('status', 'pending')->count();
        $inProgressRepairs = Repair::where('status', 'in_progress')->count();
        $completedRepairs = Repair::where('status', 'completed')->count();

        return view('repairs.index', compact(
            'repairs',
            'totalRepairs',
            'pendingRepairs',
            'inProgressRepairs',
            'completedRepairs'
        ));
    }

    public function update(Repair $repair)
    {
        $statusFlow = ['pending', 'in_progress', 'completed'];

        $statusLabels = [
            'pending' => __('Chờ xử lý'),
            'in_progress' => __('Đang xử lý'),
            'completed' => __('Hoàn thành'),
        ];

        $currentIndex = array_search($repair->status, $statusFlow);

        if ($currentIndex === false) {
            return redirect()->back()
                ->with('error', __('Trạng thái hiện tại không hợp lệ: :status', ['status' => $repair->status]));
        }

        $nextStatus = $statusFlow[$currentIndex + 1] ?? null;

        if (!$nextStatus) {
            return redirect()->back()
                ->with('warning', __('Đã ở trạng thái cuối cùng'));
        }

        $repair->update(['status' => $nextStatus]);

        return redirect()->back()
            ->with('success', __('Trạng thái đã được cập nhật thành: :status', ['status' => $statusLabels[$nextStatus]]));
    }
}
