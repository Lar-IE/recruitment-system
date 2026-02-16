<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

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
            'auth.any' => \App\Http\Middleware\AuthenticateAny::class,
        ]);
        $middleware->web(append: [\App\Http\Middleware\PreventAuthenticatedCache::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->is('login') || $request->routeIs('login')) {
                return redirect()->route('login')
                    ->withErrors(['email' => __('Your session has expired. Please try again.')]);
            }

            return redirect()->back()->withInput($request->except('password'))
                ->withErrors(['email' => __('Your session has expired. Please try again.')]);
        });
    })->create();
