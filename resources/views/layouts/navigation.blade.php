<aside :class="{ 'translate-x-0': open, '-translate-x-full': !open }"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-blue-800 text-white transform transition-transform duration-300 ease-in-out lg:translate-x-0 overflow-y-scroll scrollbar-hide">
    <div class="bg-white border-e border-blue-600 flex items-center h-16 p-3">
        <img src="{{ asset('storage/logo/huit.png') }}" alt="HUIT" class="object-cover">
    </div>

    <nav>
        <div class="px-6 py-2 mt-4">
            <h3 class="font-semibold text-xs text-gray-300 leading-tight uppercase tracking-wide">
                {{ __('Tổng quan') }}
            </h3>
        </div>
        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <i class="fas fa-tachometer-alt w-6"></i>
            {{ __('Dashboard') }}
        </x-responsive-nav-link>

        <div class="px-6 py-2 mt-4">
            <h3 class="font-semibold text-xs text-gray-300 leading-tight uppercase tracking-wide">
                {{ __('Quản lý') }}
            </h3>
        </div>
        @foreach ([
        [
            'route' => 'users.index',
            'routeMatch' => 'users.*',
            'icon' => 'fa-user-tie',
            'label' => 'Nhân viên',
        ],
        [
            'route' => 'students.index',
            'routeMatch' => ['students.*', 'bills.*'],
            'icon' => 'fa-user-graduate',
            'label' => 'Sinh viên',
        ],
        [
            'route' => 'bookings.index',
            'routeMatch' => 'bookings.*',
            'icon' => 'fa-calendar-check',
            'label' => 'Đặt phòng',
        ],
        [
            'route' => 'repairs.index',
            'routeMatch' => 'repairs.*',
            'icon' => 'fa-tools',
            'label' => 'Sửa chữa',
        ],
        [
            'route' => 'branches.index',
            'routeMatch' => 'branches.*',
            'icon' => 'fa-building',
            'label' => 'Chi nhánh',
        ],
        [
            'route' => 'rooms.index',
            'routeMatch' => ['rooms.*', 'service-usages.*'],
            'icon' => 'fa-door-open',
            'label' => 'Phòng',
        ],
        [
            'route' => 'services.index',
            'routeMatch' => 'services.*',
            'icon' => 'fa-concierge-bell',
            'label' => 'Dịch vụ',
        ],
        [
            'route' => 'amenities.index',
            'routeMatch' => 'amenities.*',
            'icon' => 'fa-swimming-pool',
            'label' => 'Tiện ích',
        ],
    ] as $item)
            <x-responsive-nav-link :href="route($item['route'])" :active="request()->routeIs($item['routeMatch'])">
                <i class="fas {{ $item['icon'] }} w-6"></i>
                {{ __($item['label']) }}
            </x-responsive-nav-link>
        @endforeach

        <div class="px-6 py-2 mt-4">
            <h3 class="font-semibold text-xs text-gray-300 leading-tight uppercase tracking-wide">
                {{ __('Thống kê') }}
            </h3>
        </div>

        <x-responsive-nav-link :href="route('statistics')" :active="request()->routeIs('statistics')">
            <i class="fas fa-chart-pie w-6"></i>
            {{ __('Thống kê') }}
        </x-responsive-nav-link>
    </nav>
</aside>

<div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="open = false"
    class="fixed inset-0 z-40 bg-black/50 lg:hidden"></div>
