@props(['field' => [], 'value' => null])

@php
    if (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile::canUnserialize($value)) {
        $value = \Livewire\Features\SupportFileUploads\TemporaryUploadedFile::unserializeFromLivewireRequest($value);
    }

    $isTemporary = $value instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
    $isStoredFile = ! $isTemporary && is_string($value) && $value !== '';
    $temporaryFileExists = $isTemporary && $value->exists();

    $previewUrl = match (true) {
        $isTemporary => $temporaryFileExists && $value->isPreviewable() ? $value->temporaryUrl() : null,
        $isStoredFile => \Illuminate\Support\Facades\Storage::disk(config('dynamic_content.disk'))->url($value),
        default => null,
    };

    $fileName = match (true) {
        $temporaryFileExists => $value->getClientOriginalName(),
        $isStoredFile => basename($value),
        default => null,
    };

    $modelName = $attributes->get('name', $field['slug']);
@endphp

<flux:field class="p-3 {{ $field['class'] }}">
    <flux:label>{{ $field['name'] }}</flux:label>
    @if (! empty($field['description']))
        <flux:description>{{ $field['description'] }}</flux:description>
    @endif

    <flux:file-upload accept="image/*" {{ $attributes->except('name') }}>
        @if ($previewUrl)
            <div
                class="relative overflow-hidden rounded-lg border border-zinc-200 in-data-dragging:border-blue-400 in-data-loading:opacity-60">
                <img
                    src="{{ $previewUrl }}"
                    alt="{{ $fileName }}"
                    class="max-h-[250px] w-full object-cover" />
                <div
                    class="pointer-events-none absolute inset-0 flex items-center justify-center bg-black/0 transition-colors hover:bg-black/20">
                    <span
                        class="rounded bg-black/60 px-2 py-1 text-xs font-medium text-white opacity-0 transition-opacity hover:opacity-100">
                        {{ __('Click to replace') }}
                    </span>
                </div>
            </div>
        @else
            <flux:file-upload.dropzone
                heading="{{ __('Drop image here or click to browse') }}"
                text="{{ __('PNG, JPG, GIF up to 2MB') }}"
                inline />
        @endif
    </flux:file-upload>

    @if ($isTemporary && ! $previewUrl)
        <flux:description>{{ __('This image cannot be previewed. Upload a replacement or remove it.') }}</flux:description>
    @endif

    @if ($isTemporary || $isStoredFile)
        <div x-data>
            <flux:button
                variant="ghost"
                size="sm"
                icon="trash"
                x-on:click.prevent="$wire.set('{{ $modelName }}', null)"
                class="mt-1 text-red-500 hover:text-red-700">
                {{ __('Remove image') }}
            </flux:button>
        </div>
    @endif

    <flux:error :name="$modelName" />
</flux:field>
