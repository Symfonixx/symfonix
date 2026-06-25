<?php

namespace Modules\Project\Actions\Project;

use Modules\Project\DTOs\Project\ProjectData;
use Modules\Project\Models\Project;
use Modules\Project\Services\Project\ProjectService;

class UpdateProjectAction
{
    public function __construct(private readonly ProjectService $service) {}

    public function execute(Project $project, ProjectData $data): ?Project
    {
        return $this->service->update($project, $data);
    }
}
