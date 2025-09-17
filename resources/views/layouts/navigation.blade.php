<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-blue-800 -translate-x-full lg:translate-x-0 transform transition-transform duration-300 ease-in-out">
    <div class="h-16 bg-white border-e border-blue-500 flex items-center">
        <div class="p-3">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQUNnL6N8n85vzxqhgDbuEdOPRc1ZJI9HRTDw&s"
                alt="HUIT" class="object-cover">
        </div>
    </div>

    <nav>
        <div class="px-6 py-2 mt-4">
            <h3 class="font-semibold text-xs text-gray-300 leading-tight uppercase tracking-wide">Tổng quan</h3>
        </div>
        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <i class="fas fa-tachometer-alt w-6"></i>
            {{ __('Dashboard') }}
        </x-nav-link>

        @cannot('is-student')
            <div class="px-6 py-2 mt-4">
                <h3 class="font-semibold text-xs text-gray-300 leading-tight uppercase tracking-wide">Quản lý</h3>
            </div>
            @foreach ([
            [
                'route' => 'users.index',
                'routeMatch' => 'users.*',
                'icon' => 'fa-user-tie',
                'label' => 'Nhân viên',
            ],
        ] as $item)
                <x-nav-link :href="route($item['route'])" :active="request()->routeIs($item['routeMatch'])">
                    <i class="fas {{ $item['icon'] }} w-6"></i>
                    {{ $item['label'] }}
                </x-nav-link>
            @endforeach
        @endcannot
    </nav>
</aside>

<div id="sidebar-overlay" class="fixed inset-0 z-40 bg-black/50 hidden lg:hidden"></div>

@pushOnce('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebar-overlay');

            const toggleSidebar = () => {
                sidebar.classList.toggle('-translate-x-full');
                sidebarOverlay.classList.toggle('hidden');
            }

            sidebarToggle.addEventListener('click', toggleSidebar);
            sidebarOverlay.addEventListener('click', toggleSidebar);
        });
    </script>
@endPushOnce
