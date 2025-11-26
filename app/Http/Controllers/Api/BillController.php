<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $bills = Bill::where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'bills' => $bills
        ]);
    }
}
