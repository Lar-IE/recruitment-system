<x-guest-layout :full-width="true">
    <div class="min-h-screen flex flex-col md:flex-row">
        {{-- Left: Branding --}}
        <div class="hidden md:flex md:w-[42%] lg:w-[40%] flex-shrink-0 flex-col justify-between bg-gradient-to-br from-slate-900 via-gray-900 to-black p-10 xl:p-14 text-white">
            <a href="{{ url('/') }}" class="inline-flex focus:outline-none focus:ring-2 focus:ring-white/40 rounded-lg">
                @if(file_exists(public_path('assets/images/sfi_tagline_main.png')))
                    <img src="{{ asset('assets/images/sfi_tagline_main_white_stroke.png') }}" alt="{{ config('app.name') }}" class="h-10 w-auto opacity-90">
                @else
                    <span class="text-xl font-bold text-white">{{ config('app.name') }}</span>
                @endif
            </a>
            <div class="mt-12">
                <h1 class="text-3xl xl:text-4xl font-bold tracking-tight text-white">
                    {{ config('app.name') }}
                </h1>
                <p class="mt-4 text-white/90 text-lg max-w-sm">
                    {{ __('Connecting Employers with Top Talent') }}
                </p>
                <div class="mt-10 flex items-center gap-3 text-slate-300">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-sm">Streamlined hiring and job search in one place.</p>
                </div>
            </div>
            <p class="text-sm text-slate-400">{{ config('app.name') }} &copy; {{ date('Y') }}</p>
        </div>

        {{-- Right: Form area — soft background, centered card --}}
        <div class="flex-1 flex flex-col justify-center items-center min-w-0 px-4 sm:px-6 md:px-12 lg:px-16 py-10 md:py-16 bg-soft/30">
            <div class="w-full max-w-[420px]">
                {{-- Logo on mobile --}}
                <div class="md:hidden flex flex-col items-center text-center mb-8">
                    <a href="{{ url('/') }}" class="inline-flex">
                        @if(file_exists(public_path('assets/images/sfi_tagline_main.png')))
                            <img src="{{ asset('assets/images/sfi_tagline_main.png') }}" alt="{{ config('app.name') }}" class="h-12 w-auto">
                        @else
                            <span class="text-xl font-bold text-primary">{{ config('app.name') }}</span>
                        @endif
                    </a>
                </div>

                {{-- Form card: white, rounded, soft shadow --}}
                <div class="bg-white rounded-2xl shadow-xl shadow-soft border border-muted p-8 sm:p-10">
                    <h1 class="text-2xl font-bold tracking-tight text-primary">{{ __('Create account') }}</h1>
                    <p class="mt-2 text-sm text-muted leading-relaxed">
                        {{ __('Join and start building your profile or hiring workflow.') }}
                    </p>

                    <form method="POST" action="{{ route('register') }}" class="mt-7 space-y-5">
                        @csrf

                        <div>
                            <x-input-label for="name" :value="__('Name')" class="!text-primary !font-medium !text-sm" />
                            <x-text-input
                                id="name"
                                type="text"
                                name="name"
                                :value="old('name')"
                                required
                                autofocus
                                autocomplete="name"
                                class="block mt-2 w-full rounded-xl border border-muted bg-white px-4 py-3 text-base text-primary placeholder-muted shadow-sm transition duration-200 focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/25"
                            />
                            <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email')" class="!text-primary !font-medium !text-sm" />
                            <x-text-input
                                id="email"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required
                                autocomplete="username"
                                class="block mt-2 w-full rounded-xl border border-muted bg-white px-4 py-3 text-base text-primary placeholder-muted shadow-sm transition duration-200 focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/25"
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm" />
                        </div>

                        <div>
                            <x-input-label for="role" :value="__('Register As')" class="!text-primary !font-medium !text-sm" />
                            <select
                                id="role"
                                name="role"
                                required
                                class="block mt-2 w-full rounded-xl border border-muted bg-white px-4 py-3 text-base text-primary shadow-sm transition duration-200 focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/25"
                            >
                                <option value="" disabled {{ old('role') ? '' : 'selected' }}>{{ __('Select role') }}</option>
                                <option value="jobseeker" {{ old('role') === 'jobseeker' ? 'selected' : '' }}>{{ __('Jobseeker') }}</option>
                                <option value="employer" {{ old('role') === 'employer' ? 'selected' : '' }}>{{ __('Employer') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2 text-sm" />
                        </div>

                        <div x-data="{ showPassword: false }">
                            <x-input-label for="password" :value="__('Password')" class="!text-primary !font-medium !text-sm" />
                            <div class="relative mt-2">
                                <x-text-input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="new-password"
                                    class="block w-full rounded-xl border border-muted bg-white px-4 py-3 pr-12 text-base text-primary placeholder-muted shadow-sm transition duration-200 focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/25"
                                    x-ref="passwordField"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword; $refs.passwordField.type = showPassword ? 'text' : 'password'"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 p-2 rounded-lg text-muted hover:text-primary hover:bg-soft focus:outline-none focus:ring-2 focus:ring-accent/25 transition duration-200"
                                    tabindex="-1"
                                    aria-label="{{ __('Show password') }}"
                                >
                                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm" />
                        </div>

                        <div x-data="{ showPasswordConfirmation: false }">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="!text-primary !font-medium !text-sm" />
                            <div class="relative mt-2">
                                <x-text-input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    class="block w-full rounded-xl border border-muted bg-white px-4 py-3 pr-12 text-base text-primary placeholder-muted shadow-sm transition duration-200 focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/25"
                                    x-ref="passwordConfirmationField"
                                />
                                <button
                                    type="button"
                                    @click="showPasswordConfirmation = !showPasswordConfirmation; $refs.passwordConfirmationField.type = showPasswordConfirmation ? 'text' : 'password'"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 p-2 rounded-lg text-muted hover:text-primary hover:bg-soft focus:outline-none focus:ring-2 focus:ring-accent/25 transition duration-200"
                                    tabindex="-1"
                                    aria-label="{{ __('Show password') }}"
                                >
                                    <svg x-show="!showPasswordConfirmation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showPasswordConfirmation" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm" />
                        </div>

                        <x-primary-button class="w-full justify-center rounded-xl py-3.5 text-base font-semibold normal-case tracking-normal">
                            {{ __('Create account') }}
                        </x-primary-button>

                        <p class="text-center text-sm text-muted pt-1">
                            {{ __('Already registered?') }}
                            <a href="{{ route('login') }}" class="font-semibold text-accent hover:text-accent-hover transition duration-200">
                                {{ __('Sign in') }}
                            </a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
