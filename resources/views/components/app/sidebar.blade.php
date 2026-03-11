@php
    $user = Auth::user();
    $isEmployerOwner = $user && $user instanceof \App\Models\User && $user->role?->value === 'employer';
    $isEmployerSubUser = $user && $user instanceof \App\Models\EmployerSubUser;
    $employerSubRole = $isEmployerSubUser ? $user->role?->value : null;
    $role = $user?->role?->value ?? null;
    $employerContext = request()->attributes->get('employer')
        ?? (($isEmployerOwner && $user) ? $user->employer : null);
    $canAccessJobseekerDirectory = (bool) ($employerContext?->jobseeker_directory_access ?? false);

    $itemBase = 'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition';
    $itemInactive = 'text-gray-700 hover:bg-gray-50 hover:text-gray-900';
    $itemActive = 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100';
    $iconBase = 'h-5 w-5 shrink-0';
@endphp

<nav class="p-4">
    <div class="flex items-center justify-between mb-4 lg:hidden">
        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Menu') }}</span>
        <button
            type="button"
            class="inline-flex items-center justify-center w-9 h-9 rounded-md hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
            x-on:click="sidebarOpen = false"
            aria-label="{{ __('Close menu') }}"
        >
            <svg class="h-5 w-5 text-gray-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <div class="space-y-1">
        @auth
            @if ($role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="{{ $itemBase }} {{ request()->routeIs('admin.dashboard') ? $itemActive : $itemInactive }}">
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L12 3l9 6.75V21a.75.75 0 01-.75.75H3.75A.75.75 0 013 21V9.75z"/></svg>
                    <span>{{ __('Dashboard') }}</span>
                </a>
                <a href="{{ route('admin.users') }}" class="{{ $itemBase }} {{ request()->routeIs('admin.users') ? $itemActive : $itemInactive }}">
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.002 9.002 0 00-6 0M4.062 18.25a9 9 0 0115.876 0M12 12a4 4 0 100-8 4 4 0 000 8z"/></svg>
                    <span>{{ __('Users') }}</span>
                </a>
                <a href="{{ route('admin.employers') }}" class="{{ $itemBase }} {{ request()->routeIs('admin.employers') ? $itemActive : $itemInactive }}">
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M4.5 21V7.5A1.5 1.5 0 016 6h12a1.5 1.5 0 011.5 1.5V21M9 21v-6h6v6"/></svg>
                    <span>{{ __('Employers') }}</span>
                </a>
                <a href="{{ route('admin.jobseekers') }}" class="{{ $itemBase }} {{ request()->routeIs('admin.jobseekers') ? $itemActive : $itemInactive }}">
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 00-12 0M12 12a4 4 0 100-8 4 4 0 000 8z"/></svg>
                    <span>{{ __('Jobseekers') }}</span>
                </a>
                <a href="{{ route('admin.job-posts') }}" class="{{ $itemBase }} {{ request()->routeIs('admin.job-posts') ? $itemActive : $itemInactive }}">
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7.5 21h9A2.25 2.25 0 0018.75 18.75V5.25A2.25 2.25 0 0016.5 3h-9A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21z"/></svg>
                    <span>{{ __('Job Posts') }}</span>
                </a>
                <a href="{{ route('admin.applications') }}" class="{{ $itemBase }} {{ request()->routeIs('admin.applications') ? $itemActive : $itemInactive }}">
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6.75V5.25A2.25 2.25 0 0014.25 3h-4.5A2.25 2.25 0 007.5 5.25v1.5m9 0H7.5m9 0h1.125A2.25 2.25 0 0120.25 9v10.5A2.25 2.25 0 0118 21.75H6A2.25 2.25 0 013.75 19.5V9A2.25 2.25 0 016 6.75h1.5"/></svg>
                    <span>{{ __('Applications') }}</span>
                </a>
                <a href="{{ route('admin.documents') }}" class="{{ $itemBase }} {{ request()->routeIs('admin.documents') ? $itemActive : $itemInactive }}">
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25V6a2.25 2.25 0 00-2.25-2.25H8.25A2.25 2.25 0 006 6v12a2.25 2.25 0 002.25 2.25h5.25"/></svg>
                    <span>{{ __('Documents') }}</span>
                </a>
                <a href="{{ route('admin.digital-ids') }}" class="{{ $itemBase }} {{ request()->routeIs('admin.digital-ids') ? $itemActive : $itemInactive }}">
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3m-3 3h3m-6 3h6M6.75 6.75h.75m-.75 3h.75m-.75 3h.75M4.5 21h15A2.25 2.25 0 0021.75 18.75V5.25A2.25 2.25 0 0019.5 3h-15A2.25 2.25 0 002.25 5.25v13.5A2.25 2.25 0 004.5 21z"/></svg>
                    <span>{{ __('Digital IDs') }}</span>
                </a>
                <a href="{{ route('admin.reports') }}" class="{{ $itemBase }} {{ request()->routeIs('admin.reports') ? $itemActive : $itemInactive }}">
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7.5 15.75V10.5m4.5 5.25V6.75m4.5 9V12"/></svg>
                    <span>{{ __('Reports') }}</span>
                </a>
                <a href="{{ route('admin.settings') }}" class="{{ $itemBase }} {{ request()->routeIs('admin.settings') ? $itemActive : $itemInactive }}">
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h3m-1.5 15.75a2.25 2.25 0 002.25-2.25V4.5A2.25 2.25 0 0012 2.25a2.25 2.25 0 00-2.25 2.25v15A2.25 2.25 0 0012 21.75z"/></svg>
                    <span>{{ __('Settings') }}</span>
                </a>
                <a href="{{ route('admin.cms.index') }}" class="{{ $itemBase }} {{ request()->routeIs('admin.cms.*') ? $itemActive : $itemInactive }}">
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>{{ __('CMS') }}</span>
                </a>

            @elseif ($isEmployerOwner || $isEmployerSubUser)
                <a href="{{ route('employer.dashboard') }}" class="{{ $itemBase }} {{ request()->routeIs('employer.dashboard') ? $itemActive : $itemInactive }}">
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L12 3l9 6.75V21a.75.75 0 01-.75.75H3.75A.75.75 0 013 21V9.75z"/></svg>
                    <span>{{ __('Dashboard') }}</span>
                </a>
                <a href="{{ route('employer.job-posts.index') }}" class="{{ $itemBase }} {{ request()->routeIs('employer.job-posts.*') ? $itemActive : $itemInactive }}">
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7.5 21h9A2.25 2.25 0 0018.75 18.75V5.25A2.25 2.25 0 0016.5 3h-9A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21z"/></svg>
                    <span>{{ __('Job Listing') }}</span>
                </a>
                <a href="{{ route('employer.virtual-events.index') }}" class="{{ $itemBase }} {{ request()->routeIs('employer.virtual-events.*') ? $itemActive : $itemInactive }}">
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4.5 11.25h15m-14.25 9h13.5A2.25 2.25 0 0021 18V7.5A2.25 2.25 0 0018.75 5.25H5.25A2.25 2.25 0 003 7.5V18a2.25 2.25 0 002.25 2.25z"/></svg>
                    <span>{{ __('Virtual Talent Connect') }}</span>
                </a>
                <a href="{{ route('employer.applicants') }}" class="{{ $itemBase }} {{ request()->routeIs('employer.applicants*') ? $itemActive : $itemInactive }}">
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.002 9.002 0 00-6 0M4.062 18.25a9 9 0 0115.876 0M12 12a4 4 0 100-8 4 4 0 000 8z"/></svg>
                    <span>{{ __('Applicant Tracking') }}</span>
                </a>
                <a href="{{ route('employer.ats') }}" class="{{ $itemBase }} {{ request()->routeIs('employer.ats') ? $itemActive : $itemInactive }}">
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7.5 15.75V10.5m4.5 5.25V6.75m4.5 9V12"/></svg>
                    <span>{{ __('Talent Pool') }}</span>
                </a>
                @if ($canAccessJobseekerDirectory)
                    <a href="{{ route('employer.jobseekers.index') }}" class="{{ $itemBase }} {{ request()->routeIs('employer.jobseekers.*') ? $itemActive : $itemInactive }}">
                        <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 00-12 0M12 12a4 4 0 100-8 4 4 0 000 8z"/></svg>
                        <span>{{ __('Jobseeker Directory') }}</span>
                    </a>
                @endif
                <a href="{{ route('employer.digital-ids') }}" class="{{ $itemBase }} {{ request()->routeIs('employer.digital-ids') ? $itemActive : $itemInactive }}">
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 21h15A2.25 2.25 0 0021.75 18.75V5.25A2.25 2.25 0 0019.5 3h-15A2.25 2.25 0 002.25 5.25v13.5A2.25 2.25 0 004.5 21z"/></svg>
                    <span>{{ __('Digital IDs') }}</span>
                </a>
                @if ($isEmployerOwner || $employerSubRole === 'admin')
                    <a href="{{ route('employer.sub-users.index') }}" class="{{ $itemBase }} {{ request()->routeIs('employer.sub-users.*') ? $itemActive : $itemInactive }}">
                        <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 00-12 0M15 7.5a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>{{ __('Sub-Users') }}</span>
                    </a>
                @endif

            @else
                <a href="{{ route('jobseeker.dashboard') }}" class="{{ $itemBase }} {{ request()->routeIs('jobseeker.dashboard') ? $itemActive : $itemInactive }}">
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L12 3l9 6.75V21a.75.75 0 01-.75.75H3.75A.75.75 0 013 21V9.75z"/></svg>
                    <span>{{ __('Home') }}</span>
                </a>
                <a href="{{ route('jobseeker.jobs') }}" class="{{ $itemBase }} {{ request()->routeIs('jobseeker.jobs') ? $itemActive : $itemInactive }}">
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7.5 21h9A2.25 2.25 0 0018.75 18.75V5.25A2.25 2.25 0 0016.5 3h-9A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21z"/></svg>
                    <span>{{ __('Jobs') }}</span>
                </a>
                <a href="{{ route('jobseeker.virtual-events.index') }}" class="{{ $itemBase }} {{ request()->routeIs('jobseeker.virtual-events.*') ? $itemActive : $itemInactive }}">
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4.5 11.25h15m-14.25 9h13.5A2.25 2.25 0 0021 18V7.5A2.25 2.25 0 0018.75 5.25H5.25A2.25 2.25 0 003 7.5V18a2.25 2.25 0 002.25 2.25z"/></svg>
                    <span>{{ __('Virtual Talent Connect') }}</span>
                </a>
                <a href="{{ route('jobseeker.documents') }}" class="{{ $itemBase }} {{ request()->routeIs('jobseeker.documents') ? $itemActive : $itemInactive }}">
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25V6a2.25 2.25 0 00-2.25-2.25H8.25A2.25 2.25 0 006 6v12a2.25 2.25 0 002.25 2.25h5.25"/></svg>
                    <span>{{ __('Docs') }}</span>
                </a>
                <a href="{{ route('jobseeker.digital-id') }}" class="{{ $itemBase }} {{ request()->routeIs('jobseeker.digital-id') ? $itemActive : $itemInactive }}">
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 21h15A2.25 2.25 0 0021.75 18.75V5.25A2.25 2.25 0 0019.5 3h-15A2.25 2.25 0 002.25 5.25v13.5A2.25 2.25 0 004.5 21z"/></svg>
                    <span>{{ __('Digital ID') }}</span>
                </a>
                <a href="{{ route('jobseeker.history') }}" class="{{ $itemBase }} {{ request()->routeIs('jobseeker.history') ? $itemActive : $itemInactive }}">
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                    <span>{{ __('History') }}</span>
                </a>
            @endif
        @endauth
    </div>
</nav>

