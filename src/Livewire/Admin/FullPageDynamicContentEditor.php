<?php

declare(strict_types=1);

namespace Pvtl\DynamicContent\Livewire\Admin;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Pvtl\DynamicContent\Models\DynamicContent as DynamicContentModel;

class FullPageDynamicContentEditor extends Component
{
    public bool $modalOpen = false;

    #[Locked]
    public ?int $dynamicContentId = null;

    public array $sections = [];

    #[On('open-full-page-dynamic-content-editor')]
    public function open(int $dynamicContentId): void
    {
        $dynamicContent = DynamicContentModel::query()
            ->with('sections')
            ->findOrFail($dynamicContentId);

        $this->dynamicContentId = $dynamicContent->id;
        $this->sections = $dynamicContent->sections
            ->mapWithKeys(function ($section): array {
                $key = 'section_'.$section->id;

                return [$key => [
                    'id' => $section->id,
                    'key' => $key,
                    'slug' => $section->slug,
                    'fields' => $section->content ?? [],
                ]];
            })
            ->all();
        $this->modalOpen = true;

        unset($this->dynamicContent);
    }

    public function updatedModalOpen(bool $open): void
    {
        if ($open) {
            return;
        }

        $this->dynamicContentId = null;
        $this->sections = [];

        unset($this->dynamicContent);
    }

    #[Computed]
    public function dynamicContent(): ?DynamicContentModel
    {
        if ($this->dynamicContentId === null) {
            return null;
        }

        return DynamicContentModel::query()->find($this->dynamicContentId);
    }

    public function render(): View
    {
        return view('dynamic-content::livewire.admin.full-page-dynamic-content-editor');
    }
}
