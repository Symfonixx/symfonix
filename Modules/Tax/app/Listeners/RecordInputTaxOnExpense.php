<?php

namespace Modules\Tax\Listeners;

use Modules\Finance\Events\JournalEntryPosted;
use Modules\Tax\Services\TaxLedgerService;

class RecordInputTaxOnExpense
{
    public function __construct(private readonly TaxLedgerService $taxLedgerService) {}

    public function handle(JournalEntryPosted $event): void
    {
        $this->taxLedgerService->recordInputTaxForExpense($event->entry);
    }
}
