<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $allValues = "'new','for_review','schedule_interview','shortlisted','hired','closed','for_pooling','on_hold'";
        $newValues = "'new','for_review','schedule_interview','shortlisted','hired','for_pooling','on_hold'";

        DB::statement("ALTER TABLE applications MODIFY current_status ENUM({$allValues}) NOT NULL DEFAULT 'new'");
        DB::statement("ALTER TABLE application_statuses MODIFY status ENUM({$allValues}) NOT NULL");

        DB::table('applications')->where('current_status', 'closed')->update(['current_status' => 'for_pooling']);
        DB::table('application_statuses')->where('status', 'closed')->update(['status' => 'for_pooling']);

        DB::statement("ALTER TABLE applications MODIFY current_status ENUM({$newValues}) NOT NULL DEFAULT 'new'");
        DB::statement("ALTER TABLE application_statuses MODIFY status ENUM({$newValues}) NOT NULL");
    }

    public function down(): void
    {
        $allValues = "'new','for_review','schedule_interview','shortlisted','hired','closed','for_pooling','on_hold'";
        $oldValues = "'new','for_review','schedule_interview','shortlisted','hired','closed','on_hold'";

        DB::statement("ALTER TABLE applications MODIFY current_status ENUM({$allValues}) NOT NULL DEFAULT 'new'");
        DB::statement("ALTER TABLE application_statuses MODIFY status ENUM({$allValues}) NOT NULL");

        DB::table('applications')->where('current_status', 'for_pooling')->update(['current_status' => 'closed']);
        DB::table('application_statuses')->where('status', 'for_pooling')->update(['status' => 'closed']);

        DB::statement("ALTER TABLE applications MODIFY current_status ENUM({$oldValues}) NOT NULL DEFAULT 'new'");
        DB::statement("ALTER TABLE application_statuses MODIFY status ENUM({$oldValues}) NOT NULL");
    }
};
