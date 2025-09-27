@props(['options' => [], 'placeholder', 'selected', 'disabled' => false])

<select @disabled($disabled)
    {{ $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>
    @if (isset($placeholder))
        <option value="">
            {{ $placeholder }}
        </option>
    @endif

    @foreach ($options as $value => $label)
        <option value="{{ $value }}"
            {{ is_array($selected) ? (in_array($value, $selected) ? 'selected' : '') : ($selected === (string) $value ? 'selected' : '') }}>
            {{ $label }}
        </option>
    @endforeach
</select>
