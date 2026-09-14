<?php

namespace Modules\Tax\Listeners;

use Modules\Finance\Events\InvoiceSentToCustomer;
use Modules\Tax\Services\TaxLedgerService;

class RecordOutputTaxOnInvoice
{
    public function __construct(private readonly TaxLedgerService $taxLedgerService) {}

    public function handle(InvoiceSentToCustomer $event): void
    {
        if (config('tax.record_output_tax_on', 'sent') !== 'sent') {
            return;
        }

        $this->taxLedgerService->recordOutputTaxForInvoice(
            $event->invoice->loadMissing(['lines', 'company', 'project', 'subscription'])
        );
    }
}
