<?php

namespace App\Http\Controllers\Jobseeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Jobseeker\ApplyJobRequest;
use App\Models\Application;
use App\Models\ApplicationStatus;
use App\Models\JobPost;
use App\Notifications\ApplicationSubmitted;
use App\Services\HybridJobMatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class JobController extends Controller
{
    public function __construct(private readonly HybridJobMatcher $hybridMatcher) {}
    public function index(Request $request): View
    {
        $query = JobPost::with('employer.companyProfile')
            ->where('status', 'published')
            ->where(function ($builder) {
                $builder->whereNull('application_deadline')
                    ->orWhere('application_deadline', '>=', now()->toDateString());
            });

        if ($request->filled('search')) {
            $search = $request->string('search')->trim();
            $query->where('title', 'like', '%'.$search.'%');
        }

        if ($request->filled('location')) {
            $location = $request->string('location')->trim();
            $query->where('location', 'like', '%'.$location.'%');
        }

        if ($request->filled('job_type')) {
            $query->where('job_type', $request->string('job_type')->value());
        }

        $jobPosts = $query->latest('published_at')->paginate(10)->withQueryString();

        $applications = $this->getApplicationsForCurrentJobseeker($request);
        $employerIdsAppliedRecently = $this->getEmployerIdsAppliedRecently($request);

        return view('jobseeker.jobs.index', [
            'jobPosts' => $jobPosts,
            'applications' => $applications,
            'employerIdsAppliedRecently' => $employerIdsAppliedRecently,
            'filters' => $request->only(['search', 'location', 'job_type']),
        ]);
    }

    public function show(Request $request, JobPost $jobPost): View|RedirectResponse
    {
        // Eager load employer relationship with company profile
        $jobPost->load('employer.companyProfile');

        if ($jobPost->status !== 'published') {
            return redirect()->route('jobseeker.jobs')
                ->withErrors(['job' => __('This job is not published yet.')]);
        }

        if ($jobPost->application_deadline && $jobPost->application_deadline->lt(now()->startOfDay())) {
            return redirect()->route('jobseeker.jobs')
                ->withErrors(['job' => __('The application deadline has passed.')]);
        }

        $application = null;
        $appliedToEmployerWithinSixMonths = false;

        $jobseeker = $request->user()->jobseeker;
        if ($jobseeker) {
            $application = Application::where('jobseeker_id', $jobseeker->id)
                ->where('job_post_id', $jobPost->id)
                ->first();

            if (! $application && $jobPost->employer_id) {
                $appliedToEmployerWithinSixMonths = Application::where('applications.jobseeker_id', $jobseeker->id)
                    ->join('job_posts', 'job_posts.id', '=', 'applications.job_post_id')
                    ->where('job_posts.employer_id', $jobPost->employer_id)
                    ->where('applications.applied_at', '>=', now()->subMonths(6))
                    ->exists();
            }
        }

        return view('jobseeker.jobs.show', [
            'jobPost' => $jobPost,
            'application' => $application,
            'appliedToEmployerWithinSixMonths' => $appliedToEmployerWithinSixMonths,
        ]);
    }

    public function apply(ApplyJobRequest $request, JobPost $jobPost): RedirectResponse
    {
        $jobseeker = $request->user()->jobseeker;

        if (! $jobseeker) {
            abort(403);
        }

        if ($jobPost->status !== 'published') {
            return back()->withErrors(['job' => __('This job is not open for applications.')]);
        }

        if ($jobPost->application_deadline && $jobPost->application_deadline->lt(now()->startOfDay())) {
            return back()->withErrors(['job' => __('The application deadline has passed.')]);
        }

        $exists = Application::where('jobseeker_id', $jobseeker->id)
            ->where('job_post_id', $jobPost->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['job' => __('You already applied for this job.')]);
        }

        if ($jobPost->employer_id) {
            $recentApplicationToEmployer = Application::where('applications.jobseeker_id', $jobseeker->id)
                ->join('job_posts', 'job_posts.id', '=', 'applications.job_post_id')
                ->where('job_posts.employer_id', $jobPost->employer_id)
                ->where('applications.applied_at', '>=', now()->subMonths(6))
                ->exists();

            if ($recentApplicationToEmployer) {
                return back()->withErrors(['job' => __('You have already applied to this company within the last 6 months.')]);
            }
        }

        $coverLetterPath = null;
        if ($request->hasFile('cover_letter_file')) {
            $coverLetterPath = $request->file('cover_letter_file')->store('cover_letters', 'public');
        }

        DB::transaction(function () use ($request, $jobseeker, $jobPost, $coverLetterPath) {
            $application = Application::create([
                'job_post_id' => $jobPost->id,
                'jobseeker_id' => $jobseeker->id,
                'current_status' => 'new',
                'applied_at' => now(),
                'cover_letter' => $request->input('cover_letter'),
                'cover_letter_file' => $coverLetterPath,
            ]);

            ApplicationStatus::create([
                'application_id' => $application->id,
                'status' => 'new',
                'note' => __('Application submitted.'),
                'set_by' => $request->user()->id,
            ]);

            $employerUser = $jobPost->employer?->user;
            if ($employerUser) {
                $employerUser->notify(new ApplicationSubmitted($application->load(['jobPost', 'jobseeker.user'])));
            }
        });

        try {
            $this->hybridMatcher->matchAndStore($jobPost, $jobseeker);
            $this->hybridMatcher->matchJobseekerAgainstEmployerJobs($jobseeker, $jobPost);
        } catch (\Throwable $e) {
            Log::error('HybridJobMatcher: Failed to score on application submit', [
                'job_post_id'  => $jobPost->id,
                'jobseeker_id' => $jobseeker->id,
                'message'      => $e->getMessage(),
            ]);
        }

        return redirect()->route('jobseeker.jobs.show', $jobPost)
            ->with('success', __('Application submitted.'));
    }

    private function getApplicationsForCurrentJobseeker(Request $request)
    {
        $jobseeker = $request->user()->jobseeker;

        if (! $jobseeker) {
            return collect();
        }

        return Application::where('jobseeker_id', $jobseeker->id)
            ->get()
            ->keyBy('job_post_id');
    }

    /**
     * Employer IDs the current jobseeker has applied to within the last 6 months.
     *
     * @return array<int>
     */
    private function getEmployerIdsAppliedRecently(Request $request): array
    {
        $jobseeker = $request->user()->jobseeker;

        if (! $jobseeker) {
            return [];
        }

        return Application::where('applications.jobseeker_id', $jobseeker->id)
            ->where('applications.applied_at', '>=', now()->subMonths(6))
            ->join('job_posts', 'job_posts.id', '=', 'applications.job_post_id')
            ->whereNotNull('job_posts.employer_id')
            ->distinct()
            ->pluck('job_posts.employer_id')
            ->all();
    }
}
