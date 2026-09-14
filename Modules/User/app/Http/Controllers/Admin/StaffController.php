<?php

namespace Modules\User\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Finance\Models\Salary;
use Modules\Project\Models\ProjectEmployee;
use Modules\User\app\Data\EmployeeData;
use Modules\User\app\Repositories\Employee\EmployeeRepository;
use Modules\User\Enums\PunchState;
use Modules\User\Http\Requests\AddEmployeeToTeamRequest;
use Modules\User\Http\Requests\ConvertEmployeeToAdminRequest;
use Modules\User\Http\Requests\StoreEmployeeRequest;
use Modules\User\Http\Requests\UpdateEmployeeRequest;
use Modules\User\Models\AttendanceLog;
use Modules\User\Models\Employee;
use Modules\User\Services\EmployeeNotificationService;
use Modules\User\Services\EmployeeProvisioningService;
use Modules\User\Support\PermissionCatalog;

class StaffController extends Controller
{
    public function __construct(
        protected EmployeeRepository $employeeRepository,
        protected EmployeeProvisioningService $employeeProvisioningService,
        protected EmployeeNotificationService $employeeNotificationService,
    ) {
        $this->setActive('hr');
        $this->setActive('employees');
    }

    public function index()
    {
        $model = $this->employeeRepository->all();
        $groups = PermissionCatalog::groups();

        return view('user::.admin.staff.index', compact('model', 'groups'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        $employeeData = EmployeeData::validateAndCreate($request->safe()->except('resume'));
        $this->employeeRepository->store($employeeData, $request->file('resume'));

        return redirect()->route('admin.employees.index');
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $employeeData = EmployeeData::validateAndCreate($request->safe()->except('resume'));
        $this->employeeRepository->update($employeeData, $employee, $request->file('resume'));

        return redirect()->route('admin.employees.index');
    }

    public function show(Employee $employee): View
    {
        $attendanceBase = AttendanceLog::query()
            ->where('employee_id', $employee->id);

        $attendanceTrendRows = (clone $attendanceBase)
            ->whereDate('recorded_at', '>=', now()->subDays(29)->toDateString())
            ->selectRaw('DATE(recorded_at) as day')
            ->selectRaw('SUM(CASE WHEN punch_state = ? THEN 1 ELSE 0 END) as check_ins', [PunchState::CHECK_IN->value])
            ->selectRaw('SUM(CASE WHEN punch_state = ? THEN 1 ELSE 0 END) as check_outs', [PunchState::CHECK_OUT->value])
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $punchTypeRows = (clone $attendanceBase)
            ->selectRaw('punch_state, COUNT(*) as count')
            ->groupBy('punch_state')
            ->get();

        $salaryRows = Salary::query()
            ->where('employee_id', $employee->id)
            ->orderBy('period')
            ->get();

        $assignments = ProjectEmployee::query()
            ->where('employee_id', $employee->id)
            ->with(['project:id,title,project_status_id,payment_status,due_date', 'project.status:id,name'])
            ->latest('started_at')
            ->paginate(config('core.page_size'));

        $projectStatusRows = ProjectEmployee::query()
            ->where('employee_id', $employee->id)
            ->with('project.status:id,name')
            ->get()
            ->groupBy(function (ProjectEmployee $assignment) {
                return $assignment->project?->status?->name ?? 'Unknown';
            })
            ->map(function ($group, string $status) {
                return ['status' => $status, 'count' => $group->count()];
            })
            ->values();

        $paidSalary = (float) $salaryRows->where('status', Salary::STATUS_PAID)->sum('base_salary');
        $pendingSalary = (float) $salaryRows->where('status', Salary::STATUS_PENDING)->sum('base_salary');

        $salaryTrend = $salaryRows
            ->groupBy(fn (Salary $salary) => $salary->period?->format('Y-m') ?? 'unknown')
            ->map(function ($group, string $period) {
                return [
                    'label' => $period,
                    'paid' => round((float) $group->where('status', Salary::STATUS_PAID)->sum('base_salary'), 2),
                    'pending' => round((float) $group->where('status', Salary::STATUS_PENDING)->sum('base_salary'), 2),
                ];
            })
            ->values();

        $recentLogs = (clone $attendanceBase)
            ->latest('recorded_at')
            ->take(25)
            ->get();

        $stats = [
            'projects_assigned' => ProjectEmployee::query()->where('employee_id', $employee->id)->count(),
            'active_assignments' => ProjectEmployee::query()->where('employee_id', $employee->id)->active()->count(),
            'completed_projects' => ProjectEmployee::query()
                ->where('employee_id', $employee->id)
                ->whereHas('project.status', function ($query) {
                    $query->whereRaw('LOWER(name) = ?', ['completed']);
                })
                ->count(),
            'attendance_days' => (int) (clone $attendanceBase)
                ->selectRaw('COUNT(DISTINCT DATE(recorded_at)) as days_count')
                ->value('days_count'),
            'attendance_events' => (int) (clone $attendanceBase)->count(),
            'paid_salary' => round($paidSalary, 2),
            'pending_salary' => round($pendingSalary, 2),
        ];

        $charts = [
            'attendance_trend' => $attendanceTrendRows->map(function ($row) {
                return [
                    'label' => (string) $row->day,
                    'check_ins' => (int) $row->check_ins,
                    'check_outs' => (int) $row->check_outs,
                ];
            })->values(),
            'salary_trend' => $salaryTrend,
            'punch_types' => $punchTypeRows->map(function ($row) {
                $state = PunchState::tryFrom((int) $row->punch_state);

                return [
                    'type' => $state?->label() ?? 'other',
                    'count' => (int) $row->count,
                ];
            })->values(),
            'project_status' => $projectStatusRows,
        ];

        $groups = PermissionCatalog::groups();
        $employee->loadMissing(['user', 'team']);

        return view('user::admin.staff.show', compact('employee', 'assignments', 'recentLogs', 'stats', 'charts', 'groups'));
    }

    public function convertToAdmin(ConvertEmployeeToAdminRequest $request, Employee $employee): RedirectResponse
    {
        $password = $request->validated('password');

        $this->employeeProvisioningService->convertToAdmin(
            $employee,
            $password,
            $request->validated('permissions') ?? [],
        );
        $this->employeeNotificationService->sendAdminAccess($employee, $password);

        session()->flushMessage(true, __('Employee can now log in to the admin panel.'));

        return redirect()->route('admin.employees.show', $employee);
    }

    public function addToTeam(AddEmployeeToTeamRequest $request, Employee $employee): RedirectResponse
    {
        $this->employeeProvisioningService->addToWebsiteTeam($employee);

        session()->flushMessage(true, __('Employee was added to Our Team on the website.'));

        return redirect()->route('admin.employees.show', $employee);
    }

    public function destroy(Employee $employee)
    {
        $this->employeeRepository->delete($employee);

        return response()->json([
            'success' => true,
        ]);
    }
}
