<?php

namespace Modules\User\Services\Fingerprint;

use Illuminate\Support\Facades\Log;
use Modules\Base\Models\Settings;
use Modules\Base\Support\FingerprintConfig;
use Modules\User\Enums\PunchState;
use Modules\User\Enums\VerifyMode as AttendanceVerifyMode;
use Modules\User\Models\AttendanceLog;
use Modules\User\Models\Employee;
use ZkTeco\Enums\VerifyMode;

class FingerprintAttendanceSyncService
{
    public function __construct(
        private readonly FingerprintDeviceFactory $deviceFactory,
    ) {}

    /**
     * Fetch attendance logs from the device and link them to employee records.
     *
     * @return array{success: bool, message: string, synced: int, skipped: int, total_on_device: int}
     */
    public function sync(): array
    {
        try {
            $this->deviceFactory->assertConfigured();
            $device = $this->deviceFactory->make();

            $records = $device->session(fn ($connected) => $connected->attendance()->all());

            $synced = 0;
            $skipped = 0;

            foreach ($records as $record) {
                $employeeId = (int) $record->userId;
                $employee = Employee::query()->find($employeeId);

                if (! $employee) {
                    $skipped++;

                    continue;
                }

                $log = AttendanceLog::query()->updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'recorded_at' => $record->recordedAt,
                        'punch_state' => PunchState::tryFrom($record->punchState->value)?->value
                            ?? PunchState::UNDEFINED->value,
                    ],
                    [
                        'device_user_id' => $record->userId,
                        'device_uid' => $record->uid,
                        'verify_mode' => $this->verifyModeCode($record->verifyMode),
                        'synced_at' => now(),
                    ]
                );

                if ($log->wasRecentlyCreated || $log->wasChanged()) {
                    $synced++;
                }
            }

            Settings::set('fingerprint_last_sync_at', now()->toDateTimeString());
            cache()->forget('settings');

            Log::info('Fingerprint attendance synced.', [
                'synced' => $synced,
                'skipped' => $skipped,
                'total_on_device' => count($records),
            ]);

            return [
                'success' => true,
                'message' => "Synced {$synced} attendance record(s).",
                'synced' => $synced,
                'skipped' => $skipped,
                'total_on_device' => count($records),
                'last_sync_at' => FingerprintConfig::get('fingerprint_last_sync_at'),
            ];
        } catch (\Throwable $e) {
            Log::error('Fingerprint attendance sync failed.', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'synced' => 0,
                'skipped' => 0,
                'total_on_device' => 0,
            ];
        }
    }

    private function verifyModeCode(VerifyMode $mode): int
    {
        return match ($mode) {
            VerifyMode::Fingerprint => AttendanceVerifyMode::FINGERPRINT->value,
            VerifyMode::Password => AttendanceVerifyMode::PASSWORD->value,
            VerifyMode::Card => AttendanceVerifyMode::CARD->value,
            VerifyMode::Face => AttendanceVerifyMode::FACE->value,
            VerifyMode::Other => AttendanceVerifyMode::OTHER->value,
        };
    }
}
