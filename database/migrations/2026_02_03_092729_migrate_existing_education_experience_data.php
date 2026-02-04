<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Jobseeker;
use App\Models\JobseekerEducation;
use App\Models\JobseekerWorkExperience;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing education and experience data from text fields to structured tables
        $jobseekers = Jobseeker::whereNotNull('education')
            ->orWhereNotNull('experience')
            ->get();

        foreach ($jobseekers as $jobseeker) {
            // Migrate education data
            if ($jobseeker->education) {
                $educationEntries = preg_split("/\r\n|\r|\n/", $jobseeker->education);
                $order = 0;
                
                foreach ($educationEntries as $entry) {
                    $entry = trim($entry);
                    if (!empty($entry)) {
                        JobseekerEducation::create([
                            'jobseeker_id' => $jobseeker->id,
                            'institution' => $entry,
                            'order' => $order++,
                        ]);
                    }
                }
            }

            // Migrate work experience data
            if ($jobseeker->experience) {
                $experienceEntries = preg_split("/\r\n|\r|\n/", $jobseeker->experience);
                $order = 0;
                
                foreach ($experienceEntries as $entry) {
                    $entry = trim($entry);
                    if (!empty($entry)) {
                        // Try to parse "Position at Company" format
                        $parts = explode(' at ', $entry, 2);
                        
                        JobseekerWorkExperience::create([
                            'jobseeker_id' => $jobseeker->id,
                            'company' => count($parts) > 1 ? $parts[1] : $entry,
                            'position' => count($parts) > 1 ? $parts[0] : 'Position',
                            'order' => $order++,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove all migrated data
        JobseekerEducation::truncate();
        JobseekerWorkExperience::truncate();
    }
};
