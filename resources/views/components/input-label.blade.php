@props(['value', 'icon'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>
    @if (isset($icon))
        <i class="{{ $icon }}"></i>
    @endif
    {{ $value ?? $slot }}
</label>
