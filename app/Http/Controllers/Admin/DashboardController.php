<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Document;
use App\Models\Employer;
use App\Models\JobPost;
use App\Models\Jobseeker;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalUsers = User::count();
        $totalEmployers = Employer::count();
        $totalJobseekers = Jobseeker::count();
        $totalJobPosts = JobPost::count();
        $totalApplications = Application::count();
        $totalHired = Application::where('current_status', 'hired')->count();
        $pendingEmployers = Employer::where('status', 'pending')->count();
        $pendingDocuments = Document::where('status', 'pending')->count();

        $start = now()->subMonths(5)->startOfMonth();
        $monthLabels = collect(range(0, 5))
            ->map(fn (int $i) => $start->copy()->addMonths($i));

        $applicationsByMonth = Application::selectRaw('DATE_FORMAT(applied_at, "%Y-%m") as month_key, COUNT(*) as total')
            ->where('applied_at', '>=', $start)
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        $chartApplications = $monthLabels->map(function (Carbon $date) use ($applicationsByMonth) {
            $key = $date->format('Y-m');
            return [
                'label' => $date->format('M Y'),
                'count' => (int) ($applicationsByMonth[$key] ?? 0),
            ];
        });

        $periodApplications = Application::where('applied_at', '>=', $start)->count();
        $periodHired = Application::where('applied_at', '>=', $start)
            ->where('current_status', 'hired')
            ->count();
        $hiringRate = $periodApplications > 0 ? round(($periodHired / $periodApplications) * 100, 1) : 0;

        return view('dashboards.admin', [
            'totalUsers' => $totalUsers,
            'totalEmployers' => $totalEmployers,
            'totalJobseekers' => $totalJobseekers,
            'totalJobPosts' => $totalJobPosts,
            'totalApplications' => $totalApplications,
            'totalHired' => $totalHired,
            'pendingEmployers' => $pendingEmployers,
            'pendingDocuments' => $pendingDocuments,
            'chartApplications' => $chartApplications,
            'hiringRate' => $hiringRate,
        ]);
    }
}
