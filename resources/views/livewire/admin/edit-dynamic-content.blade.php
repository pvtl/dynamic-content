<x-app.page :title="__('Edit dynamic content')"
    :subtitle="__('Update the slug and description for this content block.')"
    :full-width="true">

    <div class="mb-6 flex justify-end">
        <flux:button icon="arrows-pointing-out"
            wire:click="$dispatchTo('admin.full-page-dynamic-content-editor', 'open-full-page-dynamic-content-editor', { dynamicContentId: {{ $dynamicContent->id }} })">
            {{ __('Full page edit') }}
        </flux:button>
    </div>

    <x-dynamic-content::admin.dynamic-content-editor :editor-id="$editorId"
        :sections="$sections" />

    <livewire:admin.full-page-dynamic-content-editor />

</x-app.page>
