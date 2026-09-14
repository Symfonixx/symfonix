<?php

namespace Modules\User\Services\Fingerprint;

use Modules\Base\Support\FingerprintConfig;
use ZkTeco\TCP\Device;

class FingerprintDeviceFactory
{
    public function make(): Device
    {
        $config = FingerprintConfig::resolved();

        return new Device(
            host: (string) $config['host'],
            port: (int) ($config['port'] ?: 4370),
            commKey: (int) ($config['comm_key'] ?: 0),
            timeout: (float) ($config['timeout'] ?: 10),
            useUdp: false,
            nameEncoding: (string) ($config['name_encoding'] ?: 'UTF-8'),
        );
    }

    public function assertConfigured(): void
    {
        if (! FingerprintConfig::isConfigured()) {
            throw new \RuntimeException('Fingerprint machine is not configured. Enable it under System Configurations → Fingerprint.');
        }
    }
}
