<?php

namespace Modules\Project\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Project\Models\Project;
use Modules\Project\Models\ProjectStatus;

class ProjectStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Project $project,
        public readonly ?ProjectStatus $fromStatus,
        public readonly ProjectStatus $toStatus,
    ) {}
}
