<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureJobseekerDirectoryAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $employer = $request->attributes->get('employer') ?? $request->user()?->employer;

        if (! $employer || ! $employer->jobseeker_directory_access) {
            abort(403);
        }

        return $next($request);
    }
}

