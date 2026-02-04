<x-guest-layout>
    <div class="space-y-6">
        <div class="flex flex-col items-center text-center gap-3">
            <a href="/" class="inline-flex">
                <img src="{{ asset('assets/images/sfi_tagline_main.png') }}" alt="{{ config('app.name') }}" class="h-15 w-auto">
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">{{ __('Sign In') }}</h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Sign in to manage applications, profiles, and hiring workflows.') }}
                </p>
            </div>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-0" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full"
                              type="password"
                              name="password"
                              required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between text-sm">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-gray-600">{{ __('Remember me') }}</span>
                </label>
                @if (Route::has('password.request'))
                    <a class="text-gray-600 hover:text-gray-900 underline decoration-transparent hover:decoration-current transition" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <x-primary-button class="w-full justify-center">
                {{ __('Log in') }}
            </x-primary-button>

            @if (Route::has('register'))
                <p class="text-center text-sm text-gray-600">
                    {{ __('No account yet?') }}
                    <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-700">
                        {{ __('Create one') }}
                    </a>
                </p>
            @endif
        </form>

        <div class="flex items-center gap-3 text-xs text-gray-400">
            <span class="h-px flex-1 bg-gray-200"></span>
            {{ __('or continue with') }}
            <span class="h-px flex-1 bg-gray-200"></span>
        </div>

        <a href="{{ route('auth.google.redirect') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
            <svg class="h-4 w-4" viewBox="0 0 48 48" aria-hidden="true">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.72 1.22 9.22 3.23l6.9-6.9C35.86 2.36 30.27 0 24 0 14.64 0 6.38 5.38 2.38 13.22l8.04 6.24C12.3 13.36 17.7 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.5 24.5c0-1.57-.14-3.08-.4-4.5H24v9h12.7c-.55 2.95-2.22 5.45-4.74 7.14l7.33 5.68C43.86 37.65 46.5 31.5 46.5 24.5z"/>
                <path fill="#FBBC05" d="M10.42 28.46a14.5 14.5 0 0 1 0-8.92l-8.04-6.24A23.94 23.94 0 0 0 0 24c0 3.92.94 7.62 2.38 10.7l8.04-6.24z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.92-2.15 15.9-5.86l-7.33-5.68c-2.04 1.37-4.64 2.18-8.57 2.18-6.3 0-11.7-3.86-13.58-9.04l-8.04 6.24C6.38 42.62 14.64 48 24 48z"/>
            </svg>
            {{ __('Continue with Google') }}
        </a>
    </div>
</x-guest-layout>
