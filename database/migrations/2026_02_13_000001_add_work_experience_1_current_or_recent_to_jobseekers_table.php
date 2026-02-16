<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Indicates whether the applicant's Work Experience #1 is their current or most recent job.
     */
    public function up(): void
    {
        Schema::table('jobseekers', function (Blueprint $table) {
            $table->boolean('work_experience_1_current_or_recent')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobseekers', function (Blueprint $table) {
            $table->dropColumn('work_experience_1_current_or_recent');
        });
    }
};
