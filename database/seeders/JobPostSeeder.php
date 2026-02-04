<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Employer;
use App\Models\JobPost;
use App\Models\User;
use Illuminate\Database\Seeder;

class JobPostSeeder extends Seeder
{
    public function run(): void
    {
        $employers = Employer::with('user')->get();

        if ($employers->isEmpty()) {
            User::factory()
                ->count(5)
                ->create(['role' => UserRole::Employer])
                ->each(function (User $user) {
                    Employer::create([
                        'user_id' => $user->id,
                        'company_name' => $user->name.' Company',
                        'status' => 'approved',
                        'approved_at' => now(),
                    ]);
                });

            $employers = Employer::with('user')->get();
        }

        JobPost::factory()
            ->count(20)
            ->make()
            ->each(function (JobPost $jobPost) use ($employers) {
                $jobPost->employer_id = $employers->random()->id;
                $jobPost->save();
            });
    }
}
