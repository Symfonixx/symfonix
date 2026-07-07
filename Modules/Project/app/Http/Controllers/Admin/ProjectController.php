<?php

namespace Modules\Project\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Modules\Core\Http\Requests\DeleteMultiRequest;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Deal;
use Modules\Project\Actions\Project\BulkDeleteProjectsAction;
use Modules\Project\Actions\Project\CreateProjectAction;
use Modules\Project\Actions\Project\DeleteProjectAction;
use Modules\Project\Actions\Project\ListProjectsAction;
use Modules\Project\Actions\Project\UpdateProjectAction;
use Modules\Project\DTOs\Project\ProjectData;
use Modules\Project\Http\Requests\ProjectIndexRequest;
use Modules\Project\Http\Requests\StoreProjectRequest;
use Modules\Project\Http\Requests\UpdateProjectRequest;
use Modules\Project\Models\Project;
use Modules\Project\Repositories\ProjectStatus\ProjectStatusRepository;
use Modules\Project\Services\Project\ProjectService;
use Modules\Finance\Http\Requests\StoreProjectInvoiceRequest;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ListProjectsAction $listProjectsAction,
        private readonly CreateProjectAction $createProjectAction,
        private readonly UpdateProjectAction $updateProjectAction,
        private readonly DeleteProjectAction $deleteProjectAction,
        private readonly BulkDeleteProjectsAction $bulkDeleteProjectsAction,
        private readonly ProjectStatusRepository $statusRepository,
        private readonly ProjectService $projectService,
        private readonly \Modules\Finance\Services\FinanceService $financeService,
        private readonly \Modules\Finance\Services\InvoiceService $invoiceService,
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
            $this->projectService->storeAttachments($project, $request->file('attachments', []));
        }

        return redirect()->route('admin.projects.index');
    }

    public function show(Project $project)
    {
        $this->financeService->updateProjectPaymentStatus($project->id);
        $project->refresh();
        $project->load(['company', 'status', 'deal', 'invoices' => fn ($q) => $q->with('company:id,name')]);

        return view('project::admin.project.show', [
            'project' => $project,
            'collectionSummary' => $this->financeService->getProjectCollectionSummary($project),
        ]);
    }

    public function edit(Project $project)
    {
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

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $data = ProjectData::fromRequest($request->validated());
        $updated = $this->updateProjectAction->execute($project, $data);

        if ($updated) {
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

    private function formData(?Project $project = null): array
    {
        return [
            'companies' => $this->companies(),
            'statuses' => $this->statusRepository->allOrdered(),
            'deals' => $this->deals($project),
            'defaultStatusId' => $this->statusRepository->allOrdered()->first()?->id,
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

