@extends('student.layouts.app')

@section('title', 'Danh sách phòng')

@pushOnce('styles')
    <style>
        .filter-card {
            position: sticky;
            top: 5rem;
        }
    </style>
@endPushOnce

@section('content')
    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-white mb-4">Danh sách phòng</h1>
            <p class="text-blue-100 text-lg">Tìm kiếm và lựa chọn phòng phù hợp với nhu cầu của bạn</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Filter Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-md p-6 filter-card" x-data="{
                    showFilters: true,
                    priceRange: [0, 5000000],
                    capacity: 'all'
                }">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900">
                            <i class="fas fa-filter mr-2 text-blue-600"></i>Bộ lọc
                        </h2>
                        <button @click="showFilters = !showFilters" class="lg:hidden text-gray-500">
                            <i class="fas" :class="showFilters ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        </button>
                    </div>

                    <form method="GET" action="#" x-show="showFilters" x-cloak>
                        <!-- Search -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-search mr-1"></i>Tìm kiếm
                            </label>
                            <input type="text" name="room_code" value="{{ request('room_code') }}"
                                placeholder="Mã phòng..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        </div>

                        <!-- Branch -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-building mr-1"></i>Chi nhánh
                            </label>
                            <select name="branch_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="">Tất cả chi nhánh</option>
                                @foreach ($branches ?? [] as $id => $name)
                                    <option value="{{ $id }}" {{ request('branch_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Gender Type -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-venus-mars mr-1"></i>Loại phòng
                            </label>
                            <div class="space-y-2">
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="gender_type" value=""
                                        {{ !request('gender_type') ? 'checked' : '' }}
                                        class="text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 group-hover:text-gray-900">Tất cả</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="gender_type" value="male"
                                        {{ request('gender_type') == 'male' ? 'checked' : '' }}
                                        class="text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 group-hover:text-gray-900">Nam</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="gender_type" value="female"
                                        {{ request('gender_type') == 'female' ? 'checked' : '' }}
                                        class="text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 group-hover:text-gray-900">Nữ</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="gender_type" value="mixed"
                                        {{ request('gender_type') == 'mixed' ? 'checked' : '' }}
                                        class="text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 group-hover:text-gray-900">Hỗn hợp</span>
                                </label>
                            </div>
                        </div>

                        <!-- Capacity -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-users mr-1"></i>Số người/phòng
                            </label>
                            <select name="capacity"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="">Tất cả</option>
                                <option value="2" {{ request('capacity') == '2' ? 'selected' : '' }}>2 người</option>
                                <option value="4" {{ request('capacity') == '4' ? 'selected' : '' }}>4 người</option>
                                <option value="6" {{ request('capacity') == '6' ? 'selected' : '' }}>6 người</option>
                                <option value="8" {{ request('capacity') == '8' ? 'selected' : '' }}>8 người</option>
                            </select>
                        </div>

                        <!-- Availability -->
                        <div class="mb-6">
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" name="available_only" value="1"
                                    {{ request('available_only') ? 'checked' : '' }}
                                    class="text-blue-600 focus:ring-blue-500 rounded">
                                <span class="ml-2 text-sm font-medium text-gray-700 group-hover:text-gray-900">
                                    <i class="fas fa-check-circle mr-1 text-green-500"></i>Chỉ phòng còn trống
                                </span>
                            </label>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-dollar-sign mr-1"></i>Giá phòng (VNĐ/tháng)
                            </label>
                            <div class="space-y-3">
                                <div class="flex items-center space-x-3">
                                    <input type="number" name="min_price" value="{{ request('min_price') }}"
                                        placeholder="Từ"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                    <span class="text-gray-500">-</span>
                                    <input type="number" name="max_price" value="{{ request('max_price') }}"
                                        placeholder="Đến"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button"
                                        onclick="document.querySelector('[name=min_price]').value='0'; document.querySelector('[name=max_price]').value='1000000'"
                                        class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full transition-colors duration-200">
                                        < 1tr </button>
                                            <button type="button"
                                                onclick="document.querySelector('[name=min_price]').value='1000000'; document.querySelector('[name=max_price]').value='2000000'"
                                                class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full transition-colors duration-200">
                                                1tr - 2tr
                                            </button>
                                            <button type="button"
                                                onclick="document.querySelector('[name=min_price]').value='2000000'; document.querySelector('[name=max_price]').value='3000000'"
                                                class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full transition-colors duration-200">
                                                2tr - 3tr
                                            </button>
                                            <button type="button"
                                                onclick="document.querySelector('[name=min_price]').value='3000000'; document.querySelector('[name=max_price]').value=''"
                                                class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full transition-colors duration-200">
                                                > 3tr
                                            </button>
                                </div>
                            </div>
                        </div>

                        <!-- Amenities -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-sparkles mr-1"></i>Tiện nghi
                            </label>
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                @foreach ($amenities ?? [] as $amenity)
                                    <label class="flex items-center cursor-pointer group">
                                        <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}"
                                            {{ in_array($amenity->id, request('amenities', [])) ? 'checked' : '' }}
                                            class="text-blue-600 focus:ring-blue-500 rounded">
                                        <span
                                            class="ml-2 text-sm text-gray-700 group-hover:text-gray-900">{{ $amenity->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Sort -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-sort mr-1"></i>Sắp xếp
                            </label>
                            <select name="sort"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Mới nhất
                                </option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp
                                    đến cao</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá:
                                    Cao đến thấp</option>
                                <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Đánh giá cao
                                </option>
                                <option value="capacity_desc" {{ request('sort') == 'capacity_desc' ? 'selected' : '' }}>
                                    Số
                                    người
                                </option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-2">
                            <button type="submit"
                                class="flex-1 bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200 font-semibold text-sm">
                                <i class="fas fa-search mr-2"></i>Tìm kiếm
                            </button>
                            <a href="{{ route('student.rooms.index') }}"
                                class="px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-semibold text-sm">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Room List -->
            <div class="lg:col-span-3">
                <!-- Results Header -->
                <div class="bg-white rounded-xl shadow-md p-4 mb-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div>
                            <p class="text-gray-600">
                                Tìm thấy <span class="font-bold text-gray-900">{{ $rooms->total() }}</span> phòng
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button
                                onclick="document.getElementById('view-grid').classList.remove('hidden'); document.getElementById('view-list').classList.add('hidden'); this.classList.add('bg-blue-600', 'text-white'); this.classList.remove('text-gray-600'); this.nextElementSibling.classList.remove('bg-blue-600', 'text-white'); this.nextElementSibling.classList.add('text-gray-600')"
                                class="bg-blue-600 text-white p-2 rounded-lg transition-colors duration-200">
                                <i class="fas fa-th-large"></i>
                            </button>
                            <button
                                onclick="document.getElementById('view-list').classList.remove('hidden'); document.getElementById('view-grid').classList.add('hidden'); this.classList.add('bg-blue-600', 'text-white'); this.classList.remove('text-gray-600'); this.previousElementSibling.classList.remove('bg-blue-600', 'text-white'); this.previousElementSibling.classList.add('text-gray-600')"
                                class="text-gray-600 p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Grid View -->
                <div id="view-grid" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    @forelse($rooms as $room)
                        <div
                            class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden group">
                            <!-- Room Image -->
                            <div class="relative h-48 overflow-hidden">
                                @if ($room->images && $room->images->count() > 0)
                                    <img src="{{ asset('storage/' . $room->images->first()->image_path) }}"
                                        alt="{{ $room->room_code }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                @else
                                    <div
                                        class="w-full h-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                                        <i class="fas fa-image text-blue-300 text-4xl"></i>
                                    </div>
                                @endif

                                <!-- Status Badge -->
                                @if ($room->current_occupancy < $room->capacity)
                                    <span
                                        class="absolute top-3 right-3 bg-green-500 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-lg">
                                        <i class="fas fa-check-circle mr-1"></i>Còn
                                        {{ $room->capacity - $room->current_occupancy }} chỗ
                                    </span>
                                @else
                                    <span
                                        class="absolute top-3 right-3 bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-lg">
                                        <i class="fas fa-times-circle mr-1"></i>Đã đầy
                                    </span>
                                @endif

                                <!-- Favourite Button -->
                                <button data-favourite-url="{{ route('student.favourites.toggleFavourite', $room) }}"
                                    class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm p-2 rounded-full shadow-lg hover:bg-white transition-colors duration-200 favourite-btn">
                                    <i
                                        class="fas fa-heart {{ $room->is_favourited ? 'text-red-500' : 'text-gray-400' }}"></i>
                                </button>
                            </div>

                            <!-- Room Info -->
                            <div class="p-5">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">Phòng {{ $room->room_code }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            <i class="fas fa-map-marker-alt mr-1 text-blue-500"></i>
                                            {{ $room->floor->branch->name ?? 'N/A' }} - Tầng
                                            {{ $room->floor->floor_number ?? 'N/A' }}
                                        </p>
                                    </div>
                                    @if ($room->reviews_avg_rating)
                                        <div class="flex items-center bg-yellow-50 px-2 py-1 rounded-lg">
                                            <i class="fas fa-star text-yellow-400 text-sm mr-1"></i>
                                            <span
                                                class="text-sm font-semibold text-gray-900">{{ number_format($room->reviews_avg_rating, 1) }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex flex-wrap gap-2 mb-4">
                                    <span
                                        class="inline-flex items-center text-xs bg-blue-50 text-blue-700 px-3 py-1 rounded-full">
                                        <i class="fas fa-users mr-1"></i>
                                        {{ $room->current_occupancy }}/{{ $room->capacity }}
                                    </span>
                                    <span
                                        class="inline-flex items-center text-xs bg-purple-50 text-purple-700 px-3 py-1 rounded-full">
                                        <i class="fas fa-door-closed mr-1"></i>
                                        {{ $room->floor->gender_type === 'male' ? 'Nam' : ($room->floor->gender_type === 'female' ? 'Nữ' : 'Hỗn hợp') }}
                                    </span>
                                </div>

                                @if ($room->amenities && $room->amenities->count() > 0)
                                    <div class="flex flex-wrap gap-1 mb-4">
                                        @foreach ($room->amenities->take(3) as $amenity)
                                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">
                                                {{ $amenity->name }}
                                            </span>
                                        @endforeach
                                        @if ($room->amenities->count() > 3)
                                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">
                                                +{{ $room->amenities->count() - 3 }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                <div class="border-t border-gray-100 pt-4 flex items-center justify-between">
                                    <div>
                                        <p class="text-2xl font-bold text-blue-600">
                                            {{ number_format($room->price_per_month) }}đ
                                            <span class="text-sm text-gray-500 font-normal">/tháng</span>
                                        </p>
                                        <p class="text-xl font-semibold text-gray-700">
                                            {{ number_format($room->price_per_day) }}đ
                                            <span class="text-sm text-gray-500 font-normal">/ngày</span>
                                        </p>
                                    </div>
                                    <a href="{{ route('student.rooms.show', $room) }}"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm font-semibold">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 bg-white rounded-xl shadow-md p-12 text-center">
                            <i class="fas fa-search text-gray-300 text-5xl mb-4"></i>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Không tìm thấy phòng</h3>
                            <p class="text-gray-600 mb-6">Vui lòng thử lại với các tiêu chí khác</p>
                            <a href="{{ route('student.rooms.index') }}"
                                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                <i class="fas fa-redo mr-2"></i>
                                Xóa bộ lọc
                            </a>
                        </div>
                    @endforelse
                </div>

                <!-- List View -->
                <div id="view-list" class="hidden space-y-4 mb-8">
                    @foreach ($rooms as $room)
                        <div
                            class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden">
                            <div class="flex flex-col md:flex-row">
                                <!-- Image -->
                                <div class="relative md:w-64 h-48 md:h-auto overflow-hidden flex-shrink-0">
                                    @if ($room->images && $room->images->count() > 0)
                                        <img src="{{ asset('storage/' . $room->images->first()->image_path) }}"
                                            alt="{{ $room->room_code }}"
                                            class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                                    @else
                                        <div
                                            class="w-full h-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                                            <i class="fas fa-image text-blue-300 text-4xl"></i>
                                        </div>
                                    @endif

                                    <!-- Favourite Button -->
                                    <button data-favourite-url="{{ route('student.favourites.toggleFavourite', $room) }}"
                                        class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm p-2 rounded-full shadow-lg hover:bg-white transition-colors duration-200 favourite-btn">
                                        <i
                                            class="fas fa-heart {{ $room->is_favourited ? 'text-red-500' : 'text-gray-400' }}"></i>
                                    </button>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 p-6">
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900">Phòng {{ $room->room_code }}</h3>
                                            <p class="text-sm text-gray-600 mt-1">
                                                <i class="fas fa-map-marker-alt mr-1 text-blue-500"></i>
                                                {{ $room->floor->branch->name ?? 'N/A' }} - Tầng
                                                {{ $room->floor->floor_number ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <div class="flex flex-col items-end space-y-2">
                                            @if ($room->reviews_avg_rating)
                                                <div class="flex items-center bg-yellow-50 px-3 py-1 rounded-lg">
                                                    <i class="fas fa-star text-yellow-400 mr-1"></i>
                                                    <span
                                                        class="text-sm font-semibold text-gray-900">{{ number_format($room->reviews_avg_rating, 1) }}</span>
                                                </div>
                                            @endif
                                            @if ($room->current_occupancy < $room->capacity)
                                                <span
                                                    class="bg-green-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                                    Còn {{ $room->capacity - $room->current_occupancy }} chỗ
                                                </span>
                                            @else
                                                <span
                                                    class="bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                                    Đã đầy
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if ($room->description)
                                        <p class="text-gray-600 text-sm mb-4">{{ Str::limit($room->description, 150) }}
                                        </p>
                                    @endif

                                    <div class="flex flex-wrap gap-2 mb-4">
                                        <span
                                            class="inline-flex items-center text-sm bg-blue-50 text-blue-700 px-3 py-1 rounded-full">
                                            <i class="fas fa-users mr-1"></i>
                                            {{ $room->current_occupancy }}/{{ $room->capacity }} người
                                        </span>
                                        <span
                                            class="inline-flex items-center text-sm bg-purple-50 text-purple-700 px-3 py-1 rounded-full">
                                            <i class="fas fa-door-closed mr-1"></i>
                                            {{ $room->floor->gender_type === 'male' ? 'Nam' : ($room->floor->gender_type === 'female' ? 'Nữ' : 'Hỗn hợp') }}
                                        </span>
                                    </div>

                                    <div class="border-t border-gray-100 pt-4 flex items-center justify-between">
                                        <div>
                                            <p class="text-3xl font-bold text-blue-600">
                                                {{ number_format($room->price_per_month) }}đ
                                                <span class="text-sm text-gray-500 font-normal">/tháng</span>
                                            </p>
                                            <p class="text-2xl font-semibold text-gray-700">
                                                {{ number_format($room->price_per_day) }}đ
                                                <span class="text-sm text-gray-500 font-normal">/ngày</span>
                                            </p>
                                        </div>
                                        <a href="{{ route('student.rooms.show', $room) }}"
                                            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200 font-semibold">
                                            Xem chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if ($rooms->hasPages())
                    <div class="flex justify-center items-center space-x-1 mt-6 text-sm">

                        {{-- Previous --}}
                        @if ($rooms->onFirstPage())
                            <span class="px-3 py-1 bg-gray-200 rounded">Prev</span>
                        @else
                            <a href="{{ $rooms->previousPageUrl() }}"
                                class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Prev</a>
                        @endif

                        {{-- Page numbers --}}
                        @php
                            $total = $rooms->lastPage();
                            $current = $rooms->currentPage();
                            $start = max($current - 2, 1);
                            $end = min($current + 2, $total);
                        @endphp

                        {{-- First page + ellipsis --}}
                        @if ($start > 1)
                            <a href="{{ $rooms->url(1) }}" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">1</a>
                            @if ($start > 2)
                                <span class="px-2">…</span>
                            @endif
                        @endif

                        {{-- Pages around current --}}
                        @for ($i = $start; $i <= $end; $i++)
                            @if ($i == $current)
                                <span class="px-3 py-1 bg-blue-600 text-white rounded">{{ $i }}</span>
                            @else
                                <a href="{{ $rooms->url($i) }}"
                                    class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">{{ $i }}</a>
                            @endif
                        @endfor

                        {{-- Last page + ellipsis --}}
                        @if ($end < $total)
                            @if ($end < $total - 1)
                                <span class="px-2">…</span>
                            @endif
                            <a href="{{ $rooms->url($total) }}"
                                class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">{{ $total }}</a>
                        @endif

                        {{-- Next --}}
                        @if ($rooms->hasMorePages())
                            <a href="{{ $rooms->nextPageUrl() }}"
                                class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Next</a>
                        @else
                            <span class="px-3 py-1 bg-gray-200 rounded">Next</span>
                        @endif

                    </div>
                @endif
            </div>
        </div>
    </div>

    @pushOnce('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                document.querySelectorAll('.favourite-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const url = this.dataset.favouriteUrl;
                        const icon = this.querySelector('i');

                        axios.post(url, {}, {
                                headers: {
                                    'X-CSRF-TOKEN': token
                                }
                            })
                            .then(response => {
                                const status = response.data.status;

                                if (status === 'added') {
                                    icon.classList.remove('far', 'text-gray-400');
                                    icon.classList.add('fas', 'text-red-500');
                                } else {
                                    icon.classList.remove('fas', 'text-red-500');
                                    icon.classList.add('far', 'text-gray-400');
                                }
                            })
                            .catch(error => {
                                console.error(error);
                                alert('Có lỗi xảy ra, vui lòng thử lại');
                            });
                    });
                });
            });
        </script>
    @endPushOnce
@endsection
