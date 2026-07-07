@props(['field' => [], 'value' => null])

@php
    $currentValue = $value ?? $field['default'] ?? null;
    $options = is_callable($field['options']) ? call_user_func($field['options']) : $field['options'];
@endphp

<div class="p-3 {{ $field['class'] }}">
    <flux:radio.group
        :label="$field['name']"
        :description="$field['description'] ?? null"
        {{ $attributes->except('name') }}>
        @foreach ($options as $optionValue => $optionLabel)
            <flux:radio :value="$optionValue" :label="$optionLabel" :checked="$currentValue === $optionValue" />
        @endforeach
    </flux:radio.group>
    <flux:error :name="$attributes->get('name', $field['slug'])" />
</div>
