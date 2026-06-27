<?php

namespace Modules\CRM\Actions\Marketing;

use Modules\CRM\Models\MarketingCampaign;
use Modules\CRM\Services\Marketing\MarketingEmailService;

class SendMarketingEmailAction
{
    public function __construct(
        private readonly MarketingEmailService $marketingEmailService,
    ) {}

    /**
     * @param  array{
     *     subject: string,
     *     body: string,
     *     all_subscribers?: bool,
     *     subscriber_ids?: array<int>,
     *     all_contacts?: bool,
     *     contact_ids?: array<int>,
     *     all_contact_forms?: bool,
     *     contact_form_ids?: array<int>,
     *     custom_emails?: string|null,
     * }  $data
     */
    public function execute(array $data, int $userId): MarketingCampaign
    {
        return $this->marketingEmailService->send(
            $data['subject'],
            $data['body'],
            $data,
            $userId,
        );
    }
}
