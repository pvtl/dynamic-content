@props(['field' => [], 'value' => null])

@php $currentValue = $value ?? $field['default'] ?? null; @endphp

<div class="p-3 {{ $field['class'] }}">
    <flux:radio.group
        :label="$field['name']"
        :description="$field['description'] ?? null"
        {{ $attributes->except('name') }}>
        @foreach ($field['options'] as $optionValue => $optionLabel)
            <flux:radio :value="$optionValue" :label="$optionLabel" :checked="$currentValue === $optionValue" />
        @endforeach
    </flux:radio.group>
    <flux:error :name="$attributes->get('name', $field['slug'])" />
</div>
