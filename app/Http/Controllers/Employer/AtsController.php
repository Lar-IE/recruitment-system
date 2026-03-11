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
        $employer = $request->attributes->get('employer') ?? $request->user()->employer;

        if (! $employer) {
            abort(403);
        }

        $sort = $request->string('sort')->value();
        $direction = strtolower($request->string('dir')->value()) === 'asc' ? 'asc' : 'desc';
        $sortable = [
            'name' => 'users.name',
            'contact' => 'jobseekers.phone',
            'city' => 'jobseekers.city',
            'education' => 'jobseekers.educational_attainment',
            'gender' => 'jobseekers.gender',
            'age' => 'jobseekers.birth_date',
            'current_job' => 'current_job',
            'status' => 'applications.current_status',
            'applied_at' => 'applications.applied_at',
        ];

        $latestApplicationIdsSubquery = Application::query()
            ->join('job_posts', 'job_posts.id', '=', 'applications.job_post_id')
            ->where('job_posts.employer_id', $employer->id)
            ->groupBy('applications.jobseeker_id')
            ->selectRaw('MAX(applications.id)');

        $query = Application::query()
            ->select('applications.*')
            ->join('jobseekers', 'jobseekers.id', '=', 'applications.jobseeker_id')
            ->join('users', 'users.id', '=', 'jobseekers.user_id')
            ->whereIn('applications.id', $latestApplicationIdsSubquery)
            ->with(['jobseeker.user', 'jobseeker.workExperiences']);

        if ($request->filled('city')) {
            $query->where('jobseekers.city', $request->string('city')->value());
        }

        if ($request->filled('gender')) {
            $query->where('jobseekers.gender', $request->string('gender')->value());
        }

        if ($request->filled('educational_attainment')) {
            $query->where('jobseekers.educational_attainment', $request->string('educational_attainment')->value());
        }

        if ($request->filled('status')) {
            $query->where('applications.current_status', $request->string('status')->value());
        }

        if ($request->filled('age_range')) {
            $range = $request->string('age_range')->value();
            if (preg_match('/^(\d+)-(\d+)$/', $range, $m)) {
                $query->whereRaw('TIMESTAMPDIFF(YEAR, jobseekers.birth_date, CURDATE()) BETWEEN ? AND ?', [(int) $m[1], (int) $m[2]]);
            } elseif (preg_match('/^(\d+)\+$/', $range, $m)) {
                $query->whereRaw('TIMESTAMPDIFF(YEAR, jobseekers.birth_date, CURDATE()) >= ?', [(int) $m[1]]);
            }
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->value();
            $query->where(function ($builder) use ($search) {
                $builder->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhere('jobseekers.phone', 'like', "%{$search}%")
                    ->orWhere('jobseekers.city', 'like', "%{$search}%")
                    ->orWhereExists(function ($q) use ($search) {
                        $q->select(\DB::raw(1))
                            ->from('jobseeker_work_experience')
                            ->whereColumn('jobseeker_work_experience.jobseeker_id', 'jobseekers.id')
                            ->where(function ($q2) use ($search) {
                                $q2->where('jobseeker_work_experience.position', 'like', "%{$search}%")
                                    ->orWhere('jobseeker_work_experience.company', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $sortColumn = $sortable[$sort] ?? null;
        if ($sort === 'current_job') {
            $dir = $direction === 'asc' ? 'asc' : 'desc';
            $query->orderByRaw(
                "(SELECT COALESCE(we.position, we.company, '') FROM jobseeker_work_experience we " .
                "WHERE we.jobseeker_id = jobseekers.id ORDER BY we.`order` ASC, we.id ASC LIMIT 1) {$dir}"
            );
        } elseif ($sortColumn) {
            $query->orderBy($sortColumn, $direction);
        } else {
            $query->orderBy('applications.applied_at', 'desc');
        }

        $applications = $query->paginate(10)->withQueryString();

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

        $educationalAttainments = Application::query()
            ->join('job_posts', 'job_posts.id', '=', 'applications.job_post_id')
            ->join('jobseekers', 'jobseekers.id', '=', 'applications.jobseeker_id')
            ->where('job_posts.employer_id', $employer->id)
            ->whereNotNull('jobseekers.educational_attainment')
            ->distinct()
            ->orderBy('jobseekers.educational_attainment')
            ->pluck('jobseekers.educational_attainment');

        return view('employer.ats.index', [
            'applications' => $applications,
            'cities' => $cities,
            'genders' => $genders,
            'educationalAttainments' => $educationalAttainments,
            'filters' => $request->only(['search', 'status', 'age_range', 'city', 'gender', 'educational_attainment', 'sort', 'dir']),
            'statuses' => $this->statuses(),
            'sort' => $sortColumn === 'applications.applied_at' && ! isset($sortable[$sort]) ? 'applied_at' : $sort,
            'dir' => $direction,
        ]);
    }

    public function updateStatus(Request $request, Application $application): RedirectResponse
    {
        $employer = $request->user()->employer;

        if (! $employer || $application->jobPost->employer_id !== $employer->id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:new,for_review,schedule_interview,shortlisted,hired,for_pooling,on_hold'],
            'note' => ['nullable', 'string', 'max:1000'],
            'interview_at' => ['nullable', 'required_if:status,schedule_interview', 'date'],
            'interview_link' => ['nullable', 'required_if:status,schedule_interview', 'url', 'max:2048'],
        ]);

        $application->update([
            'current_status' => $validated['status'],
        ]);

        ApplicationStatus::create([
            'application_id' => $application->id,
            'status' => $validated['status'],
            'note' => $validated['note'] ?? null,
            'interview_at' => $validated['status'] === 'schedule_interview'
                ? $validated['interview_at']
                : null,
            'interview_link' => $validated['status'] === 'schedule_interview'
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
                    $validated['status'] === 'schedule_interview' ? ($validated['interview_at'] ?? null) : null,
                    $validated['status'] === 'schedule_interview' ? ($validated['interview_link'] ?? null) : null
                )
            );
        }

        return redirect()->route('employer.applicants', $request->query())
            ->with('success', __('Status updated.'));
    }

    public function show(Request $request, Application $application): View
    {
        $employer = $request->attributes->get('employer') ?? $request->user()->employer;

        if (! $employer || $application->jobPost->employer_id !== $employer->id) {
            abort(403);
        }

        $application->load([
            'jobPost',
            'jobseeker.user',
            'jobseeker.documents',
            'jobseeker.educations',
            'jobseeker.workExperiences',
            'jobseeker.skillsList',
        ]);

        return view('employer.ats.show', [
            'application' => $application,
            'jobseeker' => $application->jobseeker,
        ]);
    }

    private function statuses(): array
    {
        return [
            'new' => 'New',
            'for_review' => 'For Review',
            'schedule_interview' => 'Schedule Interview',
            'shortlisted' => 'Shortlisted',
            'hired' => 'Hired',
            'for_pooling' => 'For Pooling',
            'on_hold' => 'On Hold',
        ];
    }
}
