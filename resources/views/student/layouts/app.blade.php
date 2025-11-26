<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Quản lý ký túc xá') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @stack('styles')
</head>

<body class="bg-gray-50 font-['Inter']">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center">
                    <a href="#" class="flex items-center space-x-3">
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-2 rounded-lg">
                            <i class="fas fa-building text-white text-xl"></i>
                        </div>
                        <span class="text-xl font-bold text-gray-800">Ký túc xá UNI</span>
                    </a>
                </div>

                <!-- Main Navigation -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('student.home') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200
                              {{ request()->routeIs('student.home')
                                  ? 'bg-blue-50 text-blue-700'
                                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-home mr-2"></i>Trang chủ
                    </a>
                    <a href="{{ route('student.rooms.index') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200
                              {{ request()->routeIs('student.rooms.*')
                                  ? 'bg-blue-50 text-blue-700'
                                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-door-open mr-2"></i>Phòng
                    </a>
                    <a href="{{ route('student.bookings.index') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200
                              {{ request()->routeIs('student.bookings.*')
                                  ? 'bg-blue-50 text-blue-700'
                                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-calendar-check mr-2"></i>Đặt phòng
                    </a>
                    <a href="{{ route('student.favourites.index') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 relative
                              {{ request()->routeIs('student.favourites.*')
                                  ? 'bg-blue-50 text-blue-700'
                                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-heart mr-2"></i>Yêu thích
                        @if (isset($favouriteCount) && $favouriteCount > 0)
                            <span
                                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $favouriteCount }}
                            </span>
                        @endif
                    </a>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
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
                                    <a href="#"
                                        class="block p-4 hover:bg-gray-50 border-b border-gray-100 transition-colors duration-200">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <i class="fas fa-info-circle text-blue-600"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900">{{ $notification->title }}
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $notification->created_at->diffForHumans() }}</p>
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
                                <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                                    Xem tất cả
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <div
                                class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white font-medium">
                                {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                            </div>
                            <span
                                class="hidden md:block text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'User' }}</span>
                            <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                        </button>

                        <!-- User Dropdown -->
                        <div x-show="open" @click.away="open = false" x-cloak
                            class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                            <div class="p-4 border-b border-gray-200">
                                <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name ?? 'User' }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->email ?? 'email@example.com' }}</p>
                            </div>
                            <div class="py-2">
                                <a href="{{ route('student.profile.edit') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                    <i class="fas fa-user mr-2 text-gray-400"></i>Thông tin cá nhân
                                </a>
                                <a href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                    <i class="fas fa-history mr-2 text-gray-400"></i>Lịch sử đặt phòng
                                </a>
                                <a href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                    <i class="fas fa-cog mr-2 text-gray-400"></i>Cài đặt
                                </a>
                            </div>
                            <div class="border-t border-gray-200 py-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button class="md:hidden p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg" x-data
                        @click="$dispatch('toggle-mobile-menu')">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div class="md:hidden border-t border-gray-200" x-data="{ mobileMenuOpen: false }"
            @toggle-mobile-menu.window="mobileMenuOpen = !mobileMenuOpen" x-show="mobileMenuOpen" x-cloak>
            <div class="px-4 py-3 space-y-1">
                <a href="#"
                    class="block px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('student.home') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-home mr-2"></i>Trang chủ
                </a>
                <a href="#"
                    class="block px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('student.rooms.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-door-open mr-2"></i>Phòng
                </a>
                <a href="#"
                    class="block px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('student.bookings.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-calendar-check mr-2"></i>Đặt phòng
                </a>
                <a href="#"
                    class="block px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('student.favourites.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-heart mr-2"></i>Yêu thích
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="min-h-[calc(100vh-4rem)]">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-2 rounded-lg">
                            <i class="fas fa-building text-white text-xl"></i>
                        </div>
                        <span class="text-xl font-bold text-white">Ký túc xá UNI</span>
                    </div>
                    <p class="text-sm text-gray-400 mb-4">
                        Hệ thống quản lý ký túc xá hiện đại, tiện nghi và an toàn dành cho sinh viên.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="text-white font-semibold mb-4">Liên kết</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-sm hover:text-white transition-colors duration-200">Trang
                                chủ</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors duration-200">Danh
                                sách phòng</a>
                        </li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors duration-200">Về chúng
                                tôi</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors duration-200">Liên
                                hệ</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-semibold mb-4">Liên hệ</h3>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-2 text-blue-500"></i>
                            <span>123 Đường ABC, Quận XYZ, TP.HCM</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone mt-1 mr-2 text-blue-500"></i>
                            <span>(028) 1234 5678</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-envelope mt-1 mr-2 text-blue-500"></i>
                            <span>ktx@university.edu.vn</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} Ký túc xá UNI. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <x-toast />

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')
</body>

</html>
