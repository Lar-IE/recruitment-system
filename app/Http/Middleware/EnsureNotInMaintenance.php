<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotInMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        $maintenance = Setting::getValue('maintenance_mode', '0') === '1';

        if (! $maintenance) {
            return $next($request);
        }

        $user = $request->user();

        if ($user && $user->role === UserRole::Admin) {
            return $next($request);
        }

        if ($request->routeIs('login', 'register', 'password.*')) {
            return $next($request);
        }

        return response()->view('maintenance', [
            'siteName' => Setting::getValue('site_name', 'Recruitment System'),
            'supportEmail' => Setting::getValue('support_email', 'support@example.com'),
        ], 503);
    }
}
