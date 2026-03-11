<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

        @isset($meta)
            {!! $meta !!}
        @endisset

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

        @php
            $maintenanceEnabled = \App\Models\Setting::getValue('maintenance_mode', '0') === '1';
            $isAdmin = auth()->check() && auth()->user()->role?->value === 'admin';
        @endphp

        <x-layouts.app-shell>
            <x-slot name="topbar">
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

                <x-app.header />
            </x-slot>

            <x-slot name="sidebar">
                <x-app.sidebar />
            </x-slot>

            <div class="px-4 sm:px-6 lg:px-8 py-6">
                @isset($header)
                    <div class="mb-6">
                        {{ $header }}
                    </div>
                @endisset

                <section>
                    {{ $slot }}
                </section>
            </div>
        </x-layouts.app-shell>

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

        @if (auth()->check() || auth('employer_sub_user')->check())
        <script>
            (function() {
                'use strict';
                
                // Session timeout configuration (30 minutes = 1800000 milliseconds)
                const SESSION_TIMEOUT_MS = {{ config('session.lifetime', 30) * 60 * 1000 }};
                const WARNING_TIME_MS = 5 * 60 * 1000; // Show warning 5 minutes before timeout
                const CHECK_INTERVAL_MS = 60000; // Check every minute
                
                let idleTimer = null;
                let warningTimer = null;
                let lastActivity = Date.now();
                let isWarningShown = false;
                
                // Events that indicate user activity
                const activityEvents = [
                    'mousedown', 'mousemove', 'keypress', 'scroll', 
                    'touchstart', 'click', 'keydown'
                ];
                
                // Reset idle timer on user activity
                function resetIdleTimer() {
                    lastActivity = Date.now();
                    clearTimeout(idleTimer);
                    clearTimeout(warningTimer);
                    isWarningShown = false;
                    
                    // Hide warning if shown
                    const warningEl = document.getElementById('session-timeout-warning');
                    if (warningEl) {
                        warningEl.remove();
                    }
                    
                    // Set new timeout
                    startIdleTimer();
                }
                
                // Start the idle timer
                function startIdleTimer() {
                    const timeSinceActivity = Date.now() - lastActivity;
                    const remainingTime = SESSION_TIMEOUT_MS - timeSinceActivity;
                    
                    if (remainingTime <= 0) {
                        // Session already expired, logout immediately
                        logoutUser();
                        return;
                    }
                    
                    // Show warning before timeout
                    const warningTime = remainingTime - WARNING_TIME_MS;
                    if (warningTime > 0 && !isWarningShown) {
                        warningTimer = setTimeout(showWarning, warningTime);
                    }
                    
                    // Set logout timer
                    idleTimer = setTimeout(logoutUser, remainingTime);
                }
                
                // Show warning message
                function showWarning() {
                    isWarningShown = true;
                    
                    // Remove existing warning if any
                    const existingWarning = document.getElementById('session-timeout-warning');
                    if (existingWarning) {
                        existingWarning.remove();
                    }
                    
                    // Create warning element
                    const warning = document.createElement('div');
                    warning.id = 'session-timeout-warning';
                    warning.className = 'fixed top-4 right-4 z-[9999] bg-amber-500 text-white px-6 py-4 rounded-lg shadow-lg max-w-md';
                    warning.innerHTML = `
                        <div class="flex items-start gap-3">
                            <svg class="h-6 w-6 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div class="flex-1">
                                <p class="font-semibold mb-1">{{ __('Session Timeout Warning') }}</p>
                                <p class="text-sm">{{ __('Your session will expire in 5 minutes due to inactivity. Click anywhere to continue.') }}</p>
                            </div>
                            <button onclick="this.closest('#session-timeout-warning').remove(); resetIdleTimer();" class="text-white hover:text-gray-200 ml-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    `;
                    
                    document.body.appendChild(warning);
                    
                    // Auto-remove warning after 10 seconds if user is active
                    setTimeout(() => {
                        if (warning.parentNode) {
                            warning.remove();
                        }
                    }, 10000);
                }
                
                // Logout user and redirect
                function logoutUser() {
                    // Clear all timers
                    clearTimeout(idleTimer);
                    clearTimeout(warningTimer);
                    
                    // Remove warning if shown
                    const warningEl = document.getElementById('session-timeout-warning');
                    if (warningEl) {
                        warningEl.remove();
                    }
                    
                    // Create logout form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("logout") }}';
                    
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (csrfToken) {
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrfToken.getAttribute('content');
                        form.appendChild(csrfInput);
                    }
                    
                    document.body.appendChild(form);
                    form.submit();
                }
                
                // Attach activity listeners
                activityEvents.forEach(event => {
                    document.addEventListener(event, resetIdleTimer, { passive: true });
                });
                
                // Also track visibility changes (tab switching)
                document.addEventListener('visibilitychange', function() {
                    if (!document.hidden) {
                        // Tab became visible, check if session is still valid
                        const timeSinceActivity = Date.now() - lastActivity;
                        if (timeSinceActivity >= SESSION_TIMEOUT_MS) {
                            logoutUser();
                        } else {
                            resetIdleTimer();
                        }
                    }
                });
                
                // Check session status periodically
                setInterval(function() {
                    const timeSinceActivity = Date.now() - lastActivity;
                    if (timeSinceActivity >= SESSION_TIMEOUT_MS) {
                        logoutUser();
                    }
                }, CHECK_INTERVAL_MS);
                
                // Initialize timer on page load
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', startIdleTimer);
                } else {
                    startIdleTimer();
                }
                
                // Make resetIdleTimer available globally for warning button
                window.resetIdleTimer = resetIdleTimer;
            })();
        </script>
        @endif
    </body>
</html>
