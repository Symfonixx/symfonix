<?php

namespace Modules\User\Console;

use Illuminate\Console\Command;
use Modules\User\Services\Fingerprint\FingerprintAttendanceSyncService;

class SyncFingerprintAttendanceCommand extends Command
{
    protected $signature = 'user:sync-fingerprint-attendance';

    protected $description = 'Fetch attendance logs from the configured ZKTeco fingerprint machine.';

    public function handle(FingerprintAttendanceSyncService $syncService): int
    {
        $result = $syncService->sync();

        if ($result['success']) {
            $this->components->info($result['message']);

            return self::SUCCESS;
        }

        $this->components->error($result['message']);

        return self::FAILURE;
    }
}
