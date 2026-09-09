<?php

namespace Modules\CRM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\CRM\Http\Requests\UpdateSalesTargetsRequest;
use Modules\CRM\Services\SalesTarget\SalesTargetService;

class SalesTargetController extends Controller
{
    public function __construct(
        private readonly SalesTargetService $salesTargetService,
    ) {
        $this->setActive('crm');
        $this->setActive('crm_settings');
        $this->setActive('crm_sales_targets');
    }

    public function index()
    {
        $reps = $this->salesTargetService->listForReps();
        $currency = config('crm.default_currency', 'USD');

        return view('crm::admin.sales-targets.index', compact('reps', 'currency'));
    }

    public function update(UpdateSalesTargetsRequest $request): RedirectResponse
    {
        $this->salesTargetService->sync($request->validated('targets', []));

        return redirect()
            ->route('admin.crm.sales-targets.index')
            ->with('success', __('crm::sales_target.saved'));
    }
}
