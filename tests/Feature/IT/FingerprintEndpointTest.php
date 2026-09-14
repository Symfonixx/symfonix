<?php

namespace Tests\Feature\IT;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Services\Fingerprint\FingerprintConnectionService;
use Tests\Concerns\InteractsWithAdminPermissions;
use Tests\TestCase;

class FingerprintEndpointTest extends TestCase
{
    use InteractsWithAdminPermissions;
    use RefreshDatabase;

    public function test_test_fingerprint_endpoint_calls_connection_service(): void
    {
        $user = $this->createAdminWithPermissions(['settings.system.edit']);

        $mock = $this->createMock(FingerprintConnectionService::class);
        $mock->expects($this->once())
            ->method('testConnection')
            ->willReturn([
                'success' => true,
                'message' => 'Connected',
            ]);

        $this->app->instance(FingerprintConnectionService::class, $mock);

        $response = $this->actingAs($user)->post(route('admin.system-configurations.test-fingerprint'), [
            'data' => [
                'fingerprint_enabled' => true,
                'fingerprint_host' => '10.0.0.10',
                'fingerprint_port' => 4370,
            ],
        ]);

        $response->assertRedirect();
    }
}
