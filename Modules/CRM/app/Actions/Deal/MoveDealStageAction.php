<?php

namespace Modules\CRM\Actions\Deal;

use Modules\CRM\Models\Deal;
use Modules\CRM\Services\Deal\DealService;

class MoveDealStageAction
{
    public function __construct(private readonly DealService $service) {}

    public function execute(Deal $deal, int $stageId, ?string $notes = null): Deal
    {
        return $this->service->moveStage($deal, $stageId, $notes);
    }
}
