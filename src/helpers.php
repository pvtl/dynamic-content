<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

if (! function_exists('dcGetFileUrl')) {
    function dcGetFileUrl(string|TemporaryUploadedFile|null $file): ?string
    {
        if ($file instanceof TemporaryUploadedFile) {
            return $file->isPreviewable() ? $file->temporaryUrl() : null;
        }

        if (! $file) {
            return null;
        }

        if (Str::startsWith($file, ['http://', 'https://', '//'])) {
            return $file;
        }

        return Storage::disk(config('dynamic_content.disk'))->url($file);
    }
}
