<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('virtual_event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('virtual_event_id')->constrained('virtual_events')->cascadeOnDelete();
            $table->foreignId('jobseeker_id')->constrained('jobseekers')->cascadeOnDelete();
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamps();

            $table->unique(['virtual_event_id', 'jobseeker_id']);
            $table->index('virtual_event_id');
            $table->index('jobseeker_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_event_registrations');
    }
};
