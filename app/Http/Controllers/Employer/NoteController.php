<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\EmployerSubUser;
use App\Models\EmployerNote;
use App\Models\User;
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

        $actor = $request->user();

        EmployerNote::create([
            'employer_id' => $employer->id,
            'application_id' => $application->id,
            'note' => $validated['note'],
            'created_by' => $actor instanceof User ? $actor->id : null,
            'created_by_sub_user' => $actor instanceof EmployerSubUser ? $actor->id : null,
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
