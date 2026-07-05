<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Models\Employee;
use Modules\User\Models\LeaveRequest;

class LeaveScenarioSeeder extends Seeder
{
    /**
     * Seed leave-management scenarios for HR workflows.
     */
    public function run(): void
    {
        $scenarios = [
            [
                'employee_email' => 'maya.hassan@symfonix.com',
                'type' => LeaveRequest::TYPE_ANNUAL,
                'start_date' => now()->addDays(7)->toDateString(),
                'end_date' => now()->addDays(10)->toDateString(),
                'status' => LeaveRequest::STATUS_PENDING,
                'reason' => 'Planned annual leave.',
                'manager_note' => null,
            ],
            [
                'employee_email' => 'omar.khaled@symfonix.com',
                'type' => LeaveRequest::TYPE_SICK,
                'start_date' => now()->subDays(5)->toDateString(),
                'end_date' => now()->subDays(4)->toDateString(),
                'status' => LeaveRequest::STATUS_APPROVED,
                'reason' => 'Medical recovery leave.',
                'manager_note' => 'Approved with medical notice.',
            ],
            [
                'employee_email' => 'youssef.samir@symfonix.com',
                'type' => LeaveRequest::TYPE_UNPAID,
                'start_date' => now()->addDays(14)->toDateString(),
                'end_date' => now()->addDays(16)->toDateString(),
                'status' => LeaveRequest::STATUS_REJECTED,
                'reason' => 'Personal travel request during delivery window.',
                'manager_note' => 'Rejected because of project release coverage.',
            ],
        ];

        foreach ($scenarios as $scenario) {
            $employee = Employee::query()
                ->where('email', $scenario['employee_email'])
                ->first();

            if ($employee === null) {
                continue;
            }

            LeaveRequest::query()->updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'start_date' => $scenario['start_date'],
                    'end_date' => $scenario['end_date'],
                ],
                [
                    'type' => $scenario['type'],
                    'status' => $scenario['status'],
                    'reason' => $scenario['reason'],
                    'manager_note' => $scenario['manager_note'],
                ]
            );
        }
    }
}
