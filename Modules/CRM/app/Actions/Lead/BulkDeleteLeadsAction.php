<?php

namespace Modules\CRM\Actions\Lead;

use Modules\CRM\Services\Lead\LeadService;

class BulkDeleteLeadsAction
{
    public function __construct(private readonly LeadService $service) {}

    public function execute(array $ids): ?bool
    {
        return $this->service->bulkDelete($ids);
    }
}
