<div class="space-y-4">
    @if (count($sections) > 0)
        <div wire:sort="sortSections" class="space-y-4">
            @foreach ($sections as $index => $section)
                @php $sectionConfig = $this->sectionConfigs[$section['slug']] ?? null @endphp
                @if ($sectionConfig)
                    <flux:card x-data="{ collapsed: {{ $loop->first ? 'false' : 'true' }} }"
                        wire:key="{{ $section['key'] }}"
                        wire:sort:item="{{ $section['key'] }}"
                        class="p-3">
                        <div class="flex items-center gap-2">
                            <div wire:sort:handle
                                class="cursor-grab text-zinc-400 hover:text-zinc-600 active:cursor-grabbing">
                                <flux:icon.bars-3 class="size-5" />
                            </div>

                            <div wire:sort:ignore
                                x-on:click="collapsed = !collapsed"
                                class="flex flex-1 cursor-pointer items-center justify-between gap-3">
                                <div>
                                    <flux:heading level="3"
                                        size="sm">{{ $sectionConfig['slug'] }}</flux:heading>
                                    <flux:text class="text-xs text-zinc-500">{{ $sectionConfig['description'] }}</flux:text>
                                </div>
                                <flux:icon.chevron-up
                                    x-bind:class="{ 'rotate-180': collapsed }"
                                    class="size-4 shrink-0 text-zinc-400 transition-transform" />
                            </div>

                            <div wire:sort:ignore>
                                <flux:button variant="ghost"
                                    size="sm"
                                    icon="trash"
                                    wire:click="removeSection('{{ $section['key'] }}')"
                                    class="text-red-600 hover:text-red-700" />
                            </div>
                        </div>

                        <div x-show="!collapsed"
                            x-transition
                            class="mt-3 flex flex-wrap space-y-4 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                            @foreach ($sectionConfig['fields'] as $field)
                                <x-dynamic-component
                                    :component="$field['type']->component()"
                                    :field="$field"
                                    :value="$section['fields'][$field['slug']] ?? null"
                                    :name="'sections.'.$section['key'].'.fields.'.$field['slug']"
                                    wire:model.live.debounce.500ms="sections.{{ $section['key'] }}.fields.{{ $field['slug'] }}" />
                            @endforeach
                        </div>
                    </flux:card>
                @endif
            @endforeach
        </div>
    @endif

    <flux:button icon="plus"
        wire:click="$set('addSectionModalOpen', true)">
        {{ __('Add section') }}
    </flux:button>

    <flux:modal name="add-section"
        wire:model="addSectionModalOpen"
        class="max-w-lg">
        <flux:heading size="lg"
            level="2"
            class="mb-4">{{ __('Add section') }}</flux:heading>

        <div class="space-y-2">
            @foreach ($this->sectionConfigs as $sectionOption)
                <button type="button"
                    wire:click="addSection('{{ $sectionOption['slug'] }}')"
                    class="w-full rounded-lg border border-zinc-200 px-4 py-3 text-left transition hover:border-zinc-300 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:border-zinc-600 dark:hover:bg-zinc-800">
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $sectionOption['slug'] }}</p>
                    <p class="mt-0.5 text-xs text-zinc-500">{{ $sectionOption['description'] }}</p>
                </button>
            @endforeach
        </div>

        <div class="mt-4 flex justify-end border-t border-zinc-200 pt-4 dark:border-zinc-700">
            <flux:modal.close>
                <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
            </flux:modal.close>
        </div>
    </flux:modal>
</div>
