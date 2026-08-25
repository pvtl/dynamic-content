<?php

declare(strict_types=1);

namespace Pvtl\DynamicContent\Livewire\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Support\ViewErrorBag;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\Mechanisms\FrontendAssets\FrontendAssets;
use Throwable;

class DynamicContentPreview extends Component
{
    private const array VIEWPORTS = ['base', 'mobile', 'tablet', 'desktop'];

    private const string SECTIONS_END_MARKER = '<!-- __DYNAMIC_CONTENT_PREVIEW_SECTIONS_END__ -->';

    private const string SECTIONS_START_MARKER = '<!-- __DYNAMIC_CONTENT_PREVIEW_SECTIONS_START__ -->';

    #[Reactive]
    public array $sections = [];

    #[Locked]
    public string $renderedDocument = '';

    public string $viewport = 'base';

    public function mount(): void
    {
        $this->renderedDocument = $this->renderPreviewDocument();
    }

    public function updatedSections(): void
    {
        $this->refreshRenderedSections();
    }

    public function setViewport(string $viewport): void
    {
        if (! in_array($viewport, self::VIEWPORTS, strict: true)) {
            return;
        }

        $this->viewport = $viewport;
    }

    /** @return array<string, array{label: string, width: string}> */
    public function viewportOptions(): array
    {
        return [
            'base' => ['label' => __('Base'), 'width' => __('Available width')],
            'mobile' => ['label' => __('Mobile'), 'width' => '375px'],
            'tablet' => ['label' => __('Tablet'), 'width' => '768px'],
            'desktop' => ['label' => __('Desktop'), 'width' => '1280px'],
        ];
    }

    public function render(): View
    {
        return view('dynamic-content::livewire.admin.dynamic-content-preview');
    }

    protected function refreshRenderedSections(): void
    {
        $sectionsHtml = $this->renderPreviewSections();
        $contentStart = strpos($this->renderedDocument, self::SECTIONS_START_MARKER);

        if ($contentStart === false) {
            $this->renderedDocument = $this->renderPreviewDocument();

            return;
        }

        $contentStart += strlen(self::SECTIONS_START_MARKER);
        $contentEnd = strpos($this->renderedDocument, self::SECTIONS_END_MARKER, $contentStart);

        if ($contentEnd === false) {
            $this->renderedDocument = $this->renderPreviewDocument();

            return;
        }

        $this->renderedDocument = substr($this->renderedDocument, 0, $contentStart)
            .$sectionsHtml
            .substr($this->renderedDocument, $contentEnd);
    }

    protected function renderPreviewDocument(): string
    {
        $this->shareViewErrorBag();

        try {
            $document = view('dynamic-content::livewire.admin.dynamic-content-preview-document', [
                'previewLayout' => config('dynamic_content.preview_layout'),
                'sectionsHtml' => $this->renderPreviewSections(),
                'sectionsEndMarker' => self::SECTIONS_END_MARKER,
                'sectionsStartMarker' => self::SECTIONS_START_MARKER,
            ])->render();

            return $this->injectLivewireScript($document);
        } catch (Throwable $exception) {
            report($exception);

            return view('dynamic-content::livewire.admin.dynamic-content-preview-error-document', [
                'message' => $this->previewErrorMessage($exception),
            ])->render();
        }
    }

    protected function renderPreviewSections(): string
    {
        $this->shareViewErrorBag();

        try {
            return view('dynamic-content::livewire.admin.dynamic-content-preview-sections', [
                'sections' => $this->sections,
                'sectionConfigs' => collect(config('sections', []))->keyBy('slug')->all(),
                'componentDirectory' => config('dynamic_content.component_directory'),
            ])->render();
        } catch (Throwable $exception) {
            report($exception);

            return view('dynamic-content::livewire.admin.dynamic-content-preview-error', [
                'message' => $this->previewErrorMessage($exception),
            ])->render();
        }
    }

    protected function injectLivewireScript(string $document): string
    {
        $script = FrontendAssets::js([]);

        if (! preg_match('/<\/body\s*>/i', $document)) {
            return $document.$script;
        }

        $documentWithScript = preg_replace_callback(
            '/<\/body\s*>/i',
            fn (array $matches): string => $script.$matches[0],
            $document,
            1,
        );

        return $documentWithScript ?? $document.$script;
    }

    protected function previewErrorMessage(Throwable $exception): string
    {
        return app()->hasDebugModeEnabled()
            ? $exception->getMessage()
            : __('The preview could not be rendered.');
    }

    protected function shareViewErrorBag(): void
    {
        view()->share('errors', view()->shared('errors', new ViewErrorBag));
    }
}
