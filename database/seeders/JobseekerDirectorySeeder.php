<?php

namespace Database\Seeders;

use App\Models\Jobseeker;
use App\Models\User;
use Illuminate\Database\Seeder;

class JobseekerDirectorySeeder extends Seeder
{
    /**
     * Seed jobseeker directory sample data.
     */
    public function run(): void
    {
        $existing = Jobseeker::count();
        $needed = 20 - $existing;

        if ($needed <= 0) {
            return;
        }

        $users = User::factory()
            ->count($needed)
            ->create();

        foreach ($users as $user) {
            Jobseeker::create([
                'user_id' => $user->id,
                'phone' => fake()->phoneNumber(),
                'address' => fake()->streetAddress(),
                'barangay' => fake()->streetName(),
                'city' => fake()->city(),
                'province' => fake()->state(),
                'region' => fake()->stateAbbr(),
                'country' => 'Philippines',
                'birth_date' => fake()->dateTimeBetween('-45 years', '-18 years')->format('Y-m-d'),
                'gender' => fake()->randomElement(['Male', 'Female', 'Other']),
                'bio' => fake()->paragraph(),
                'education' => implode("\n", [
                    fake()->sentence(4),
                    fake()->sentence(5),
                ]),
                'experience' => implode("\n", [
                    fake()->sentence(6),
                    fake()->sentence(6),
                ]),
                'skills' => implode("\n", [
                    fake()->word(),
                    fake()->word(),
                    fake()->word(),
                ]),
                'status' => 'active',
            ]);
        }
    }
}
