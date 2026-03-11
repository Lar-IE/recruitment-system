<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employer_notes', function (Blueprint $table) {
            $table->foreignId('created_by_sub_user')
                ->nullable()
                ->after('created_by')
                ->constrained('employer_sub_users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('employer_notes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by_sub_user');
        });
    }
};

