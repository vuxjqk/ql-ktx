<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function confirm(Request $request, Contract $contract)
    {
        $user = $request->user();

        if ($contract->booking->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xác nhận hợp đồng này.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Hợp đồng đã được xác nhận thành công.',
            'contract' => $contract
        ]);
    }
}
