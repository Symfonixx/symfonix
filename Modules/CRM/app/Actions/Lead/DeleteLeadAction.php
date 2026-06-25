<?php

namespace Modules\CRM\Actions\Lead;

use Modules\CRM\Models\Lead;
use Modules\CRM\Services\Lead\LeadService;

class DeleteLeadAction
{
    public function __construct(private readonly LeadService $service) {}

    public function execute(Lead $lead): ?bool
    {
        return $this->service->delete($lead);
    }
}
