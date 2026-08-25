<div class="h-full overflow-y-auto bg-white p-6 dark:bg-zinc-900">
    <div class="mb-6">
        <flux:heading level="1" size="xl">{{ __('Edit dynamic content') }}</flux:heading>
        <flux:text class="mt-1">{{ __('Update the content details and manage its sections.') }}</flux:text>
    </div>

    <x-dynamic-content::admin.dynamic-content-editor :editor-id="$editorId"
        :sections="$sections" />
</div>
