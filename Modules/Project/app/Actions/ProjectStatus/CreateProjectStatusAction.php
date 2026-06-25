<?php

namespace Modules\Project\Actions\ProjectStatus;

use Modules\Project\DTOs\ProjectStatus\ProjectStatusData;
use Modules\Project\Models\ProjectStatus;
use Modules\Project\Services\ProjectStatus\ProjectStatusService;

class CreateProjectStatusAction
{
    public function __construct(private readonly ProjectStatusService $service) {}

    public function execute(ProjectStatusData $data): ?ProjectStatus
    {
        return $this->service->create($data);
    }
}
