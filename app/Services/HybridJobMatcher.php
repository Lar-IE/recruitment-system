<?php

namespace App\Services;

use App\Models\Application;
use App\Models\JobMatch;
use App\Models\JobPost;
use App\Models\Jobseeker;

/**
 * Job Matching Service (rule-based only).
 *
 * Uses CandidateMatchingService for all scoring. AI semantic matching is
 * disabled for now; final_score = rule_score and ai_semantic_score is null.
 *
 * Same flow as before: scores are stored in job_matches, getTopApplicantsForJob
 * and getRecommendedJobsForApplicant work unchanged.
 */
class HybridJobMatcher
{
    private const RULE_WEIGHT   = 0.6;
    private const AI_WEIGHT    = 0.4;
    private const RESUME_LIMIT = 1500;

    public function __construct(
        private readonly CandidateMatchingService $ruleBasedMatcher
    ) {}

    /**
     * Run hybrid matching for a published job against all eligible jobseekers.
     *
     * Includes:
     *  - Jobseekers who applied directly to this job post
     *  - Jobseekers who applied to any other job at the same employer
     *    (they are blocked from re-applying due to the 6-month rule, but
     *     should still appear as ranked candidates for this job)
     *
     * Scores are persisted in the job_matches table (upsert).
     */
    public function matchJobApplicants(JobPost $jobPost): void
    {
        $jobPost->loadMissing('requiredSkills');

        $jobseekerIds = Application::query()
            ->when(
                $jobPost->employer_id,
                fn ($q) => $q->join('job_posts', 'job_posts.id', '=', 'applications.job_post_id')
                             ->where('job_posts.employer_id', $jobPost->employer_id)
                             ->select('applications.jobseeker_id'),
                fn ($q) => $q->where('applications.job_post_id', $jobPost->id)
                             ->select('jobseeker_id')
            )
            ->distinct()
            ->pluck('jobseeker_id');

        if ($jobseekerIds->isEmpty()) {
            return;
        }

        $jobseekers = Jobseeker::with('skillsList')->whereIn('id', $jobseekerIds)->get();

        foreach ($jobseekers as $jobseeker) {
            $this->matchAndStore($jobPost, $jobseeker);
        }
    }

    /**
     * Score a jobseeker against all published jobs at the same employer.
     *
     * Called after a new application so that the applicant appears as a
     * ranked candidate for every job the employer has open — not just the
     * one they applied to (the 6-month rule prevents them applying to the
     * others directly).
     */
    public function matchJobseekerAgainstEmployerJobs(Jobseeker $jobseeker, JobPost $appliedJobPost): void
    {
        if (! $appliedJobPost->employer_id) {
            return;
        }

        $otherJobs = JobPost::with('requiredSkills')
            ->where('employer_id', $appliedJobPost->employer_id)
            ->where('status', 'published')
            ->where('id', '!=', $appliedJobPost->id)
            ->whereHas('requiredSkills')
            ->get();

        foreach ($otherJobs as $job) {
            $this->matchAndStore($job, $jobseeker);
        }
    }

    /**
     * Run hybrid matching for a single jobseeker against a job post.
     * Persists the result and returns the structured scores.
     *
     * @return array{rule_score: float, ai_semantic_score: float|null, final_score: float}
     */
    public function matchAndStore(JobPost $jobPost, Jobseeker $jobseeker): array
    {
        $ruleScore = $this->getRuleBasedScore($jobPost, $jobseeker);
        $aiScore   = $this->getAISemanticScore($jobPost, $jobseeker);

        $finalScore = $this->computeFinalScore($ruleScore, $aiScore);

        JobMatch::updateOrCreate(
            [
                'job_post_id'  => $jobPost->id,
                'jobseeker_id' => $jobseeker->id,
            ],
            [
                'rule_score'       => $ruleScore,
                'ai_semantic_score' => $aiScore,
                'final_score'      => $finalScore,
            ]
        );

        return [
            'rule_score'        => $ruleScore,
            'ai_semantic_score' => $aiScore,
            'final_score'       => $finalScore,
        ];
    }

    /**
     * Return rule-based score (0–100) for a jobseeker against a job post.
     * Delegates to CandidateMatchingService and normalises the raw score
     * to a 0–100 percentage.
     */
    public function getRuleBasedScore(JobPost $jobPost, Jobseeker $jobseeker): float
    {
        $jobPost->loadMissing('requiredSkills');
        $jobseeker->loadMissing('skillsList');

        $requiredSkills = $jobPost->requiredSkills;
        if ($requiredSkills->isEmpty()) {
            return 0.0;
        }

        $result = $this->ruleBasedMatcher->calculateScore($jobseeker, $requiredSkills);

        $maxScore = $requiredSkills->sum(fn ($s) => $s->weight ?: 1);
        if ($maxScore <= 0) {
            return 0.0;
        }

        $normalised = ($result['score'] / $maxScore) * 100;

        return (float) min(100, max(0, round($normalised, 2)));
    }

    /**
     * AI semantic score disabled. Always returns null so final_score = rule_score.
     */
    public function getAISemanticScore(JobPost $jobPost, Jobseeker $jobseeker): ?float
    {
        return null;
    }

    /**
     * Calculate final hybrid score from rule and AI scores (60% rule, 40% AI).
     * If AI score is null, returns rule score only. Result is capped at 100.
     */
    public function calculateFinalScore(float $ruleScore, ?float $aiScore): float
    {
        return $this->computeFinalScore($ruleScore, $aiScore);
    }

    /**
     * Combine rule-based and AI scores into the final hybrid score.
     *
     * Falls back to rule_score when AI score is unavailable.
     */
    public function computeFinalScore(float $ruleScore, ?float $aiScore): float
    {
        if ($aiScore === null) {
            return min(100.0, max(0.0, round($ruleScore, 2)));
        }

        $final = ($ruleScore * self::RULE_WEIGHT) + ($aiScore * self::AI_WEIGHT);

        return min(100.0, max(0.0, round($final, 2)));
    }

    /**
     * Retrieve the top applicants for a job post ordered by final_score DESC.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, JobMatch>
     */
    public function getTopApplicantsForJob(int $jobId, int $limit = 20)
    {
        return JobMatch::with(['jobseeker.user', 'jobseeker.skillsList'])
            ->where('job_post_id', $jobId)
            ->orderByDesc('final_score')
            ->limit($limit)
            ->get();
    }

    /**
     * Retrieve the best-matching jobs for an applicant ordered by final_score DESC.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, JobMatch>
     */
    public function getRecommendedJobsForApplicant(int $applicantId, int $limit = 20)
    {
        return JobMatch::with(['jobPost.employer.companyProfile'])
            ->where('jobseeker_id', $applicantId)
            ->orderByDesc('final_score')
            ->limit($limit)
            ->get();
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function buildJobDescription(JobPost $jobPost): string
    {
        return implode(' ', array_filter([
            $jobPost->title,
            $jobPost->description,
            $jobPost->responsibilities,
            $jobPost->requirements,
        ]));
    }

    private function buildResumeText(Jobseeker $jobseeker): string
    {
        $text = implode(' ', array_filter([
            $jobseeker->bio,
            $jobseeker->education,
            $jobseeker->experience,
            $jobseeker->skills,
        ]));

        return mb_substr(trim($text), 0, self::RESUME_LIMIT);
    }
}
