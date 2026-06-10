@props(['field' => [], 'value' => null])

@php
    $isTemporary = $value instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
    $isStoredFile = ! $isTemporary && is_string($value) && $value !== '';

    $fileName = match (true) {
        $isTemporary => $value->getClientOriginalName(),
        $isStoredFile => basename($value),
        default => null,
    };

    $fileSize = $isTemporary ? $value->getSize() : null;
    $fileUrl = $isStoredFile ? \Illuminate\Support\Facades\Storage::url($value) : null;
    $modelName = $attributes->get('name', $field['slug']);
@endphp

<flux:field class="p-3 {{ $field['class'] }}">
    <flux:label>{{ $field['name'] }}</flux:label>
    @if (! empty($field['description']))
        <flux:description>{{ $field['description'] }}</flux:description>
    @endif

    <flux:file-upload {{ $attributes->except('name') }}>
        @if ($fileName)
            <div
                class="flex items-center gap-3 rounded-lg border border-zinc-200 p-3 in-data-dragging:border-blue-400 in-data-loading:opacity-60">
                <flux:icon.document class="size-8 shrink-0 text-zinc-400" />
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-zinc-800">{{ $fileName }}</p>
                    <p class="text-xs text-zinc-500">{{ __('Click to replace') }}</p>
                </div>
            </div>
        @else
            <flux:file-upload.dropzone
                heading="{{ __('Drop file here or click to browse') }}"
                text="{{ __('PDF, DOC, XLS up to 5MB') }}"
                inline />
        @endif
    </flux:file-upload>

    @if ($fileName)
        <div x-data class="mt-1 flex items-center gap-3">
            @if ($fileUrl)
                <a
                    href="{{ $fileUrl }}"
                    target="_blank"
                    class="flex items-center gap-1 text-sm text-blue-600 underline hover:text-blue-800">
                    <flux:icon.arrow-down-tray class="size-4" />
                    {{ __('Download') }}
                </a>
            @endif
            <flux:button
                variant="ghost"
                size="sm"
                icon="trash"
                x-on:click.prevent="$wire.set('{{ $modelName }}', null)"
                class="text-red-500 hover:text-red-700">
                {{ __('Remove file') }}
            </flux:button>
        </div>
    @endif

    <flux:error :name="$modelName" />
</flux:field>
