<?php

declare(strict_types=1);

namespace Pvtl\DynamicContent\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Pvtl\DynamicContent\Models\DynamicContent;

class DynamicContentRenderer extends Component
{
    #[Locked]
    public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    #[Computed]
    public function sections(): Collection
    {
        return DynamicContent::getBySlug($this->slug)->sections;
    }

    #[Computed]
    public function sectionConfigs(): array
    {
        return collect(config('sections', []))->keyBy('slug')->toArray();
    }

    public function component(string $name): string
    {
        return config('dynamic_content.component_directory').'.'.$name;
    }

    public function render(): View
    {
        return view('dynamic-content::livewire.dynamic-content-renderer');
    }
}
