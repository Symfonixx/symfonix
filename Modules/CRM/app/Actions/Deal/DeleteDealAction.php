<?php

namespace Modules\CRM\Actions\Deal;

use Modules\CRM\Models\Deal;
use Modules\CRM\Services\Deal\DealService;

class DeleteDealAction
{
    public function __construct(private readonly DealService $service) {}

    public function execute(Deal $deal): ?bool
    {
        return $this->service->delete($deal);
    }
}
