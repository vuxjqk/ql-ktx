<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Branch;
use App\Models\Notification;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        return view('student.index', [
            'totalRooms'         => Room::count(),
            'availableRooms'     => Room::where('current_occupancy', '<', DB::raw('capacity'))->count(),
            'totalStudents'      => Booking::distinct('user_id')->count('user_id'),
            'branches'           => Branch::all(),
            'popularRooms'       => Room::with(['images', 'floor.branch'])
                ->where('current_occupancy', '<', DB::raw('capacity'))
                ->orWhere->orderByDesc('favourites_count')
                ->limit(8)->get(),
            'recentNotifications' => Notification::latest()->limit(10)->get(),
        ]);
    }
}
