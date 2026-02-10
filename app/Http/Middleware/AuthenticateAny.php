<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAny
{
    /**
     * Handle an incoming request.
     * Checks if user is authenticated on either web or employer_sub_user guard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if authenticated on web guard
        if (Auth::guard('web')->check()) {
            Auth::shouldUse('web');
            return $next($request);
        }

        // Check if authenticated on employer_sub_user guard
        if (Auth::guard('employer_sub_user')->check()) {
            Auth::shouldUse('employer_sub_user');
            return $next($request);
        }

        // Not authenticated on any guard, redirect to login
        return redirect()->route('login');
    }
}
