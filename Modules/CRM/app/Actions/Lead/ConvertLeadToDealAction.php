<?php

namespace Modules\CRM\Actions\Lead;

use Modules\CRM\Models\Lead;
use Modules\CRM\Models\Deal;
use Modules\CRM\Services\Lead\LeadConversionService;

class ConvertLeadToDealAction
{
    public function __construct(private readonly LeadConversionService $service) {}

    public function execute(Lead $lead, array $options = []): Deal
    {
        return $this->service->convert($lead, $options);
    }
}
