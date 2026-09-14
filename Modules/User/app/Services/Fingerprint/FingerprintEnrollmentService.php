<?php

namespace Modules\User\Services\Fingerprint;

use Illuminate\Support\Facades\Log;
use Modules\User\Models\Employee;
use ZkTeco\Enums\Privilege;
use ZkTeco\Values\User as DeviceUser;

class FingerprintEnrollmentService
{
    public function __construct(
        private readonly FingerprintDeviceFactory $deviceFactory,
    ) {}

    /**
     * Map internal employee_id to device user_id and enroll on the machine.
     *
     * @return array{success: bool, message: string, employee_id?: int, device_user_id?: string, device_uid?: int, fingerprint_captured?: bool}
     */
    public function enroll(Employee $employee, ?int $fingerIndex = null): array
    {
        try {
            $this->deviceFactory->assertConfigured();

            if ($employee->status !== Employee::STATUS_ACTIVE) {
                return [
                    'success' => false,
                    'message' => 'Only active employees can be enrolled on the fingerprint machine.',
                ];
            }

            $deviceUserId = (string) $employee->id;
            $deviceUid = $employee->fingerprint_device_uid ?? $employee->id;

            $deviceUser = new DeviceUser(
                uid: $deviceUid,
                userId: $deviceUserId,
                name: $employee->name,
                privilege: Privilege::User,
            );

            $device = $this->deviceFactory->make();
            $device->session(fn ($connected) => $connected->users()->save($deviceUser));

            $fingerprintCaptured = false;

            if ($fingerIndex !== null) {
                $device->connect();

                try {
                    $fingerprintCaptured = $device->templates()->enroll($deviceUser, fingerIndex: $fingerIndex);
                } finally {
                    $device->disconnect();
                }
            }

            $employee->update([
                'fingerprint_device_uid' => $deviceUid,
                'fingerprint_enrolled_at' => now(),
            ]);

            Log::info('Employee enrolled on fingerprint machine.', [
                'employee_id' => $employee->id,
                'device_user_id' => $deviceUserId,
                'device_uid' => $deviceUid,
            ]);

            return [
                'success' => true,
                'message' => $fingerIndex !== null
                    ? ($fingerprintCaptured
                        ? 'Employee enrolled and fingerprint captured successfully.'
                        : 'Employee enrolled on device, but fingerprint capture did not complete.')
                    : 'Employee enrolled on fingerprint machine successfully.',
                'employee_id' => $employee->id,
                'device_user_id' => $deviceUserId,
                'device_uid' => $deviceUid,
                'fingerprint_captured' => $fingerIndex !== null ? $fingerprintCaptured : null,
            ];
        } catch (\Throwable $e) {
            Log::error('Fingerprint enrollment failed.', [
                'employee_id' => $employee->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * @return array{success: bool, message: string, enrolled: int, failed: int, results: list<array<string, mixed>>}
     */
    public function enrollAllActive(): array
    {
        $employees = Employee::query()->active()->orderBy('id')->get();
        $results = [];
        $enrolled = 0;
        $failed = 0;

        foreach ($employees as $employee) {
            $result = $this->enroll($employee);
            $results[] = [
                'employee_id' => $employee->id,
                'name' => $employee->name,
                'success' => $result['success'],
                'message' => $result['message'],
            ];

            if ($result['success']) {
                $enrolled++;
            } else {
                $failed++;
            }
        }

        return [
            'success' => $failed === 0,
            'message' => "Enrolled {$enrolled} employee(s)".($failed > 0 ? ", {$failed} failed." : '.'),
            'enrolled' => $enrolled,
            'failed' => $failed,
            'results' => $results,
        ];
    }
}
