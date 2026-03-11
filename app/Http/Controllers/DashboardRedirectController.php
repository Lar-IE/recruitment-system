<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardRedirectController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();
        $role = $user->role;

        if ($role === UserRole::Employer && $user->employer?->status === 'pending') {
            return redirect('/employer/pending');
        }

        if ($role === UserRole::Employer && ! $user->employer) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => __('Your employer profile is missing. Please contact support.')]);
        }

        return match ($role) {
            UserRole::Admin => redirect()->route('admin.dashboard'),
            UserRole::Employer => redirect()->route('employer.dashboard'),
            default => redirect()->route('jobseeker.dashboard'),
        };
    }
}
