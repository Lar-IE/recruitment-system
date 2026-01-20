<?php

namespace App\Http\Controllers\Jobseeker;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(Request $request): View
    {
        $jobseeker = $request->user()->jobseeker;

        $applications = collect();

        if ($jobseeker) {
            $applications = Application::with(['jobPost', 'statuses'])
                ->where('jobseeker_id', $jobseeker->id)
                ->latest('applied_at')
                ->paginate(10);
        }

        return view('jobseeker.history.index', [
            'applications' => $applications,
        ]);
    }

    public function show(Request $request, Application $application): View
    {
        $jobseeker = $request->user()->jobseeker;

        if (! $jobseeker || $application->jobseeker_id !== $jobseeker->id) {
            abort(403);
        }

        $application->load(['jobPost', 'statuses.setBy']);

        return view('jobseeker.history.show', [
            'application' => $application,
        ]);
    }
}
