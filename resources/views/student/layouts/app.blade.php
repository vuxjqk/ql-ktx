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
                    <a href="{{ route('student.home') }}" class="flex items-center space-x-3">
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-2 rounded-lg">
                            <i class="fas fa-building text-white text-xl"></i>
                        </div>
                        <span class="text-xl font-bold text-gray-800">Ký túc xá HUIT</span>
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
                    <a href="#"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200
                            {{ request()->routeIs('student.about')
                                ? 'bg-blue-50 text-blue-700'
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-info-circle mr-2"></i>Về chúng tôi
                    </a>
                    <a href="#"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200
                            {{ request()->routeIs('student.contact')
                                ? 'bg-blue-50 text-blue-700'
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-envelope mr-2"></i>Liên hệ
                    </a>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <div class="relative" x-data="notificationDropdown()" x-init="fetchNotifications()">
                        <button @click="toggle()"
                            class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                            <i class="fas fa-bell text-lg"></i>
                            <span x-show="unreadCount > 0"
                                class="absolute top-1 right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center"
                                x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                        </button>

                        <!-- Notification Dropdown -->
                        <div x-show="open" @click.away="open = false" x-cloak
                            class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                            <div class="p-4 border-b border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-900">Thông báo</h3>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                <template x-for="notification in notifications" :key="notification.id">
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
                                                <p class="text-sm font-medium text-gray-900"
                                                    x-text="notification.title"></p>
                                                <p class="text-xs text-gray-500 mt-1" x-text="notification.created_at">
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </template>

                                <div x-show="notifications.length === 0" class="p-8 text-center text-gray-500">
                                    <i class="fas fa-bell-slash text-3xl mb-2"></i>
                                    <p class="text-sm">Không có thông báo mới</p>
                                </div>
                            </div>
                            <div class="p-3 text-center border-t border-gray-200">
                                <a href="{{ route('student.notifications.index') }}"
                                    class="text-sm font-medium text-blue-600 hover:text-blue-700">
                                    Xem tất cả
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- User Authentication Links / Profile Dropdown -->
                    <div class="flex items-center space-x-4">
                        @auth
                            <!-- Đã đăng nhập → hiện avatar + dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open"
                                    class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                    <div
                                        class="h-9 w-9 rounded-full bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white font-bold text-sm">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                    <span class="hidden md:block text-sm font-medium text-gray-700">
                                        {{ auth()->user()->name }}
                                    </span>
                                    <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                                </button>

                                <!-- Dropdown Menu -->
                                <div x-show="open" @click.away="open = false" x-cloak
                                    class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden">
                                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                                        <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                    </div>
                                    <div class="py-2">
                                        <a href="{{ route('student.profile.edit') }}"
                                            class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors flex items-center">
                                            <i class="fas fa-user mr-3 text-gray-400"></i>Thông tin cá nhân
                                        </a>
                                        <a href="{{ route('student.bookings.history') }}"
                                            class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors flex items-center">
                                            <i class="fas fa-history mr-3 text-gray-400"></i>Lịch sử đặt phòng
                                        </a>
                                        <a href="{{ route('student.service-costs.index') }}"
                                            class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors flex items-center">
                                            <i class="fas fa-cog mr-3 text-gray-400"></i>Chi phí dịch vụ
                                        </a>
                                    </div>
                                    <div class="border-t border-gray-200 py-2 bg-gray-50">
                                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                                            @csrf
                                            <button type="submit"
                                                class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors flex items-center font-medium">
                                                <i class="fas fa-sign-out-alt mr-3"></i>Đăng xuất
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Chưa đăng nhập → hiện 2 nút kiểu nổi bật -->
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('login') }}"
                                    class="flex items-center px-4 py-2 rounded-lg text-sm font-medium 
                                        {{ request()->routeIs('login') ? 'bg-blue-50 text-blue-700' : 'bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} 
                                        border border-gray-300 shadow-sm transition-all duration-200">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Đăng nhập
                                </a>
                                <a href="{{ route('register') }}"
                                    class="flex items-center px-4 py-2 rounded-lg text-sm font-medium 
                                        {{ request()->routeIs('register') ? 'bg-blue-600 text-white' : 'bg-blue-600 text-white hover:bg-blue-700' }} 
                                        shadow-md transition-all duration-200">
                                    <i class="fas fa-user-plus mr-2"></i>Đăng ký
                                </a>
                            </div>
                        @endauth
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
                <a href="{{ route('student.home') }}"
                    class="block px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('student.home') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-home mr-2"></i>Trang chủ
                </a>
                <a href="{{ route('student.rooms.index') }}"
                    class="block px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('student.rooms.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-door-open mr-2"></i>Phòng
                </a>
                <a href="{{ route('student.bookings.index') }}"
                    class="block px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('student.bookings.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-calendar-check mr-2"></i>Đặt phòng
                </a>
                <a href="{{ route('student.favourites.index') }}"
                    class="block px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('student.favourites.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-heart mr-2"></i>Yêu thích
                </a>
                <a href="#"
                    class="block px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('student.about') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-info-circle mr-2"></i>Về chúng tôi
                </a>
                <a href="#"
                    class="block px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('student.contact') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-envelope mr-2"></i>Liên hệ
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
                        <span class="text-xl font-bold text-white">Ký túc xá HUIT</span>
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
                        <li><a href="{{ route('student.home') }}"
                                class="text-sm hover:text-white transition-colors duration-200">Trang
                                chủ</a></li>
                        <li><a href="{{ route('student.rooms.index') }}"
                                class="text-sm hover:text-white transition-colors duration-200">Danh
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
                <p>&copy; {{ date('Y') }} Ký túc xá HUIT. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <x-toast />

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')

    <script>
        function notificationDropdown() {
            return {
                open: false,
                notifications: [],
                unreadCount: 0,

                toggle() {
                    this.open = !this.open;
                    if (this.open) {
                        this.fetchNotifications();
                    }
                },

                fetchNotifications() {
                    axios.get('{{ route('student.notifications.getNotifications') }}')
                        .then(res => {
                            this.notifications = res.data.notifications;
                            this.unreadCount = res.data.unread_count;
                        })
                        .catch(err => console.error(err));
                }
            }
        }
    </script>
</body>

</html>
