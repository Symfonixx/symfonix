<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Models\Employee;

class EmployeeScenarioSeeder extends Seeder
{
    /**
     * Seed HR employee scenarios used by CRM assignment and finance workflows.
     */
    public function run(): void
    {
        $employees = [
            [
                'name' => 'Maya Hassan',
                'email' => 'maya.hassan@symfonix.com',
                'mobile' => '01010000001',
                'status' => Employee::STATUS_ACTIVE,
            ],
            [
                'name' => 'Omar Khaled',
                'email' => 'omar.khaled@symfonix.com',
                'mobile' => '01010000002',
                'status' => Employee::STATUS_ACTIVE,
            ],
            [
                'name' => 'Nour Adel',
                'email' => 'nour.adel@symfonix.com',
                'mobile' => '01010000003',
                'status' => Employee::STATUS_ACTIVE,
            ],
            [
                'name' => 'Youssef Samir',
                'email' => 'youssef.samir@symfonix.com',
                'mobile' => '01010000004',
                'status' => Employee::STATUS_ACTIVE,
            ],
            [
                'name' => 'Lina Fouad',
                'email' => 'lina.fouad@symfonix.com',
                'mobile' => '01010000005',
                'status' => Employee::STATUS_ACTIVE,
            ],
            [
                'name' => 'Karim Nabil',
                'email' => 'karim.nabil@symfonix.com',
                'mobile' => '01010000006',
                'status' => Employee::STATUS_INACTIVE,
            ],
        ];

        foreach ($employees as $employee) {
            Employee::query()->updateOrCreate(
                ['email' => $employee['email']],
                $employee
            );
        }
    }
}
