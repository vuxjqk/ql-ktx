@props(['options' => [], 'selected' => null, 'placeholder' => null, 'disabled' => false])

<select @disabled($disabled)
    {{ $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>
    @if ($placeholder)
        <option value="">
            {{ __($placeholder) }}
        </option>
    @endif

    @foreach ($options as $value => $label)
        <option value="{{ $value }}" @selected(is_array($selected) ? in_array($value, $selected) : (string) $selected === (string) $value)>
            {{ __($label) }}
        </option>
    @endforeach
</select>
