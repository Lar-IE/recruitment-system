<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing user names to jobseeker name fields
        $jobseekers = DB::table('jobseekers')
            ->join('users', 'users.id', '=', 'jobseekers.user_id')
            ->whereNull('jobseekers.first_name')
            ->select('jobseekers.id', 'users.name')
            ->get();

        foreach ($jobseekers as $jobseeker) {
            $nameParts = explode(' ', trim($jobseeker->name));
            
            $firstName = $nameParts[0] ?? '';
            $lastName = count($nameParts) > 1 ? end($nameParts) : '';
            $middleName = '';
            
            // If there are 3+ parts, assume middle part(s) as middle name
            if (count($nameParts) > 2) {
                array_shift($nameParts); // Remove first name
                array_pop($nameParts);   // Remove last name
                $middleName = implode(' ', $nameParts);
            }

            DB::table('jobseekers')
                ->where('id', $jobseeker->id)
                ->update([
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear the name fields
        DB::table('jobseekers')->update([
            'first_name' => null,
            'middle_name' => null,
            'last_name' => null,
        ]);
    }
};
