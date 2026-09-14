<?php

namespace Modules\Core\Console;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Modules\Base\Models\Settings;
use Modules\User\Services\PermissionSync;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InstallAppCommand extends Command
{
    protected $signature = 'app:install
                            {--email=admin@symfonix.com : Admin user email}
                            {--password=password : Admin user password}
                            {--name=Admin : Admin user display name}
                            {--mobile=0000000000 : Admin user mobile number}
                            {--fresh : Drop all tables and reinstall}';

    protected $description = 'Install Symfonix: migrate database, seed reference data, permissions, and create the admin user.';

    public function handle(): int
    {
        if (! $this->option('no-interaction') && ! $this->confirm('This will run migrations and seed the database. Continue?', true)) {
            return self::SUCCESS;
        }

        if ($this->option('fresh')) {
            Artisan::call('migrate:fresh', ['--force' => true]);
            $this->components->info('Database refreshed.');
        } else {
            Artisan::call('migrate', ['--force' => true]);
            $this->components->info('Database migrated.');
        }

        if (empty(config('app.key'))) {
            Artisan::call('key:generate', ['--force' => true]);
            $this->components->info('Application key generated.');
        }

        if (! $this->seedCountries()) {
            return self::FAILURE;
        }

        $this->seedPermissions((bool) $this->option('fresh'));
        $this->components->info('Permissions synchronized.');

        $this->seedRoleScenarios();
        $this->components->info('Role scenarios seeded.');

        $this->seedPipelineStages();
        $this->components->info('CRM pipeline stages seeded.');

        $this->seedTicketCategories();
        $this->components->info('Support ticket categories seeded.');

        $this->seedCurrencySettings();
        $this->components->info('Currency settings and baseline exchange rates seeded.');

        $role = Role::query()->firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);
        $role->syncPermissions(Permission::all());

        $user = User::query()->updateOrCreate(
            ['email' => $this->option('email')],
            [
                'name' => $this->option('name'),
                'password' => Hash::make($this->option('password')),
                'mobile' => $this->option('mobile'),
                'type' => 'admin',
            ]
        );

        $role->users()->syncWithoutDetaching([$user->id]);

        $this->newLine();
        $this->components->info('Symfonix installed successfully.');
        $this->line("  Email:    {$user->email}");
        $this->line("  Password: {$this->option('password')}");
        $this->line('  Default currency: '.(Settings::get('default_currency') ?: config('finance.default_currency', 'USD')));
        $this->newLine();

        return self::SUCCESS;
    }

    private function seedCountries(): bool
    {
        if (Schema::hasTable('countries') && DB::table('countries')->exists()) {
            $this->components->info('Countries already seeded.');

            return true;
        }

        $sqlFilePath = module_path('Core', 'database/db.sql');

        if (! file_exists($sqlFilePath)) {
            $this->components->error("SQL file not found: {$sqlFilePath}");

            return false;
        }

        DB::unprepared(file_get_contents($sqlFilePath));
        $this->components->info('Countries seeded.');

        return true;
    }

    private function seedPermissions(bool $pruneUnknown): void
    {
        app(PermissionSync::class)->sync(pruneUnknown: $pruneUnknown);
    }

    private function seedRoleScenarios(): void
    {
        Artisan::call('db:seed', [
            '--class' => 'Modules\\User\\Database\\Seeders\\RoleScenarioSeeder',
            '--force' => true,
        ]);
    }

    private function seedPipelineStages(): void
    {
        Artisan::call('db:seed', [
            '--class' => 'Modules\\CRM\\Database\\Seeders\\PipelineStageSeeder',
            '--force' => true,
        ]);
    }

    private function seedTicketCategories(): void
    {
        Artisan::call('db:seed', [
            '--class' => 'Modules\\Support\\Database\\Seeders\\TicketCategorySeeder',
            '--force' => true,
        ]);
    }

    private function seedCurrencySettings(): void
    {
        Artisan::call('db:seed', [
            '--class' => 'Modules\\Finance\\Database\\Seeders\\CurrencySettingsSeeder',
            '--force' => true,
        ]);
    }
}
