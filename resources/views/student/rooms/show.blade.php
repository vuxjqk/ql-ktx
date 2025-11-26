@extends('student.layouts.app')

@section('title', 'Chi tiết phòng - ' . $room->room_code)

@push('styles')
    <style>
        .gallery-main {
            height: 500px;
        }

        .gallery-thumbs {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
        }

        .gallery-thumb {
            height: 100px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .gallery-thumb:hover {
            transform: scale(1.05);
        }

        .gallery-thumb.active {
            border: 3px solid #3B82F6;
        }
    </style>
@endpush

@section('content')
    <!-- Breadcrumb -->
    <div class="bg-gray-50 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-2 text-sm">
                    <li>
                        <a href="#" class="text-gray-600 hover:text-blue-600">
                            <i class="fas fa-home"></i>
                        </a>
                    </li>
                    <li><i class="fas fa-chevron-right text-gray-400 text-xs"></i></li>
                    <li>
                        <a href="#" class="text-gray-600 hover:text-blue-600">
                            Danh sách phòng
                        </a>
                    </li>
                    <li><i class="fas fa-chevron-right text-gray-400 text-xs"></i></li>
                    <li class="text-gray-900 font-medium">{{ $room->room_code }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $room->room_code }}</h1>
                        @if ($room->current_occupancy < $room->capacity)
                            <span class="bg-green-500 text-white text-sm font-semibold px-4 py-1 rounded-full">
                                <i class="fas fa-check-circle mr-1"></i>Còn trống
                            </span>
                        @else
                            <span class="bg-red-500 text-white text-sm font-semibold px-4 py-1 rounded-full">
                                <i class="fas fa-times-circle mr-1"></i>Đã đầy
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center text-gray-600 space-x-4">
                        <span>
                            <i class="fas fa-map-marker-alt mr-1 text-blue-500"></i>
                            {{ $room->floor->branch->name ?? 'N/A' }} - Tầng {{ $room->floor->floor_number ?? 'N/A' }}
                        </span>
                        @if ($room->reviews_avg_rating)
                            <span class="flex items-center">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <span class="font-semibold">{{ number_format($room->reviews_avg_rating, 1) }}</span>
                                <span class="text-gray-500 ml-1">({{ $room->reviews_count }} đánh giá)</span>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <form action="#" method="POST">
                        @csrf
                        <button type="submit"
                            class="flex items-center space-x-2 px-4 py-2 bg-white border-2 border-gray-300 rounded-lg hover:border-red-500 hover:text-red-500 transition-all duration-200">
                            <i class="fas fa-heart {{ $room->is_favourited ?? false ? 'text-red-500' : '' }}"></i>
                            <span class="font-medium">{{ $room->is_favourited ?? false ? 'Đã lưu' : 'Lưu' }}</span>
                        </button>
                    </form>
                    <button
                        onclick="if(navigator.share){navigator.share({title:'{{ $room->room_code }}',url:window.location.href})}else{navigator.clipboard.writeText(window.location.href);alert('Đã sao chép link!')}"
                        class="flex items-center space-x-2 px-4 py-2 bg-white border-2 border-gray-300 rounded-lg hover:border-blue-500 hover:text-blue-500 transition-all duration-200">
                        <i class="fas fa-share-alt"></i>
                        <span class="font-medium">Chia sẻ</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Image Gallery -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div x-data="{
                        currentImage: 0,
                        images: {{ json_encode($room->images->pluck('image_path')->toArray()) }}
                    }">
                        <!-- Main Image -->
                        <div class="gallery-main overflow-hidden">
                            @if ($room->images && $room->images->count() > 0)
                                <img :src="'{{ asset('storage') . '/' }}' + images[currentImage]"
                                    alt="{{ $room->room_code }}" class="w-full h-full object-cover">
                            @else
                                <div
                                    class="w-full h-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                                    <i class="fas fa-image text-blue-300 text-6xl"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Thumbnails -->
                        @if ($room->images && $room->images->count() > 1)
                            <div class="gallery-thumbs p-4">
                                @foreach ($room->images as $index => $image)
                                    <div @click="currentImage = {{ $index }}"
                                        class="gallery-thumb rounded-lg overflow-hidden"
                                        :class="{ 'active': currentImage === {{ $index }} }">
                                        <img src="{{ asset('storage/' . $image->image_path) }}"
                                            alt="Thumbnail {{ $index + 1 }}" class="w-full h-full object-cover">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Room Details -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">
                        <i class="fas fa-info-circle mr-2 text-blue-600"></i>Thông tin phòng
                    </h2>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mb-6">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <i class="fas fa-users text-blue-600 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-600">Sức chứa</p>
                            <p class="text-lg font-bold text-gray-900">{{ $room->capacity }} người</p>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <i class="fas fa-user-check text-green-600 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-600">Đang ở</p>
                            <p class="text-lg font-bold text-gray-900">{{ $room->current_occupancy }} người</p>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <i class="fas fa-door-open text-purple-600 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-600">Còn trống</p>
                            <p class="text-lg font-bold text-gray-900">{{ $room->capacity - $room->current_occupancy }} chỗ
                            </p>
                        </div>
                        <div class="text-center p-4 bg-indigo-50 rounded-lg">
                            <i class="fas fa-venus-mars text-indigo-600 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-600">Loại phòng</p>
                            <p class="text-lg font-bold text-gray-900">
                                {{ $room->floor->gender_type === 'male' ? 'Nam' : ($room->floor->gender_type === 'female' ? 'Nữ' : 'Hỗn hợp') }}
                            </p>
                        </div>
                        <div class="text-center p-4 bg-orange-50 rounded-lg">
                            <i class="fas fa-building text-orange-600 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-600">Chi nhánh</p>
                            <p class="text-lg font-bold text-gray-900">{{ $room->floor->branch->name ?? 'N/A' }}</p>
                        </div>
                        <div class="text-center p-4 bg-red-50 rounded-lg">
                            <i class="fas fa-layer-group text-red-600 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-600">Tầng</p>
                            <p class="text-lg font-bold text-gray-900">Tầng {{ $room->floor->floor_number ?? 'N/A' }}</p>
                        </div>
                    </div>

                    @if ($room->description)
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Mô tả</h3>
                            <p class="text-gray-600 leading-relaxed">{{ $room->description }}</p>
                        </div>
                    @endif
                </div>

                <!-- Amenities -->
                @if ($room->amenities && $room->amenities->count() > 0)
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">
                            <i class="fas fa-sparkles mr-2 text-blue-600"></i>Tiện nghi
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($room->amenities as $amenity)
                                <div
                                    class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                    <i class="fas fa-check-circle text-green-500 mt-1"></i>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $amenity->name }}</p>
                                        @if ($amenity->description)
                                            <p class="text-sm text-gray-600">{{ $amenity->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Services -->
                @if ($room->services && $room->services->count() > 0)
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">
                            <i class="fas fa-concierge-bell mr-2 text-blue-600"></i>Dịch vụ
                        </h2>
                        <div class="space-y-3">
                            @foreach ($room->services as $service)
                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900">{{ $service->name }}</p>
                                        <p class="text-sm text-gray-600">
                                            Đơn giá: {{ number_format($service->unit_price) }}đ/{{ $service->unit }}
                                            @if ($service->free_quota > 0)
                                                <span class="ml-2 text-green-600">
                                                    (Miễn phí {{ $service->free_quota }} {{ $service->unit }})
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                    @if ($service->is_mandatory)
                                        <span class="bg-red-100 text-red-700 text-xs font-semibold px-3 py-1 rounded-full">
                                            Bắt buộc
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Reviews -->
                <div class="bg-white rounded-xl shadow-md p-6" id="reviews">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">
                            <i class="fas fa-comments mr-2 text-blue-600"></i>Đánh giá
                        </h2>
                        @if ($room->reviews_avg_rating)
                            <div class="flex items-center bg-yellow-50 px-4 py-2 rounded-lg">
                                <i class="fas fa-star text-yellow-400 text-xl mr-2"></i>
                                <span
                                    class="text-2xl font-bold text-gray-900">{{ number_format($room->reviews_avg_rating, 1) }}</span>
                                <span class="text-gray-600 ml-2">/ 5</span>
                            </div>
                        @endif
                    </div>

                    <!-- Review Form -->
                    @auth
                        @if (!$userReview)
                            <form action="#" method="POST" class="mb-8 p-6 bg-blue-50 rounded-lg">
                                @csrf
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Viết đánh giá của bạn</h3>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Đánh giá</label>
                                    <div class="flex items-center space-x-2" x-data="{ rating: 5 }">
                                        <template x-for="star in 5" :key="star">
                                            <button type="button"
                                                @click="rating = star; document.getElementById('rating-input').value = star"
                                                class="text-3xl transition-colors duration-200"
                                                :class="star <= rating ? 'text-yellow-400' : 'text-gray-300'">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        </template>
                                        <input type="hidden" name="rating" id="rating-input" value="5">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nhận xét</label>
                                    <textarea name="comment" rows="4" placeholder="Chia sẻ trải nghiệm của bạn về phòng này..."
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                </div>

                                <button type="submit"
                                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200 font-semibold">
                                    <i class="fas fa-paper-plane mr-2"></i>Gửi đánh giá
                                </button>
                            </form>
                        @else
                            <div class="mb-8 p-6 bg-green-50 border-l-4 border-green-500 rounded-lg">
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-check-circle text-green-500 text-xl mt-1"></i>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Đánh giá của bạn</h3>
                                        <div class="flex items-center mb-2">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i
                                                    class="fas fa-star {{ $i <= $userReview->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                            @endfor
                                            <span
                                                class="ml-2 text-gray-600">{{ $userReview->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-gray-700">{{ $userReview->comment }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endauth

                    <!-- Reviews List -->
                    <div class="space-y-4">
                        @forelse($reviews as $review)
                            <div class="p-6 bg-gray-50 rounded-lg">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white font-bold">
                                            {{ substr($review->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $review->user->name }}</p>
                                            <div class="flex items-center text-sm text-gray-600">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i
                                                        class="fas fa-star text-xs {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                @endfor
                                                <span class="ml-2">{{ $review->created_at?->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if ($review->comment)
                                    <p class="text-gray-700">{{ $review->comment }}</p>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <i class="fas fa-comments text-gray-300 text-5xl mb-4"></i>
                                <p class="text-gray-500">Chưa có đánh giá nào</p>
                            </div>
                        @endforelse
                    </div>

                    @if ($reviews->hasPages())
                        <div class="mt-6">
                            {{ $reviews->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-md p-6 sticky top-24">
                    <!-- Price -->
                    <div class="mb-6">
                        <div class="flex items-baseline">
                            <span
                                class="text-4xl font-bold text-blue-600">{{ number_format($room->price_per_month) }}đ</span>
                            <span class="text-gray-600 ml-2">/tháng</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">
                            <i class="fas fa-calendar-day mr-1"></i>
                            Theo ngày: {{ number_format($room->price_per_day) }}đ
                        </p>
                    </div>

                    <!-- Booking Form -->
                    @if ($room->current_occupancy < $room->capacity)
                        <form action="{{ route('student.bookings.store', $room) }}" method="POST"
                            class="space-y-4 mb-6" x-data="{ rentalType: 'daily' }">
                            @csrf

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Loại thuê</label>
                                <select x-model="rentalType" name="rental_type"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="monthly">Theo tháng</option>
                                    <option value="daily">Theo ngày</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Ngày nhận phòng</label>
                                <input type="date" name="start_date" required min="{{ date('Y-m-d') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2" :for="rentalType">
                                    <span x-text="rentalType === 'monthly' ? 'Số tháng thuê' : 'Số ngày thuê'"></span>
                                </label>
                                <input type="number" name="duration" required min="1"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    :placeholder="rentalType === 'monthly' ? 'Nhập số tháng thuê' : 'Nhập số ngày thuê'">
                            </div>

                            <button type="submit"
                                class="w-full bg-blue-600 text-white py-4 rounded-lg hover:bg-blue-700 transition-colors duration-200 font-bold text-lg shadow-lg hover:shadow-xl">
                                <i class="fas fa-calendar-check mr-2"></i>Đặt phòng ngay
                            </button>
                        </form>
                    @else
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                            <p class="text-red-700 font-semibold">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                Phòng đã hết chỗ
                            </p>
                        </div>
                    @endif

                    <!-- Contact Info -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Cần hỗ trợ?</h3>
                        <div class="space-y-3">
                            <a href="tel:0281234567"
                                class="flex items-center text-gray-700 hover:text-blue-600 transition-colors duration-200">
                                <i class="fas fa-phone text-blue-600 w-6"></i>
                                <span class="ml-2">(028) 1234 5678</span>
                            </a>
                            <a href="mailto:ktx@university.edu.vn"
                                class="flex items-center text-gray-700 hover:text-blue-600 transition-colors duration-200">
                                <i class="fas fa-envelope text-blue-600 w-6"></i>
                                <span class="ml-2">ktx@university.edu.vn</span>
                            </a>
                            <a href="#"
                                class="flex items-center text-gray-700 hover:text-blue-600 transition-colors duration-200">
                                <i class="fab fa-facebook text-blue-600 w-6"></i>
                                <span class="ml-2">Facebook Support</span>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="border-t border-gray-200 pt-6 mt-6">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $room->reviews_count ?? 0 }}</p>
                                <p class="text-xs text-gray-600">Đánh giá</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $room->favourites_count ?? 0 }}</p>
                                <p class="text-xs text-gray-600">Yêu thích</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Similar Rooms -->
        @if ($similarRooms && $similarRooms->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Phòng tương tự</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($similarRooms as $similarRoom)
                        <div
                            class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden group">
                            <div class="relative h-48 overflow-hidden">
                                @if ($similarRoom->images && $similarRoom->images->count() > 0)
                                    <img src="{{ asset('storage/' . $similarRoom->images->first()->image_path) }}"
                                        alt="{{ $similarRoom->room_code }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                @else
                                    <div
                                        class="w-full h-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                                        <i class="fas fa-image text-blue-300 text-4xl"></i>
                                    </div>
                                @endif

                                @if ($similarRoom->current_occupancy < $similarRoom->capacity)
                                    <span
                                        class="absolute top-3 right-3 bg-green-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                        Còn trống
                                    </span>
                                @endif
                            </div>

                            <div class="p-5">
                                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $similarRoom->room_code }}</h3>
                                <p class="text-sm text-gray-600 mb-4">
                                    <i class="fas fa-map-marker-alt mr-1 text-blue-500"></i>
                                    {{ $similarRoom->floor->branch->name ?? 'N/A' }}
                                </p>
                                <div class="flex items-center justify-between">
                                    <span class="text-xl font-bold text-blue-600">
                                        {{ number_format($similarRoom->price_per_month) }}đ
                                    </span>
                                    <a href="#" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">
                                        Xem chi tiết →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        // Auto-scroll to reviews if hash is present
        if (window.location.hash === '#reviews') {
            document.getElementById('reviews').scrollIntoView({
                behavior: 'smooth'
            });
        }
    </script>
@endpush
