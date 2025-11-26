<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favourite;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_rooms' => Room::where('is_active', true)->count(),
            'available_rooms' => Room::where('is_active', true)
                ->whereRaw('current_occupancy < capacity')
                ->count(),
            'total_branches' => DB::table('branches')->count(),
            'favourites' => Favourite::where('user_id', Auth::id())->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
