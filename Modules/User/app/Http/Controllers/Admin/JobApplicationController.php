<?php

namespace Modules\User\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\User\Http\Requests\HireJobApplicationRequest;
use Modules\User\Http\Requests\JobApplicationIndexRequest;
use Modules\User\Http\Requests\UpdateJobApplicationStatusRequest;
use Modules\User\Models\JobApplication;
use Modules\User\Models\JobPosition;
use Modules\User\Services\EmployeeNotificationService;
use Modules\User\Services\EmployeeProvisioningService;

class JobApplicationController extends Controller
{
    public function __construct(
        protected EmployeeProvisioningService $employeeProvisioningService,
        protected EmployeeNotificationService $employeeNotificationService,
    ) {
        $this->setActive('hr');
        $this->setActive('job_applications');
    }

    public function index(JobApplicationIndexRequest $request)
    {
        $filters = $request->validated();

        $model = JobApplication::query()
            ->with(['candidate', 'position'])
            ->when($filters['position_id'] ?? null, fn ($query, int $positionId) => $query->where('job_position_id', $positionId))
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->whereHas('candidate', fn ($candidateQuery) => $candidateQuery
                    ->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"));
            })
            ->latest('submitted_at')
            ->paginate(config('core.page_size', 15))
            ->withQueryString();

        $positions = JobPosition::query()->orderBy('title')->get(['id', 'title']);
        $statuses = JobApplication::STATUSES;

        return view('user::admin.recruitment.applications.index', compact('model', 'filters', 'positions', 'statuses'));
    }

    public function show(JobApplication $jobApplication)
    {
        $jobApplication->load(['candidate', 'position']);
        $statuses = JobApplication::STATUSES;

        return view('user::admin.recruitment.applications.show', compact('jobApplication', 'statuses'));
    }

    public function update(UpdateJobApplicationStatusRequest $request, JobApplication $jobApplication): RedirectResponse
    {
        $jobApplication->update(['status' => $request->validated('status')]);

        if ($request->has('profile_notes')) {
            $jobApplication->candidate->update([
                'profile_notes' => $request->validated('profile_notes'),
            ]);
        }

        session()->flushMessage(true);

        return back();
    }

    public function hire(HireJobApplicationRequest $request, JobApplication $jobApplication): RedirectResponse
    {
        $employee = $this->employeeProvisioningService->hireFromApplication($jobApplication);
        $this->employeeNotificationService->sendHired($employee);

        session()->flushMessage(true, __('Candidate hired as an employee.'));

        return redirect()->route('admin.employees.show', $employee);
    }
}
