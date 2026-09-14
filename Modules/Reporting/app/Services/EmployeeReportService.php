<?php

namespace Modules\Reporting\Services;

use Carbon\Carbon;
use Modules\Finance\Models\Salary;
use Modules\Project\Models\ProjectEmployee;
use Modules\User\Enums\PunchState;
use Modules\User\Models\AttendanceLog;
use Modules\User\Models\Employee;

class EmployeeReportService extends BaseReportService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $resolved = $this->resolveFilters($filters);

        $employeeId = $resolved->employeeId;
        $employees = Employee::query()->assignable()->select(['id', 'name'])->get();

        $attendance = $this->attendanceSummary(
            $resolved->start->toDateString(),
            $resolved->end->toDateString(),
            $employeeId
        );

        $previousAttendance = $this->attendanceSummary(
            $resolved->previousStart->toDateString(),
            $resolved->previousEnd->toDateString(),
            $employeeId
        );

        $projectStats = $this->projectAssignmentSummary(
            $resolved->start->toDateString(),
            $resolved->end->toDateString(),
            $employeeId
        );

        return [
            'filters' => $resolved->toArray(),
            'employees' => $employees,
            'chart_colors' => $this->chartColors(),
            'kpis' => [
                'attendance_days' => $this->kpiMetric((float) $attendance['days_present'], (float) $previousAttendance['days_present']),
                'attendance_events' => $this->kpiMetric((float) $attendance['events_count'], (float) $previousAttendance['events_count']),
                'projects_assigned' => $this->kpiMetric((float) $projectStats['assigned_count'], (float) $projectStats['assigned_count']),
                'projects_completed' => $this->kpiMetric((float) $projectStats['completed_count'], (float) $projectStats['completed_count']),
                'utilization_rate' => [
                    'value' => $projectStats['utilization_rate'],
                    'previous' => $projectStats['utilization_rate'],
                    'change' => null,
                    'trend' => 'flat',
                ],
            ],
            'charts' => [
                'attendance_trend' => $attendance['trend'],
                'punch_types' => $attendance['punch_types'],
                'project_employee_workload' => $this->projectWorkloadByEmployee(
                    $resolved->start->toDateString(),
                    $resolved->end->toDateString()
                ),
            ],
            'tables' => [
                'employee_project_performance' => $this->employeeProjectPerformance(
                    $resolved->start->toDateString(),
                    $resolved->end->toDateString(),
                    $employeeId
                ),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function buildProfile(Employee $employee, array $filters = []): array
    {
        $resolved = $this->resolveFilters(array_merge($filters, ['employee_id' => $employee->id]));
        $from = $resolved->start->toDateString();
        $to = $resolved->end->toDateString();

        $attendance = $this->attendanceSummary($from, $to, $employee->id);
        $previousAttendance = $this->attendanceSummary(
            $resolved->previousStart->toDateString(),
            $resolved->previousEnd->toDateString(),
            $employee->id
        );
        $projectStats = $this->projectAssignmentSummary($from, $to, $employee->id);
        $salaryStats = $this->salarySummary($employee->id, $from, $to);
        $previousSalaryStats = $this->salarySummary(
            $employee->id,
            $resolved->previousStart->toDateString(),
            $resolved->previousEnd->toDateString()
        );

        $lastAttendance = AttendanceLog::query()
            ->where('employee_id', $employee->id)
            ->latest('recorded_at')
            ->first();

        return [
            'filters' => $resolved->toArray(),
            'employee' => $employee->only(['id', 'name', 'email', 'mobile', 'status', 'fingerprint_device_uid', 'fingerprint_enrolled_at']),
            'chart_colors' => $this->chartColors(),
            'kpis' => [
                'attendance_days' => $this->kpiMetric((float) $attendance['days_present'], (float) $previousAttendance['days_present']),
                'projects_assigned' => $this->kpiMetric((float) $projectStats['assigned_count'], (float) $projectStats['assigned_count']),
                'projects_completed' => $this->kpiMetric((float) $projectStats['completed_count'], (float) $projectStats['completed_count']),
                'salary_paid_total' => $this->kpiMetric($salaryStats['paid_total'], $previousSalaryStats['paid_total']),
                'salary_pending_total' => $this->kpiMetric($salaryStats['pending_total'], $previousSalaryStats['pending_total']),
                'utilization_rate' => [
                    'value' => $projectStats['utilization_rate'],
                    'previous' => $projectStats['utilization_rate'],
                    'change' => null,
                    'trend' => 'flat',
                ],
            ],
            'summary' => [
                'last_attendance_at' => $lastAttendance?->recorded_at?->toDateTimeString(),
                'fingerprint_enrolled' => $employee->isFingerprintEnrolled(),
                'active_assignments' => ProjectEmployee::query()->where('employee_id', $employee->id)->active()->count(),
            ],
            'charts' => [
                'attendance_trend' => $attendance['trend'],
                'punch_types' => $attendance['punch_types'],
                'salary_trend' => $salaryStats['trend'],
                'project_status_breakdown' => $this->projectStatusBreakdownForEmployee($employee->id, $from, $to),
            ],
            'tables' => [
                'project_assignments' => $this->employeeProjectPerformance($from, $to, $employee->id),
                'salary_history' => $this->salaryHistory($employee->id),
                'attendance_logs' => $this->recentAttendanceLogs($employee->id, $from, $to),
            ],
        ];
    }

    /**
     * @return array{
     *   days_present: int,
     *   events_count: int,
     *   trend: array<int, array{label: string, check_ins: int, check_outs: int}>,
     *   punch_types: array<int, array{type: string, count: int}>
     * }
     */
    private function attendanceSummary(string $from, string $to, ?int $employeeId): array
    {
        $base = AttendanceLog::query()
            ->whereDate('recorded_at', '>=', $from)
            ->whereDate('recorded_at', '<=', $to);

        if ($employeeId) {
            $base->where('employee_id', $employeeId);
        }

        $eventsCount = (clone $base)->count();
        $daysPresent = (clone $base)
            ->selectRaw('COUNT(DISTINCT DATE(recorded_at)) as days_count')
            ->value('days_count');

        $trendRows = (clone $base)
            ->selectRaw('DATE(recorded_at) as day')
            ->selectRaw('SUM(CASE WHEN punch_state = ? THEN 1 ELSE 0 END) as check_ins', [PunchState::CHECK_IN->value])
            ->selectRaw('SUM(CASE WHEN punch_state = ? THEN 1 ELSE 0 END) as check_outs', [PunchState::CHECK_OUT->value])
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $punchRows = (clone $base)
            ->selectRaw('punch_state, COUNT(*) as count')
            ->groupBy('punch_state')
            ->get();

        return [
            'days_present' => (int) ($daysPresent ?? 0),
            'events_count' => (int) $eventsCount,
            'trend' => $trendRows->map(fn ($row) => [
                'label' => (string) $row->day,
                'check_ins' => (int) $row->check_ins,
                'check_outs' => (int) $row->check_outs,
            ])->values()->all(),
            'punch_types' => $punchRows->map(function ($row) {
                $state = PunchState::tryFrom((int) $row->punch_state);

                return [
                    'type' => $state?->label() ?? 'other',
                    'count' => (int) $row->count,
                ];
            })->values()->all(),
        ];
    }

    /**
     * @return array{assigned_count: int, completed_count: int, utilization_rate: float}
     */
    private function projectAssignmentSummary(string $from, string $to, ?int $employeeId): array
    {
        $assignments = ProjectEmployee::query()
            ->whereDate('started_at', '<=', $to)
            ->where(function ($query) use ($from) {
                $query->whereNull('ended_at')
                    ->orWhereDate('ended_at', '>=', $from);
            })
            ->with('project.status:id,name');

        if ($employeeId) {
            $assignments->where('employee_id', $employeeId);
        }

        $rows = $assignments->get();
        $assignedCount = $rows->count();
        $completedCount = $rows->filter(function (ProjectEmployee $row) {
            return strcasecmp((string) $row->project?->status?->name, 'Completed') === 0;
        })->count();

        $periodDays = max(1, Carbon::parse($from)->diffInDays(Carbon::parse($to)) + 1);
        $activeEmployees = max(1, $employeeId ? 1 : Employee::query()->active()->count());
        $maxAssignmentCapacity = $periodDays * $activeEmployees;
        $totalAssignmentDays = $rows->sum(fn (ProjectEmployee $assignment) => $assignment->daysWorked());
        $utilizationRate = round(min(100, ($totalAssignmentDays / $maxAssignmentCapacity) * 100), 1);

        return [
            'assigned_count' => (int) $assignedCount,
            'completed_count' => (int) $completedCount,
            'utilization_rate' => $utilizationRate,
        ];
    }

    /**
     * @return array<int, array{name: string, assignments: int}>
     */
    private function projectWorkloadByEmployee(string $from, string $to): array
    {
        return Employee::query()
            ->active()
            ->withCount(['projectAssignments as assignments_count' => function ($query) use ($from, $to) {
                $query->whereDate('started_at', '<=', $to)
                    ->where(function ($q) use ($from) {
                        $q->whereNull('ended_at')
                            ->orWhereDate('ended_at', '>=', $from);
                    });
            }])
            ->orderByDesc('assignments_count')
            ->take(10)
            ->get(['id', 'name'])
            ->map(fn (Employee $employee) => [
                'name' => $employee->name,
                'assignments' => (int) $employee->assignments_count,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function employeeProjectPerformance(string $from, string $to, ?int $employeeId): array
    {
        $query = ProjectEmployee::query()
            ->whereDate('started_at', '<=', $to)
            ->where(function ($builder) use ($from) {
                $builder->whereNull('ended_at')
                    ->orWhereDate('ended_at', '>=', $from);
            })
            ->with([
                'employee:id,name',
                'project:id,title,project_status_id,payment_status,due_date',
                'project.status:id,name',
            ])
            ->latest('started_at');

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        return $query
            ->take(20)
            ->get()
            ->map(function (ProjectEmployee $assignment) {
                return [
                    'employee_id' => $assignment->employee?->id,
                    'employee' => $assignment->employee?->name,
                    'project_id' => $assignment->project?->id,
                    'project' => $assignment->project?->title,
                    'role' => $assignment->role,
                    'started_at' => $assignment->started_at?->toDateString(),
                    'ended_at' => $assignment->ended_at?->toDateString(),
                    'days_worked' => $assignment->daysWorked(),
                    'project_status' => $assignment->project?->status?->name,
                    'payment_status' => $assignment->project?->payment_status,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array{paid_total: float, pending_total: float, trend: array<int, array{label: string, paid: float, pending: float}>}
     */
    private function salarySummary(int $employeeId, string $from, string $to): array
    {
        $rows = Salary::query()
            ->where('employee_id', $employeeId)
            ->whereDate('period', '>=', $from)
            ->whereDate('period', '<=', $to)
            ->orderBy('period')
            ->get();

        $paidTotal = (float) $rows->where('status', Salary::STATUS_PAID)->sum('base_salary');
        $pendingTotal = (float) $rows->where('status', Salary::STATUS_PENDING)->sum('base_salary');

        $trend = $rows
            ->groupBy(fn (Salary $salary) => $salary->period?->format('Y-m') ?? 'unknown')
            ->map(function ($group, string $period) {
                return [
                    'label' => $period,
                    'paid' => round((float) $group->where('status', Salary::STATUS_PAID)->sum('base_salary'), 2),
                    'pending' => round((float) $group->where('status', Salary::STATUS_PENDING)->sum('base_salary'), 2),
                ];
            })
            ->values()
            ->all();

        return [
            'paid_total' => round($paidTotal, 2),
            'pending_total' => round($pendingTotal, 2),
            'trend' => $trend,
        ];
    }

    /**
     * @return array<int, array{status: string, count: int}>
     */
    private function projectStatusBreakdownForEmployee(int $employeeId, string $from, string $to): array
    {
        $assignments = ProjectEmployee::query()
            ->where('employee_id', $employeeId)
            ->whereDate('started_at', '<=', $to)
            ->where(function ($query) use ($from) {
                $query->whereNull('ended_at')
                    ->orWhereDate('ended_at', '>=', $from);
            })
            ->with('project.status:id,name')
            ->get();

        return $assignments
            ->groupBy(function (ProjectEmployee $assignment) {
                return $assignment->project?->status?->name ?? 'Unknown';
            })
            ->map(function ($group, string $status) {
                return [
                    'status' => $status,
                    'count' => $group->count(),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function salaryHistory(int $employeeId): array
    {
        return Salary::query()
            ->where('employee_id', $employeeId)
            ->latest('period')
            ->take(24)
            ->get()
            ->map(function (Salary $salary) {
                return [
                    'period' => $salary->period?->toDateString(),
                    'base_salary' => (float) $salary->base_salary,
                    'status' => $salary->status,
                    'paid_at' => $salary->paid_at?->toDateString(),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function recentAttendanceLogs(int $employeeId, string $from, string $to): array
    {
        return AttendanceLog::query()
            ->where('employee_id', $employeeId)
            ->whereDate('recorded_at', '>=', $from)
            ->whereDate('recorded_at', '<=', $to)
            ->latest('recorded_at')
            ->take(40)
            ->get()
            ->map(function (AttendanceLog $log) {
                return [
                    'recorded_at' => $log->recorded_at?->toDateTimeString(),
                    'punch_state' => $log->punchStateLabel(),
                    'verify_mode' => $log->verifyModeLabel(),
                    'device_user_id' => $log->device_user_id,
                ];
            })
            ->values()
            ->all();
    }
}
