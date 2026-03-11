<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('jobseeker_id')->constrained()->cascadeOnDelete();
            $table->float('rule_score')->default(0);
            $table->float('ai_semantic_score')->nullable();
            $table->float('final_score')->default(0);
            $table->timestamps();

            $table->unique(['job_post_id', 'jobseeker_id']);
            $table->index('final_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_matches');
    }
};
