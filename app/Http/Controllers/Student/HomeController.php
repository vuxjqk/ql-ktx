<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Branch;
use App\Models\Notification;
use App\Models\Room;

class HomeController extends Controller
{
    public function index()
    {
        return view('student.index', [
            'totalRooms' => Room::count(),
            'availableRooms' => Room::whereColumn('current_occupancy', '<', 'capacity')->count(),
            'totalStudents' => Booking::where('status', 'active')->count(),
            'totalBranches' => Branch::count(),
            'popularRooms' => Room::with([
                'images' => fn($q) => $q->limit(1),
                'floor.branch'
            ])
                ->whereColumn('current_occupancy', '<', 'capacity')
                ->limit(8)
                ->get(),
            'recentNotifications' => Notification::latest()->limit(4)->get(),
        ]);
    }
}
