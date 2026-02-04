<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\Employer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employer>
 */
class EmployerFactory extends Factory
{
    protected $model = Employer::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state([
                'role' => UserRole::Employer,
            ]),
            'company_name' => fake()->company(),
            'company_email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'website' => fake()->url(),
            'industry' => fake()->word(),
            'company_size' => fake()->randomElement(['1-10', '11-50', '51-200', '201-500', '500+']),
            'address' => fake()->address(),
            'status' => 'approved',
            'approved_at' => now(),
            'suspended_at' => null,
        ];
    }
}
