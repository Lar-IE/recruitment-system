<?php

namespace Database\Factories;

use App\Models\Employer;
use App\Models\JobPost;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobPost>
 */
class JobPostFactory extends Factory
{
    protected $model = JobPost::class;

    public function definition(): array
    {
        $title = fake()->jobTitle();

        return [
            'employer_id' => Employer::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::random(6),
            'location' => fake()->city(),
            'job_type' => fake()->randomElement(['full_time', 'part_time', 'contract', 'temporary', 'internship']),
            'description' => fake()->paragraphs(3, true),
            'responsibilities' => fake()->paragraphs(2, true),
            'requirements' => fake()->paragraphs(2, true),
            'salary_min' => fake()->numberBetween(15000, 40000),
            'salary_max' => fake()->numberBetween(45000, 120000),
            'currency' => 'PHP',
            'status' => 'published',
            'application_deadline' => now()->addDays(fake()->numberBetween(10, 45)),
            'published_at' => now()->subDays(fake()->numberBetween(0, 14)),
            'closed_at' => null,
        ];
    }
}
