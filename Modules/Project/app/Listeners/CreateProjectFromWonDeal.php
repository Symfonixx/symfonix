<?php

namespace Modules\Project\Listeners;

use Modules\CRM\Events\DealStageChanged;
use Modules\Project\Models\Project;

class CreateProjectFromWonDeal
{
    /**
     * When a deal moves to a won pipeline stage, auto-create a linked project.
     */
    public function handle(DealStageChanged $event): void
    {
        if (! $event->toStage->is_won) {
            return;
        }

        $event->deal->refresh();

        if ($event->deal->status !== \Modules\CRM\Models\Deal::STATUS_WON) {
            return;
        }

        Project::createFromDeal($event->deal);
    }
}
