<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Jobseeker;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobseekerDirectoryController extends Controller
{
    public function index(Request $request): View
    {
        $employer = $request->attributes->get('employer') ?? $request->user()->employer;
        $sort = $request->string('sort')->value();
        $direction = strtolower($request->string('dir')->value()) === 'asc' ? 'asc' : 'desc';
        $sortable = [
            'name' => 'users.name',
            'contact' => 'jobseekers.phone',
            'city' => 'jobseekers.city',
            'education' => 'jobseekers.education',
            'gender' => 'jobseekers.gender',
            'age' => 'jobseekers.birth_date',
            'status' => 'jobseekers.status',
        ];

        $query = Jobseeker::query()
            ->select('jobseekers.*')
            ->join('users', 'users.id', '=', 'jobseekers.user_id')
            ->with('user');

        if ($request->filled('search')) {
            $search = $request->string('search')->value();
            $query->where(function ($builder) use ($search) {
                $builder->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%")
                ->orWhere('education', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('jobseekers.status', $request->string('status')->value());
        }

        if ($request->filled('city')) {
            $query->where('jobseekers.city', $request->string('city')->value());
        }

        if ($request->filled('gender')) {
            $query->where('jobseekers.gender', $request->string('gender')->value());
        }

        if ($request->filled('job_post_id')) {
            $query->join('applications', 'applications.jobseeker_id', '=', 'jobseekers.id')
                ->where('applications.job_post_id', $request->integer('job_post_id'))
                ->distinct('jobseekers.id');
        }

        $sortColumn = $sortable[$sort] ?? 'jobseekers.created_at';
        $query->orderBy($sortColumn, $direction);

        $jobseekers = $query->paginate(12)->withQueryString();
        $jobPosts = $employer?->jobPosts()->orderBy('title')->get(['id', 'title']) ?? collect();
        $cities = Jobseeker::query()
            ->whereNotNull('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');
        $genders = Jobseeker::query()
            ->whereNotNull('gender')
            ->distinct()
            ->orderBy('gender')
            ->pluck('gender');

        return view('employer.jobseekers.index', [
            'jobseekers' => $jobseekers,
            'jobPosts' => $jobPosts,
            'cities' => $cities,
            'genders' => $genders,
            'filters' => $request->only(['search', 'status', 'city', 'gender', 'job_post_id', 'sort', 'dir']),
            'sort' => $sortColumn === 'jobseekers.created_at' && ! isset($sortable[$sort]) ? 'created_at' : $sort,
            'dir' => $direction,
        ]);
    }

    public function show(Request $request, Jobseeker $jobseeker): View
    {
        $jobseeker->load(['user', 'documents']);

        return view('employer.jobseekers.show', [
            'jobseeker' => $jobseeker,
        ]);
    }
}
