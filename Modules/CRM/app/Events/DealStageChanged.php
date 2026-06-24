<?php

namespace Modules\CRM\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\PipelineStage;

class DealStageChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Deal $deal,
        public ?PipelineStage $fromStage,
        public PipelineStage $toStage,
        public ?User $changedBy = null,
    ) {}
}
