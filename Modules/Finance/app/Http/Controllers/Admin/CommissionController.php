<?php

namespace Modules\Finance\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Finance\Models\Commission;
use Modules\Finance\Services\FinanceService;

class CommissionController extends Controller
{
    public function __construct(
        private readonly FinanceService $financeService,
    ) {
        $this->setActive('finance_commissions');
    }

    public function index(): View
    {
        $this->authorize('viewAny', Commission::class);

        $commissions = Commission::query()
            ->with(['employee:id,name,email', 'deal:id,title,value'])
            ->latest()
            ->paginate((int) config('core.page_size', 15));

        return view('finance::admin.commission.index', compact('commissions'));
    }

    public function recordPayout(Commission $commission): RedirectResponse
    {
        $this->authorize('update', $commission);

        $this->financeService->recordCommissionPayout($commission->load(['employee', 'deal']));

        session()->flushMessage(true, __('finance::commission.messages.paid'));

        return back();
    }
}
