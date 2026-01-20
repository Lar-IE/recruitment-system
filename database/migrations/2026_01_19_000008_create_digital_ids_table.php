<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_ids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jobseeker_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_post_id')->nullable()->constrained('job_posts')->nullOnDelete();
            $table->string('file_path');
            $table->string('company_name');
            $table->string('job_title');
            $table->string('employee_identifier');
            $table->date('issue_date');
            $table->enum('status', ['active', 'inactive'])->default('active')->index();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['jobseeker_id', 'employer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_ids');
    }
};
