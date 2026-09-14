<?php

namespace Modules\CRM\Actions\Marketing;

use Modules\CRM\Models\WhatsAppCampaign;
use Modules\CRM\Services\Marketing\WhatsAppCampaignService;

class SendWhatsAppCampaignAction
{
    public function __construct(
        private readonly WhatsAppCampaignService $campaignService,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data, int $userId): WhatsAppCampaign
    {
        return $this->campaignService->send(
            (int) $data['whatsapp_template_id'],
            $data['template_parameters'] ?? [],
            $data,
            $userId,
        );
    }
}
