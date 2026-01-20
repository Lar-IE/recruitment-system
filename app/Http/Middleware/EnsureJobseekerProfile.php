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

        if ($user && $user->role === UserRole::Jobseeker && ! $user->jobseeker) {
            Jobseeker::create([
                'user_id' => $user->id,
            ]);
            $user->refresh();
        }

        return $next($request);
    }
}
