<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Modules\Core\Database\Seeders\SystemDummyDataSeeder;

class SeedDummyDataCommand extends Command
{
    protected $signature = 'app:seed-dummy
                            {--fresh : Drop all tables, reinstall base data, then seed dummy data}
                            {--force : Skip confirmation prompts}';

    protected $description = 'Seed realistic dummy data with placeholder images across the whole Symfonix system.';

    public function handle(): int
    {
        if (! $this->option('force') && ! $this->option('no-interaction')) {
            $this->warn('This will create demo records (users, CRM, CMS, products, projects, etc.) with placeholder images.');

            if (! $this->confirm('Continue?', true)) {
                return self::SUCCESS;
            }
        }

        if ($this->option('fresh')) {
            if (! $this->option('force') && ! $this->option('no-interaction')) {
                if (! $this->confirm('This will WIPE the database with migrate:fresh and reinstall. Are you sure?', false)) {
                    return self::SUCCESS;
                }
            }

            Artisan::call('app:install', [
                '--fresh' => true,
                '--no-interaction' => true,
            ]);
            $this->components->info('Base install completed.');
        }

        Artisan::call('storage:link', ['--force' => true]);
        $this->components->info('Public storage linked.');

        $this->components->info('Seeding system dummy data…');

        $seeder = new SystemDummyDataSeeder;
        $seeder->setCommand($this);
        $seeder->run();

        $this->newLine();
        $this->components->info('Dummy data seeded successfully.');
        $this->line('  Customer login examples: customer1@demo.symfonix.com / password');
        $this->line('  Admin scenario users use password: password');
        $this->newLine();

        return self::SUCCESS;
    }
}
