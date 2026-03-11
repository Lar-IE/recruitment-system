<?php

namespace App\Services;

use App\Models\Application;
use App\Models\JobPost;
use App\Models\Jobseeker;
use Illuminate\Support\Collection;

/**
 * Rule-based candidate matching service.
 * Calculates suitability score: Σ (Job Skill Weight × Applicant Proficiency %)
 * Designed for future AI integration.
 */
class CandidateMatchingService
{
    /** Default minimum match percentage for job recommendations. */
    public const DEFAULT_RECOMMENDATION_THRESHOLD = 0;

    /**
     * Get recommended jobs for a jobseeker based on skill match.
     * Match % = (Σ (Job Skill Weight × Jobseeker Proficiency %) / Σ (Job Skill Weight)) × 100
     * Only returns jobs meeting the threshold (default 80%).
     *
     * @param  array{threshold?: int, limit?: int, location?: string, job_type?: string}  $options
     * @return array<int, array{
     *   job_post: JobPost,
     *   score: float,
     *   match_percentage: float,
     *   matched_skills: array<array{skill_name: string, job_weight: int, proficiency: int, contribution: float}>
     * }>
     */
    public function getRecommendedJobsForJobseeker(Jobseeker $jobseeker, array $options = []): array
    {
        $threshold = $options['threshold'] ?? self::DEFAULT_RECOMMENDATION_THRESHOLD;
        $limit = $options['limit'] ?? 20;
        $location = $options['location'] ?? null;
        $jobType = $options['job_type'] ?? null;

        $jobseeker->load('skillsList');
        if ($jobseeker->skillsList->isEmpty()) {
            return [];
        }

        $query = JobPost::with(['employer.companyProfile', 'requiredSkills'])
            ->where('status', 'published')
            ->whereHas('requiredSkills');

        if ($location) {
            $query->where('location', 'like', '%' . trim($location) . '%');
        }
        if ($jobType) {
            $query->where('job_type', $jobType);
        }

        $jobPosts = $query->get();

        $scored = [];
        foreach ($jobPosts as $jobPost) {
            $requiredSkills = $jobPost->requiredSkills;
            if ($requiredSkills->isEmpty()) {
                continue;
            }

            $result = $this->calculateScore($jobseeker, $requiredSkills);
            if ($result['score'] <= 0) {
                continue;
            }

            $maxScore = 0.0;
            foreach ($requiredSkills as $jobSkill) {
                $maxScore += $jobSkill->weight ?: 1;
            }
            $matchPercentage = $maxScore > 0 ? round(($result['score'] / $maxScore) * 100, 1) : 0;

            if ($matchPercentage >= $threshold) {
                $scored[] = [
                    'job_post' => $jobPost,
                    'score' => $result['score'],
                    'match_percentage' => $matchPercentage,
                    'matched_skills' => $result['matched_skills'],
                ];
            }
        }

        usort($scored, fn ($a, $b) => $b['match_percentage'] <=> $a['match_percentage']);

        return array_slice($scored, 0, $limit);
    }
    /**
     * Get ranked candidates for a job post based on skill match (applicants only).
     *
     * @return array<int, array{
     *   application: Application,
     *   score: float,
     *   matched_skills: array<array{skill_name: string, job_weight: int, proficiency: int, contribution: float}>
     * }>
     */
    public function getRankedCandidates(JobPost $jobPost, int $limit = 10): array
    {
        $requiredSkills = $jobPost->requiredSkills;
        if ($requiredSkills->isEmpty()) {
            return [];
        }

        $applications = Application::with(['jobseeker.user', 'jobseeker.skillsList'])
            ->where('job_post_id', $jobPost->id)
            ->get();

        $scored = [];
        foreach ($applications as $application) {
            $result = $this->calculateScore($application->jobseeker, $requiredSkills);
            if ($result['score'] > 0) {
                $scored[] = [
                    'application' => $application,
                    'score' => $result['score'],
                    'matched_skills' => $result['matched_skills'],
                ];
            }
        }

        usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($scored, 0, $limit);
    }

    /**
     * Get suggested jobseekers who match the job's required skills but haven't applied.
     * Respects min_proficiency; only includes jobseekers who meet each skill's minimum.
     *
     * @return array<int, array{
     *   jobseeker: Jobseeker,
     *   score: float,
     *   matched_skills: array<array{skill_name: string, job_weight: int, proficiency: int, contribution: float}>
     * }>
     */
    public function getSuggestedJobseekers(JobPost $jobPost, int $limit = 10): array
    {
        $requiredSkills = $jobPost->requiredSkills;
        if ($requiredSkills->isEmpty()) {
            return [];
        }

        $applicantIds = Application::where('job_post_id', $jobPost->id)->pluck('jobseeker_id');

        $jobseekers = Jobseeker::with(['user', 'skillsList'])
            ->whereNotIn('id', $applicantIds)
            ->whereHas('skillsList')
            ->get();

        $scored = [];
        foreach ($jobseekers as $jobseeker) {
            $result = $this->calculateScore($jobseeker, $requiredSkills);
            if ($result['score'] > 0) {
                $scored[] = [
                    'jobseeker' => $jobseeker,
                    'score' => $result['score'],
                    'matched_skills' => $result['matched_skills'],
                ];
            }
        }

        usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($scored, 0, $limit);
    }

    /**
     * Calculate match score for a jobseeker against job required skills.
     * Formula: Σ (Job Skill Weight × Applicant Proficiency %)
     */
    public function calculateScore($jobseeker, $requiredSkills): array
    {
        $jobseekerSkills = $jobseeker->skillsList ?? collect();
        $normalizedJobseeker = $this->normalizeSkillMap($jobseekerSkills);

        $totalScore = 0.0;
        $matchedSkills = [];

        foreach ($requiredSkills as $jobSkill) {
            $key = $this->normalizeSkillName($jobSkill->skill_name);
            $jobWeight = $jobSkill->weight ?: 1;
            $minProficiency = $jobSkill->min_proficiency;

            if (isset($normalizedJobseeker[$key])) {
                $proficiency = $normalizedJobseeker[$key];
                if ($minProficiency !== null && $proficiency < $minProficiency) {
                    continue;
                }
                $contribution = $jobWeight * ($proficiency / 100);
                $totalScore += $contribution;
                $matchedSkills[] = [
                    'skill_name' => $jobSkill->skill_name,
                    'job_weight' => $jobWeight,
                    'proficiency' => $proficiency,
                    'contribution' => round($contribution, 2),
                ];
            }
        }

        return [
            'score' => round($totalScore, 2),
            'matched_skills' => $matchedSkills,
        ];
    }

    /**
     * Normalize skill names for case-insensitive matching.
     */
    protected function normalizeSkillName(string $name): string
    {
        return strtolower(trim($name));
    }

    /**
     * Build map of normalized skill name => proficiency.
     * For legacy skills (no proficiency), assume 50%.
     */
    protected function normalizeSkillMap(Collection $skills): array
    {
        $map = [];
        foreach ($skills as $skill) {
            $name = is_object($skill) ? ($skill->skill_name ?? '') : (string) $skill;
            $proficiency = is_object($skill) && isset($skill->proficiency_percentage)
                ? (int) $skill->proficiency_percentage
                : 50;
            $key = $this->normalizeSkillName($name);
            if ($key !== '') {
                $map[$key] = min(100, max(0, $proficiency));
            }
        }
        return $map;
    }
}
