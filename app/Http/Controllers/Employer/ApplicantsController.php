<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicantsController extends Controller
{
    public function index(Request $request): View
    {
        $employer = $request->user()->employer;

        if (! $employer) {
            abort(403);
        }

        $query = Application::with(['jobPost', 'jobseeker.user'])
            ->whereHas('jobPost', function ($builder) use ($employer) {
                $builder->where('employer_id', $employer->id);
            })
            ->latest('applied_at');

        if ($request->filled('job_post_id')) {
            $query->where('job_post_id', $request->integer('job_post_id'));
        }

        if ($request->filled('status')) {
            $query->where('current_status', $request->string('status')->value());
        }

        $applications = $query->paginate(10)->withQueryString();
        $jobPosts = $employer->jobPosts()->orderBy('title')->get(['id', 'title']);

        return view('employer.applicants.index', [
            'applications' => $applications,
            'jobPosts' => $jobPosts,
            'filters' => $request->only(['job_post_id', 'status']),
            'statuses' => $this->statuses(),
        ]);
    }

    public function show(Request $request, Application $application): View
    {
        $employer = $request->user()->employer;

        if (! $employer || $application->jobPost->employer_id !== $employer->id) {
            abort(403);
        }

        $application->load([
            'jobPost',
            'jobseeker.user',
            'jobseeker.documents',
            'statuses.setBy',
            'notes.creator',
        ]);

        $resume = $application->jobseeker->documents
            ->firstWhere('type', 'resume');

        $otherApplications = Application::with('jobPost')
            ->where('jobseeker_id', $application->jobseeker_id)
            ->whereHas('jobPost', function ($builder) use ($employer) {
                $builder->where('employer_id', $employer->id);
            })
            ->latest('applied_at')
            ->get();

        return view('employer.applicants.show', [
            'application' => $application,
            'resume' => $resume,
            'otherApplications' => $otherApplications,
            'statuses' => $this->statuses(),
        ]);
    }

    private function statuses(): array
    {
        return [
            'new' => 'New',
            'under_review' => 'Under Review',
            'interview_scheduled' => 'Interview Scheduled',
            'shortlisted' => 'Shortlisted',
            'hired' => 'Hired',
            'rejected' => 'Rejected',
            'on_hold' => 'On Hold',
        ];
    }
}
