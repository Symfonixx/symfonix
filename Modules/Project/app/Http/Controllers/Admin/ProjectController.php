<?php

namespace Modules\Project\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Modules\Core\Http\Requests\DeleteMultiRequest;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Deal;
use Modules\Finance\Http\Requests\StoreProjectInvoiceRequest;
use Modules\Finance\Models\ExpenseCategory;
use Modules\Finance\Services\FinanceService;
use Modules\Finance\Services\InvoiceService;
use Modules\Project\Actions\Project\BulkDeleteProjectsAction;
use Modules\Project\Actions\Project\CreateProjectAction;
use Modules\Project\Actions\Project\DeleteProjectAction;
use Modules\Project\Actions\Project\ListProjectsAction;
use Modules\Project\Actions\Project\UpdateProjectAction;
use Modules\Project\Actions\Project\UpdateProjectStatusAction;
use Modules\Project\DTOs\Project\ProjectData;
use Modules\Project\Http\Requests\FinishProjectEmployeeRequest;
use Modules\Project\Http\Requests\ProjectIndexRequest;
use Modules\Project\Http\Requests\StoreProjectEmployeeRequest;
use Modules\Project\Http\Requests\StoreProjectExpenseRequest;
use Modules\Project\Http\Requests\StoreProjectRequest;
use Modules\Project\Http\Requests\UpdateProjectRequest;
use Modules\Project\Http\Requests\UpdateProjectStatusRequest;
use Modules\Project\Models\Project;
use Modules\Project\Models\ProjectEmployee;
use Modules\Project\Repositories\ProjectStatus\ProjectStatusRepository;
use Modules\Project\Services\Project\ProjectCostingService;
use Modules\Project\Services\Project\ProjectService;
use Modules\Services\Models\Service;
use Modules\Tax\Models\TaxRate;
use Modules\Tax\Services\TaxCalculationService;
use Modules\Tax\Services\TaxRate\TaxRateService;
use Modules\User\Models\Employee;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ListProjectsAction $listProjectsAction,
        private readonly CreateProjectAction $createProjectAction,
        private readonly UpdateProjectAction $updateProjectAction,
        private readonly DeleteProjectAction $deleteProjectAction,
        private readonly BulkDeleteProjectsAction $bulkDeleteProjectsAction,
        private readonly UpdateProjectStatusAction $updateProjectStatusAction,
        private readonly ProjectStatusRepository $statusRepository,
        private readonly ProjectService $projectService,
        private readonly ProjectCostingService $projectCostingService,
        private readonly FinanceService $financeService,
        private readonly InvoiceService $invoiceService,
        private readonly TaxRateService $taxRateService,
    ) {
        $this->authorizeResource(Project::class, 'project');
        $this->setActive('projects');
    }

    public function index(ProjectIndexRequest $request)
    {
        $filters = $request->validated();
        $model = $this->listProjectsAction->execute($filters);
        $companies = $this->companies();
        $statuses = $this->statusRepository->allOrdered();

        return view('project::admin.project.index', compact('model', 'filters', 'companies', 'statuses'));
    }

    public function create()
    {
        return view('project::admin.project.create', $this->formData());
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $data = ProjectData::fromRequest($request->validated());
        $project = $this->createProjectAction->execute($data);

        if ($project) {
            $this->projectService->syncServices($project, $request->input('service_ids', []));
            $this->projectService->storeAttachments($project, $request->file('attachments', []));
        }

        return redirect()->route('admin.projects.index');
    }

    public function show(Project $project)
    {
        $project->load([
            'company',
            'status',
            'deal',
            'services:services.id,title',
            'testimonial.customer:id,name,email,img',
            'invoices' => fn ($q) => $q->with('company:id,name'),
            'assignments.employee',
        ]);

        return view('project::admin.project.show', [
            'project' => $project,
            'collectionSummary' => $this->financeService->getProjectCollectionSummary($project),
            'profitAndLoss' => $this->projectCostingService->getProjectProfitAndLoss($project),
            'employees' => Employee::query()->assignable()->get(['id', 'name', 'email']),
            'expenseCategories' => ExpenseCategory::query()->orderBy('name')->get(['id', 'name']),
            'taxRates' => $this->taxRateService->activeOptions(),
            'statuses' => $this->statusRepository->allOrdered(),
        ]);
    }

    public function updateStatus(UpdateProjectStatusRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $this->updateProjectStatusAction->execute(
            $project,
            (int) $request->validated('project_status_id')
        );

        session()->flushMessage(true, __('project::project.messages.status_updated'));

        return back();
    }

    public function edit(Project $project)
    {
        $project->loadMissing('services:services.id');

        return view('project::admin.project.edit', array_merge([
            'project' => $project,
        ], $this->formData($project)));
    }

    public function storeInvoice(StoreProjectInvoiceRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('view', $project);

        $this->invoiceService->createForProject($project, $request->validated());

        session()->flushMessage(true, __('finance::invoice.messages.created'));

        return redirect()->route('admin.projects.show', $project);
    }

    public function assignEmployee(StoreProjectEmployeeRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $project->assignments()->create($request->validated());

        session()->flushMessage(true, __('project::project.messages.employee_assigned'));

        return redirect()->route('admin.projects.show', $project);
    }

    public function finishEmployee(
        FinishProjectEmployeeRequest $request,
        Project $project,
        ProjectEmployee $assignment
    ): RedirectResponse {
        $this->authorize('update', $project);
        $this->ensureAssignmentBelongsToProject($project, $assignment);

        if ($assignment->isActive()) {
            $assignment->finish($request->date('ended_at'));
        }

        session()->flushMessage(true, __('project::project.messages.employee_finished'));

        return redirect()->route('admin.projects.show', $project);
    }

    public function removeEmployee(Project $project, ProjectEmployee $assignment): RedirectResponse
    {
        $this->authorize('update', $project);
        $this->ensureAssignmentBelongsToProject($project, $assignment);

        $assignment->delete();

        session()->flushMessage(true, __('project::project.messages.employee_removed'));

        return redirect()->route('admin.projects.show', $project);
    }

    public function storeExpense(StoreProjectExpenseRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('view', $project);

        $data = $request->validated();

        $taxRate = ! empty($data['tax_rate_id'])
            ? TaxRate::query()->active()->find((int) $data['tax_rate_id'])
            : ($project->tax_rate_id ? TaxRate::query()->active()->find($project->tax_rate_id) : null);

        $amounts = app(TaxCalculationService::class)->calculateLine(1, (float) $data['amount'], $taxRate);

        $this->financeService->logTransaction([
            'flow' => 'expense',
            'amount' => $amounts['amount'],
            'currency' => $data['currency'],
            'expense_category_id' => $data['expense_category_id'],
            'tax_rate_id' => $taxRate?->id,
            'tax_amount' => $amounts['tax_amount'],
            'description' => $data['description'] ?: __('project::project.messages.expense_description', [
                'title' => $project->title,
            ]),
            'transaction_date' => $data['transaction_date'],
            'reference_type' => Project::class,
            'reference_id' => $project->id,
        ]);

        session()->flushMessage(true, __('project::project.messages.expense_logged'));

        return redirect()->route('admin.projects.show', $project);
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $data = ProjectData::fromRequest($request->validated());
        $updated = $this->updateProjectAction->execute($project, $data);

        if ($updated) {
            $this->projectService->syncServices($updated, $request->input('service_ids', []));
            $this->projectService->storeAttachments($updated, $request->file('attachments', []));
        }

        return redirect()->route('admin.projects.index');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->deleteProjectAction->execute($project);

        return redirect()->route('admin.projects.index');
    }

    public function deleteMulti(DeleteMultiRequest $request): RedirectResponse
    {
        $this->bulkDeleteProjectsAction->execute($request->input('ids', []));

        return back();
    }

    private function ensureAssignmentBelongsToProject(Project $project, ProjectEmployee $assignment): void
    {
        abort_unless((int) $assignment->project_id === (int) $project->id, 404);
    }

    private function formData(?Project $project = null): array
    {
        return [
            'companies' => $this->companies(),
            'statuses' => $this->statusRepository->allOrdered(),
            'deals' => $this->deals($project),
            'services' => Service::query()
                ->select(['id', 'title'])
                ->orderBy('title')
                ->get(),
            'defaultStatusId' => $this->statusRepository->allOrdered()->first()?->id,
            'taxRates' => app(TaxRateService::class)->activeOptions(),
        ];
    }

    private function companies(): Collection
    {
        return Company::query()->select(['id', 'name'])->orderBy('name')->get();
    }

    private function deals(?Project $project = null): Collection
    {
        return Deal::query()
            ->select(['id', 'title', 'company_id', 'status'])
            ->where(function ($query) use ($project) {
                $query->whereIn('status', [Deal::STATUS_OPEN, Deal::STATUS_WON]);

                if ($project?->deal_id) {
                    $query->orWhere('id', $project->deal_id);
                }
            })
            ->whereDoesntHave('project', function ($query) use ($project) {
                if ($project) {
                    $query->where('id', '!=', $project->id);
                }
            })
            ->with('company:id,name')
            ->orderBy('title')
            ->get();
    }
}
