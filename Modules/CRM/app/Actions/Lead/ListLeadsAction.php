<?php

namespace Modules\CRM\Actions\Lead;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\CRM\Services\Lead\LeadService;

class ListLeadsAction
{
    public function __construct(private readonly LeadService $service) {}

    public function execute(array $filters = []): LengthAwarePaginator
    {
        return $this->service->list($filters);
    }
}
