<?php

namespace Tests\Unit\Marketing;

use Modules\CRM\Services\Marketing\WhatsAppCampaignService;
use Modules\CRM\Services\Marketing\WhatsAppTemplateService;
use PHPUnit\Framework\TestCase;

class WhatsAppCampaignServiceTest extends TestCase
{
    private WhatsAppCampaignService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $templateService = $this->createMock(WhatsAppTemplateService::class);
        $this->service = new WhatsAppCampaignService($templateService);
    }

    public function test_it_normalizes_phone_numbers(): void
    {
        $this->assertSame('+20100111222', $this->service->normalizePhone('+20 100-111-222'));
        $this->assertSame('0100111222', $this->service->normalizePhone('0100 111 222'));
        $this->assertSame('', $this->service->normalizePhone('abc'));
    }

    public function test_it_parses_custom_phones_from_text(): void
    {
        $phones = $this->service->parseCustomPhones('+20 100111222, 00966555111222; 0555 000 111');

        $this->assertSame([
            '+20',
            '100111222',
            '00966555111222',
            '0555',
            '000',
            '111',
        ], $phones);
    }

    public function test_it_parses_custom_phones_from_json_array_payload(): void
    {
        $phones = $this->service->parseCustomPhones('[{"value":"+1 (555) 100-1000"},{"value":"555-200-2000"}]');

        $this->assertSame([
            '+15551001000',
            '5552002000',
        ], $phones);
    }
}
