<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobseeker_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jobseeker_id')->constrained()->cascadeOnDelete();
            $table->string('skill_name');
            $table->unsignedTinyInteger('proficiency_percentage')->default(50); // 0-100
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->index(['jobseeker_id', 'skill_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobseeker_skills');
    }
};
