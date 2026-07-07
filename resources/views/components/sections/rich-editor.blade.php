@props(['field' => [], 'value' => null])

@php $currentValue = $value ?? $field['default'] ?? null; @endphp

<flux:field class="p-3 {{ $field['class'] }}">
    <flux:label>{{ $field['name'] }}</flux:label>
    @if (!empty($field['description']))
        <flux:description>{{ $field['description'] }}</flux:description>
    @endif

    <flux:editor {{ $attributes->except('name') }}>
        <flux:editor.toolbar>
            <flux:dropdown>
                <flux:editor.button tooltip="{{ __('Heading') }}">
                    <span class="text-xs font-semibold">{{ __('H') }}</span>
                    <flux:icon.chevron-down variant="micro" class="ms-0.5 size-3" />
                </flux:editor.button>

                <flux:menu>
                    <flux:menu.item
                        x-on:click="$el.closest('[data-flux-editor]').editor.chain().focus().setParagraph().run()">
                        {{ __('Paragraph') }}
                    </flux:menu.item>
                    @for ($level = 1; $level <= 6; $level++)
                        <flux:menu.item
                            x-on:click="$el.closest('[data-flux-editor]').editor.chain().focus().toggleHeading({ level: {{ $level }} }).run()">
                            {{ __('Heading :level', ['level' => $level]) }}
                        </flux:menu.item>
                    @endfor
                </flux:menu>
            </flux:dropdown>

            <flux:editor.separator />
            <flux:editor.bold />
            <flux:editor.italic />
            <flux:editor.strike />
            <flux:editor.underline />
            <flux:editor.separator />
            <flux:editor.bullet />
            <flux:editor.ordered />
            <flux:editor.blockquote />
            <flux:editor.separator />
            <flux:editor.link />
            <flux:editor.separator />
            <flux:editor.align />
        </flux:editor.toolbar>

        <flux:editor.content>{!! $currentValue !!}</flux:editor.content>
    </flux:editor>

    <flux:error :name="$attributes->get('name', $field['slug'])" />
</flux:field>
