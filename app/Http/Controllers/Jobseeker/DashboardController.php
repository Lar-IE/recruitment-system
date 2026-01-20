<?php

namespace App\Http\Controllers\Jobseeker;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobPost;
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
        $activeJobs = collect();
        $recentApplications = collect();

        if ($jobseeker) {
            $appliedJobs = Application::where('jobseeker_id', $jobseeker->id)->count();
            $interviews = Application::where('jobseeker_id', $jobseeker->id)
                ->where('current_status', 'interview_scheduled')
                ->count();
            $hired = Application::where('jobseeker_id', $jobseeker->id)
                ->where('current_status', 'hired')
                ->count();

            $activeJobs = JobPost::where('status', 'published')
                ->orderByDesc('published_at')
                ->take(5)
                ->get();

            $recentApplications = Application::with('jobPost')
                ->where('jobseeker_id', $jobseeker->id)
                ->latest('applied_at')
                ->take(5)
                ->get();
        }

        return view('dashboards.jobseeker', [
            'appliedJobs' => $appliedJobs,
            'interviews' => $interviews,
            'hired' => $hired,
            'activeJobs' => $activeJobs,
            'recentApplications' => $recentApplications,
        ]);
    }
}
