<?php

namespace Modules\CRM\Repositories\PipelineStage;

use Illuminate\Support\Collection;
use Modules\CRM\Models\PipelineStage;

class PipelineStageModelRepository implements PipelineStageRepository
{
    public function allActive(): Collection
    {
        return PipelineStage::query()->active()->ordered()->get();
    }

    public function findDefault(): ?PipelineStage
    {
        return PipelineStage::query()->active()->where('is_default', true)->first()
            ?? PipelineStage::query()->active()->ordered()->first();
    }

    public function findOrFail(int $id): PipelineStage
    {
        return PipelineStage::query()->findOrFail($id);
    }
}
