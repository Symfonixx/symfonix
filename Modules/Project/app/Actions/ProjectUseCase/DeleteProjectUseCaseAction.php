<?php

namespace Modules\Project\Actions\ProjectUseCase;

use Modules\Project\Models\ProjectUseCase;
use Modules\Project\Services\ProjectUseCase\ProjectUseCaseService;

class DeleteProjectUseCaseAction
{
    public function __construct(private readonly ProjectUseCaseService $service) {}

    public function execute(ProjectUseCase $useCase): ?bool
    {
        return $this->service->delete($useCase);
    }
}
