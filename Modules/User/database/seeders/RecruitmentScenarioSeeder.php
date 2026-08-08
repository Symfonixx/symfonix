<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Models\Candidate;
use Modules\User\Models\JobApplication;
use Modules\User\Models\JobPosition;

class RecruitmentScenarioSeeder extends Seeder
{
    public function run(): void
    {
        $positions = collect([
            [
                'title' => 'Senior Laravel Developer',
                'department' => 'Engineering',
                'location' => 'Remote',
                'employment_type' => JobPosition::EMPLOYMENT_FULL_TIME,
                'description' => 'Build and maintain reliable Laravel applications, APIs, and integrations for customer-facing products.',
                'requirements' => 'Strong PHP and Laravel experience, SQL proficiency, automated testing, and clear communication skills.',
            ],
            [
                'title' => 'Product Designer',
                'department' => 'Product',
                'location' => 'Istanbul, Türkiye',
                'employment_type' => JobPosition::EMPLOYMENT_FULL_TIME,
                'description' => 'Turn customer problems into intuitive workflows and polished product interfaces.',
                'requirements' => 'Portfolio demonstrating product thinking, Figma proficiency, and experience collaborating with engineers.',
            ],
            [
                'title' => 'Customer Success Specialist',
                'department' => 'Operations',
                'location' => 'Remote',
                'employment_type' => JobPosition::EMPLOYMENT_CONTRACT,
                'description' => 'Help customers successfully adopt our products and identify opportunities to improve their experience.',
                'requirements' => 'Excellent written communication, problem-solving ability, and experience supporting SaaS customers.',
            ],
        ])->map(fn (array $attributes) => JobPosition::query()->firstOrCreate(
            ['title' => $attributes['title']],
            [...$attributes, 'status' => JobPosition::STATUS_ACTIVE, 'posted_at' => now()->subDays(fake()->numberBetween(3, 30))],
        ));

        $candidates = Candidate::factory()->count(8)->create();

        $candidates->each(function (Candidate $candidate, int $index) use ($positions): void {
            JobApplication::query()->firstOrCreate(
                [
                    'candidate_id' => $candidate->id,
                    'job_position_id' => $positions[$index % $positions->count()]->id,
                ],
                [
                    'status' => JobApplication::STATUSES[$index % count(JobApplication::STATUSES)],
                    'submitted_at' => now()->subDays($index + 1),
                ],
            );
        });
    }
}
