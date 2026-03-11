<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'jobseeker.profile' => \App\Http\Middleware\EnsureJobseekerProfile::class,
            'active' => \App\Http\Middleware\EnsureUserIsActive::class,
            'maintenance' => \App\Http\Middleware\EnsureNotInMaintenance::class,
            'employer.approved' => \App\Http\Middleware\EnsureEmployerApproved::class,
            'employer.user' => \App\Http\Middleware\EnsureEmployerUser::class,
            'employer.role' => \App\Http\Middleware\EnsureEmployerRole::class,
            'employer.jobseeker-directory-access' => \App\Http\Middleware\EnsureJobseekerDirectoryAccess::class,
            'auth.any' => \App\Http\Middleware\AuthenticateAny::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\PreventAuthenticatedCache::class,
            \App\Http\Middleware\CheckSessionTimeout::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $handleSessionExpiry = function (Request $request) {
            Auth::guard('web')->logout();
            Auth::guard('employer_sub_user')->logout();

            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return redirect('/')
                ->with('session_expired', true)
                ->withErrors(['email' => __('Your session has expired. Please try again.')]);
        };

        $exceptions->render(function (TokenMismatchException $e, Request $request) use ($handleSessionExpiry) {
            return $handleSessionExpiry($request);
        });

        $exceptions->render(function (HttpExceptionInterface $e, Request $request) use ($handleSessionExpiry) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            return $handleSessionExpiry($request);
        });
    })->create();
