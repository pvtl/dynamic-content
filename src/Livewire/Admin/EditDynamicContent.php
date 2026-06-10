<?php

declare(strict_types=1);

namespace Pvtl\DynamicContent\Livewire\Admin;

use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Pvtl\DynamicContent\Models\DynamicContent as DynamicContentModel;

class EditDynamicContent extends Component
{
    public DynamicContentModel $dynamicContent;

    public string $slug = '';

    public string $description = '';

    public array $sections = [];

    public function mount(DynamicContentModel $dynamicContent): void
    {
        $this->dynamicContent = $dynamicContent;
        $this->slug = $dynamicContent->slug;
        $this->description = $dynamicContent->description;

        $this->sections = $dynamicContent->sections
            ->map(fn ($section) => [
                'id' => $section->id,
                'key' => 'section_'.$section->id,
                'slug' => $section->slug,
                'fields' => $section->content ?? [],
            ])
            ->toArray();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9_-]+$/', Rule::unique(DynamicContentModel::class, 'slug')->ignore($this->dynamicContent->id)],
            'description' => ['required', 'string'],
        ]);

        $this->dynamicContent->update($validated);

        $this->dispatch('dc-saved', dynamicContentId: $this->dynamicContent->id);
    }

    #[On('dc-sections-saved')]
    public function onSectionsSaved(): void
    {
        Flux::toast(__('Changes have been saved'), variant: 'success', heading: __('Dynamic content updated'));
    }

    public function render(): View
    {
        return view('dynamic-content::livewire.admin.edit-dynamic-content');
    }
}
