<?php

declare(strict_types=1);

namespace Pvtl\DynamicContent\Commands;

use Illuminate\Console\Command;

class PublishSectionsCommand extends Command
{
    protected $signature = 'pvtl-dynamic-content:publish-sections';

    protected $description = 'Publish the Dynamic Content section field Blade components to resources/views/components/sections/';

    public function handle(): int
    {
        $this->call('vendor:publish', ['--tag' => 'pvtl-dynamic-content-components', '--force' => true]);

        $this->components->info('Section Blade components published to resources/views/components/sections/.');

        return self::SUCCESS;
    }
}
