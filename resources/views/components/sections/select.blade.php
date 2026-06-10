@props(['field' => [], 'value' => null])

@php $currentValue = $value ?? $field['default'] ?? null; @endphp

<flux:field class="p-3 {{ $field['class'] }}">
    <flux:label>{{ $field['name'] }}</flux:label>
    @if (!empty($field['description']))
        <flux:description>{{ $field['description'] }}</flux:description>
    @endif
    <flux:select :value="$currentValue" {{ $attributes->except('name') }}>
        @foreach ($field['options'] as $optionValue => $optionLabel)
            <flux:select.option :value="$optionValue" :selected="$currentValue === $optionValue">
                {{ $optionLabel }}
            </flux:select.option>
        @endforeach
    </flux:select>
    <flux:error :name="$attributes->get('name', $field['slug'])" />
</flux:field>
