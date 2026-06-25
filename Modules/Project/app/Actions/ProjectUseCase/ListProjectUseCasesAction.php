<?php

namespace Modules\Project\Actions\ProjectUseCase;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Project\Services\ProjectUseCase\ProjectUseCaseService;

class ListProjectUseCasesAction
{
    public function __construct(private readonly ProjectUseCaseService $service) {}

    public function execute(int $perPage = 15): LengthAwarePaginator
    {
        return $this->service->listAdmin($perPage);
    }
}
