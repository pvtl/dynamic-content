<?php

declare(strict_types=1);

namespace Pvtl\DynamicContent;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Pvtl\DynamicContent\Commands\PublishCommand;
use Pvtl\DynamicContent\Commands\PublishSectionsCommand;
use Pvtl\DynamicContent\Livewire\Admin\CreateDynamicContent;
use Pvtl\DynamicContent\Livewire\Admin\DeleteDynamicContent;
use Pvtl\DynamicContent\Livewire\Admin\DynamicContent;
use Pvtl\DynamicContent\Livewire\Admin\EditDynamicContent;
use Pvtl\DynamicContent\Livewire\Admin\SectionManager;
use Pvtl\DynamicContent\Livewire\DynamicContentRenderer;

class DynamicContentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/dynamic_content.php', 'dynamic_content');
    }

    public function boot(): void
    {
        $routesFile = file_exists(base_path('routes/dynamic_content.php'))
            ? base_path('routes/dynamic_content.php')
            : __DIR__.'/../routes/dynamic_content.php';

        $this->loadRoutesFrom($routesFile);

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'dynamic-content');

        Blade::anonymousComponentPath(__DIR__.'/../resources/views/components');

        Livewire::component('admin.dynamic-content', DynamicContent::class);
        Livewire::component('admin.create-dynamic-content', CreateDynamicContent::class);
        Livewire::component('admin.edit-dynamic-content', EditDynamicContent::class);
        Livewire::component('admin.delete-dynamic-content', DeleteDynamicContent::class);
        Livewire::component('admin.section-manager', SectionManager::class);
        Livewire::component('dynamic-content-renderer', DynamicContentRenderer::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                PublishCommand::class,
                PublishSectionsCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../database/migrations/' => database_path('migrations'),
            ], 'pvtl-dynamic-content-migrations');

            $this->publishes([
                __DIR__.'/../config/dynamic_content.php' => config_path('dynamic_content.php'),
            ], 'pvtl-dynamic-content-config');

            $this->publishes([
                __DIR__.'/../routes/dynamic_content.php' => base_path('routes/dynamic_content.php'),
            ], 'pvtl-dynamic-content-routes');

            $this->publishes([
                __DIR__.'/../stubs/sections.php' => config_path('sections.php'),
            ], 'pvtl-dynamic-content-stubs');

            $this->publishes([
                __DIR__.'/../resources/views/components' => resource_path('views/components'),
            ], 'pvtl-dynamic-content-components');
        }
    }
}
