<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_post_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->constrained()->cascadeOnDelete();
            $table->string('skill_name');
            $table->unsignedTinyInteger('weight')->default(1); // 1-10, for scoring: Job Skill Weight × Applicant Proficiency %
            $table->unsignedTinyInteger('min_proficiency')->nullable(); // 0-100, optional desired minimum
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->index(['job_post_id', 'skill_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_post_skills');
    }
};
