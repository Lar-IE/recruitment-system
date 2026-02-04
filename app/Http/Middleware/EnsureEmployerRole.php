<?php

namespace App\Http\Middleware;

use App\Models\EmployerSubUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployerRole
{
    /**
     * @param  array<int, string>  $roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $isOwner = $request->attributes->get('employer_owner', false);

        if ($isOwner) {
            return $next($request);
        }

        $user = $request->user();

        if (! $user instanceof EmployerSubUser) {
            abort(403);
        }

        if (! $user->role || ! in_array($user->role->value, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
