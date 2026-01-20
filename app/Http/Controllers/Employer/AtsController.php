<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationStatus;
use App\Notifications\ApplicationStatusUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AtsController extends Controller
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

        if ($request->filled('status')) {
            $query->where('current_status', $request->string('status')->value());
        }

        if ($request->filled('job_post_id')) {
            $query->where('job_post_id', $request->integer('job_post_id'));
        }

        $applications = $query->paginate(10)->withQueryString();

        $jobPosts = $employer->jobPosts()
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('employer.ats.index', [
            'applications' => $applications,
            'jobPosts' => $jobPosts,
            'filters' => $request->only(['status', 'job_post_id']),
            'statuses' => $this->statuses(),
        ]);
    }

    public function updateStatus(Request $request, Application $application): RedirectResponse
    {
        $employer = $request->user()->employer;

        if (! $employer || $application->jobPost->employer_id !== $employer->id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:new,under_review,interview_scheduled,shortlisted,hired,rejected,on_hold'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $application->update([
            'current_status' => $validated['status'],
        ]);

        ApplicationStatus::create([
            'application_id' => $application->id,
            'status' => $validated['status'],
            'note' => $validated['note'] ?? null,
            'set_by' => $request->user()->id,
        ]);

        if ($application->jobseeker && $application->jobseeker->user) {
            $application->jobseeker->user->notify(
                new ApplicationStatusUpdated($application->load('jobPost.employer.user'), $validated['note'] ?? null)
            );
        }

        return redirect()->route('employer.ats', $request->query())
            ->with('success', __('Status updated.'));
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
