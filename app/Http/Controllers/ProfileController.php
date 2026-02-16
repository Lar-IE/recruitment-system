<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Jobseeker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display account settings (name, password, delete).
     */
    public function accountSettings(Request $request): View
    {
        $user = $request->user();
        $employer = null;
        $isOwner = false;

        // Check if main employer
        if ($user && $user->role === UserRole::Employer) {
            $employer = $user->employer;
            $isOwner = true;
        }

        // Check if employer sub-user
        if ($user instanceof \App\Models\EmployerSubUser) {
            $employer = $user->employer;
            $isOwner = false;
        }

        return view('profile.account-settings', [
            'user' => $user,
            'employer' => $employer,
            'isOwner' => $isOwner,
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $jobseeker = null;
        $employer = null;
        $isOwner = false;

        if ($user && $user->role === UserRole::Jobseeker) {
            $jobseeker = Jobseeker::firstOrCreate([
                'user_id' => $user->id,
            ]);
        }

        // Check if main employer
        if ($user && $user->role === UserRole::Employer) {
            $employer = $user->employer;
            $isOwner = true;
        }

        // Check if employer sub-user
        if ($user instanceof \App\Models\EmployerSubUser) {
            $employer = $user->employer;
            $isOwner = false; // Sub-users are not owners
        }

        return view('profile.edit', [
            'user' => $user,
            'jobseeker' => $jobseeker,
            'employer' => $employer,
            'isOwner' => $isOwner,
        ]);
    }

    /**
     * Update the user's profile information.
     * Redirects back to the page the user came from (Profile or Account Settings)
     * so they do not see both pages merged after saving.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Handle employer sub-users (they don't have email_verified_at)
        if ($user instanceof \App\Models\EmployerSubUser) {
            $user->fill($request->only(['name', 'email']));
            $user->save();

            return redirect()->back()->with('status', 'profile-updated');
        }

        // Handle regular users
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->back()->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Handle employer sub-users
        if ($user instanceof \App\Models\EmployerSubUser) {
            $request->validateWithBag('userDeletion', [
                'password' => ['required', 'current_password:employer_sub_user'],
            ]);

            Auth::guard('employer_sub_user')->logout();
            $user->delete();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return Redirect::to('/');
        }

        // Handle regular users
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
