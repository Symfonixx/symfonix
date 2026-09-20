<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Modules\AI\Support\ToolResult;
use Modules\User\Models\Employee;
use Modules\User\Models\LeaveRequest;

class GetEmployeeStatsTool extends AbstractAssistantTool
{
    public function name(): string
    {
        return 'get_employee_stats';
    }

    public function description(): string
    {
        return 'Get employee headcount, active vs inactive, and pending leave requests. For attendance and utilization use get_employee_report.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => new \stdClass,
        ];
    }

    public function permissions(): array
    {
        return ['hr.employees.view'];
    }

    protected function execute(User $user, array $arguments): ToolResult
    {
        $pendingLeaves = 0;
        if ($user->canany(['hr.leaves.view', 'hr.employees.view'])) {
            $pendingLeaves = LeaveRequest::query()
                ->where('status', LeaveRequest::STATUS_PENDING)
                ->count();
        }

        $data = [
            'total' => Employee::query()->count(),
            'active' => Employee::query()->where('status', Employee::STATUS_ACTIVE)->count(),
            'inactive' => Employee::query()->where('status', Employee::STATUS_INACTIVE)->count(),
            'pending_leave_requests' => $pendingLeaves,
        ];

        return ToolResult::success($data, ['Employees']);
    }
}
