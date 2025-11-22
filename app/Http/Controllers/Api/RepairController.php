<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Repair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RepairController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        $activeBooking = $user->bookings()->where('status', 'active')->first();

        if (!$activeBooking) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chỉ có thể báo cáo sửa chữa cho phòng mà bạn đang ở.'
            ], 403);
        }

        $data = [
            'user_id' => $user->id,
            'room_id' => $activeBooking->room_id,
            'description' => $request->description,
            'status' => 'pending',
        ];

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('repairs', 'public');
        }

        $repair = Repair::create($data);

        return response()->json([
            'success' => true,
            'repair' => $repair
        ], 201);
    }

    public function destroy(Request $request, Repair $repair)
    {
        if ($repair->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xoá báo cáo này.'
            ], 403);
        }

        if ($repair->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xoá báo cáo này.'
            ], 403);
        }

        if ($repair->image_path) {
            Storage::disk('public')->delete($repair->image_path);
        }

        $repair->delete();

        return response()->json([
            'success' => true,
            'message' => 'Báo cáo đã được xoá.'
        ], 200);
    }
}
