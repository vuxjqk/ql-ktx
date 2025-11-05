<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function me(Request $request)
    {
        $u = $request->user()->load([
            'student',
            'branches',
            'activeBooking.room',
            'bills',
        ]);
        return response()->json($u);
    }
}
