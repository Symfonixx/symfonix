<?php

namespace Tests\Unit\SystemSettings;

use Modules\User\Services\Fingerprint\FingerprintConnectionService;
use Modules\User\Services\Fingerprint\FingerprintDeviceFactory;
use Tests\TestCase;

class FingerprintConnectionServiceTest extends TestCase
{
    public function test_it_returns_timeout_friendly_message_on_timeout_failure(): void
    {
        $factory = $this->createMock(FingerprintDeviceFactory::class);
        $factory->method('assertConfigured')
            ->willThrowException(new \RuntimeException('Connection timeout after 3s'));

        $service = new FingerprintConnectionService($factory);
        $result = $service->testConnection();

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Connection timed out', $result['message']);
    }
}
