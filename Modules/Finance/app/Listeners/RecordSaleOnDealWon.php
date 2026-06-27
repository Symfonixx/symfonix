<?php

namespace Modules\Finance\Listeners;

use Modules\CRM\Events\DealStageChanged;
use Modules\CRM\Models\Deal;
use Modules\Finance\Services\FinanceService;

class RecordSaleOnDealWon
{
    public function __construct(
        private readonly FinanceService $financeService,
    ) {}

    /**
     * When a deal is marked won, log income and create a pending commission.
     */
    public function handle(DealStageChanged $event): void
    {
        if ($event->deal->status !== Deal::STATUS_WON) {
            return;
        }

        $deal = $event->deal->refresh()->load(['project', 'assignee', 'services']);

        $this->financeService->recordDealWonFinance($deal);
    }
}
