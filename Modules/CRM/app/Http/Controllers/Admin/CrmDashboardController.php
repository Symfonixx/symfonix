<?php

namespace Modules\CRM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\CRM\Actions\Analytics\GetCrmDashboardAnalyticsAction;
use Modules\CRM\Http\Requests\CrmDashboardRequest;
use Modules\CRM\Http\Requests\UpdateCrmDashboardLayoutRequest;
use Modules\CRM\Services\Dashboard\DashboardLayoutService;

class CrmDashboardController extends Controller
{
    public function __construct(
        private readonly GetCrmDashboardAnalyticsAction $getCrmDashboardAnalyticsAction,
        private readonly DashboardLayoutService $dashboardLayoutService,
    ) {
        $this->setActive('crm');
        $this->setActive('crm_dashboard');
    }

    public function index(CrmDashboardRequest $request)
    {
        $analytics = $this->getCrmDashboardAnalyticsAction->execute($request->validated());
        $layout = $this->dashboardLayoutService->forUser($request->user());

        return view('crm::admin.dashboard.index', compact('analytics', 'layout'));
    }

    public function updateLayout(UpdateCrmDashboardLayoutRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($request->boolean('reset')) {
            $layout = $this->dashboardLayoutService->reset($user);
        } else {
            $layout = $this->dashboardLayoutService->save($user, $request->validated('widgets'));
        }

        return response()->json([
            'message' => __('crm::dashboard.customize.saved'),
            'layout' => $layout,
        ]);
    }
}
