<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class DashboardRedirectController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();
        $role = $user->role;

        if ($role === UserRole::Employer && $user->employer?->status === 'pending') {
            return redirect('/employer/pending');
        }

        return match ($role) {
            UserRole::Admin => redirect()->route('admin.dashboard'),
            UserRole::Employer => redirect()->route('employer.dashboard'),
            default => redirect()->route('jobseeker.dashboard'),
        };
    }
}
