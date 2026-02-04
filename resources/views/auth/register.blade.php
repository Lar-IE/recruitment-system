<x-guest-layout>
    <div class="space-y-6">
        <div class="flex flex-col items-center text-center gap-3">
            <a href="/" class="inline-flex">
                <img src="{{ asset('assets/images/sfi_tagline_main.png') }}" alt="{{ config('app.name') }}" class="h-15 w-auto">
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">{{ __('Create account') }}</h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Join and start building your profile or hiring workflow.') }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Role -->
            <div>
                <x-input-label for="role" :value="__('Register As')" />
                <select id="role" name="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="" disabled {{ old('role') ? '' : 'selected' }}>{{ __('Select role') }}</option>
                    <option value="jobseeker" {{ old('role') === 'jobseeker' ? 'selected' : '' }}>{{ __('Jobseeker') }}</option>
                    <option value="employer" {{ old('role') === 'employer' ? 'selected' : '' }}>{{ __('Employer') }}</option>
                </select>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full"
                              type="password"
                              name="password"
                              required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full"
                              type="password"
                              name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <x-primary-button class="w-full justify-center">
                {{ __('Create account') }}
            </x-primary-button>

            <p class="text-center text-sm text-gray-600">
                {{ __('Already registered?') }}
                <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-700">
                    {{ __('Sign in') }}
                </a>
            </p>
        </form>
    </div>
</x-guest-layout>
