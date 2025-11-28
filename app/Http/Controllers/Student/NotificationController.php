<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationRead;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getNotifications()
    {
        $userId = Auth::id();

        $notifications = Notification::where('user_id', $userId)
            ->orWhereNull('user_id')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            });

        $unreadCount = Notification::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->orWhereNull('user_id');
        })
            ->whereDoesntHave('reads', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function index()
    {
        $userId = Auth::id();

        $notifications = Notification::where('user_id', $userId)
            ->orWhereNull('user_id')
            ->latest()
            ->paginate(10);

        $unreadCount = Notification::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->orWhereNull('user_id');
        })
            ->whereDoesntHave('reads', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->count();

        return view('student.notifications.index', compact('notifications', 'unreadCount'));
    }

    public function show(Notification $notification)
    {
        $this->markRead($notification);

        return view('student.notifications.show', compact('notification'));
    }

    public function markRead(Notification $notification)
    {
        $userId = Auth::id();

        NotificationRead::firstOrCreate([
            'user_id' => $userId,
            'notification_id' => $notification->id,
        ]);

        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        $userId = Auth::id();

        $unreadNotifications = Notification::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->orWhereNull('user_id');
        })
            ->whereDoesntHave('reads', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->pluck('id');

        $data = $unreadNotifications->map(function ($notificationId) use ($userId) {
            return [
                'user_id' => $userId,
                'notification_id' => $notificationId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        NotificationRead::insert($data);

        return back()->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc!');
    }
}
