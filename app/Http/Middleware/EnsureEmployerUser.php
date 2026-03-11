<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Models\EmployerSubUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployerUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $webUser = Auth::guard('web')->user();

        if ($webUser) {
            if ($webUser->role === UserRole::Employer) {
                // Prevent dashboard/login redirect loops when an employer-role user
                // exists without an associated employer profile record.
                if (! $webUser->employer) {
                    Auth::guard('web')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')
                        ->withErrors(['email' => __('Your employer profile is missing. Please contact support.')]);
                }

                if (! $webUser->email_verified_at) {
                    return redirect()->route('verification.notice');
                }

                if (! $webUser->is_active || $webUser->employer->status === 'suspended') {
                    Auth::guard('web')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')
                        ->withErrors(['email' => __('Your account has been suspended.')]);
                }

                Auth::shouldUse('web');
                $request->attributes->set('employer', $webUser->employer);
                $request->attributes->set('employer_user', $webUser);
                $request->attributes->set('employer_owner', true);

                return $next($request);
            }

            // Authenticated web users that are not employers should not hit employer routes.
            // Send them to the role-based dashboard instead of redirecting to login.
            return redirect()->route('dashboard');
        }

        $subUser = Auth::guard('employer_sub_user')->user();

        if ($subUser instanceof EmployerSubUser) {
            if (! $subUser->employer || ! $subUser->isActive() || $subUser->employer->status === 'suspended') {
                Auth::guard('employer_sub_user')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors(['email' => __('Your account has been suspended.')]);
            }

            Auth::shouldUse('employer_sub_user');
            $request->attributes->set('employer', $subUser->employer);
            $request->attributes->set('employer_user', $subUser);
            $request->attributes->set('employer_owner', false);

            return $next($request);
        }

        return redirect()->route('login');
    }
}
