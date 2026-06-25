<?php

namespace Modules\Project\Actions\Project;

use Modules\Project\Services\Project\ProjectService;

class BulkDeleteProjectsAction
{
    public function __construct(private readonly ProjectService $service) {}

    public function execute(array $ids): ?bool
    {
        return $this->service->bulkDelete($ids);
    }
}
