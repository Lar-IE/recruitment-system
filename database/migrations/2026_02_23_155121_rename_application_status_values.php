<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $renames = [
        'under_review'        => 'for_review',
        'interview_scheduled' => 'schedule_interview',
        'rejected'            => 'closed',
    ];

    public function up(): void
    {
        $allValues = "'new','under_review','for_review','interview_scheduled','schedule_interview','shortlisted','hired','rejected','closed','on_hold'";
        $newValues = "'new','for_review','schedule_interview','shortlisted','hired','closed','on_hold'";

        DB::statement("ALTER TABLE applications MODIFY current_status ENUM({$allValues}) NOT NULL DEFAULT 'new'");
        DB::statement("ALTER TABLE application_statuses MODIFY status ENUM({$allValues}) NOT NULL");

        foreach ($this->renames as $old => $new) {
            DB::table('applications')->where('current_status', $old)->update(['current_status' => $new]);
            DB::table('application_statuses')->where('status', $old)->update(['status' => $new]);
        }

        DB::statement("ALTER TABLE applications MODIFY current_status ENUM({$newValues}) NOT NULL DEFAULT 'new'");
        DB::statement("ALTER TABLE application_statuses MODIFY status ENUM({$newValues}) NOT NULL");
    }

    public function down(): void
    {
        $allValues = "'new','under_review','for_review','interview_scheduled','schedule_interview','shortlisted','hired','rejected','closed','on_hold'";
        $oldValues = "'new','under_review','interview_scheduled','shortlisted','hired','rejected','on_hold'";

        DB::statement("ALTER TABLE applications MODIFY current_status ENUM({$allValues}) NOT NULL DEFAULT 'new'");
        DB::statement("ALTER TABLE application_statuses MODIFY status ENUM({$allValues}) NOT NULL");

        foreach (array_flip($this->renames) as $old => $new) {
            DB::table('applications')->where('current_status', $old)->update(['current_status' => $new]);
            DB::table('application_statuses')->where('status', $old)->update(['status' => $new]);
        }

        DB::statement("ALTER TABLE applications MODIFY current_status ENUM({$oldValues}) NOT NULL DEFAULT 'new'");
        DB::statement("ALTER TABLE application_statuses MODIFY status ENUM({$oldValues}) NOT NULL");
    }
};
