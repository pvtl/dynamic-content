<?php

declare(strict_types=1);

namespace Pvtl\DynamicContent\Livewire\Admin;

use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;
use Pvtl\DynamicContent\Models\DynamicContent as DynamicContentModel;

class EditDynamicContent extends Component
{
    public DynamicContentModel $dynamicContent;

    public string $slug = '';

    public string $description = '';

    #[Modelable]
    public array $sections = [];

    #[Locked]
    public string $editorId = '';

    public bool $embedded = false;

    public function mount(DynamicContentModel $dynamicContent, bool $embedded = false): void
    {
        $this->dynamicContent = $dynamicContent;
        $this->slug = $dynamicContent->slug;
        $this->description = $dynamicContent->description;
        $this->editorId = (string) Str::uuid();
        $this->embedded = $embedded;

        if ($this->sections === []) {
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
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9_-]+$/', Rule::unique(DynamicContentModel::class, 'slug')->ignore($this->dynamicContent->id)],
            'description' => ['required', 'string'],
        ]);

        $this->dynamicContent->update($validated);

        $this->dispatch(
            'dc-saved',
            dynamicContentId: $this->dynamicContent->id,
            editorId: $this->editorId,
        );
    }

    #[On('dc-sections-saved.{editorId}')]
    public function onSectionsSaved(): void
    {
        Flux::toast(__('Changes have been saved'), variant: 'success', heading: __('Dynamic content updated'));

        $this->dispatch('dynamic-content-changed');
    }

    public function render(): View
    {
        return view($this->embedded
            ? 'dynamic-content::livewire.admin.edit-dynamic-content-embedded'
            : 'dynamic-content::livewire.admin.edit-dynamic-content');
    }
}
