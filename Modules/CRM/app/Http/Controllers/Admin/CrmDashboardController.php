<?php

namespace Modules\CRM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\CRM\Actions\Analytics\GetCrmDashboardAnalyticsAction;
use Modules\CRM\Http\Requests\CrmDashboardRequest;

class CrmDashboardController extends Controller
{
    public function __construct(
        private readonly GetCrmDashboardAnalyticsAction $getCrmDashboardAnalyticsAction,
    ) {
        $this->setActive('crm');
        $this->setActive('crm_dashboard');
    }

    public function index(CrmDashboardRequest $request)
    {
        $analytics = $this->getCrmDashboardAnalyticsAction->execute($request->validated());

        return view('crm::admin.dashboard.index', compact('analytics'));
    }
}
