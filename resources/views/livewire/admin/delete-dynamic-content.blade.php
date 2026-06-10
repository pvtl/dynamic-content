<div>
    <flux:button variant="ghost"
        size="sm"
        icon="trash"
        wire:click="open"
        class="text-red-600 hover:text-red-700">
        {{ __('Delete') }}
    </flux:button>

    <flux:modal name="delete-dynamic-content-{{ $content->id }}"
        wire:model="modalOpen"
        class="max-w-sm">
        <div class="space-y-3">
            <flux:heading size="lg"
                level="2">{{ __('Delete dynamic content') }}</flux:heading>
            <flux:text>
                {{ __('Are you sure you want to delete this item? This action cannot be undone.') }}
            </flux:text>
        </div>

        <div class="mt-6 flex justify-end gap-2 border-t border-zinc-200 pt-4 dark:border-zinc-700">
            <flux:modal.close>
                <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
            </flux:modal.close>
            <flux:button variant="danger"
                wire:click="delete"
                wire:loading.attr="disabled">
                {{ __('Delete') }}
            </flux:button>
        </div>
    </flux:modal>
</div>
