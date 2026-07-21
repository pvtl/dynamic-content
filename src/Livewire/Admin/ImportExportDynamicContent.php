<?php

declare(strict_types=1);

namespace Pvtl\DynamicContent\Livewire\Admin;

use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rules\File;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Pvtl\DynamicContent\Models\DynamicContent;
use Pvtl\DynamicContent\Services\DynamicContentJsonService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportExportDynamicContent extends Component
{
    use WithFileUploads;

    public DynamicContent $content;

    public ?TemporaryUploadedFile $importFile = null;

    public bool $modalOpen = false;

    public function export(DynamicContentJsonService $dynamicContentJsonService): StreamedResponse
    {
        $filename = $this->content->slug.'.json';

        return response()->streamDownload(function () use ($dynamicContentJsonService): void {
            echo $dynamicContentJsonService->export($this->content);
        }, $filename, ['Content-Type' => 'application/json']);
    }

    public function openImport(): void
    {
        $this->resetValidation();
        $this->importFile = null;
        $this->modalOpen = true;
    }

    public function import(DynamicContentJsonService $dynamicContentJsonService): void
    {
        $this->validate([
            'importFile' => ['required', File::types(['json'])->max('5mb')],
        ]);

        $dynamicContentJsonService->replace($this->content, $this->importFile->get());

        $this->content->refresh();
        $this->importFile = null;
        $this->modalOpen = false;

        $this->dispatch('dynamic-content-changed');

        Flux::toast(__('Dynamic content imported'), variant: 'success');
    }

    public function render(): View
    {
        return view('dynamic-content::livewire.admin.import-export-dynamic-content');
    }
}
