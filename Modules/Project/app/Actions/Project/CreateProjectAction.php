<?php

namespace Modules\Project\Actions\Project;

use Modules\Project\DTOs\Project\ProjectData;
use Modules\Project\Models\Project;
use Modules\Project\Services\Project\ProjectService;

class CreateProjectAction
{
    public function __construct(private readonly ProjectService $service) {}

    public function execute(ProjectData $data): ?Project
    {
        return $this->service->create($data);
    }
}
