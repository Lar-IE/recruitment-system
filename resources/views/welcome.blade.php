<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}">
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
            <style>
        [x-cloak] { display: none !important; }
            </style>
</head>
<body class="font-sans text-gray-900 antialiased bg-white">
    {{-- 1. Navigation Bar - Sticky --}}
    <header class="sticky top-0 z-50 w-full border-b border-gray-200 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-8">
                    <a href="{{ url('/') }}" class="flex items-center shrink-0">
                        @if(file_exists(public_path('assets/images/sfi_tagline_main.png')))
                            <img src="{{ asset('assets/images/sfi_tagline_main.png') }}" alt="{{ config('app.name') }}" class="h-9 w-auto">
                        @else
                            <span class="text-xl font-bold text-gray-900">{{ config('app.name') }}</span>
        @endif
                    </a>
                    <div class="hidden md:flex items-center gap-6">
                        <a href="{{ url('/') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900 transition">{{ __('Home') }}</a>
                        <a href="#jobs" class="text-sm font-medium text-gray-700 hover:text-gray-900 transition">{{ __('Jobs') }}</a>
                        <a href="#employers" class="text-sm font-medium text-gray-700 hover:text-gray-900 transition">{{ __('Employers') }}</a>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-gray-900 bg-gray-100 hover:bg-gray-200 transition">
                            {{ __('Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900 transition">{{ __('Login') }}</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-white bg-gray-900 hover:bg-black transition shadow-sm">
                                {{ __('Register') }}
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
                </nav>
        </header>

    <main>
        {{-- 2. Hero Section --}}
        <section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-gray-900 to-black text-white">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-80"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 lg:py-28">
                <div class="max-w-3xl">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight">
                        {{ __('Find Your Dream Job Today') }}
                    </h1>
                    <p class="mt-4 text-lg sm:text-xl text-white/90">
                        {{ __('Connecting talented professionals with trusted employers.') }}
                    </p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('jobseeker.jobs') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl text-base font-semibold text-gray-900 bg-white shadow-lg hover:bg-gray-50 transition">
                            {{ __('Browse Jobs') }}
                        </a>
                        <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl text-base font-semibold text-white bg-gray-800 hover:bg-gray-700 border border-white/30 transition">
                            {{ __('Post a Job') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- 3. Features Section --}}
        <section id="jobs" class="py-16 sm:py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-gray-900 text-center">{{ __('Why Choose Us') }}</h2>
                <p class="mt-2 text-gray-600 text-center max-w-2xl mx-auto">{{ __('Everything you need to hire or get hired, in one place.') }}</p>
                <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-xl bg-gray-200 text-gray-900 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="mt-4 font-semibold text-gray-900">{{ __('Easy Job Application') }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ __('Apply to jobs in a few clicks and track your applications in real time.') }}</p>
                    </div>
                    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-xl bg-gray-200 text-gray-900 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <h3 class="mt-4 font-semibold text-gray-900">{{ __('Employer Dashboard') }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ __('Post jobs, manage applicants, and streamline your hiring pipeline.') }}</p>
                    </div>
                    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-xl bg-gray-200 text-gray-900 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <h3 class="mt-4 font-semibold text-gray-900">{{ __('Real-time Application Tracking') }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ __('See application status updates and stay in sync with employers.') }}</p>
                    </div>
                    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-xl bg-gray-200 text-gray-900 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h3 class="mt-4 font-semibold text-gray-900">{{ __('Secure & Verified Accounts') }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ __('Verified employers and secure document handling for your peace of mind.') }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- 4. How It Works --}}
        <section id="employers" class="py-16 sm:py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-gray-900 text-center">{{ __('How It Works') }}</h2>
                <p class="mt-2 text-gray-600 text-center max-w-2xl mx-auto">{{ __('Get started in three simple steps.') }}</p>
                <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
                    <div class="relative text-center">
                        <div class="mx-auto w-14 h-14 rounded-2xl bg-gray-900 text-white flex items-center justify-center text-xl font-bold shadow-lg">1</div>
                        <h3 class="mt-4 font-semibold text-gray-900">{{ __('Create Account') }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ __('Register as a job seeker or employer in minutes.') }}</p>
                        @if (!auth()->check() && Route::has('register'))
                            <a href="{{ route('register') }}" class="mt-3 inline-block text-sm font-medium text-gray-900 hover:text-black">{{ __('Sign up') }} &rarr;</a>
                        @endif
                    </div>
                    <div class="relative text-center">
                        <div class="mx-auto w-14 h-14 rounded-2xl bg-gray-900 text-white flex items-center justify-center text-xl font-bold shadow-lg">2</div>
                        <h3 class="mt-4 font-semibold text-gray-900">{{ __('Apply or Post Jobs') }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ __('Browse and apply to jobs, or post openings and receive applications.') }}</p>
                    </div>
                    <div class="relative text-center">
                        <div class="mx-auto w-14 h-14 rounded-2xl bg-gray-900 text-white flex items-center justify-center text-xl font-bold shadow-lg">3</div>
                        <h3 class="mt-4 font-semibold text-gray-900">{{ __('Get Hired / Hire Talent') }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ __('Connect, interview, and build your team or land your next role.') }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- 5. Call-to-Action --}}
        <section class="py-16 sm:py-20 bg-gray-900">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl sm:text-4xl font-bold text-white">{{ __('Start Hiring or Get Hired Today') }}</h2>
                <p class="mt-3 text-white/90">{{ __('Join thousands of professionals and companies already using our platform.') }}</p>
                <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="mt-8 inline-flex items-center justify-center px-8 py-4 rounded-xl text-base font-semibold text-gray-900 bg-white shadow-lg hover:bg-gray-50 transition">
                    {{ __('Get Started') }}
                </a>
            </div>
        </section>

        {{-- 6. Footer --}}
        <footer class="border-t border-gray-200 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="md:col-span-2">
                        <a href="{{ url('/') }}" class="inline-flex">
                            @if(file_exists(public_path('assets/images/sfi_tagline_main.png')))
                                <img src="{{ asset('assets/images/sfi_tagline_main.png') }}" alt="{{ config('app.name') }}" class="h-8 w-auto">
                            @else
                                <span class="text-lg font-bold text-gray-900">{{ config('app.name') }}</span>
                            @endif
                        </a>
                        <p class="mt-3 text-sm text-gray-600 max-w-sm">{{ __('Connecting talented professionals with trusted employers.') }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900">{{ __('Company') }}</h4>
                        <ul class="mt-3 space-y-2">
                            <li><a href="{{ url('/') }}#jobs" class="text-sm text-gray-600 hover:text-gray-900 transition">{{ __('About') }}</a></li>
                            <li><a href="{{ url('/') }}#employers" class="text-sm text-gray-600 hover:text-gray-900 transition">{{ __('Contact') }}</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900">{{ __('Legal') }}</h4>
                        <ul class="mt-3 space-y-2">
                            <li><a href="#" class="text-sm text-gray-600 hover:text-gray-900 transition">{{ __('Privacy Policy') }}</a></li>
                            <li><a href="#" class="text-sm text-gray-600 hover:text-gray-900 transition">{{ __('Terms') }}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-10 pt-8 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <p class="text-sm text-gray-500">&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}</p>
                    <div class="flex items-center gap-4">
                        <a href="#" class="text-gray-400 hover:text-gray-900 transition" aria-label="Twitter"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
                        <a href="#" class="text-gray-400 hover:text-gray-900 transition" aria-label="LinkedIn"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></a>
                        <a href="#" class="text-gray-400 hover:text-gray-900 transition" aria-label="GitHub"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"/></svg></a>
                    </div>
                </div>
            </div>
        </footer>
            </main>
    </body>
</html>
