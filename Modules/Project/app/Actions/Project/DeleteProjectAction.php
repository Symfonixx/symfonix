<?php

namespace Modules\Project\Actions\Project;

use Modules\Project\Models\Project;
use Modules\Project\Services\Project\ProjectService;

class DeleteProjectAction
{
    public function __construct(private readonly ProjectService $service) {}

    public function execute(Project $project): ?bool
    {
        return $this->service->delete($project);
    }
}
