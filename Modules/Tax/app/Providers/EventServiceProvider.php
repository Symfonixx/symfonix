<?php

namespace Modules\Tax\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Finance\Events\InvoiceSentToCustomer;
use Modules\Finance\Events\JournalEntryPosted;
use Modules\Tax\Listeners\RecordInputTaxOnExpense;
use Modules\Tax\Listeners\RecordOutputTaxOnInvoice;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        InvoiceSentToCustomer::class => [
            RecordOutputTaxOnInvoice::class,
        ],
        JournalEntryPosted::class => [
            RecordInputTaxOnExpense::class,
        ],
    ];
}
