<?php

namespace Modules\CRM\Actions\Activity;

use Illuminate\Database\Eloquent\Model;
use Modules\CRM\DTOs\Activity\ActivityData;
use Modules\CRM\Models\CrmActivity;
use Modules\CRM\Services\Activity\ActivityService;

class CreateActivityAction
{
    public function __construct(private readonly ActivityService $service) {}

    public function execute(Model $subject, ActivityData $data): CrmActivity
    {
        return $this->service->create($subject, $data);
    }
}
