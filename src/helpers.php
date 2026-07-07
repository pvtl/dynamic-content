<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;

if (! function_exists('dcGetFileUrl')) {
    function dcGetFileUrl(?string $file): ?string
    {
        return $file
            ? Storage::disk(config('dynamic_content.disk'))->url($file)
            : null;
    }
}
