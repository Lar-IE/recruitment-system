@php
    use App\Helpers\CmsHelper;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ CmsHelper::siteName() }}</title>
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
    @if (session('session_expired'))
        <x-modal name="session-expired-modal" :show="true" maxWidth="md">
            <div class="p-6 sm:p-7">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 shrink-0 rounded-full bg-amber-100 p-2 text-amber-700">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.72-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.981-1.742 2.981H4.42c-1.53 0-2.492-1.647-1.742-2.98l5.58-9.921zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-1-7a1 1 0 00-1 1v3a1 1 0 102 0V7a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('Session expired') }}</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ __('Your session expired due to inactivity. Please log in again to continue.') }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        x-on:click="$dispatch('close')"
                        class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        {{ __('Close') }}
                    </button>
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black"
                    >
                        {{ __('Login') }}
                    </a>
                </div>
            </div>
        </x-modal>
    @endif

    {{-- 1. Navigation Bar - Sticky --}}
    <header class="sticky top-0 z-50 w-full border-b border-gray-200 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-8">
                    <a href="{{ url('/') }}" class="flex items-center shrink-0">
                        @if(CmsHelper::logo())
                            <img src="{{ CmsHelper::logo() }}" alt="{{ CmsHelper::siteName() }}" class="h-9 w-auto">
                        @else
                            <span class="text-xl font-bold text-gray-900">{{ CmsHelper::siteName() }}</span>
                        @endif
                    </a>
                    <div class="hidden md:flex items-center gap-6">
                        <a href="{{ url('/') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900 transition">{{ __('Home') }}</a>
                        <a href="#jobs" class="text-sm font-medium text-gray-700 hover:text-gray-900 transition">{{ __('Jobs') }}</a>
                        <a href="#employers" class="text-sm font-medium text-gray-700 hover:text-gray-900 transition">{{ __('Employers') }}</a>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if (auth()->check() || auth('employer_sub_user')->check())
                        @php
                            $dashboardUrl = url('/dashboard');
                            if (auth('employer_sub_user')->check()) {
                                $dashboardUrl = route('employer.dashboard');
                            } elseif (auth()->check() && auth()->user()->role?->value === 'employer') {
                                $dashboardUrl = route('employer.dashboard');
                            }
                        @endphp
                        <a href="{{ $dashboardUrl }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-gray-900 bg-gray-100 hover:bg-gray-200 transition">
                            {{ __('Dashboard') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-white bg-red-600 hover:bg-red-700 transition shadow-sm">
                                {{ __('Logout') }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900 transition">{{ __('Login') }}</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-white bg-gray-900 hover:bg-black transition shadow-sm">
                                {{ __('Register') }}
                            </a>
                        @endif
                    @endif
                </div>
            </div>
                </nav>
        </header>

    <main>
        {{-- 2. Hero Section with Carousel --}}
        @php
            $heroSlides = CmsHelper::heroCarouselSlides();
            $hasCarousel = count($heroSlides) > 0;
        @endphp
        <section
            class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-gray-900 to-black text-white flex flex-col"
            style="min-height: calc(100svh - 4rem); min-height: calc(100vh - 4rem);"
            x-data="{
                current: 0,
                total: {{ count($heroSlides) }},
                timer: null,
                autoplay() {
                    if (this.total <= 1) return;
                    this.timer = setInterval(() => { this.current = (this.current + 1) % this.total; }, 5000);
                },
                go(index) { this.current = index; clearInterval(this.timer); this.autoplay(); },
                prev() { this.go((this.current - 1 + this.total) % this.total); },
                next() { this.go((this.current + 1) % this.total); }
            }"
            x-init="autoplay()"
        >
            {{-- Carousel slides background --}}
            @if($hasCarousel)
                @foreach($heroSlides as $i => $slide)
                    <div
                        class="absolute inset-0 transition-opacity duration-700"
                        style="background-image: url('{{ $slide['url'] }}'); background-size: cover; background-position: center center; background-repeat: no-repeat;"
                        x-show="current === {{ $i }}"
                        x-transition:enter="transition-opacity duration-700"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity duration-700"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        aria-hidden="true"
                    ></div>
                @endforeach
                {{-- Dark overlay for readability --}}
                <div class="absolute inset-0 bg-black/50 pointer-events-none" aria-hidden="true"></div>
            @else
                {{-- Fallback: pattern overlay when no images --}}
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-80" aria-hidden="true"></div>
            @endif

            {{-- Hero content — vertically centered --}}
            <div class="relative flex-1 flex items-center">
                <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
                    <div class="max-w-3xl">
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight drop-shadow-sm">
                            {{ CmsHelper::heroTitle() }}
                        </h1>
                        <p class="mt-4 text-lg sm:text-xl text-white/90 drop-shadow-sm">
                            {{ CmsHelper::heroDescription() }}
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
            </div>

            {{-- Carousel controls (only when multiple slides) --}}
            @if(count($heroSlides) > 1)
                {{-- Prev / Next arrows --}}
                <button @click="prev()" type="button"
                    class="absolute left-3 sm:left-4 top-1/2 -translate-y-1/2 w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-full bg-black/30 text-white hover:bg-black/50 focus:outline-none focus-visible:ring-2 focus-visible:ring-white transition"
                    aria-label="{{ __('Previous slide') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button @click="next()" type="button"
                    class="absolute right-3 sm:right-4 top-1/2 -translate-y-1/2 w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-full bg-black/30 text-white hover:bg-black/50 focus:outline-none focus-visible:ring-2 focus-visible:ring-white transition"
                    aria-label="{{ __('Next slide') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>

                {{-- Dot indicators --}}
                <div class="absolute bottom-5 left-1/2 -translate-x-1/2 flex gap-2">
                    @foreach($heroSlides as $i => $slide)
                        <button @click="go({{ $i }})" type="button"
                            :class="current === {{ $i }} ? 'bg-white w-6' : 'bg-white/50 w-2.5'"
                            class="h-2.5 rounded-full transition-all duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                            aria-label="{{ __('Go to slide') }} {{ $i + 1 }}">
                        </button>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- 3. Features Section --}}
        <section id="jobs" class="py-16 sm:py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-gray-900 text-center">{{ CmsHelper::featuresTitle() }}</h2>
                <p class="mt-2 text-gray-600 text-center max-w-2xl mx-auto">{{ CmsHelper::featuresDescription() }}</p>
                <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                    @foreach(CmsHelper::features() as $feature)
                        <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 hover:shadow-md transition">
                            <div class="w-12 h-12 rounded-xl bg-gray-200 text-gray-900 flex items-center justify-center">
                                @if(($feature['icon'] ?? '') === 'document')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @elseif(($feature['icon'] ?? '') === 'chart')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                @elseif(($feature['icon'] ?? '') === 'check')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                @elseif(($feature['icon'] ?? '') === 'shield')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @endif
                            </div>
                            <h3 class="mt-4 font-semibold text-gray-900">{{ $feature['title'] ?? '' }}</h3>
                            <p class="mt-2 text-sm text-gray-600">{{ $feature['description'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- 4. How It Works --}}
        <section id="employers" class="py-16 sm:py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-gray-900 text-center">{{ CmsHelper::howItWorksTitle() }}</h2>
                <p class="mt-2 text-gray-600 text-center max-w-2xl mx-auto">{{ CmsHelper::howItWorksDescription() }}</p>
                <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
                    @foreach(CmsHelper::howItWorksSteps() as $index => $step)
                        <div class="relative text-center">
                            <div class="mx-auto w-14 h-14 rounded-2xl bg-gray-900 text-white flex items-center justify-center text-xl font-bold shadow-lg">{{ $index + 1 }}</div>
                            <h3 class="mt-4 font-semibold text-gray-900">{{ $step['title'] ?? '' }}</h3>
                            <p class="mt-2 text-sm text-gray-600">{{ $step['description'] ?? '' }}</p>
                            @if ($index === 0 && !auth()->check() && Route::has('register'))
                                <a href="{{ route('register') }}" class="mt-3 inline-block text-sm font-medium text-gray-900 hover:text-black">{{ __('Sign up') }} &rarr;</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- 5. Call-to-Action --}}
        <section class="py-16 sm:py-20 bg-gray-900">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl sm:text-4xl font-bold text-white">{{ CmsHelper::ctaTitle() }}</h2>
                <p class="mt-3 text-white/90">{{ CmsHelper::ctaDescription() }}</p>
                <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="mt-8 inline-flex items-center justify-center px-8 py-4 rounded-xl text-base font-semibold text-gray-900 bg-white shadow-lg hover:bg-gray-50 transition">
                    {{ __('Get Started') }}
                </a>
            </div>
        </section>

        {{-- 6. Footer --}}
        @php $activeSocials = CmsHelper::activeSocialLinks(); @endphp
        <footer class="border-t border-gray-200 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="md:col-span-2">
                        <a href="{{ url('/') }}" class="inline-flex">
                            @if(CmsHelper::logo())
                                <img src="{{ CmsHelper::logo() }}" alt="{{ CmsHelper::siteName() }}" class="h-8 w-auto">
                            @else
                                <span class="text-lg font-bold text-gray-900">{{ CmsHelper::siteName() }}</span>
                            @endif
                        </a>
                        <p class="mt-3 text-sm text-gray-600 max-w-sm">{{ CmsHelper::footerDescription() }}</p>
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

                {{-- Bottom bar: copyright left, social icons right --}}
                <div class="mt-10 pt-8 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <p class="text-sm text-gray-500">&copy; {{ date('Y') }} {{ CmsHelper::siteName() }}. {{ __('All rights reserved.') }}</p>

                    {{-- Social media icons --}}
                    @if(count($activeSocials) > 0)
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach($activeSocials as $social)
                                <a href="{{ $social['url'] }}"
                                    target="_blank" rel="noopener noreferrer"
                                    class="w-9 h-9 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-gray-900 hover:border-gray-400 hover:bg-gray-100 transition shadow-sm"
                                    aria-label="{{ $social['label'] }}"
                                    title="{{ $social['label'] }}">
                                    @include('admin.cms._social-icon', ['key' => $social['key']])
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </footer>
            </main>
    </body>
</html>
