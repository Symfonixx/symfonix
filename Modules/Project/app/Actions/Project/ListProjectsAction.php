<?php

namespace Modules\Project\Actions\Project;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Project\Services\Project\ProjectService;

class ListProjectsAction
{
    public function __construct(private readonly ProjectService $service) {}

    public function execute(array $filters = []): LengthAwarePaginator
    {
        return $this->service->list($filters);
    }
}
