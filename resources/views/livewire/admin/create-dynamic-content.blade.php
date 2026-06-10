<x-app.page :title="__('Add dynamic content')"
    :subtitle="__('Create a new dynamic content block.')"
    :full-width="true">

    <div class="space-y-6">
        <flux:field>
            <flux:label>{{ __('Slug') }}</flux:label>
            <flux:description>{{ __('Lowercase letters, numbers, hyphens and underscores only.') }}</flux:description>
            <flux:input wire:model="slug"
                :placeholder="__('e.g. homepage-hero')"
                autofocus />
            <flux:error name="slug" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Description') }}</flux:label>
            <flux:textarea wire:model="description"
                :placeholder="__('Describe where and how this content is used.')"
                rows="3" />
            <flux:error name="description" />
        </flux:field>

        <livewire:admin.section-manager :initial-sections="[]" />

        <div class="flex items-center gap-3 border-t border-zinc-200 pt-6 dark:border-zinc-700">
            <flux:button variant="primary"
                wire:click="save"
                wire:loading.attr="disabled">
                {{ __('Create') }}
            </flux:button>

        </div>
    </div>

</x-app.page>
