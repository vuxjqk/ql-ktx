@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'flex items-center px-6 py-3 gap-2 font-medium leading-5 text-blue-800 bg-gray-100 focus:outline-none transition duration-150 ease-in-out'
            : 'flex items-center px-6 py-3 gap-2 font-medium leading-5 text-gray-200 hover:text-white hover:bg-blue-700 focus:outline-none focus:text-white focus:bg-blue-700 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
