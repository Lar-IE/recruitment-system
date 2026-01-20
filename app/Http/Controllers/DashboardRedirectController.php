<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardRedirectController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $role = $request->user()->role;

        return match ($role) {
            UserRole::Admin => redirect()->route('admin.dashboard'),
            UserRole::Employer => redirect()->route('employer.dashboard'),
            default => redirect()->route('jobseeker.dashboard'),
        };
    }
}
