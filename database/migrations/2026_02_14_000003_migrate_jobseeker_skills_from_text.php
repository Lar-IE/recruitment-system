<?php

use App\Models\Jobseeker;
use App\Models\JobseekerSkill;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $jobseekers = Jobseeker::whereNotNull('skills')->where('skills', '!=', '')->get();

        foreach ($jobseekers as $jobseeker) {
            $items = collect(preg_split("/\r\n|\r|\n/", $jobseeker->skills))
                ->map(fn ($item) => trim($item))
                ->filter();

            foreach ($items as $index => $item) {
                JobseekerSkill::create([
                    'jobseeker_id' => $jobseeker->id,
                    'skill_name' => $item,
                    'proficiency_percentage' => 50,
                    'order' => $index,
                ]);
            }
        }
    }

    public function down(): void
    {
        // No need to restore text skills on rollback
    }
};
