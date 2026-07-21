<div class="flex items-center gap-2">
    <flux:button variant="ghost"
        size="sm"
        icon="arrow-down-tray"
        wire:click="export">
        {{ __('Export') }}
    </flux:button>

    <flux:button variant="ghost"
        size="sm"
        icon="arrow-up-tray"
        wire:click="openImport">
        {{ __('Import') }}
    </flux:button>

    <flux:modal name="import-dynamic-content-{{ $content->id }}"
        wire:model="modalOpen"
        class="max-w-sm">
        <form wire:submit="import"
            class="space-y-4">
            <div class="space-y-3">
                <flux:heading size="lg"
                    level="2">{{ __('Import dynamic content') }}</flux:heading>
                <flux:text>
                    {{ __('Importing will replace all sections for :slug. Image and file references are retained, but their files are not imported.', ['slug' => $content->slug]) }}
                </flux:text>
            </div>

            <flux:field>
                <flux:label>{{ __('JSON file') }}</flux:label>
                <flux:input type="file"
                    wire:model="importFile"
                    accept="application/json,.json" />
                <flux:error name="importFile" />
            </flux:field>

            <div class="flex justify-end gap-2 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button variant="danger"
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="import">
                    {{ __('Import and replace') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
