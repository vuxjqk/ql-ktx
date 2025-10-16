@props(['icon' => null, 'value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>
    @if ($icon)
        <i class="{{ $icon }}"></i>
    @endif
    {{ $value ?? $slot }}
</label>
