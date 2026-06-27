<?php

namespace Modules\Finance\Console;

use Illuminate\Console\Command;
use Modules\Finance\Services\InvoiceService;

class ProcessSubscriptionRenewalsCommand extends Command
{
    protected $signature = 'finance:process-subscription-renewals';

    protected $description = 'Bill due subscription renewals and mark overdue invoices';

    public function handle(InvoiceService $invoiceService): int
    {
        $count = $invoiceService->processDueSubscriptionRenewals();

        $this->info("Processed {$count} subscription renewal(s).");

        return self::SUCCESS;
    }
}
