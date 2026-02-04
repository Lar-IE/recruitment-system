<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Models\EmployerSubUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployerApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user instanceof EmployerSubUser) {
            $employer = $user->employer;

            if ($employer && $employer->status === 'pending') {
                abort(403);
            }

            return $next($request);
        }

        if (! $user || $user->role !== UserRole::Employer) {
            return $next($request);
        }

        $employer = $user->employer;

        if (! $employer) {
            return $next($request);
        }

        if ($employer->status === 'pending' && ! $request->routeIs('employer.pending')) {
            return redirect('/employer/pending');
        }

        return $next($request);
    }
}
