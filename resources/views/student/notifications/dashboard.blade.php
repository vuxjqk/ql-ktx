@php
    $userId = Auth::id();

    $notifications = \App\Models\Notification::where('user_id', $userId)
        ->orWhereNull('user_id')
        ->latest()
        ->take(4)
        ->get()
        ->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'is_read' => $notification->is_read,
                'created_at' => $notification->created_at->diffForHumans(),
            ];
        });

    $unreadCount = \App\Models\Notification::where(function ($query) use ($userId) {
        $query->where('user_id', $userId)->orWhereNull('user_id');
    })
        ->whereDoesntHave('reads', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->count();
@endphp

<!-- Notifications -->
<div class="relative" x-data="{ open: false }">
    <button @click="open = !open"
        class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
        <i class="fas fa-bell text-lg"></i>
        @if (isset($unreadNotifications) && $unreadNotifications > 0)
            <span
                class="absolute top-1 right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">
                {{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}
            </span>
        @endif
    </button>

    <!-- Notification Dropdown -->
    <div x-show="open" @click.away="open = false" x-cloak
        class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900">Thông báo</h3>
        </div>
        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications ?? [] as $notification)
                <a href="{{ route('student.notifications.show', $notification['id']) }}"
                    class="block p-4 hover:bg-gray-50 border-b border-gray-100 transition-colors duration-200">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-info-circle text-blue-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $notification['title'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $notification['created_at'] }}</p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-bell-slash text-3xl mb-2"></i>
                    <p class="text-sm">Không có thông báo mới</p>
                </div>
            @endforelse
        </div>
        <div class="p-3 text-center border-t border-gray-200">
            <a href="{{ route('student.notifications.index') }}"
                class="text-sm font-medium text-blue-600 hover:text-blue-700">
                Xem tất cả
            </a>
        </div>
    </div>
</div>
