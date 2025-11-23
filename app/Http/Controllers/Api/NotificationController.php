<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $perPage = min($request->integer('per_page', 10) ?? 10, 50);

        $notifications = Notification::with('sender:id,name,avatar')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $notifications->getCollection()->transform(fn($notification) => $this->transform($notification));

        return response()->json($notifications);
    }

    public function show(Notification $notification)
    {
        $notification->load('sender:id,name,avatar');

        return response()->json($this->transform($notification));
    }

    protected function transform(Notification $notification): array
    {
        return [
            'id' => $notification->id,
            'title' => $notification->title,
            'content' => $notification->content,
            'attachment' => $notification->attachment
                ? Storage::disk('public')->url($notification->attachment)
                : null,
            'sender' => $notification->sender ? [
                'id' => $notification->sender->id,
                'name' => $notification->sender->name,
                'avatar' => $notification->sender->avatar,
            ] : null,
            'created_at' => $notification->created_at,
            'updated_at' => $notification->updated_at,
        ];
    }
}
