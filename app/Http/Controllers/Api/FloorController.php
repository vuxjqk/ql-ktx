<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Floor;
use Illuminate\Http\Request;

class FloorController extends Controller
{
    public function getByBranch($branchId)
    {
        return Floor::where('branch_id', $branchId)->pluck('floor_number', 'id');
    }
}
