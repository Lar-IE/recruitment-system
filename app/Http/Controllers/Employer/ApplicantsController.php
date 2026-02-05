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
            'education' => 'jobseekers.educational_attainment',
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

        if ($request->filled('educational_attainment')) {
            $query->where('jobseekers.educational_attainment', $request->string('educational_attainment')->value());
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
        $educationalAttainments = Application::query()
            ->join('job_posts', 'job_posts.id', '=', 'applications.job_post_id')
            ->join('jobseekers', 'jobseekers.id', '=', 'applications.jobseeker_id')
            ->where('job_posts.employer_id', $employer->id)
            ->whereNotNull('jobseekers.educational_attainment')
            ->distinct()
            ->orderBy('jobseekers.educational_attainment')
            ->pluck('jobseekers.educational_attainment');

        return view('employer.applicants.index', [
            'applications' => $applications,
            'jobPosts' => $jobPosts,
            'cities' => $cities,
            'genders' => $genders,
            'educationalAttainments' => $educationalAttainments,
            'filters' => $request->only(['job_post_id', 'status', 'city', 'gender', 'educational_attainment', 'sort', 'dir', 'search']),
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

    public function downloadTemplate()
    {
        $filename = 'applicants_template.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'first_name', 
                'middle_name', 
                'last_name', 
                'email', 
                'phone', 
                'city', 
                'education', 
                'educational_attainment',
                'gender', 
                'birth_date', 
                'position_applied', 
                'skills', 
                'experience_years', 
                'status',
                'applied_at'
            ]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls,txt',
        ]);

        $employer = $request->user()->employer;
        if (! $employer) {
            abort(403);
        }

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        
        // Skip header row
        fgetcsv($handle);
        
        $imported = 0;
        $validStatuses = array_keys($this->statuses());

        while (($row = fgetcsv($handle)) !== false) {
            // Skip if row doesn't have all required columns
            if (count($row) < 14) {
                continue;
            }

            [$firstName, 
            $middleName, 
            $lastName, 
            $email, 
            $phone, 
            $city, 
            $education,
            $educationalAttainment,
            $gender, 
            $birthDate, 
            $positionApplied, $skills, $experienceYears, $status, $appliedAt] = $row;

            // Validate required fields
            if (empty($firstName) || empty($lastName) || empty($email) || empty($positionApplied)) {
                continue;
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            // Validate status
            $status = strtolower(str_replace(' ', '_', trim($status)));
            if (!in_array($status, $validStatuses)) {
                $status = 'new';
            }

            // Validate educational_attainment
            $validEducationalAttainments = ['Elementary Graduate', 'High School Graduate', 'Vocational Graduate', 'College Undergraduate', 'College Graduate', 'Post Graduate'];
            if (!empty($educationalAttainment) && !in_array($educationalAttainment, $validEducationalAttainments)) {
                $educationalAttainment = null;
            }

            // Parse birth date
            $parsedBirthDate = null;
            if (!empty($birthDate)) {
                try {
                    $parsedBirthDate = \Carbon\Carbon::parse($birthDate)->format('Y-m-d');
                } catch (\Exception $e) {
                    $parsedBirthDate = null;
                }
            }

            // Format phone number with +63 prefix
            if (!empty($phone)) {
                $phone = trim($phone);
                if (!str_starts_with($phone, '+63')) {
                    // Remove leading zero if present
                    $phone = '+63' . ltrim($phone, '0');
                }
            }

            // Build full name for user
            $fullName = trim(($firstName ?? '') . ' ' . ($middleName ?? '') . ' ' . ($lastName ?? ''));

            // Find or create user
            $user = \App\Models\User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $fullName,
                    'role' => \App\Enums\UserRole::Jobseeker,
                    'password' => bcrypt(str()->random(16)),
                    'email_verified_at' => now(),
                ]
            );

            // Find or create jobseeker
            $jobseeker = $user->jobseeker()->firstOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'phone' => $phone,
                    'city' => $city,
                    'education' => $education,
                    'educational_attainment' => $educationalAttainment,
                    'gender' => strtolower($gender),
                    'birth_date' => $parsedBirthDate,
                    'skills' => $skills,
                    'experience' => $experienceYears,
                    'status' => 'active',
                ]
            );

            // Update jobseeker info if exists
            if (!$jobseeker->wasRecentlyCreated) {
                $jobseeker->update([
                    'first_name' => $firstName ?: $jobseeker->first_name,
                    'middle_name' => $middleName ?: $jobseeker->middle_name,
                    'last_name' => $lastName ?: $jobseeker->last_name,
                    'phone' => $phone ?: $jobseeker->phone,
                    'city' => $city ?: $jobseeker->city,
                    'education' => $education ?: $jobseeker->education,
                    'educational_attainment' => $educationalAttainment ?: $jobseeker->educational_attainment,
                    'gender' => $gender ? strtolower($gender) : $jobseeker->gender,
                    'birth_date' => $parsedBirthDate ?: $jobseeker->birth_date,
                    'skills' => $skills ?: $jobseeker->skills,
                    'experience' => $experienceYears ?: $jobseeker->experience,
                ]);
            }

            // Find job post by title
            $jobPost = $employer->jobPosts()
                ->where('title', 'like', '%' . $positionApplied . '%')
                ->orWhere('title', $positionApplied)
                ->first();

            // Skip if job post not found
            if (!$jobPost) {
                continue;
            }

            // Check if application already exists
            $existingApplication = Application::where('job_post_id', $jobPost->id)
                ->where('jobseeker_id', $jobseeker->id)
                ->first();

            if (!$existingApplication) {
                Application::create([
                    'job_post_id' => $jobPost->id,
                    'jobseeker_id' => $jobseeker->id,
                    'current_status' => $status,
                    'applied_at' => now(),
                ]);

                $imported++;
            }
        }

        fclose($handle);

        return redirect()->route('employer.applicants')
            ->with('success', "Successfully imported {$imported} applicant(s).");
    }

    public function export(Request $request)
    {
        $employer = $request->user()->employer;
        if (! $employer) {
            abort(403);
        }

        $query = Application::query()
            ->join('job_posts', 'job_posts.id', '=', 'applications.job_post_id')
            ->join('jobseekers', 'jobseekers.id', '=', 'applications.jobseeker_id')
            ->join('users', 'users.id', '=', 'jobseekers.user_id')
            ->where('job_posts.employer_id', $employer->id)
            ->select('applications.*');

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('applications.applied_at', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('applications.applied_at', '<=', $request->date('date_to'));
        }

        $applications = $query->with(['jobPost', 'jobseeker.user'])->get();

        $dateRange = '';
        if ($request->filled('date_from') || $request->filled('date_to')) {
            $from = $request->filled('date_from') ? $request->date('date_from')->format('Y-m-d') : 'start';
            $to = $request->filled('date_to') ? $request->date('date_to')->format('Y-m-d') : 'end';
            $dateRange = "_{$from}_to_{$to}";
        }

        $filename = 'applicants' . $dateRange . '_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($applications) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'first_name', 
                'middle_name', 
                'last_name', 
                'email', 
                'phone', 
                'city', 
                'education',
                'educational_attainment',
                'gender', 
                'birth_date', 
                'position_applied', 
                'skills', 
                'experience_years', 
                'status', 
                'applied_at'
            ]);
            
            foreach ($applications as $application) {
                $user = $application->jobseeker->user;
                $jobseeker = $application->jobseeker;
                
                // Format phone number with +63 prefix
                $phone = $jobseeker->phone ?? '';
                if (!empty($phone)) {
                    if (!str_starts_with($phone, '+63')) {
                        $phone = '+63' . ltrim($phone, '0');
                    }
                    // Add single quote prefix to prevent Excel from converting to scientific notation
                    $phone = "'" . $phone;
                }
                
                fputcsv($file, [
                    $jobseeker->first_name ?? '',
                    $jobseeker->middle_name ?? '',
                    $jobseeker->last_name ?? '',
                    $user->email,
                    $phone,
                    $jobseeker->city ?? '',
                    $jobseeker->education ?? '',
                    $jobseeker->educational_attainment ?? '',
                    $jobseeker->gender ?? '',
                    $jobseeker->birth_date?->format('Y-m-d') ?? '',
                    $application->jobPost->title ?? '',
                    $jobseeker->skills ?? '',
                    $jobseeker->experience ?? '',
                    $application->current_status,
                    $application->applied_at?->format('Y-m-d H:i:s') ?? '',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
