<?php

namespace Modules\Finance\Providers;

use Livewire\Livewire;
use Modules\CRM\Events\SubscriptionCreated;
use Modules\Finance\Listeners\BillNewSubscription;
use Modules\Finance\Livewire\DailyTransactionLogger;
use Modules\Finance\Livewire\FinancialDashboard;
use Modules\Finance\Livewire\TodayTransactions;

class EventServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        SubscriptionCreated::class => [
            BillNewSubscription::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();

        Livewire::component('finance.financial-dashboard', FinancialDashboard::class);
        Livewire::component('finance.daily-transaction-logger', DailyTransactionLogger::class);
        Livewire::component('finance.today-transactions', TodayTransactions::class);
    }
}
