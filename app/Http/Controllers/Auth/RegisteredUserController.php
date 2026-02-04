<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\Jobseeker;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'role' => ['required', Rule::in([UserRole::Jobseeker->value, UserRole::Employer->value])],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($request->role === UserRole::Employer->value) {
            $allowEmployer = Setting::getValue('allow_employer_registration', '0') === '1';
            if (! $allowEmployer) {
                return back()
                    ->withInput()
                    ->withErrors(['role' => __('Employer registration is currently disabled.')]);
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        if ($user->role === UserRole::Employer) {
            Employer::create([
                'user_id' => $user->id,
                'company_name' => $user->name.' Company',
            ]);
        }

        if ($user->role === UserRole::Jobseeker) {
            Jobseeker::create([
                'user_id' => $user->id,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        // Redirect jobseekers to profile edit page to complete their profile
        if ($user->role === UserRole::Jobseeker) {
            return redirect(route('jobseeker.profile.edit', absolute: false))
                ->with('success', __('Welcome! Please complete your profile to get started.'));
        }

        return redirect(route('dashboard', absolute: false));
    }
}
