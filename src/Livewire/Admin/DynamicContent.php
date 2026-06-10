<?php

declare(strict_types=1);

namespace Pvtl\DynamicContent\Livewire\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Pvtl\DynamicContent\Models\DynamicContent as DynamicContentModel;

class DynamicContent extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[On('dynamic-content-changed')]
    public function refresh(): void
    {
        unset($this->items);
    }

    /** @return LengthAwarePaginator<int, DynamicContentModel> */
    #[Computed]
    public function items(): LengthAwarePaginator
    {
        $query = DynamicContentModel::query()->orderBy('slug');

        if ($this->search !== '') {
            $term = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($term) {
                $q->where('slug', 'like', $term)
                    ->orWhere('description', 'like', $term);
            });
        }

        return $query->paginate(15);
    }

    public function render(): View
    {
        return view('dynamic-content::livewire.admin.dynamic-content');
    }
}
