<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Models\EmployerSubUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        // Handle employer sub-users
        if ($user instanceof EmployerSubUser) {
            if ($user->status !== 'active' || !$user->employer || $user->employer->status === 'suspended') {
                Auth::guard('employer_sub_user')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors(['email' => __('Your account has been suspended.')]);
            }

            return $next($request);
        }

        // Handle regular users
        $isActive = $user->is_active;
        $employerActive = true;
        $jobseekerActive = true;

        if ($user->role === UserRole::Employer && $user->employer) {
            $employerActive = $user->employer->status !== 'suspended';
        }

        if ($user->role === UserRole::Jobseeker && $user->jobseeker) {
            $jobseekerActive = $user->jobseeker->status !== 'suspended';
        }

        if (! $isActive || ! $employerActive || ! $jobseekerActive) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => __('Your account has been suspended.')]);
        }

        return $next($request);
    }
}
