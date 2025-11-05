@props(['disabled' => false])

<input type="file" @disabled($disabled)
    {{ $attributes->merge(['class' => 'border border-gray-300 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-md shadow-sm cursor-pointer file:me-4 file:px-4 file:py-2 file:border-0 file:text-indigo-600 file:bg-indigo-50 hover:file:bg-indigo-100']) }}>
