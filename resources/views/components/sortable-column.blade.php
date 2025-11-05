@props(['options' => []])

@php
    if (request('sort') === $options[0]) {
        $sortValue = $options[1];
        $icon = 'fa-arrow-up-short-wide';
    } elseif (request('sort') === $options[1]) {
        $sortValue = 'none';
        $icon = 'fa-arrow-down-wide-short';
    } else {
        $sortValue = $options[0];
        $icon = 'fa-sort';
    }
@endphp

<form>
    @foreach (request()->except(['sort']) as $key => $value)
        @if (is_array($value))
            @foreach ($value as $subKey => $subValue)
                <input type="hidden" name="{{ $key }}[{{ $subKey }}]" value="{{ $subValue }}">
            @endforeach
        @else
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach

    <input type="hidden" name="sort" value="{{ $sortValue }}">
    <button type="submit"
        class="text-xs text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        <i class="fas {{ $icon }}"></i>
    </button>
</form>
