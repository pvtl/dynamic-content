<?php

declare(strict_types=1);

namespace Pvtl\DynamicContent\Livewire\Admin;

use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Pvtl\DynamicContent\Models\DynamicContent as DynamicContentModel;

class DeleteDynamicContent extends Component
{
    public DynamicContentModel $content;

    public bool $modalOpen = false;

    public function open(): void
    {
        $this->modalOpen = true;
    }

    public function delete(): void
    {
        $this->content->delete();

        $this->modalOpen = false;

        $this->dispatch('dynamic-content-changed');

        Flux::toast(__('Dynamic content deleted'), variant: 'success');
    }

    public function render(): View
    {
        return view('dynamic-content::livewire.admin.delete-dynamic-content');
    }
}
