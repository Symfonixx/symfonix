<?php

namespace Modules\Project\Actions\ProjectStatus;

use Modules\Project\Models\ProjectStatus;
use Modules\Project\Services\ProjectStatus\ProjectStatusService;

class DeleteProjectStatusAction
{
    public function __construct(private readonly ProjectStatusService $service) {}

    public function execute(ProjectStatus $status): ?bool
    {
        return $this->service->delete($status);
    }
}
