<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Models\Jobseeker;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureJobseekerProfile
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === UserRole::Jobseeker) {
            // Create jobseeker profile if it doesn't exist
            if (! $user->jobseeker) {
                Jobseeker::create([
                    'user_id' => $user->id,
                    'status' => 'active',
                ]);
                $user->refresh();
            }

            // Check if required profile fields are completed
            $jobseeker = $user->jobseeker;
            $requiredFields = ['first_name', 'last_name', 'phone', 'city', 'birth_date', 'gender', 'educational_attainment'];
            
            foreach ($requiredFields as $field) {
                if (empty($jobseeker->$field)) {
                    return redirect()
                        ->route('jobseeker.profile.edit')
                        ->with('warning', 'Please complete all required profile fields to continue.');
                }
            }
        }

        return $next($request);
    }
}
