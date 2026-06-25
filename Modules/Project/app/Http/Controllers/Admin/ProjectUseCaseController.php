<?php

namespace Modules\Project\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Cms\Enums\CmsStatus;
use Modules\Core\Http\Requests\DeleteMultiRequest;
use Modules\Project\Actions\ProjectUseCase\BulkDeleteProjectUseCasesAction;
use Modules\Project\Actions\ProjectUseCase\CreateProjectUseCaseAction;
use Modules\Project\Actions\ProjectUseCase\DeleteProjectUseCaseAction;
use Modules\Project\Actions\ProjectUseCase\ListProjectUseCasesAction;
use Modules\Project\Actions\ProjectUseCase\UpdateProjectUseCaseAction;
use Modules\Project\DTOs\ProjectUseCase\ProjectUseCaseData;
use Modules\Project\Http\Requests\StoreProjectUseCaseRequest;
use Modules\Project\Http\Requests\UpdateProjectUseCaseRequest;
use Modules\Project\Models\Project;
use Modules\Project\Models\ProjectUseCase;

class ProjectUseCaseController extends Controller
{
    public function __construct(
        private readonly ListProjectUseCasesAction $listProjectUseCasesAction,
        private readonly CreateProjectUseCaseAction $createProjectUseCaseAction,
        private readonly UpdateProjectUseCaseAction $updateProjectUseCaseAction,
        private readonly DeleteProjectUseCaseAction $deleteProjectUseCaseAction,
        private readonly BulkDeleteProjectUseCasesAction $bulkDeleteProjectUseCasesAction,
    ) {
        $this->setActive('projects');
        $this->setActive('project_use_cases');
    }

    public function index()
    {
        $this->authorize('viewAny', ProjectUseCase::class);

        $model = $this->listProjectUseCasesAction->execute((int) config('core.page_size', 15));

        return view('project::admin.use_case.index', compact('model'));
    }

    public function create()
    {
        $this->authorize('create', ProjectUseCase::class);

        $projects = Project::query()->select(['id', 'title'])->latest()->get();
        $nextSortOrder = (int) ProjectUseCase::query()->max('sort_order') + 1;

        return view('project::admin.use_case.create', compact('projects', 'nextSortOrder'));
    }

    public function store(StoreProjectUseCaseRequest $request): RedirectResponse
    {
        $this->authorize('create', ProjectUseCase::class);

        $payload = $request->validated();
        $payload['image'] = $request->file('image');
        $payload['featured'] = $request->boolean('featured');
        $payload['status'] = $request->boolean('publish') ? CmsStatus::PUBLISHED : CmsStatus::ARCHIVED;

        $data = ProjectUseCaseData::fromRequest($payload);
        $this->createProjectUseCaseAction->execute($data);

        return redirect()->route('admin.project-use-cases.index');
    }

    public function edit(ProjectUseCase $project_use_case)
    {
        $this->authorize('update', $project_use_case);

        $projects = Project::query()->select(['id', 'title'])->latest()->get();

        return view('project::admin.use_case.edit', [
            'useCase' => $project_use_case,
            'projects' => $projects,
        ]);
    }

    public function update(UpdateProjectUseCaseRequest $request, ProjectUseCase $project_use_case): RedirectResponse
    {
        $this->authorize('update', $project_use_case);

        $payload = $request->validated();
        $payload['image'] = $request->file('image');
        $payload['featured'] = $request->boolean('featured');
        $payload['status'] = $request->boolean('publish') ? CmsStatus::PUBLISHED : CmsStatus::ARCHIVED;

        $data = ProjectUseCaseData::fromRequest($payload);
        $this->updateProjectUseCaseAction->execute($project_use_case, $data);

        return redirect()->route('admin.project-use-cases.index');
    }

    public function destroy(ProjectUseCase $project_use_case): RedirectResponse
    {
        $this->authorize('delete', $project_use_case);
        $this->deleteProjectUseCaseAction->execute($project_use_case);

        return redirect()->route('admin.project-use-cases.index');
    }

    public function deleteMulti(DeleteMultiRequest $request): RedirectResponse
    {
        $this->bulkDeleteProjectUseCasesAction->execute($request->input('ids', []));

        return back();
    }
}
