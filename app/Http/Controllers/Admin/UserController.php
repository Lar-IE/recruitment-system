<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->latest()
            ->paginate(10);

        return view('admin.users.index', [
            'users' => $users,
            'roles' => UserRole::labels(),
        ]);
    }

    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        $user->update([
            'is_active' => ! $user->is_active,
            'suspended_at' => $user->is_active ? now() : null,
        ]);

        return redirect()->route('admin.users')
            ->with('success', __('User status updated.'));
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:jobseeker,employer,admin'],
        ]);

        $user->update([
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users')
            ->with('success', __('User role updated.'));
    }
}
