<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Modules\AI\Support\CostLimiter;
use Modules\AI\Support\ToolResult;
use Modules\Reporting\Services\EmployeeReportService;
use Modules\User\Models\Employee;
use Modules\User\Models\LeaveRequest;

class GetEmployeeReportTool extends AbstractAssistantTool
{
    public function __construct(
        CostLimiter $limiter,
        private readonly EmployeeReportService $employeeReportService,
    ) {
        parent::__construct($limiter);
    }

    public function name(): string
    {
        return 'get_employee_report';
    }

    public function description(): string
    {
        return 'Get an employee operations report: attendance, project assignments, utilization, workload, and pending leaves. Optionally pass employee_id for one person.';
    }

    public function parameters(): array
    {
        $schema = $this->periodParameters();
        $schema['properties']['employee_id'] = [
            'type' => 'integer',
            'description' => 'Optional employee id for a single-person report',
        ];

        return $schema;
    }

    public function permissions(): array
    {
        return ['reporting.employee.view', 'hr.employees.view'];
    }

    protected function execute(User $user, array $arguments): ToolResult
    {
        $range = $this->period($arguments);
        $employeeId = isset($arguments['employee_id']) ? (int) $arguments['employee_id'] : null;
        $sources = ['Employees', $range['source_label']];

        $headcount = [
            'total' => Employee::query()->count(),
            'active' => Employee::query()->where('status', Employee::STATUS_ACTIVE)->count(),
            'inactive' => Employee::query()->where('status', Employee::STATUS_INACTIVE)->count(),
            'pending_leave_requests' => $user->canany(['hr.leaves.view', 'hr.employees.view'])
                ? LeaveRequest::query()->where('status', LeaveRequest::STATUS_PENDING)->count()
                : null,
        ];

        if (! $user->can('reporting.employee.view')) {
            return ToolResult::success([
                'period' => $range['period'],
                'period_label' => $range['source_label'],
                'headcount' => $headcount,
                'note' => 'Detailed attendance and utilization require the employee reporting permission.',
            ], $sources);
        }

        $filters = ['period' => $range['period']];
        if ($employeeId) {
            $employee = Employee::query()->find($employeeId);
            if ($employee === null) {
                return ToolResult::empty(__('ai::assistant.errors.not_found'), $sources);
            }

            $report = $this->employeeReportService->buildProfile($employee, $filters);

            return ToolResult::success($this->limiter->truncate([
                'period' => $range['period'],
                'period_label' => $range['source_label'],
                'headcount' => $headcount,
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'status' => $employee->status,
                ],
                'kpis' => $this->summarizeKpis($report['kpis'] ?? [], $user),
                'last_attendance_at' => $report['summary']['last_attendance_at'] ?? null,
                'active_assignments' => $report['summary']['active_assignments'] ?? null,
                'project_status_breakdown' => $this->limiter->limitList($report['charts']['project_status_breakdown'] ?? []),
            ]), array_values(array_unique([...$sources, 'Employee reports'])));
        }

        $report = $this->employeeReportService->build($filters);

        return ToolResult::success($this->limiter->truncate([
            'period' => $range['period'],
            'period_label' => $range['source_label'],
            'headcount' => $headcount,
            'kpis' => $this->summarizeKpis($report['kpis'] ?? [], $user),
            'top_workload' => $this->limiter->limitList($report['charts']['project_employee_workload'] ?? []),
            'recent_assignments' => $this->limiter->limitList($report['tables']['employee_project_performance'] ?? []),
        ]), array_values(array_unique([...$sources, 'Employee reports'])));
    }

    /**
     * @param  array<string, mixed>  $kpis
     * @return array<string, mixed>
     */
    private function summarizeKpis(array $kpis, User $user): array
    {
        $out = [];

        foreach ($kpis as $key => $metric) {
            if (! is_array($metric)) {
                $out[$key] = $metric;

                continue;
            }

            $out[$key] = [
                'value' => $metric['value'] ?? null,
                'previous' => $metric['previous'] ?? null,
                'trend' => $metric['trend'] ?? null,
            ];
        }

        if (! $user->canany(['finance.dashboard.view', 'finance.salaries.view'])) {
            unset($out['salary_paid_total'], $out['salary_pending_total']);
        }

        return $out;
    }
}
