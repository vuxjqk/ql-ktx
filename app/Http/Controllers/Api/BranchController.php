<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    public function index()
    {
        $branches = DB::table('branches')
            ->select('id', 'name', 'address')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $branches
        ]);
    }
}
