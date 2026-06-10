@props(['field' => [], 'value' => null])

@php $currentValue = $value ?? $field['default'] ?? null; @endphp

<flux:field class="p-3 {{ $field['class'] }}">
    <flux:label>{{ $field['name'] }}</flux:label>
    @if (!empty($field['description']))
        <flux:description>{{ $field['description'] }}</flux:description>
    @endif
    <flux:input type="number" :value="$currentValue" {{ $attributes->except('name') }} />
    <flux:error :name="$attributes->get('name', $field['slug'])" />
</flux:field>
