@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'flex items-center gap-2 px-6 py-3 text-base font-medium text-blue-800 bg-gray-100 focus:outline-none transition duration-150 ease-in-out'
            : 'flex items-center gap-2 px-6 py-3 text-base font-medium text-gray-200 hover:text-white hover:bg-blue-700 focus:outline-none focus:text-white focus:bg-blue-700 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
