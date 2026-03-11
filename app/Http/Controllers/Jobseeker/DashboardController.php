<?php

namespace App\Http\Controllers\Jobseeker;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobPost;
use App\Services\CandidateMatchingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $jobseeker = $request->user()->jobseeker;

        $appliedJobs = 0;
        $interviews = 0;
        $hired = 0;
        $recentApplications = collect();
        $recommendedJobs = [];
        $applicationsByJob = collect();
        $employerIdsAppliedRecently = [];

        if ($jobseeker) {
            $appliedJobs = Application::where('jobseeker_id', $jobseeker->id)->count();
            $interviews = Application::where('jobseeker_id', $jobseeker->id)
                ->where('current_status', 'schedule_interview')
                ->count();
            $hired = Application::where('jobseeker_id', $jobseeker->id)
                ->where('current_status', 'hired')
                ->count();

            $recentApplications = Application::with('jobPost')
                ->where('jobseeker_id', $jobseeker->id)
                ->latest('applied_at')
                ->take(5)
                ->get();

            $matchingService = app(CandidateMatchingService::class);
            $recommendedJobs = $matchingService->getRecommendedJobsForJobseeker($jobseeker, [
                'threshold' => (int) ($request->query('min_match') ?: CandidateMatchingService::DEFAULT_RECOMMENDATION_THRESHOLD),
                'limit' => 20,
                'location' => $request->query('location'),
                'job_type' => $request->query('job_type'),
            ]);

            $jobIds = collect($recommendedJobs)->pluck('job_post.id')->all();
            $applicationsByJob = Application::where('jobseeker_id', $jobseeker->id)
                ->whereIn('job_post_id', $jobIds)
                ->get()
                ->keyBy('job_post_id');

            $employerIdsAppliedRecently = Application::where('applications.jobseeker_id', $jobseeker->id)
                ->where('applications.applied_at', '>=', now()->subMonths(6))
                ->join('job_posts', 'job_posts.id', '=', 'applications.job_post_id')
                ->whereNotNull('job_posts.employer_id')
                ->distinct()
                ->pluck('job_posts.employer_id')
                ->all();
        }

        return view('dashboards.jobseeker', [
            'appliedJobs' => $appliedJobs,
            'interviews' => $interviews,
            'hired' => $hired,
            'jobseeker' => $jobseeker,
            'recommendedJobs' => $recommendedJobs,
            'applicationsByJob' => $applicationsByJob,
            'employerIdsAppliedRecently' => $employerIdsAppliedRecently,
            'recentApplications' => $recentApplications,
            'filters' => $request->only(['location', 'job_type', 'min_match']),
        ]);
    }
}
