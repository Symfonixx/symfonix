<?php

namespace Modules\Project\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Base\Support\Meta;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Services\FinanceService;
use Modules\Project\Models\Project;

class ProjectController extends Controller
{
    public function __construct(private readonly FinanceService $financeService) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Project::class);

        /** @var User $user */
        $user = $request->user();

        $projects = Project::query()
            ->whereIn('company_id', $user->companyIds())
            ->with(['status:id,name,color_code', 'company:id,name'])
            ->latest()
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Project $project) => $this->formatProjectSummary($project));

        return $this->inertia('Project::Portal/Projects/Index', [
            'projects' => $projects,
        ], (new Meta)
            ->title(__('user::portal.pages.projects_title'))
            ->description(__('user::portal.pages.projects_description'))
            ->robots('noindex, nofollow')
            ->toArray());
    }

    public function show(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        $this->financeService->updateProjectPaymentStatus($project->id);
        $project->refresh();

        $project->load([
            'status:id,name,color_code',
            'company:id,name',
            'testimonial',
            'invoices' => fn ($q) => $q
                ->whereNotIn('status', [Invoice::STATUS_DRAFT, Invoice::STATUS_VOID])
                ->with('lines'),
        ]);

        $collection = $this->financeService->getProjectCollectionSummary($project);

        $invoices = $project->invoices->map(fn (Invoice $invoice) => [
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'status' => $invoice->status,
            'total' => (float) $invoice->total,
            'currency' => $invoice->currency,
            'issued_at' => $invoice->issued_at?->toDateString(),
            'due_at' => $invoice->due_at?->toDateString(),
            'paid_at' => $invoice->paid_at?->toDateString(),
            'pdf_url' => route('portal.invoices.pdf', $invoice),
        ]);

        $canReview = $project->canBeReviewedBy($request->user());
        $existingReview = $project->testimonial;

        return $this->inertia('Project::Portal/Projects/Show', [
            'project' => [
                'id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'status' => $project->status?->only(['id', 'name', 'color_code']),
                'company' => $project->company?->only(['id', 'name']),
                'payment_status' => $project->payment_status,
                'budget' => (float) ($project->budget ?? 0),
                'start_date' => $project->start_date?->toDateString(),
                'due_date' => $project->due_date?->toDateString(),
                'collection' => $collection,
                'is_completed' => $project->isCompleted(),
                'can_review' => $canReview,
                'review' => $existingReview ? [
                    'quote' => $existingReview->quote,
                    'status' => $existingReview->status,
                    'created_at' => $existingReview->created_at?->toDateString(),
                ] : null,
                'attachments' => collect($project->attachments ?? [])->map(fn (array $attachment) => [
                    'name' => $attachment['name'] ?? basename($attachment['path'] ?? ''),
                    'url' => ! empty($attachment['path']) ? asset('storage/'.$attachment['path']) : null,
                    'size' => $attachment['size'] ?? null,
                ])->filter(fn (array $attachment) => $attachment['url'] !== null)->values(),
            ],
            'invoices' => $invoices->values()->all(),
        ], (new Meta)
            ->title($project->title.' | '.__('user::portal.pages.projects_title'))
            ->description($project->description ?? __('user::portal.pages.project_show_description'))
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
