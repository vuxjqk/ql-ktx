@extends('student.layouts.app')

@section('title', 'Trang chủ')

@section('content')
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 text-white overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-20"></div>
        <div class="absolute inset-0">
            <div
                class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1543785734-4b6e564642f8?ixlib=rb-4.0.3&auto=format&fit=crop&q=80')] bg-cover bg-center opacity-10">
            </div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    Chào mừng đến với <span class="text-yellow-300">Ký túc xá HUIT</span>
                </h1>
                <p class="text-xl md:text-2xl text-blue-100 mb-10 max-w-3xl mx-auto">
                    Nơi ở tiện nghi, an toàn và hiện đại dành riêng cho sinh viên.
                    Tìm phòng phù hợp và đặt chỗ chỉ trong vài phút!
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('student.rooms.index') }}"
                        class="inline-flex items-center px-8 py-4 bg-white text-blue-700 font-bold text-lg rounded-xl hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fas fa-search mr-3"></i>
                        Tìm phòng ngay
                    </a>
                    <a href="{{ route('student.bookings.index') }}"
                        class="inline-flex items-center px-8 py-4 bg-transparent border-2 border-white text-white font-bold text-lg rounded-xl hover:bg-white hover:text-blue-700 transition-all duration-300">
                        <i class="fas fa-calendar-check mr-3"></i>
                        Lịch sử đặt phòng
                    </a>
                </div>
            </div>
        </div>

        <!-- Wave bottom -->
        {{-- <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" class="w-full h-24 md:h-32 text-gray-50">
                <path fill="currentColor" d="M0,0 L1440,0 L1440,120 C1200,0 1000,120 720,80 C440,40 240,100 0,60 Z"></path>
            </svg>
        </div> --}}
    </div>

    <!-- Quick Stats -->
    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div class="bg-white rounded-xl shadow-md p-6 transform hover:scale-105 transition-transform duration-300">
                    <div class="text-4xl font-bold text-blue-600">{{ $totalRooms ?? 0 }}</div>
                    <p class="text-gray-600 mt-2">Tổng số phòng</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 transform hover:scale-105 transition-transform duration-300">
                    <div class="text-4xl font-bold text-green-600">{{ $availableRooms ?? 0 }}</div>
                    <p class="text-gray-600 mt-2">Phòng còn trống</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 transform hover:scale-105 transition-transform duration-300">
                    <div class="text-4xl font-bold text-purple-600">{{ $totalStudents ?? 0 }}+</div>
                    <p class="text-gray-600 mt-2">Sinh viên đang ở</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 transform hover:scale-105 transition-transform duration-300">
                    <div class="text-4xl font-bold text-orange-600">{{ $totalBranches ?? 0 }}</div>
                    <p class="text-gray-600 mt-2">Chi nhánh KTX</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Features -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">
                    Tại sao chọn <span class="text-blue-600">Ký túc xá HUIT</span>?
                </h2>
                <p class="text-lg text-gray-600 mt-4">Chúng tôi mang đến không gian sống lý tưởng cho sinh viên</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center group">
                    <div
                        class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 rounded-full mb-6 group-hover:bg-blue-600 transition-colors duration-300">
                        <i class="fas fa-shield-alt text-3xl text-blue-600 group-hover:text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">An toàn 24/7</h3>
                    <p class="text-gray-600">Hệ thống camera, bảo vệ trực 24/24, kiểm soát ra vào bằng thẻ từ</p>
                </div>

                <div class="text-center group">
                    <div
                        class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-6 group-hover:bg-green-600 transition-colors duration-300">
                        <i class="fas fa-wifi text-3xl text-green-600 group-hover:text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Tiện nghi hiện đại</h3>
                    <p class="text-gray-600">Wifi tốc độ cao, điều hòa, tủ lạnh, máy giặt, khu học tập chung</p>
                </div>

                <div class="text-center group">
                    <div
                        class="inline-flex items-center justify-center w-20 h-20 bg-purple-100 rounded-full mb-6 group-hover:bg-purple-600 transition-colors duration-300">
                        <i class="fas fa-handshake text-3xl text-purple-600 group-hover:text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Hỗ trợ tận tình</h3>
                    <p class="text-gray-600">Đội ngũ quản lý luôn sẵn sàng hỗ trợ sinh viên mọi lúc</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Rooms -->
    <div class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Phòng được quan tâm nhiều</h2>
                    <p class="text-gray-600 mt-2">Những phòng có lượt xem và yêu thích cao nhất</p>
                </div>
                <a href="{{ route('student.rooms.index') }}"
                    class="mt-4 md:mt-0 text-blue-600 hover:text-blue-700 font-semibold flex items-center">
                    Xem tất cả <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>

            @if ($popularRooms->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($popularRooms as $room)
                        <div
                            class="bg-white rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden group">
                            <div class="relative h-48 overflow-hidden">
                                @if ($room->images->count() > 0)
                                    <img src="{{ asset('storage/' . $room->images->first()->image_path) }}"
                                        alt="{{ $room->room_code }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div
                                        class="w-full h-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                                        <i class="fas fa-image text-blue-300 text-4xl"></i>
                                    </div>
                                @endif

                                @if ($room->current_occupancy < $room->capacity)
                                    <span
                                        class="absolute top-3 right-3 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow">
                                        Còn {{ $room->capacity - $room->current_occupancy }} chỗ
                                    </span>
                                @endif

                                <div
                                    class="absolute top-3 left-3 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-semibold">
                                    <i class="fas fa-fire text-orange-500 mr-1"></i> Hot
                                </div>
                            </div>

                            <div class="p-5">
                                <h3 class="text-lg font-bold text-gray-900">Phòng {{ $room->room_code }}</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    <i class="fas fa-map-marker-alt text-blue-500 mr-1"></i>
                                    {{ $room->floor->branch->name }}
                                </p>

                                <div class="flex items-center justify-between mt-4">
                                    <div>
                                        <p class="text-lg font-bold text-blue-600">
                                            {{ number_format($room->price_per_month) }}đ
                                            <span class="text-sm text-gray-500 font-normal">/tháng</span>
                                        </p>
                                        <p class="text-base font-semibold text-gray-700">
                                            {{ number_format($room->price_per_day) }}đ
                                            <span class="text-sm text-gray-500 font-normal">/ngày</span>
                                        </p>
                                    </div>
                                    <a href="{{ route('student.rooms.show', $room) }}"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-semibold">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <i class="fas fa-bed text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg">Chưa có phòng nào nổi bật</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Notifications -->
    @if ($recentNotifications->count() > 0)
        <div class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-bell mr-3 text-blue-600"></i>Thông báo mới
                    </h2>
                    <a href="{{ route('student.notifications.index') }}"
                        class="text-blue-600 hover:text-blue-700 font-medium">
                        Xem tất cả <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($recentNotifications as $notification)
                        <div
                            class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-600 rounded-r-lg p-6 hover:shadow-lg transition-shadow">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-info text-white text-xl"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-900">{{ $notification->title }}</h4>
                                    <p class="text-gray-600 mt-1 text-sm">
                                        {{ Str::limit($notification->content ?? 'Xem chi tiết để biết thêm thông tin', 100) }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-3">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- CTA Final -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 py-16">
        <div class="max-w-4xl mx-auto text-center px-6">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                Sẵn sàng tìm phòng mơ ước của bạn?
            </h2>
            <p class="text-xl text-blue-100 mb-10">
                Hàng trăm phòng chất lượng đang chờ bạn khám phá
            </p>
            <a href="{{ route('student.rooms.index') }}"
                class="inline-flex items-center px-10 py-5 bg-white text-blue-700 font-bold text-xl rounded-xl hover:bg-gray-100 transition-all duration-300 shadow-2xl hover:shadow-xl transform hover:-translate-y-1">
                <i class="fas fa-door-open mr-3"></i>
                Bắt đầu tìm phòng ngay
            </a>
        </div>
    </div>
@endsection
