<?php

namespace Modules\Project\Actions\Project;

use Modules\Project\Models\Project;
use Modules\Project\Services\Project\ProjectService;

class UpdateProjectStatusAction
{
    public function __construct(private readonly ProjectService $service) {}

    public function execute(Project $project, int $statusId): Project
    {
        return $this->service->updateStatus($project, $statusId);
    }
}
