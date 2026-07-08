<?php

namespace Modules\Base\Console;

use Illuminate\Console\Command;
use Modules\Base\Services\BackupService;
use Throwable;

class RunAutoBackupCommand extends Command
{
    protected $signature = 'base:run-auto-backup {--force : Run even if the interval has not elapsed}';

    protected $description = 'Create an automatic database backup when the configured interval has elapsed';

    public function handle(BackupService $backupService): int
    {
        if (! $this->option('force') && ! $backupService->shouldRunAutoBackup()) {
            $this->info('Auto backup skipped (disabled or interval not reached).');

            return self::SUCCESS;
        }

        try {
            $backup = $backupService->create('auto');
            $backupService->markAutoBackupRan();

            $this->info("Auto backup created: {$backup['filename']}");

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Auto backup failed: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
