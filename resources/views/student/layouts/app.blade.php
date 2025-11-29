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
                    <a href="{{ route('student.about') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200
                            {{ request()->routeIs('student.about')
                                ? 'bg-blue-50 text-blue-700'
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-info-circle mr-2"></i>Về chúng tôi
                    </a>
                    <a href="{{ route('student.contact') }}"
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
                    @include('student.notifications.dashboard')

                    <!-- User Authentication Links / Profile Dropdown -->
                    <div class="flex items-center space-x-4">
                        @auth
                            <!-- Đã đăng nhập → hiện avatar + dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open"
                                    class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                    @if (Auth::user()->avatar)
                                        <!-- Nếu có avatar -->
                                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}"
                                            alt="{{ Auth::user()->name }}" class="h-9 w-9 rounded-full object-cover">
                                    @else
                                        <!-- Nếu không có avatar, hiển thị chữ cái đầu -->
                                        <div
                                            class="h-9 w-9 rounded-full bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white font-bold text-sm">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </div>
                                    @endif
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
                                            <i class="fas fa-concierge-bell mr-3 text-gray-400"></i>Chi phí dịch vụ
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
                <a href="{{ route('student.about') }}"
                    class="block px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('student.about') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="fas fa-info-circle mr-2"></i>Về chúng tôi
                </a>
                <a href="{{ route('student.contact') }}"
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
                        <li><a href="{{ route('student.about') }}"
                                class="text-sm hover:text-white transition-colors duration-200">Về chúng
                                tôi</a></li>
                        <li><a href="{{ route('student.contact') }}"
                                class="text-sm hover:text-white transition-colors duration-200">Liên
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
                            <span>info@huit.edu.vn</span>
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

    <div x-cloak x-data="{
        open: false,
        minimized: false,
        messages: [],
        input: '',
        loading: false,
        async sendMessage() {
            const text = this.input.trim();
            if (!text) return;
            this.messages.push({ text, from: 'user', time: new Date().toLocaleTimeString() });
            this.input = '';
            this.$nextTick(() => {
                const el = this.$refs.messages;
                if (el) el.scrollTop = el.scrollHeight;
            });
    
            // Call backend ChatbotController
            try {
                this.loading = true;
                const res = await fetch('{{ route('chatbot.handle') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=&quot;csrf-token&quot;]').getAttribute('content')
                    },
                    body: JSON.stringify({ message: text })
                });
    
                if (!res.ok) throw new Error('Network response was not ok');
                const data = await res.json();
                const reply = data.reply ?? 'Xin lỗi, không có phản hồi.';
                this.messages.push({ text: reply, from: 'bot', time: new Date().toLocaleTimeString() });
                this.$nextTick(() => { const el = this.$refs.messages; if (el) el.scrollTop = el.scrollHeight; });
            } catch (e) {
                console.error(e);
                this.messages.push({ text: 'Lỗi khi liên hệ dịch vụ. Vui lòng thử lại sau.', from: 'bot', time: new Date().toLocaleTimeString() });
            } finally {
                this.loading = false;
            }
        }
    }" class="fixed bottom-6 right-6 z-50" aria-live="polite">

        <!-- Minimized / Toggle button -->
        <div x-cloak x-show="!open || minimized" class="flex items-end justify-end">
            <button @click="open = true; minimized = false"
                class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 p-3 rounded-full shadow-lg hover:scale-105 transition-transform"
                aria-label="Mở chat" title="Mở chat">
                <i class="fas fa-comment-dots"></i>
            </button>
        </div>

        <!-- Chat panel -->
        <div x-show="open" x-cloak x-transition:enter="transition transform duration-200"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition transform duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95" @keydown.window.escape="open = false"
            class="mt-2 w-80 md:w-96 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl overflow-hidden flex flex-col"
            style="max-height: 80vh;">

            <!-- Header -->
            <div
                class="flex items-center justify-between px-3 py-2 bg-gradient-to-r from-white to-white/90 dark:from-gray-800 dark:to-gray-800 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-white rounded-lg flex items-center justify-center">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('Hỗ trợ') }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Chat AI') }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button @click="minimized = !minimized"
                        class="p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i :class="minimized ? 'fas fa-window-maximize' : 'fas fa-window-minimize'"></i>
                    </button>
                    <button @click="open = false"
                        class="p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                        aria-label="Đóng">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Messages area -->
            <div x-show="!minimized" class="flex-1 p-3 overflow-hidden">
                <div x-ref="messages" class="h-60 md:h-72 overflow-y-auto space-y-3 pr-2 scrollbar-hide">
                    <template x-for="(m, i) in messages" :key="i">
                        <div class="flex" :class="m.from === 'user' ? 'justify-end' : 'justify-start'">
                            <div class="max-w-[80%]">
                                <div class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-sm text-gray-800 dark:text-gray-100"
                                    :class="m.from === 'user' ? '!bg-blue-600 text-white' : ''" x-text="m.text">
                                </div>
                                <div class="text-xs text-gray-400 mt-1 text-right" x-text="m.time"></div>
                            </div>
                        </div>
                    </template>

                    <div x-show="messages.length === 0" class="text-center text-sm text-gray-400 mt-8">
                        {{ __('Bạn có thể nhập câu hỏi, tôi sẽ giúp bạn.') }}
                    </div>

                    <!-- Typing indicator -->
                    <div x-show="loading" class="flex justify-start items-center gap-2">
                        <div class="w-3 h-3 bg-gray-400 rounded-full animate-pulse"></div>
                        <div class="w-3 h-3 bg-gray-500 rounded-full animate-pulse delay-75"></div>
                        <div class="w-3 h-3 bg-gray-600 rounded-full animate-pulse delay-150"></div>
                    </div>
                </div>
            </div>

            <!-- Input area -->
            <form x-show="!minimized" @submit.prevent="sendMessage()"
                class="px-3 py-2 border-t border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800">
                <div class="flex items-end gap-2">
                    <textarea x-model="input" rows="2"
                        @keydown.enter="if (!event.shiftKey) { event.preventDefault(); sendMessage(); }"
                        class="flex-1 resize-none px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="{{ __('Nhập tin nhắn... (Enter để gửi, Shift+Enter xuống dòng)') }}"></textarea>

                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg shadow-sm">
                        <i class="fas fa-paper-plane"></i>
                        <span class="text-sm">{{ __('Gửi') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')
</body>

</html>
