<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $employer = $request->user()->employer;

        $activeJobs = 0;
        $totalApplicants = 0;
        $hired = 0;
        $pending = 0;
        $recentApplications = collect();

        if ($employer) {
            $activeJobs = JobPost::where('employer_id', $employer->id)
                ->where('status', 'published')
                ->count();

            $totalApplicants = Application::whereHas('jobPost', function ($query) use ($employer) {
                $query->where('employer_id', $employer->id);
            })->count();

            $hired = Application::whereHas('jobPost', function ($query) use ($employer) {
                $query->where('employer_id', $employer->id);
            })->where('current_status', 'hired')->count();

            $pending = Application::whereHas('jobPost', function ($query) use ($employer) {
                $query->where('employer_id', $employer->id);
            })->whereIn('current_status', ['new', 'under_review'])->count();

            $recentApplications = Application::with(['jobPost', 'jobseeker.user'])
                ->whereHas('jobPost', function ($query) use ($employer) {
                    $query->where('employer_id', $employer->id);
                })
                ->latest('applied_at')
                ->take(5)
                ->get();
        }

        return view('dashboards.employer', [
            'activeJobs' => $activeJobs,
            'totalApplicants' => $totalApplicants,
            'hired' => $hired,
            'pending' => $pending,
            'recentApplications' => $recentApplications,
        ]);
    }
}
