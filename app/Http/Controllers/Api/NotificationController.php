<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::latest();

        if ($request->has('limit')) {
            $query->limit($request->limit);
        }

        $notifications = $query->get();

        // Get read notifications for current user
        $readNotifications = DB::table('notification_reads')
            ->where('user_id', Auth::id())
            ->pluck('notification_id')
            ->toArray();

        $data = $notifications->map(function ($notification) use ($readNotifications) {
            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'content' => $notification->content,
                'attachment' => $notification->attachment,
                'created_at' => $notification->created_at->format('d/m/Y H:i'),
                'time' => $notification->created_at->diffForHumans(),
                'read' => in_array($notification->id, $readNotifications)
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function markAsRead(Request $request, Notification $notification)
    {
        DB::table('notification_reads')->updateOrInsert([
            'user_id' => $request->user()->id,
            'notification_id' => $notification->id
        ], [
            'created_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu là đã đọc'
        ]);
    }

    public function markAllAsRead()
    {
        $notifications = Notification::pluck('id');

        foreach ($notifications as $notificationId) {
            DB::table('notification_reads')->updateOrInsert([
                'user_id' => Auth::id(),
                'notification_id' => $notificationId
            ], [
                'created_at' => now()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu tất cả là đã đọc'
        ]);
    }
}
