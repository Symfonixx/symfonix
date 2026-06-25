<?php

namespace Modules\CRM\Actions\Lead;

use Modules\CRM\Models\Lead;
use Modules\CRM\Services\Lead\LeadService;

class UnblockLeadAction
{
    public function __construct(private readonly LeadService $service) {}

    public function execute(Lead $lead): ?Lead
    {
        return $this->service->unblock($lead);
    }
}
