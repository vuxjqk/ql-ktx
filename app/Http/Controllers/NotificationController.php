<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::orderBy('created_at', 'desc')
            ->paginate(10);

        return view('notifications.index', compact('notifications'));
    }

    public function store(Request $request)
    {
        $validated = $request->validateWithBag('notificationCreation', [
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'attachment' => 'nullable|file|max:4096',
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('notifications', 'public');
        }

        $validated['sender_id'] = Auth::id();

        Notification::create($validated);

        return redirect()->route('notifications.index')->with('success', __('Đã tạo thành công'));
    }

    public function destroy(Notification $notification)
    {
        try {
            if ($notification->attachment) {
                Storage::disk('public')->delete($notification->attachment);
            }

            $notification->delete();

            return redirect()->route('notifications.index')->with('success', __('Đã xoá thành công'));
        } catch (QueryException $e) {
            $msg = $e->getCode() === '23000'
                ? __('Không thể xóa vì có dữ liệu liên quan')
                : __('Đã xảy ra lỗi khi xoá');

            return redirect()->back()->with('error', $msg);
        }
    }
}
