<?php

namespace Modules\User\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Base\Support\Meta;
use Modules\Finance\Services\FinanceService;
use Modules\Project\Models\Project;

class DashboardController extends Controller
{
    public function __construct(private readonly FinanceService $financeService) {}

    public function index(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $companyIds = $user->companyIds();

        $projects = Project::query()
            ->whereIn('company_id', $companyIds)
            ->with(['status:id,name,color_code', 'company:id,name'])
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (Project $project) => $this->formatProjectSummary($project));

        $stats = [
            'total_projects' => Project::query()->whereIn('company_id', $companyIds)->count(),
            'active_projects' => Project::query()
                ->whereIn('company_id', $companyIds)
                ->whereHas('status', fn ($q) => $q->where('name', '!=', 'Completed'))
                ->count(),
            'unread_notifications' => $user->unreadNotifications()->count(),
        ];

        $notifications = $user->notifications()
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn ($notification) => [
                'id' => $notification->id,
                'type' => $notification->data['type'] ?? 'general',
                'message' => $notification->data['message'] ?? '',
                'url' => $notification->data['url'] ?? null,
                'read_at' => $notification->read_at?->toIso8601String(),
                'created_at' => $notification->created_at->diffForHumans(),
            ]);

        return $this->inertia('User::Portal/Dashboard', [
            'projects' => $projects,
            'stats' => $stats,
            'notifications' => $notifications,
        ], (new Meta)
            ->title(__('user::portal.pages.dashboard_title'))
            ->description(__('user::portal.pages.dashboard_description'))
            ->robots('noindex, nofollow')
            ->toArray());
    }

    private function formatProjectSummary(Project $project): array
    {
        $collection = $this->financeService->getProjectCollectionSummary($project);

        return [
            'id' => $project->id,
            'title' => $project->title,
            'company' => $project->company?->only(['id', 'name']),
            'status' => $project->status?->only(['id', 'name', 'color_code']),
            'payment_status' => $project->payment_status,
            'start_date' => $project->start_date?->toDateString(),
            'due_date' => $project->due_date?->toDateString(),
            'collection' => $collection,
        ];
    }
}
