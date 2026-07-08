@props(['field' => [], 'value' => null])

@php $currentValue = (bool) ($value ?? $field['default'] ?? false); @endphp

<flux:field variant="inline" class="p-3 {{ $field['class'] }}">
    <flux:label>{{ $field['name'] }}</flux:label>
    @if (!empty($field['description']))
        <flux:description>{{ $field['description'] }}</flux:description>
    @endif
    <flux:switch :checked="$currentValue" {{ $attributes->except('name') }} />
    <flux:error :name="$attributes->get('name', $field['slug'])" />
</flux:field>
