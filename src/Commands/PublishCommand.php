<?php

declare(strict_types=1);

namespace Pvtl\DynamicContent\Commands;

use Illuminate\Console\Command;

class PublishCommand extends Command
{
    protected $signature = 'pvtl-dynamic-content:publish';

    protected $description = 'Publish Dynamic Content migrations, config, routes, and sections config stub';

    public function handle(): int
    {
        $this->call('vendor:publish', ['--tag' => 'pvtl-dynamic-content-migrations', '--force' => true]);
        $this->call('vendor:publish', ['--tag' => 'pvtl-dynamic-content-config']);
        $this->call('vendor:publish', ['--tag' => 'pvtl-dynamic-content-routes']);
        $this->call('vendor:publish', ['--tag' => 'pvtl-dynamic-content-stubs']);

        $this->components->info('Dynamic Content resources published successfully.');
        $this->components->info('Run php artisan migrate to create the database tables.');

        return self::SUCCESS;
    }
}
