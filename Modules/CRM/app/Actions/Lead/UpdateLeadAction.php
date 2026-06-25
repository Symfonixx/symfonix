<?php

namespace Modules\CRM\Actions\Lead;

use Modules\CRM\DTOs\Lead\LeadData;
use Modules\CRM\Models\Lead;
use Modules\CRM\Services\Lead\LeadService;

class UpdateLeadAction
{
    public function __construct(private readonly LeadService $service) {}

    public function execute(Lead $lead, LeadData $data): ?Lead
    {
        return $this->service->update($lead, $data);
    }
}
