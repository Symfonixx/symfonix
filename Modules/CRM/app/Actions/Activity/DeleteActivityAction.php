<?php

namespace Modules\CRM\Actions\Activity;

use Modules\CRM\Models\CrmActivity;
use Modules\CRM\Services\Activity\ActivityService;

class DeleteActivityAction
{
    public function __construct(private readonly ActivityService $service) {}

    public function execute(CrmActivity $activity): bool
    {
        return $this->service->delete($activity);
    }
}
