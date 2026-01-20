<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->constrained('job_posts')->cascadeOnDelete();
            $table->foreignId('jobseeker_id')->constrained()->cascadeOnDelete();
            $table->enum('current_status', [
                'new',
                'under_review',
                'interview_scheduled',
                'shortlisted',
                'hired',
                'rejected',
                'on_hold',
            ])->default('new')->index();
            $table->timestamp('applied_at')->useCurrent()->index();
            $table->text('cover_letter')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['job_post_id', 'jobseeker_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
