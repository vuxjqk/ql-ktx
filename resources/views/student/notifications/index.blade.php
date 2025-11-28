{{-- resources/views/student/notifications/index.blade.php --}}
@extends('student.layouts.app')

@section('title', 'Thông báo')

@pushOnce('styles')
    <style>
        .notification-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .notification-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .notification-card.unread {
            border-left-color: #3B82F6;
            background: linear-gradient(to right, #EBF4FF 0%, white 10%, white 100%);
        }

        .notification-card.unread .title {
            font-weight: 700;
        }
    </style>
@endPushOnce

@section('content')
    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-white mb-4">Thông báo</h1>
                    <p class="text-blue-100 text-lg">Cập nhật tin tức, quy định và thông tin quan trọng từ Ban quản lý KTX
                    </p>
                </div>
                @if ($unreadCount > 0)
                    <div class="bg-white/20 backdrop-blur-sm rounded-full px-6 py-3">
                        <span class="text-white font-bold text-2xl">{{ $unreadCount }}</span>
                        <span class="text-blue-100 ml-2">chưa đọc</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Summary & Actions -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-6 text-sm">
                <span class="text-gray-600">
                    Tổng: <strong class="text-gray-900">{{ $notifications->total() }}</strong> thông báo
                </span>
                <span class="text-gray-600">
                    Chưa đọc: <strong class="text-blue-600">{{ $unreadCount }}</strong>
                </span>
            </div>

            <div class="flex space-x-3">
                @if ($unreadCount > 0)
                    <form action="{{ route('student.notifications.markAllRead') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium flex items-center">
                            <i class="fas fa-check-double mr-2"></i>Đánh dấu tất cả đã đọc
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Notification List -->
        <div class="space-y-6">
            @forelse($notifications as $notification)
                <div
                    class="notification-card bg-white rounded-xl shadow-md overflow-hidden {{ $notification->is_read ? '' : 'unread' }}">
                    <a href="{{ route('student.notifications.show', $notification) }}" class="block p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    @if (!$notification->is_read)
                                        <span class="flex-shrink-0 w-3 h-3 bg-blue-600 rounded-full"></span>
                                    @endif
                                    <h3 class="text-lg title text-gray-900">
                                        {{ $notification->title }}
                                    </h3>
                                </div>

                                @if ($notification->content)
                                    <p class="text-gray-600 line-clamp-2">
                                        {{ Str::limit(strip_tags($notification->content), 150) }}
                                    </p>
                                @endif

                                <div class="flex items-center gap-4 mt-4 text-sm text-gray-500">
                                    <span>
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                    <span>
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $notification->created_at->format('d/m/Y H:i') }}
                                    </span>

                                    @if ($notification->attachment)
                                        <span class="text-blue-600 flex items-center">
                                            <i class="fas fa-paperclip mr-1"></i> Có tệp đính kèm
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex-shrink-0 text-right">
                                @if (!$notification->is_read)
                                    <span
                                        class="inline-block w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-envelope text-blue-600"></i>
                                    </span>
                                @else
                                    <span
                                        class="inline-block w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-envelope-open text-gray-400"></i>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-md p-16 text-center">
                    <i class="fas fa-bell-slash text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Chưa có thông báo nào</h3>
                    <p class="text-gray-600">Thông báo mới sẽ được hiển thị tại đây</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($notifications->hasPages())
            <div class="mt-10">
                {{ $notifications->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection
