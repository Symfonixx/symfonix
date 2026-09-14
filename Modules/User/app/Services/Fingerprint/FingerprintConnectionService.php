<?php

namespace Modules\User\Services\Fingerprint;

use Illuminate\Support\Facades\Log;

class FingerprintConnectionService
{
    public function __construct(
        private readonly FingerprintDeviceFactory $deviceFactory,
    ) {}

    /**
     * @return array{success: bool, message: string, device?: array<string, mixed>}
     */
    public function testConnection(): array
    {
        try {
            $this->deviceFactory->assertConfigured();
            $device = $this->deviceFactory->make();

            $info = $device->session(function ($connected) {
                return [
                    'firmware' => $connected->info()->firmwareVersion(),
                    'serial' => $connected->info()->serialNumber(),
                    'name' => $connected->info()->name(),
                    'time' => $connected->info()->time()->format('Y-m-d H:i:s'),
                ];
            });

            return [
                'success' => true,
                'message' => 'Connected to fingerprint machine successfully.',
                'device' => $info,
            ];
        } catch (\Throwable $e) {
            Log::warning('Fingerprint connection test failed.', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $this->formatError($e),
            ];
        }
    }

    private function formatError(\Throwable $e): string
    {
        $message = $e->getMessage();

        if (str_contains(strtolower($message), 'timed out') || str_contains(strtolower($message), 'timeout')) {
            return 'Connection timed out. Verify the device IP, port (default 4370), and network access.';
        }

        return $message;
    }
}
