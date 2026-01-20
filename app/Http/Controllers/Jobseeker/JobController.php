<?php

namespace App\Http\Controllers\Jobseeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Jobseeker\ApplyJobRequest;
use App\Models\Application;
use App\Models\ApplicationStatus;
use App\Models\JobPost;
use App\Notifications\ApplicationSubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class JobController extends Controller
{
    public function index(Request $request): View
    {
        $query = JobPost::query()
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

        $jobseeker = $request->user()->jobseeker;
        $applications = collect();

        if ($jobseeker) {
            $applications = Application::where('jobseeker_id', $jobseeker->id)
                ->get()
                ->keyBy('job_post_id');
        }

        return view('jobseeker.jobs.index', [
            'jobPosts' => $jobPosts,
            'applications' => $applications,
            'filters' => $request->only(['search', 'location', 'job_type']),
        ]);
    }

    public function show(Request $request, JobPost $jobPost): View
    {
        abort_if($jobPost->status !== 'published', 404);

        if ($jobPost->application_deadline && $jobPost->application_deadline->isPast()) {
            abort(404);
        }

        $jobseeker = $request->user()->jobseeker;
        $application = null;

        if ($jobseeker) {
            $application = Application::where('jobseeker_id', $jobseeker->id)
                ->where('job_post_id', $jobPost->id)
                ->first();
        }

        return view('jobseeker.jobs.show', [
            'jobPost' => $jobPost,
            'application' => $application,
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

        if ($jobPost->application_deadline && $jobPost->application_deadline->isPast()) {
            return back()->withErrors(['job' => __('The application deadline has passed.')]);
        }

        $exists = Application::where('jobseeker_id', $jobseeker->id)
            ->where('job_post_id', $jobPost->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['job' => __('You already applied for this job.')]);
        }

        DB::transaction(function () use ($request, $jobseeker, $jobPost) {
            $application = Application::create([
                'job_post_id' => $jobPost->id,
                'jobseeker_id' => $jobseeker->id,
                'current_status' => 'new',
                'applied_at' => now(),
                'cover_letter' => $request->input('cover_letter'),
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

        return redirect()->route('jobseeker.jobs.show', $jobPost)
            ->with('success', __('Application submitted.'));
    }
}
