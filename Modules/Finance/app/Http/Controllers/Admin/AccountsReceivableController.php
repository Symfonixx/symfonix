<?php

namespace Modules\Finance\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Modules\Finance\Services\CurrencyService;
use Modules\Finance\Services\FinanceService;

class AccountsReceivableController extends Controller
{
    public function __construct(
        private readonly FinanceService $financeService,
    ) {
        $this->setActive('finance_accounts_receivable');
    }

    public function index(): View
    {
        $currency = request('currency', app(CurrencyService::class)->displayCurrency());
        $aging = $this->financeService->getAccountsReceivableAging($currency);

        return view('finance::admin.accounts_receivable.index', compact('aging', 'currency'));
    }
}
