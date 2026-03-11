<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$matches = App\Models\JobMatch::latest()->limit(10)->get();

if ($matches->isEmpty()) {
    echo "No job_matches records yet. Publish a job post first.\n";
    exit;
}

echo str_pad('job_post_id', 14) .
     str_pad('jobseeker_id', 14) .
     str_pad('rule_score', 12) .
     str_pad('ai_score', 12) .
     "final_score\n";
echo str_repeat('-', 64) . "\n";

foreach ($matches as $m) {
    $ai = $m->ai_semantic_score !== null ? number_format($m->ai_semantic_score, 2) : 'NULL (fallback)';
    echo str_pad($m->job_post_id, 14) .
         str_pad($m->jobseeker_id, 14) .
         str_pad(number_format($m->rule_score, 2), 12) .
         str_pad($ai, 12) .
         number_format($m->final_score, 2) . "\n";
}
