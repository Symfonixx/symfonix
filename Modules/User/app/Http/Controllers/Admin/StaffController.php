<?php

namespace Modules\User\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Modules\Finance\Models\Salary;
use Modules\Project\Models\ProjectEmployee;
use Modules\User\app\Data\EmployeeData;
use Modules\User\app\Repositories\Employee\EmployeeRepository;
use Modules\User\Http\Requests\StoreEmployeeRequest;
use Modules\User\Http\Requests\UpdateEmployeeRequest;
use Modules\User\Models\AttendanceLog;
use Modules\User\Models\Employee;
use ZkTeco\Enums\PunchState;

class StaffController extends Controller
{
    public function __construct(protected EmployeeRepository $employeeRepository)
    {
        $this->setActive('hr');
        $this->setActive('employees');
    }

    public function index()
    {
        $model = $this->employeeRepository->all();

        return view('user::.admin.staff.index', compact('model'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        $employeeData = EmployeeData::validateAndCreate($request->validated());
        $this->employeeRepository->store($employeeData);

        return redirect()->route('admin.employees.index');
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $employeeData = EmployeeData::validateAndCreate($request->validated());
        $this->employeeRepository->update($employeeData, $employee);

        return redirect()->route('admin.employees.index');
    }

    public function show(Employee $employee): View
    {
        $attendanceBase = AttendanceLog::query()
            ->where('employee_id', $employee->id);

        $attendanceTrendRows = (clone $attendanceBase)
            ->whereDate('recorded_at', '>=', now()->subDays(29)->toDateString())
            ->selectRaw('DATE(recorded_at) as day')
            ->selectRaw('SUM(CASE WHEN punch_state = ? THEN 1 ELSE 0 END) as check_ins', [PunchState::CheckIn->value])
            ->selectRaw('SUM(CASE WHEN punch_state = ? THEN 1 ELSE 0 END) as check_outs', [PunchState::CheckOut->value])
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
                    'type' => $state?->name ?? 'Other',
                    'count' => (int) $row->count,
                ];
            })->values(),
            'project_status' => $projectStatusRows,
        ];

        return view('user::admin.staff.show', compact('employee', 'assignments', 'recentLogs', 'stats', 'charts'));
    }

    public function destroy(Employee $employee)
    {
        $this->employeeRepository->delete($employee);

        return response()->json([
            'success' => true,
        ]);
    }
}
