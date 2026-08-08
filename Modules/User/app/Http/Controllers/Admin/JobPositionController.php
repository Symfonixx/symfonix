<?php

namespace Modules\User\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\User\Http\Requests\JobPositionIndexRequest;
use Modules\User\Http\Requests\StoreJobPositionRequest;
use Modules\User\Http\Requests\UpdateJobPositionRequest;
use Modules\User\Models\JobPosition;

class JobPositionController extends Controller
{
    public function __construct()
    {
        $this->setActive('hr');
        $this->setActive('job_positions');
    }

    public function index(JobPositionIndexRequest $request)
    {
        $filters = $request->validated();

        $model = JobPosition::query()
            ->withCount('applications')
            ->when($filters['search'] ?? null, fn ($query, string $search) => $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            }))
            ->when($filters['department'] ?? null, fn ($query, string $department) => $query->where('department', $department))
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->latest('posted_at')
            ->paginate(config('core.page_size', 15))
            ->withQueryString();

        $departments = JobPosition::query()->distinct()->orderBy('department')->pluck('department');

        return view('user::admin.recruitment.positions.index', compact('model', 'filters', 'departments'));
    }

    public function create()
    {
        return view('user::admin.recruitment.positions.create');
    }

    public function store(StoreJobPositionRequest $request): RedirectResponse
    {
        JobPosition::query()->create($request->validated());

        session()->flushMessage(true);

        return redirect()->route('admin.job-positions.index');
    }

    public function edit(JobPosition $jobPosition)
    {
        return view('user::admin.recruitment.positions.edit', compact('jobPosition'));
    }

    public function update(UpdateJobPositionRequest $request, JobPosition $jobPosition): RedirectResponse
    {
        $jobPosition->update($request->validated());

        session()->flushMessage(true);

        return redirect()->route('admin.job-positions.index');
    }

    public function destroy(JobPosition $jobPosition): RedirectResponse
    {
        $jobPosition->delete();

        session()->flushMessage(true);

        return redirect()->route('admin.job-positions.index');
    }
}
