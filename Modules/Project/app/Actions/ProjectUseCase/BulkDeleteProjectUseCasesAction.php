<?php

namespace Modules\Project\Actions\ProjectUseCase;

use Modules\Project\Services\ProjectUseCase\ProjectUseCaseService;

class BulkDeleteProjectUseCasesAction
{
    public function __construct(private readonly ProjectUseCaseService $service) {}

    public function execute(array $ids): ?bool
    {
        return $this->service->bulkDelete($ids);
    }
}
