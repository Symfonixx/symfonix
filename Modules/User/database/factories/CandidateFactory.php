<?php

namespace Modules\User\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\User\Models\Candidate;

class CandidateFactory extends Factory
{
    protected $model = Candidate::class;

    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->numerify('01#########'),
            'expected_salary' => fake()->numberBetween(35000, 140000),
            'motivation' => fake()->sentence(14),
            'resume_path' => 'resumes/sample-'.fake()->uuid().'.pdf',
            'cover_letter' => fake()->paragraphs(2, true),
            'profile_notes' => fake()->optional()->sentence(),
        ];
    }
}
