<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\EmployerNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function store(Request $request, Application $application): RedirectResponse
    {
        $employer = $request->user()->employer;

        if (! $employer || $application->jobPost->employer_id !== $employer->id) {
            abort(403);
        }

        $validated = $request->validate([
            'note' => ['required', 'string', 'max:2000'],
        ]);

        EmployerNote::create([
            'employer_id' => $employer->id,
            'application_id' => $application->id,
            'note' => $validated['note'],
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('employer.applicants.show', $application)
            ->with('success', __('Note added.'));
    }

    public function update(Request $request, Application $application, EmployerNote $note): RedirectResponse
    {
        $employer = $request->user()->employer;

        if (! $employer || $application->jobPost->employer_id !== $employer->id || $note->employer_id !== $employer->id || $note->application_id !== $application->id) {
            abort(403);
        }

        $validated = $request->validate([
            'note' => ['required', 'string', 'max:2000'],
        ]);

        $note->update([
            'note' => $validated['note'],
        ]);

        return redirect()->route('employer.applicants.show', $application)
            ->with('success', __('Note updated.'));
    }

    public function destroy(Request $request, Application $application, EmployerNote $note): RedirectResponse
    {
        $employer = $request->user()->employer;

        if (! $employer || $application->jobPost->employer_id !== $employer->id || $note->employer_id !== $employer->id || $note->application_id !== $application->id) {
            abort(403);
        }

        $note->delete();

        return redirect()->route('employer.applicants.show', $application)
            ->with('success', __('Note deleted.'));
    }
}
