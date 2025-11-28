<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Repair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RepairController extends Controller
{
    public function store(Request $request)
    {
        $userId = Auth::id();

        $booking = Booking::where('user_id', $userId)
            ->where('status', 'active')
            ->first();

        if (!$booking) {
            return redirect()->back()->with('error', 'Bạn không có booking đang hoạt động.');
        }

        $validated = $request->validate([
            'description' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        $image_path = null;
        if ($request->hasFile('image')) {
            $image_path = $request->file('image')->store('repairs', 'public');
        }

        Repair::create([
            'user_id' => $userId,
            'room_id' => $booking->room_id,
            'description' => $validated['description'],
            'image_path' => $image_path,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Yêu cầu sửa chữa đã được gửi.');
    }
}
