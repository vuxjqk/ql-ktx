@extends('student.layouts.app')

@section('title', 'Phòng yêu thích')

@pushOnce('styles')
    <style>
        .empty-state {
            min-height: 60vh;
        }

        .room-card:hover .remove-favourite {
            opacity: 1;
        }

        .remove-favourite {
            opacity: 0;
            transition: opacity 0.2s;
        }
    </style>
@endPushOnce

@section('content')
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-pink-500 via-red-500 to-rose-600 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                <i class="fas fa-heart text-rose-200 mr-3"></i>
                Phòng yêu thích của bạn
            </h1>
            <p class="text-xl text-rose-100">
                Những căn phòng bạn đã lưu lại để xem sau • Tổng cộng: <strong>{{ $favourites->count() }}</strong> phòng
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if ($favourites->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($favourites as $room)
                    <div
                        class="bg-white rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden group">
                        <div class="relative h-48 overflow-hidden">
                            @if ($room->images->count() > 0)
                                <img src="{{ asset('storage/' . $room->images->first()->image_path) }}"
                                    alt="{{ $room->room_code }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div
                                    class="w-full h-full bg-gradient-to-br from-rose-100 to-pink-100 flex items-center justify-center">
                                    <i class="fas fa-image text-rose-300 text-5xl"></i>
                                </div>
                            @endif

                            @if ($room->current_occupancy < $room->capacity)
                                <span
                                    class="absolute top-3 right-3 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow">
                                    Còn {{ $room->capacity - $room->current_occupancy }} chỗ
                                </span>
                            @endif

                            <div class="absolute top-3 left-3 z-10">
                                <span class="bg-rose-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow">
                                    <i class="fas fa-heart mr-1"></i>Yêu thích
                                </span>
                            </div>
                        </div>

                        <div class="p-5">
                            <h3 class="text-lg font-bold text-gray-900">Phòng {{ $room->room_code }}</h3>
                            <p class="text-sm text-gray-600 mb-3 flex items-center">
                                <i class="fas fa-map-marker-alt text-rose-500 mr-2"></i>
                                {{ $room->floor->branch->name ?? 'N/A' }} - Tầng {{ $room->floor->floor_number ?? '?' }}
                            </p>

                            <div class="flex items-center justify-between mt-4">
                                <div>
                                    <p class="text-lg font-bold text-rose-600">
                                        {{ number_format($room->price_per_month) }}đ
                                        <span class="text-sm text-gray-500 font-normal">/tháng</span>
                                    </p>
                                    <p class="text-base font-semibold text-gray-600">
                                        {{ number_format($room->price_per_day) }}đ
                                        <span class="text-sm text-gray-500 font-normal">/ngày</span>
                                    </p>
                                </div>
                                <a href="{{ route('student.rooms.show', $room) }}"
                                    class="bg-rose-600 text-white px-4 py-2 rounded-lg hover:bg-rose-700 transition-colors text-sm font-semibold">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="empty-state flex flex-col items-center justify-center text-center py-20">
                <div class="bg-rose-50 rounded-full p-8 mb-8">
                    <i class="fas fa-heart text-rose-300 text-7xl"></i>
                </div>
                <h3 class="text-3xl font-bold text-gray-800 mb-4">Chưa có phòng nào được yêu thích</h3>
                <p class="text-lg text-gray-600 mb-8 max-w-md">
                    Khi bạn nhấn nút <i class="fas fa-heart text-rose-500"></i> ở trang chi tiết phòng, phòng đó sẽ xuất
                    hiện ở đây.
                </p>
                <a href="{{ route('student.rooms.index') }}"
                    class="inline-flex items-center px-8 py-4 bg-rose-600 text-white font-bold text-lg rounded-xl hover:bg-rose-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-search mr-3"></i>
                    Tìm phòng ngay bây giờ
                </a>
            </div>
        @endif
    </div>
@endsection

@pushOnce('scripts')
    <script>
        // Toast khi xóa thành công (nếu bạn dùng <x-toast />)
        document.addEventListener('livewire:load', function() {
            // Nếu dùng @livewireScripts, có thể bắt sự kiện xóa thành công
        });
    </script>
@endPushOnce
