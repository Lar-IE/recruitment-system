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

        $sort = $request->string('sort')->value();
        $direction = strtolower($request->string('dir')->value()) === 'asc' ? 'asc' : 'desc';
        $sortable = [
            'name' => 'users.name',
            'contact' => 'jobseekers.phone',
            'city' => 'jobseekers.city',
            'education' => 'jobseekers.education',
            'gender' => 'jobseekers.gender',
            'age' => 'jobseekers.birth_date',
            'job_title' => 'job_posts.title',
            'status' => 'applications.current_status',
            'applied_at' => 'applications.applied_at',
        ];

        $query = Application::query()
            ->select('applications.*')
            ->join('job_posts', 'job_posts.id', '=', 'applications.job_post_id')
            ->join('jobseekers', 'jobseekers.id', '=', 'applications.jobseeker_id')
            ->join('users', 'users.id', '=', 'jobseekers.user_id')
            ->where('job_posts.employer_id', $employer->id)
            ->with(['jobPost', 'jobseeker.user']);

        if ($request->filled('job_post_id')) {
            $query->where('job_post_id', $request->integer('job_post_id'));
        }

        if ($request->filled('city')) {
            $query->where('jobseekers.city', $request->string('city')->value());
        }

        if ($request->filled('gender')) {
            $query->where('jobseekers.gender', $request->string('gender')->value());
        }

        if ($request->filled('status')) {
            $query->where('current_status', $request->string('status')->value());
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->value();
            $query->where(function ($builder) use ($search) {
                $builder->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhere('jobseekers.phone', 'like', "%{$search}%")
                    ->orWhere('jobseekers.city', 'like', "%{$search}%")
                    ->orWhere('job_posts.title', 'like', "%{$search}%");
            });
        }

        $sortColumn = $sortable[$sort] ?? 'applications.applied_at';
        $query->orderBy($sortColumn, $direction);

        $applications = $query->paginate(10)->withQueryString();
        $jobPosts = $employer->jobPosts()->orderBy('title')->get(['id', 'title']);
        $cities = Application::query()
            ->join('job_posts', 'job_posts.id', '=', 'applications.job_post_id')
            ->join('jobseekers', 'jobseekers.id', '=', 'applications.jobseeker_id')
            ->where('job_posts.employer_id', $employer->id)
            ->whereNotNull('jobseekers.city')
            ->distinct()
            ->orderBy('jobseekers.city')
            ->pluck('jobseekers.city');
        $genders = Application::query()
            ->join('job_posts', 'job_posts.id', '=', 'applications.job_post_id')
            ->join('jobseekers', 'jobseekers.id', '=', 'applications.jobseeker_id')
            ->where('job_posts.employer_id', $employer->id)
            ->whereNotNull('jobseekers.gender')
            ->distinct()
            ->orderBy('jobseekers.gender')
            ->pluck('jobseekers.gender');

        return view('employer.applicants.index', [
            'applications' => $applications,
            'jobPosts' => $jobPosts,
            'cities' => $cities,
            'genders' => $genders,
            'filters' => $request->only(['job_post_id', 'status', 'city', 'gender', 'sort', 'dir', 'search']),
            'statuses' => $this->statuses(),
            'sort' => $sortColumn === 'applications.applied_at' && ! isset($sortable[$sort]) ? 'applied_at' : $sort,
            'dir' => $direction,
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
