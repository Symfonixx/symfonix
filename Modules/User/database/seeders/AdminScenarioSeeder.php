<?php

namespace Modules\User\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminScenarioSeeder extends Seeder
{
    /**
     * Seed admin users that map to employee records by email.
     */
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Maya Hassan',
                'email' => 'maya.hassan@symfonix.com',
                'mobile' => '01020000001',
                'role' => 'HR Manager',
            ],
            [
                'name' => 'Omar Khaled',
                'email' => 'omar.khaled@symfonix.com',
                'mobile' => '01020000002',
                'role' => 'Sales Manager',
            ],
            [
                'name' => 'Nour Adel',
                'email' => 'nour.adel@symfonix.com',
                'mobile' => '01020000003',
                'role' => 'Finance Manager',
            ],
            [
                'name' => 'Youssef Samir',
                'email' => 'youssef.samir@symfonix.com',
                'mobile' => '01020000004',
                'role' => 'Project Manager',
            ],
            [
                'name' => 'Lina Fouad',
                'email' => 'lina.fouad@symfonix.com',
                'mobile' => '01020000005',
                'role' => 'Operations Manager',
            ],
        ];

        foreach ($admins as $admin) {
            $user = User::query()->updateOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'mobile' => $admin['mobile'],
                    'password' => Hash::make('password'),
                    'type' => User::TYPE_ADMIN,
                ]
            );

            $user->syncRoles([$admin['role']]);
        }
    }
}
