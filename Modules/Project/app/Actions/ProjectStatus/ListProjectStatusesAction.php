<?php

namespace Modules\Project\Actions\ProjectStatus;

use Illuminate\Support\Collection;
use Modules\Project\Services\ProjectStatus\ProjectStatusService;

class ListProjectStatusesAction
{
    public function __construct(private readonly ProjectStatusService $service) {}

    public function execute(): Collection
    {
        return $this->service->list();
    }
}
