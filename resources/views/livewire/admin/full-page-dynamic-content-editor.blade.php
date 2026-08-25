<flux:modal name="full-page-dynamic-content-editor"
    wire:model.self="modalOpen"
    variant="bare"
    :closable="true"
    :dismissible="false"
    class="fixed inset-0 m-0 h-dvh! w-dvw! max-h-none! max-w-none! overflow-hidden bg-white! dark:bg-zinc-900!">
    @if ($this->dynamicContent)
        <div class="grid h-full min-h-0 grid-cols-[30%_70%]">
            <div class="min-h-0 border-e border-zinc-200 dark:border-zinc-700">
                <livewire:admin.edit-dynamic-content wire:model.live.debounce.1000ms="sections"
                    :dynamic-content="$this->dynamicContent"
                    :embedded="true"
                    :key="'full-page-edit-'.$this->dynamicContent->id" />
            </div>

            <div class="min-h-0">
                <livewire:admin.dynamic-content-preview :sections="$sections"
                    :key="'dynamic-content-preview-'.$this->dynamicContent->id" />
            </div>
        </div>
    @endif
</flux:modal>
