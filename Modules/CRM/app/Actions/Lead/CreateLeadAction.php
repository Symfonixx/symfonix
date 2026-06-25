<?php

namespace Modules\CRM\Actions\Lead;

use Modules\CRM\DTOs\Lead\LeadData;
use Modules\CRM\Models\Lead;
use Modules\CRM\Services\Lead\LeadService;

class CreateLeadAction
{
    public function __construct(private readonly LeadService $service) {}

    public function execute(LeadData $data): ?Lead
    {
        return $this->service->create($data);
    }
}
