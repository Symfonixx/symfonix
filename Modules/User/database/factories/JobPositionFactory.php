<?php

namespace Modules\User\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\User\Models\JobPosition;

class JobPositionFactory extends Factory
{
    protected $model = JobPosition::class;

    public function definition(): array
    {
        return [
            'title' => fake()->randomElement(['Backend Developer', 'Product Designer', 'DevOps Engineer', 'Project Manager']),
            'department' => fake()->randomElement(['Engineering', 'Product', 'Operations', 'Sales']),
            'location' => fake()->city().', '.fake()->country(),
            'employment_type' => fake()->randomElement(JobPosition::EMPLOYMENT_TYPES),
            'description' => fake()->paragraphs(3, true),
            'requirements' => fake()->paragraphs(2, true),
            'status' => JobPosition::STATUS_ACTIVE,
            'posted_at' => fake()->dateTimeBetween('-2 months', 'today'),
        ];
    }

    public function closed(): static
    {
        return $this->state(fn () => ['status' => JobPosition::STATUS_CLOSED]);
    }
}
