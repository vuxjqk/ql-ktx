@php
    $defaultClass =
        'inline-flex items-center p-2 border border-gray-300 rounded-md font-semibold text-xs uppercase tracking-widest shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150';
@endphp

@props(['href', 'class' => 'bg-white hover:bg-gray-50 text-gray-700'])

@if (isset($href))
    <a href="{{ $href }}" {{ $attributes->class($defaultClass)->merge(['class' => $class]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->class($defaultClass)->merge(['type' => 'button', 'class' => $class]) }}>
        {{ $slot }}
    </button>
@endif
