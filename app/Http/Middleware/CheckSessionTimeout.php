<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * Check if the session has expired due to inactivity and log the user out.
     * This middleware works in conjunction with JavaScript inactivity detection.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isAuthenticated = Auth::guard('web')->check() || Auth::guard('employer_sub_user')->check();
        $lastActivity = Session::get('last_activity');
        $sessionLifetime = (int) config('session.lifetime', 30); // minutes

        if ($lastActivity && is_string($lastActivity)) {
            $lastActivity = Carbon::parse($lastActivity);
        } elseif ($lastActivity && ! ($lastActivity instanceof Carbon)) {
            $lastActivity = Carbon::parse($lastActivity);
        }

        // Handle timeout before route-level middlewares potentially abort with 403.
        // Only redirect for timeout when there is an active authenticated guard.
        if ($lastActivity instanceof Carbon && now()->diffInMinutes($lastActivity) >= $sessionLifetime) {
            if (! $isAuthenticated) {
                Session::forget('last_activity');

                return $next($request);
            }

            Auth::guard('web')->logout();
            Auth::guard('employer_sub_user')->logout();

            Session::invalidate();
            Session::regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('Your session has expired due to inactivity. Please log in again.'),
                    'session_expired' => true,
                ], 401);
            }

            return redirect('/')
                ->with('session_expired', true)
                ->withErrors(['email' => __('Your session has expired due to inactivity. Please log in again.')]);
        }

        // Track activity for active authenticated sessions only.
        if ($isAuthenticated) {
            Session::put('last_activity', now());
        }

        return $next($request);
    }
}
