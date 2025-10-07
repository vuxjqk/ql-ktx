@props(['href' => null, 'icon' => null])

@if ($href)
    <a href="{{ $href }}"
        {{ $attributes->merge(['class' => 'inline-flex items-center p-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
        @if ($icon)
            <i class="{{ $icon }}"></i>
        @else
            {{ $slot }}
        @endif
    </a>
@else
    <button
        {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center p-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
        @if ($icon)
            <i class="{{ $icon }}"></i>
        @else
            {{ $slot }}
        @endif
    </button>
@endif
