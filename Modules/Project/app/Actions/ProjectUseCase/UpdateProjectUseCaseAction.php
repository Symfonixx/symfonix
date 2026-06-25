<?php

namespace Modules\Project\Actions\ProjectUseCase;

use Modules\Project\DTOs\ProjectUseCase\ProjectUseCaseData;
use Modules\Project\Models\ProjectUseCase;
use Modules\Project\Services\ProjectUseCase\ProjectUseCaseService;

class UpdateProjectUseCaseAction
{
    public function __construct(private readonly ProjectUseCaseService $service) {}

    public function execute(ProjectUseCase $useCase, ProjectUseCaseData $data): ?ProjectUseCase
    {
        return $this->service->update($useCase, $data);
    }
}
