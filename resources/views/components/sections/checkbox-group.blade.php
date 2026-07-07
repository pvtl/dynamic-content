@props(['field' => [], 'value' => null])

@php
    $currentValue = (array) ($value ?? $field['default'] ?? []);
    $options = is_callable($field['options']) ? call_user_func($field['options']) : $field['options'];
@endphp

<flux:field class="p-3 {{ $field['class'] }}">
    <flux:label>{{ $field['name'] }}</flux:label>
    @if (!empty($field['description']))
        <flux:description>{{ $field['description'] }}</flux:description>
    @endif
    <div class="mt-1 space-y-2">
        @foreach ($options as $optionValue => $optionLabel)
            <flux:checkbox
                :value="$optionValue"
                :label="$optionLabel"
                :checked="in_array($optionValue, $currentValue)"
                {{ $attributes->except('name') }} />
        @endforeach
    </div>
    <flux:error :name="$attributes->get('name', $field['slug'])" />
</flux:field>
