<?php

namespace Modules\Project\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Project\Actions\ProjectStatus\CreateProjectStatusAction;
use Modules\Project\Actions\ProjectStatus\DeleteProjectStatusAction;
use Modules\Project\Actions\ProjectStatus\ListProjectStatusesAction;
use Modules\Project\Actions\ProjectStatus\UpdateProjectStatusAction;
use Modules\Project\DTOs\ProjectStatus\ProjectStatusData;
use Modules\Project\Http\Requests\StoreProjectStatusRequest;
use Modules\Project\Http\Requests\UpdateProjectStatusRequest;
use Modules\Project\Models\ProjectStatus;

class ProjectStatusController extends Controller
{
    public function __construct(
        private readonly ListProjectStatusesAction $listProjectStatusesAction,
        private readonly CreateProjectStatusAction $createProjectStatusAction,
        private readonly UpdateProjectStatusAction $updateProjectStatusAction,
        private readonly DeleteProjectStatusAction $deleteProjectStatusAction,
    ) {
        $this->setActive('projects');
        $this->setActive('project_statuses');
    }

    public function index()
    {
        $this->authorize('viewAny', ProjectStatus::class);

        $statuses = $this->listProjectStatusesAction->execute();

        return view('project::admin.project_status.index', compact('statuses'));
    }

    public function create()
    {
        $this->authorize('create', ProjectStatus::class);

        $nextSortOrder = (int) ProjectStatus::query()->max('sort_order') + 1;

        return view('project::admin.project_status.create', compact('nextSortOrder'));
    }

    public function store(StoreProjectStatusRequest $request): RedirectResponse
    {
        $this->authorize('create', ProjectStatus::class);

        $data = ProjectStatusData::fromRequest($request->validated());
        $this->createProjectStatusAction->execute($data);

        return redirect()->route('admin.project-statuses.index');
    }

    public function edit(ProjectStatus $project_status)
    {
        $this->authorize('update', $project_status);

        return view('project::admin.project_status.edit', ['status' => $project_status]);
    }

    public function update(UpdateProjectStatusRequest $request, ProjectStatus $project_status): RedirectResponse
    {
        $this->authorize('update', $project_status);

        $data = ProjectStatusData::fromRequest($request->validated());
        $this->updateProjectStatusAction->execute($project_status, $data);

        return redirect()->route('admin.project-statuses.index');
    }

    public function destroy(ProjectStatus $project_status): RedirectResponse
    {
        $this->authorize('delete', $project_status);
        $this->deleteProjectStatusAction->execute($project_status);

        return redirect()->route('admin.project-statuses.index');
    }
}
