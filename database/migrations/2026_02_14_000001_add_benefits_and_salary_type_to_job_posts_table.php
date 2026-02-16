<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            $table->longText('benefits')->nullable()->after('responsibilities');
            $table->string('salary_type', 20)->default('salary_range')->after('currency');
            $table->decimal('salary_daily', 12, 2)->nullable()->after('salary_type');
            $table->decimal('salary_monthly', 12, 2)->nullable()->after('salary_daily');
        });
    }

    public function down(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            $table->dropColumn(['benefits', 'salary_type', 'salary_daily', 'salary_monthly']);
        });
    }
};
