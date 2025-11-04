<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        [x-cloak] {
            display: none !important;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div x-data="{ open: false }" class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <div class="lg:ms-64 transition-[margin] duration-300 ease-in-out">
            <!-- Page Heading -->
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
                    <div>
                        @isset($header)
                            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                                {{ $header }}
                            </h2>
                        @endisset
                    </div>

                    <div class="flex items-center gap-2">
                        <div>
                            <a class="px-3 text-xl text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                href="{{ route('notifications.index') }}">
                                <i class="fas fa-bell"></i>
                            </a>
                        </div>

                        <div>
                            <x-select x-data
                                x-on:change="window.location.href = '{{ url('lang') }}/' + $event.target.value"
                                class="block mt-1 w-full" :options="[
                                    'vi' => 'Tiếng Việt',
                                    'en' => 'Tiếng Anh',
                                ]" :selected="app()->getLocale()" />
                        </div>

                        <!-- Settings Dropdown -->
                        <div class="flex items-center">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                        @if (Auth::user()->avatar)
                                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar"
                                                class="w-8 h-8 rounded-full object-cover">
                                        @else
                                            <div
                                                class="w-8 h-8 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center">
                                                {{ mb_substr(Auth::user()->name, 0, 2, 'UTF-8') }}
                                            </div>
                                        @endif

                                        <div class="ms-2">{{ Auth::user()->name }}</div>

                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">
                                        <i class="fas fa-user me-1"></i>
                                        {{ __('Profile') }}
                                    </x-dropdown-link>

                                    <!-- Authentication -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf

                                        <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                            <i class="fas fa-sign-out-alt me-1"></i>
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>

                        <!-- Hamburger -->
                        <div class="flex items-center lg:hidden">
                            <button @click="open = ! open"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </div>

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
                        class="p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700" aria-label="Đóng">
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
                                    :class="m.from === 'user' ? 'bg-blue-600 text-white' : ''" x-text="m.text">
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

    @stack('scripts')
</body>

</html>
