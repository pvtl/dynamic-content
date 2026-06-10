<x-app.page :title="__('Dynamic content')"
    :subtitle="__('Manage dynamic content blocks used across the site.')"
    :full-width="true">

    <div class="mb-4 flex items-center justify-between gap-4">
        <div class="max-w-sm flex-1">
            <flux:input wire:model.live.debounce.300ms="search"
                icon="magnifying-glass"
                :placeholder="__('Search by slug or description')" />
        </div>

        <flux:button variant="primary"
            icon="plus"
            wire:navigate
            :href="route('admin.dynamic-content.create')">
            {{ __('Add content') }}
        </flux:button>
    </div>

    <flux:card>
        @if ($this->items->isNotEmpty())
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Slug') }}</flux:table.column>
                    <flux:table.column>{{ __('Description') }}</flux:table.column>
                    <flux:table.column>{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($this->items as $item)
                        <flux:table.row :key="$item->id">
                            <flux:table.cell class="font-mono font-medium">
                                {{ $item->slug }}
                            </flux:table.cell>

                            <flux:table.cell class="max-w-md text-zinc-600">
                                {{ $item->description ?: '—' }}
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex items-center gap-2">
                                    <flux:button variant="ghost"
                                        size="sm"
                                        icon="pencil"
                                        wire:navigate
                                        :href="route('admin.dynamic-content.edit', $item)">
                                        {{ __('Edit') }}
                                    </flux:button>

                                    <livewire:admin.delete-dynamic-content :content="$item" :key="'delete-'.$item->id" />
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>

            <div class="mt-6 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                {{ $this->items->links() }}
            </div>
        @else
            <div class="py-8 text-center">
                <flux:icon.squares-2x2 class="mx-auto size-12 text-zinc-400" />
                <flux:heading size="lg"
                    level="2"
                    class="mt-4">{{ __('No dynamic content found') }}</flux:heading>
                <flux:text class="mt-2">
                    {{ $search !== '' ? __('No items match your search.') : __('No dynamic content has been added yet.') }}
                </flux:text>
            </div>
        @endif
    </flux:card>

</x-app.page>
