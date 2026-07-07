@props(['field' => [], 'value' => null])

@php
    $name = $attributes->get('name', $field['slug']);
    $rows = $value ?? $field['default'] ?? [];
    $rows = is_array($rows) ? $rows : [];
    $nestedFields = $field['fields'] ?? [];
@endphp

<div class="p-3 {{ $field['class'] }}">
    <flux:field>
        <flux:label>{{ $field['name'] }}</flux:label>
        @if (!empty($field['description']))
            <flux:description>{{ $field['description'] }}</flux:description>
        @endif
        <flux:error :name="$name" />
    </flux:field>

    <div class="mt-2 space-y-3">
        @forelse ($rows as $rowKey => $row)
            <flux:card wire:key="{{ $name }}-{{ $rowKey }}" class="p-3">
                <div class="flex flex-wrap gap-2">
                    @foreach ($nestedFields as $nestedField)
                        <x-dynamic-component
                            :component="$nestedField['type']->component()"
                            :field="$nestedField"
                            :value="$row[$nestedField['slug']] ?? null"
                            :name="$name.'.'.$rowKey.'.'.$nestedField['slug']"
                            wire:model.live.debounce.500ms="{{ $name }}.{{ $rowKey }}.{{ $nestedField['slug'] }}" />
                    @endforeach
                </div>

                <div class="mt-2 flex justify-end border-t border-zinc-200 pt-2 dark:border-zinc-700">
                    <flux:button
                        variant="ghost"
                        size="sm"
                        icon="trash"
                        wire:click="removeRepeaterRow('{{ $name }}', '{{ $rowKey }}')"
                        class="text-red-500 hover:text-red-700">
                        {{ __('Remove') }}
                    </flux:button>
                </div>
            </flux:card>
        @empty
            <flux:text class="text-sm text-zinc-500">{{ __('No items added yet.') }}</flux:text>
        @endforelse
    </div>

    <flux:button
        variant="ghost"
        size="sm"
        icon="plus"
        wire:click="addRepeaterRow('{{ $name }}')"
        class="mt-2">
        {{ __('Add item') }}
    </flux:button>
</div>
