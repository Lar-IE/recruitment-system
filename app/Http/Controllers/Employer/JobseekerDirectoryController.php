<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Jobseeker;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            'educational_attainment' => 'jobseekers.educational_attainment',
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
                ->orWhere('first_name', 'like', "%{$search}%")
                ->orWhere('middle_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%");
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

        if ($request->filled('educational_attainment')) {
            $query->where('jobseekers.educational_attainment', $request->string('educational_attainment')->value());
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
        $educationalAttainments = Jobseeker::query()
            ->whereNotNull('educational_attainment')
            ->distinct()
            ->orderBy('educational_attainment')
            ->pluck('educational_attainment');

        return view('employer.jobseekers.index', [
            'jobseekers' => $jobseekers,
            'jobPosts' => $jobPosts,
            'cities' => $cities,
            'genders' => $genders,
            'educationalAttainments' => $educationalAttainments,
            'filters' => $request->only(['search', 'status', 'city', 'gender', 'educational_attainment', 'job_post_id', 'sort', 'dir']),
            'sort' => $sortColumn === 'jobseekers.created_at' && ! isset($sortable[$sort]) ? 'created_at' : $sort,
            'dir' => $direction,
        ]);
    }

    public function show(Request $request, Jobseeker $jobseeker): View
    {
        $jobseeker->load(['user', 'documents', 'educations', 'workExperiences']);

        return view('employer.jobseekers.show', [
            'jobseeker' => $jobseeker,
        ]);
    }

    public function downloadTemplate(): StreamedResponse
    {
        $filename = 'jobseekers_template.csv';
        
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
                'educational_attainment',
                'gender', 
                'birth_date',
                'skills',
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

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        
        // Skip header row
        fgetcsv($handle);
        
        $imported = 0;
        $validEducationalAttainments = ['Elementary Graduate', 'High School Graduate', 'Vocational Graduate', 'College Undergraduate', 'College Graduate', 'Post Graduate'];

        while (($row = fgetcsv($handle)) !== false) {
            // Skip if row doesn't have all required columns
            if (count($row) < 10) {
                continue;
            }

            [$firstName, $middleName, $lastName, $email, $phone, $city, $educationalAttainment, $gender, $birthDate, $skills] = $row;

            // Validate required fields
            if (empty($firstName) || empty($lastName) || empty($email)) {
                continue;
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            // Validate educational_attainment
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
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $fullName,
                    'role' => UserRole::Jobseeker,
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
                    'educational_attainment' => $educationalAttainment,
                    'gender' => $gender ? strtolower($gender) : null,
                    'birth_date' => $parsedBirthDate,
                    'skills' => $skills,
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
                    'educational_attainment' => $educationalAttainment ?: $jobseeker->educational_attainment,
                    'gender' => $gender ? strtolower($gender) : $jobseeker->gender,
                    'birth_date' => $parsedBirthDate ?: $jobseeker->birth_date,
                    'skills' => $skills ?: $jobseeker->skills,
                ]);
            }

            $imported++;
        }

        fclose($handle);

        return redirect()->route('employer.jobseekers.index')
            ->with('success', "Successfully imported {$imported} jobseeker(s).");
    }

    public function export(Request $request): StreamedResponse
    {
        $query = Jobseeker::query()
            ->join('users', 'users.id', '=', 'jobseekers.user_id')
            ->select('jobseekers.*');

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->string('search')->value();
            $query->where(function ($builder) use ($search) {
                $builder->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('first_name', 'like', "%{$search}%")
                ->orWhere('middle_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%");
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

        if ($request->filled('educational_attainment')) {
            $query->where('jobseekers.educational_attainment', $request->string('educational_attainment')->value());
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('jobseekers.created_at', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('jobseekers.created_at', '<=', $request->date('date_to'));
        }

        $jobseekers = $query->with('user')->get();

        $dateRange = '';
        if ($request->filled('date_from') || $request->filled('date_to')) {
            $from = $request->filled('date_from') ? $request->date('date_from')->format('Y-m-d') : 'start';
            $to = $request->filled('date_to') ? $request->date('date_to')->format('Y-m-d') : 'end';
            $dateRange = "_{$from}_to_{$to}";
        }

        $filename = 'jobseekers' . $dateRange . '_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($jobseekers) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'first_name', 
                'middle_name', 
                'last_name', 
                'email', 
                'phone', 
                'city', 
                'educational_attainment',
                'gender', 
                'birth_date',
                'skills',
                'status',
                'created_at'
            ]);
            
            foreach ($jobseekers as $jobseeker) {
                $user = $jobseeker->user;
                
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
                    $user->email ?? '',
                    $phone,
                    $jobseeker->city ?? '',
                    $jobseeker->educational_attainment ?? '',
                    $jobseeker->gender ?? '',
                    $jobseeker->birth_date?->format('Y-m-d') ?? '',
                    $jobseeker->skills ?? '',
                    $jobseeker->status ?? '',
                    $jobseeker->created_at?->format('Y-m-d H:i:s') ?? '',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
