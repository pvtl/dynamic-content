<?php

use App\Enums\Permission;
use Illuminate\Support\Facades\Route;
use Pvtl\DynamicContent\Livewire\Admin\CreateDynamicContent;
use Pvtl\DynamicContent\Livewire\Admin\DynamicContent;
use Pvtl\DynamicContent\Livewire\Admin\EditDynamicContent;

Route::livewire('/admin/dynamic-content', DynamicContent::class)
    ->name('admin.dynamic-content');

Route::livewire('/admin/dynamic-content/create', CreateDynamicContent::class)
    ->name('admin.dynamic-content.create');

Route::livewire('/admin/dynamic-content/{dynamicContent}/edit', EditDynamicContent::class)
    ->name('admin.dynamic-content.edit');
