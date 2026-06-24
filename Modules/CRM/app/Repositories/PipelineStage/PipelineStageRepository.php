<?php

namespace Modules\CRM\Repositories\PipelineStage;

use Illuminate\Support\Collection;
use Modules\CRM\Models\PipelineStage;

interface PipelineStageRepository
{
    public function allActive(): Collection;

    public function findDefault(): ?PipelineStage;

    public function findOrFail(int $id): PipelineStage;
}
