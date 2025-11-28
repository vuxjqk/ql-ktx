{{-- resources/views/student/notifications/show.blade.php --}}
@extends('student.layouts.app')

@section('title', $notification->title)

@pushOnce('styles')
    <style>
        .content img {
            max-width: 100%;
            border-radius: 0.5rem;
            margin: 1rem 0;
        }
    </style>
@endPushOnce

@section('content')
    <!-- Breadcrumb -->
    <div class="bg-gray-50 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-2 text-sm">
                    <li><a href="{{ route('student.home') }}" class="text-gray-600 hover:text-blue-600"><i
                                class="fas fa-home"></i></a></li>
                    <li><i class="fas fa-chevron-right text-gray-400 text-xs"></i></li>
                    <li><a href="{{ route('student.notifications.index') }}" class="text-gray-600 hover:text-blue-600">Thông
                            báo</a></li>
                    <li><i class="fas fa-chevron-right text-gray-400 text-xs"></i></li>
                    <li class="text-gray-900 font-medium truncate max-w-xs">{{ Str::limit($notification->title, 40) }}</li>
                </ol>
            </nav>
        </div>
    </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-8">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-3xl font-bold mb-3">{{ $notification->title }}</h1>
                        <div class="flex items-center gap-4 text-blue-100 text-sm">
                            <span><i class="fas fa-clock mr-1"></i>
                                {{ $notification->created_at->format('d/m/Y H:i') }}</span>
                            <span><i class="fas fa-user mr-1"></i> Ban quản lý KTX</span>
                        </div>
                    </div>
                    @if (!$notification->is_read)
                        <span class="bg-white/30 backdrop-blur-sm px-4 py-2 rounded-full text-sm font-medium">
                            Mới
                        </span>
                    @endif
                </div>
            </div>

            <!-- Content -->
            <div class="p-8 prose prose-lg max-w-none">
                @if ($notification->content)
                    <div class="content text-gray-700 leading-relaxed">
                        {!! nl2br(e($notification->content)) !!}
                    </div>
                @else
                    <p class="text-gray-500 italic">Không có nội dung chi tiết.</p>
                @endif

                <!-- Attachment -->
                @if ($notification->attachment)
                    <div class="mt-10 p-6 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-file-pdf text-blue-600 text-3xl"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Tệp đính kèm</p>
                                    <p class="text-sm text-gray-600">{{ basename($notification->attachment) }}</p>
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $notification->attachment) }}" target="_blank"
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center">
                                <i class="fas fa-download mr-2"></i> Tải xuống
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Footer Actions -->
            <div class="bg-gray-50 px-8 py-6 border-t border-gray-200 flex justify-between items-center">
                <a href="{{ route('student.notifications.index') }}"
                    class="flex items-center text-gray-600 hover:text-blue-600 transition font-medium">
                    <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
                </a>

                <div class="flex space-x-3">
                    @if (!$notification->is_read)
                        <form action="{{ route('student.notifications.markRead', $notification) }}" method="POST"
                            class="inline">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium">
                                <i class="fas fa-check mr-2"></i>Đánh dấu đã đọc
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Back to top -->
        <div class="mt-8 text-center">
            <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
                class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                <i class="fas fa-arrow-up mr-2"></i> Lên đầu trang
            </button>
        </div>
    </div>
@endsection
