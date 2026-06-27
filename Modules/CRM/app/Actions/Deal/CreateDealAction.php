<?php

namespace Modules\CRM\Actions\Deal;

use Modules\CRM\DTOs\Deal\DealData;
use Modules\CRM\Models\Deal;
use Modules\CRM\Services\Deal\DealService;

class CreateDealAction
{
    public function __construct(private readonly DealService $service) {}

    public function execute(DealData $data, array $services = []): ?Deal
    {
        return $this->service->create($data, $services);
    }
}
