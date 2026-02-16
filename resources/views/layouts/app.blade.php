<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    @php
        $dashboardArea = null;
        if (auth()->check()) {
            $u = auth()->user();
            if ($u instanceof \App\Models\EmployerSubUser) {
                $dashboardArea = 'employer';
            } else {
                $dashboardArea = match ($u->role?->value ?? null) {
                    'admin' => 'admin',
                    'employer' => 'employer',
                    'jobseeker' => 'jobseeker',
                    default => null,
                };
            }
        }
    @endphp
    @php
        $loaderArea = $dashboardArea ?? 'guest';
    @endphp
    <body class="font-sans antialiased" @if($dashboardArea) data-dashboard-area="{{ $dashboardArea }}" @endif data-loader-area="{{ $loaderArea }}">
        <div id="global-loader-{{ $loaderArea }}" class="fixed inset-0 bg-white/70 flex items-center justify-center z-50" style="display:none;" data-dashboard-area="{{ $loaderArea }}" aria-hidden="true">
            <l-mirage size="60" speed="2.5" color="black"></l-mirage>
        </div>

        <div class="min-h-screen bg-gray-100">
            @php
                $maintenanceEnabled = \App\Models\Setting::getValue('maintenance_mode', '0') === '1';
                $isAdmin = auth()->check() && auth()->user()->role?->value === 'admin';
            @endphp

            @if ($isAdmin && $maintenanceEnabled)
                <div class="bg-amber-500 text-white">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div class="text-sm font-medium">
                            {{ __('Maintenance mode is currently enabled.') }}
                        </div>
                        <form method="POST" action="{{ route('admin.settings.toggle-maintenance') }}">
                            @csrf
                            <button type="submit" class="text-xs font-semibold bg-white/20 hover:bg-white/30 px-3 py-1 rounded">
                                {{ __('Disable now') }}
                            </button>
                        </form>
                    </div>
                </div>
            @elseif ($isAdmin && ! $maintenanceEnabled)
                <div class="bg-indigo-600 text-white">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div class="text-sm font-medium">
                            {{ __('Maintenance mode is off.') }}
                        </div>
                        <form method="POST" action="{{ route('admin.settings.toggle-maintenance') }}">
                            @csrf
                            <button type="submit" class="text-xs font-semibold bg-white/20 hover:bg-white/30 px-3 py-1 rounded">
                                {{ __('Enable now') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/mirage.js"></script>
        <script>
            (function () {
                // Scope loader to THIS window and THIS dashboard area only.
                // Each tab/window has its own document; loader id is per-area so it never affects other account types.
                const thisWindow = window;
                const thisDocument = document;

                const getLoader = () => {
                    const area = thisDocument.body.getAttribute('data-loader-area') || thisDocument.body.getAttribute('data-dashboard-area') || 'guest';
                    const el = thisDocument.getElementById('global-loader-' + area);
                    // Only use loader that belongs to this document and matches this page's area
                    if (!el || el.getAttribute('data-dashboard-area') !== area) return null;
                    return el;
                };

                const loader = getLoader();
                if (!loader) return;

                // Track navigation state for THIS tab only (closure-scoped)
                let isNavigating = false;

                const showLoader = () => {
                    const currentLoader = getLoader();
                    if (!currentLoader) return;
                    // Only show if this tab's loader exists, matches this document, and tab is visible
                    if (!isNavigating && !thisDocument.hidden) {
                        isNavigating = true;
                        currentLoader.style.display = 'flex';
                    }
                };

                const hideLoader = () => {
                    if (!isNavigating) return;
                    const currentLoader = getLoader();
                    if (currentLoader) currentLoader.style.display = 'none';
                    isNavigating = false;
                };

                // Only show loader when navigating away in THIS window
                thisWindow.addEventListener('beforeunload', function (event) {
                    if (event.target === thisWindow || event.target === thisDocument || !event.target) {
                        showLoader();
                    }
                }, { capture: false, passive: true });

                if (thisDocument.readyState === 'complete') {
                    hideLoader();
                } else {
                    thisWindow.addEventListener('load', function (event) {
                        if (event.target === thisWindow || event.target === thisDocument || event.target === thisDocument.documentElement) {
                            hideLoader();
                        }
                    }, { once: true, capture: false, passive: true });
                }

                // Form submit: only show loader for forms in THIS document
                thisDocument.addEventListener('submit', function (event) {
                    const form = event.target;
                    if (form instanceof HTMLFormElement && form.ownerDocument === thisDocument && form.closest('body') === thisDocument.body) {
                        showLoader();
                    }
                }, { capture: false, passive: true });

                // Link click: only show loader for same-dashboard-area navigation in THIS document
                thisDocument.addEventListener('click', function (event) {
                    const target = event.target.closest('a');
                    if (!target || target.ownerDocument !== thisDocument || target.closest('body') !== thisDocument.body || !target.href) return;
                    try {
                        const url = new URL(target.href, thisWindow.location.origin);
                        if (url.origin !== thisWindow.location.origin ||
                            url.pathname === thisWindow.location.pathname ||
                            target.hasAttribute('target') ||
                            target.hasAttribute('download') ||
                            (target.getAttribute('href') || '').match(/^#/)) return;

                        const area = thisDocument.body.getAttribute('data-dashboard-area') || thisDocument.body.getAttribute('data-loader-area');
                        if (area) {
                            const path = url.pathname.replace(/\/$/, '') || '/';
                            const currentPath = thisWindow.location.pathname.replace(/\/$/, '') || '/';
                            const areaPrefix = '/' + area;
                            const sameArea = path === '/dashboard' || path === areaPrefix || path.startsWith(areaPrefix + '/');
                            const currentSameArea = currentPath === '/dashboard' || currentPath === areaPrefix || currentPath.startsWith(areaPrefix + '/');
                            if (!sameArea || !currentSameArea) return;
                        }
                        showLoader();
                    } catch (e) {}
                }, { capture: false, passive: true });

                thisDocument.addEventListener('visibilitychange', function () {
                    if (thisDocument.hidden) {
                        isNavigating = false;
                        const currentLoader = getLoader();
                        if (currentLoader) currentLoader.style.display = 'none';
                    }
                }, { capture: false, passive: true });

                thisWindow.addEventListener('pageshow', function (event) {
                    if (event.persisted) hideLoader();
                }, { capture: false, passive: true });

                if (thisDocument.readyState === 'loading') {
                    thisDocument.addEventListener('DOMContentLoaded', function () {
                        setTimeout(function () {
                            if (!isNavigating) hideLoader();
                        }, 100);
                    }, { once: true, passive: true });
                }
            })();
        </script>
    </body>
</html>
