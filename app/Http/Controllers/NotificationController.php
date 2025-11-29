<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::latest()
            ->paginate(10);

        $users = User::where('role', 'student')
            ->with('student:id,user_id,student_code')
            ->get()
            ->mapWithKeys(function ($user) {
                $label = $user->student
                    ? "{$user->name} - MSSV: {$user->student->student_code}"
                    : "{$user->name} - Email: {$user->email}";

                return [$user->id => $label];
            })->toArray();

        return view('notifications.index', compact('notifications', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validateWithBag('notificationCreation', [
            'user_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'attachment' => 'nullable|file|max:4096',
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('notifications', 'public');
        }

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
