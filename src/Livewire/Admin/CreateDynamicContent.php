<?php

declare(strict_types=1);

namespace Pvtl\DynamicContent\Livewire\Admin;

use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Pvtl\DynamicContent\Models\DynamicContent as DynamicContentModel;

class CreateDynamicContent extends Component
{
    public string $slug = '';

    public string $description = '';

    public function save(): void
    {
        $validated = $this->validate([
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9_-]+$/', Rule::unique(DynamicContentModel::class, 'slug')],
            'description' => ['required', 'string'],
        ]);

        $dynamicContent = DynamicContentModel::query()->create($validated);

        $this->dispatch('dc-saved', dynamicContentId: $dynamicContent->id);
    }

    #[On('dc-sections-saved')]
    public function onSectionsSaved(): void
    {
        Flux::toast(__('Changes have been saved'), variant: 'success', heading: __('Dynamic content created'));

        $this->redirect(route('admin.dynamic-content'), navigate: true);
    }

    public function render(): View
    {
        return view('dynamic-content::livewire.admin.create-dynamic-content');
    }
}
