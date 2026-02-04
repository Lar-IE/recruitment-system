<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employer_sub_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role');
            $table->string('status')->default('active');
            $table->rememberToken();
            $table->timestamps();

            $table->index(['employer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employer_sub_users');
    }
};
