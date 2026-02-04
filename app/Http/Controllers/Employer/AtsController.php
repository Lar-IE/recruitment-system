<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationStatus;
use App\Notifications\ApplicationStatusUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AtsController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        return redirect()
            ->route('employer.applicants', $request->query());
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
            'interview_at' => ['required_if:status,interview_scheduled', 'date'],
            'interview_link' => ['required_if:status,interview_scheduled', 'url', 'max:2048'],
        ]);

        $application->update([
            'current_status' => $validated['status'],
        ]);

        ApplicationStatus::create([
            'application_id' => $application->id,
            'status' => $validated['status'],
            'note' => $validated['note'] ?? null,
            'interview_at' => $validated['status'] === 'interview_scheduled'
                ? $validated['interview_at']
                : null,
            'interview_link' => $validated['status'] === 'interview_scheduled'
                ? $validated['interview_link']
                : null,
            'set_by' => $request->user() instanceof \App\Models\User
                ? $request->user()->id
                : null,
        ]);

        if ($application->jobseeker && $application->jobseeker->user) {
            $application->jobseeker->user->notify(
                new ApplicationStatusUpdated(
                    $application->load('jobPost.employer.user'),
                    $validated['note'] ?? null,
                    $validated['status'] === 'interview_scheduled' ? ($validated['interview_at'] ?? null) : null,
                    $validated['status'] === 'interview_scheduled' ? ($validated['interview_link'] ?? null) : null
                )
            );
        }

        return redirect()->route('employer.applicants', $request->query())
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
