<?php

namespace Modules\Core\Console;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
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

    /**
     * @var list<string>
     */
    private const PERMISSIONS = [
        'Settings Management',
        'CMS Management',
        'Support Management',
        'Hr Management',
        'App Monitoring',
        'Logs Management',
        'CRM Management',
        'CRM View All',
        'Sales Management',
        'Project Management',
        'Finance Management',
        'Services Management',
        'Product Management',
        'Testimonials Management',
    ];

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

        $this->seedPermissions();
        $this->components->info('Permissions seeded.');

        $this->seedPipelineStages();
        $this->components->info('CRM pipeline stages seeded.');

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

    private function seedPermissions(): void
    {
        foreach (self::PERMISSIONS as $permission) {
            Permission::query()->firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
    }

    private function seedPipelineStages(): void
    {
        Artisan::call('db:seed', [
            '--class' => 'Modules\\CRM\\Database\\Seeders\\PipelineStageSeeder',
            '--force' => true,
        ]);
    }
}
