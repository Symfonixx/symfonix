<?php

namespace Modules\User\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\User\Models\Candidate;
use Modules\User\Models\JobApplication;
use Modules\User\Models\JobPosition;

class JobApplicationFactory extends Factory
{
    protected $model = JobApplication::class;

    public function definition(): array
    {
        return [
            'candidate_id' => Candidate::factory(),
            'job_position_id' => JobPosition::factory(),
            'status' => fake()->randomElement(JobApplication::STATUSES),
            'submitted_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
