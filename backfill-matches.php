<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$matcher = app(App\Services\HybridJobMatcher::class);

// Score every published job that has required skills against all jobseekers
// who have ever applied to any job at the same employer.
$jobs = App\Models\JobPost::with('requiredSkills')
    ->where('status', 'published')
    ->whereHas('requiredSkills')
    ->get();

if ($jobs->isEmpty()) {
    echo "No published jobs with required skills found.\n";
    exit;
}

echo "Found " . $jobs->count() . " published job(s) with required skills.\n\n";

foreach ($jobs as $job) {
    echo "Job [{$job->id}] {$job->title}\n";

    $jobseekerIds = App\Models\Application::query()
        ->when(
            $job->employer_id,
            fn ($q) => $q->join('job_posts', 'job_posts.id', '=', 'applications.job_post_id')
                         ->where('job_posts.employer_id', $job->employer_id)
                         ->select('applications.jobseeker_id'),
            fn ($q) => $q->where('applications.job_post_id', $job->id)
                         ->select('jobseeker_id')
        )
        ->distinct()
        ->pluck('jobseeker_id');

    if ($jobseekerIds->isEmpty()) {
        echo "  (no applicants at this employer)\n";
        continue;
    }

    $jobseekers = App\Models\Jobseeker::with('skillsList')->whereIn('id', $jobseekerIds)->get();

    foreach ($jobseekers as $seeker) {
        $scores = $matcher->matchAndStore($job, $seeker);

        $ai = $scores['ai_semantic_score'] !== null
            ? number_format($scores['ai_semantic_score'], 2)
            : 'NULL (fallback)';

        echo "  Jobseeker [{$seeker->id}] {$seeker->first_name} {$seeker->last_name}"
           . " → rule=" . number_format($scores['rule_score'], 2)
           . " ai={$ai}"
           . " final=" . number_format($scores['final_score'], 2) . "\n";
    }
}

echo "\nDone.\n";
