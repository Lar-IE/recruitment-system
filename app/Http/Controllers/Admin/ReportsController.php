<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response as ResponseFactory;
use Illuminate\View\View;

class ReportsController extends Controller
{
    public function index(): View
    {
        return view('admin.reports.index');
    }

    public function applicationsCsv(): Response
    {
        $rows = Application::with(['jobPost', 'jobseeker.user'])
            ->latest('applied_at')
            ->get()
            ->map(function (Application $application) {
                return [
                    'application_id' => $application->id,
                    'job_title' => $application->jobPost->title ?? '',
                    'jobseeker_name' => $application->jobseeker->user->name ?? '',
                    'jobseeker_email' => $application->jobseeker->user->email ?? '',
                    'status' => $application->current_status,
                    'applied_at' => optional($application->applied_at)->format('Y-m-d H:i:s'),
                ];
            })
            ->toArray();

        return $this->streamCsv('applications-report.csv', [
            'application_id',
            'job_title',
            'jobseeker_name',
            'jobseeker_email',
            'status',
            'applied_at',
        ], $rows);
    }

    public function usersCsv(): Response
    {
        $rows = User::query()
            ->latest()
            ->get()
            ->map(function (User $user) {
                return [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role?->value ?? '',
                    'created_at' => optional($user->created_at)->format('Y-m-d H:i:s'),
                ];
            })
            ->toArray();

        return $this->streamCsv('users-report.csv', [
            'user_id',
            'name',
            'email',
            'role',
            'created_at',
        ], $rows);
    }

    public function hiringCsv(): Response
    {
        $rows = Application::selectRaw('DATE_FORMAT(applied_at, "%Y-%m") as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($row) {
                return [
                    'month' => Carbon::createFromFormat('Y-m', $row->month)->format('M Y'),
                    'total_applications' => $row->total,
                    'hired' => Application::where('current_status', 'hired')
                        ->whereRaw('DATE_FORMAT(applied_at, "%Y-%m") = ?', [$row->month])
                        ->count(),
                ];
            })
            ->toArray();

        return $this->streamCsv('hiring-report.csv', [
            'month',
            'total_applications',
            'hired',
        ], $rows);
    }

    private function streamCsv(string $filename, array $headers, array $rows): Response
    {
        return ResponseFactory::streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'wb');
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
