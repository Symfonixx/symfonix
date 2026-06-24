<?php

namespace Modules\CRM\Actions\Deal;

use Modules\CRM\Services\Deal\DealService;

class BulkDeleteDealsAction
{
    public function __construct(private readonly DealService $service) {}

    public function execute(array $ids): ?bool
    {
        return $this->service->bulkDelete($ids);
    }
}
