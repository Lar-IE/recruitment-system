<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('assets/images/sfi_tagline_main.png') }}" alt="{{ config('app.name') }}" class="block h-9 w-auto">
                    </a>
                </div>

                @php
                    $user = Auth::user();
                    $isEmployerOwner = $user instanceof \App\Models\User && $user->role?->value === 'employer';
                    $isEmployerSubUser = $user instanceof \App\Models\EmployerSubUser;
                    $employerSubRole = $isEmployerSubUser ? $user->role?->value : null;
                    $role = $user?->role?->value;
                @endphp
                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if ($role === 'admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.users')" :active="request()->routeIs('admin.users')">
                            {{ __('Users') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.employers')" :active="request()->routeIs('admin.employers')">
                            {{ __('Employers') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.jobseekers')" :active="request()->routeIs('admin.jobseekers')">
                            {{ __('Jobseekers') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.job-posts')" :active="request()->routeIs('admin.job-posts')">
                            {{ __('Job Posts') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.applications')" :active="request()->routeIs('admin.applications')">
                            {{ __('Applications') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.documents')" :active="request()->routeIs('admin.documents')">
                            {{ __('Documents') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.digital-ids')" :active="request()->routeIs('admin.digital-ids')">
                            {{ __('Digital IDs') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.reports')" :active="request()->routeIs('admin.reports')">
                            {{ __('Reports') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.settings')" :active="request()->routeIs('admin.settings')">
                            {{ __('Settings') }}
                        </x-nav-link>
                    @elseif ($isEmployerOwner || $isEmployerSubUser)
                        <x-nav-link :href="route('employer.dashboard')" :active="request()->routeIs('employer.dashboard')">
                            {{ __('Home') }}
                        </x-nav-link>
                        <x-nav-link :href="route('employer.job-posts.index')" :active="request()->routeIs('employer.job-posts.*')">
                            {{ __('Job Posting') }}
                        </x-nav-link>
                        <x-nav-link :href="route('employer.applicants')" :active="request()->routeIs('employer.applicants')">
                            {{ __('Applicants') }}
                        </x-nav-link>
                        <x-nav-link :href="route('employer.jobseekers.index')" :active="request()->routeIs('employer.jobseekers.*')">
                            {{ __('Jobseeker Directory') }}
                        </x-nav-link>
                        <x-nav-link :href="route('employer.digital-ids')" :active="request()->routeIs('employer.digital-ids')">
                            {{ __('Digital IDs') }}
                        </x-nav-link>
                        @if ($isEmployerOwner || $employerSubRole === 'admin')
                            <x-nav-link :href="route('employer.sub-users.index')" :active="request()->routeIs('employer.sub-users.*')">
                                {{ __('Sub-Users') }}
                            </x-nav-link>
                        @endif
                    @else
                        <x-nav-link :href="route('jobseeker.dashboard')" :active="request()->routeIs('jobseeker.dashboard')">
                            {{ __('Home') }}
                        </x-nav-link>
                        <x-nav-link :href="route('jobseeker.jobs')" :active="request()->routeIs('jobseeker.jobs')">
                            {{ __('Jobs') }}
                        </x-nav-link>
                        <x-nav-link :href="route('jobseeker.documents')" :active="request()->routeIs('jobseeker.documents')">
                            {{ __('Docs') }}
                        </x-nav-link>
                        <x-nav-link :href="route('jobseeker.digital-id')" :active="request()->routeIs('jobseeker.digital-id')">
                            {{ __('Digital ID') }}
                        </x-nav-link>
                        <x-nav-link :href="route('jobseeker.history')" :active="request()->routeIs('jobseeker.history')">
                            {{ __('History') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Notifications + Settings -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-3">
                @php
                    $notificationRoute = null;
                    if ($role === 'employer') {
                        $notificationRoute = 'employer.notifications.read';
                    } elseif ($role === 'jobseeker') {
                        $notificationRoute = 'jobseeker.notifications.read';
                    }

                    $profileRoute = $role === 'jobseeker'
                        ? 'jobseeker.profile.show'
                        : 'profile.edit';

                    $unreadCount = 0;
                    $recentNotifications = collect();
                    if ($notificationRoute && \Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                        $unreadCount = Auth::user()->unreadNotifications()->count();
                        $recentNotifications = Auth::user()->notifications()->latest()->take(5)->get();
                    }
                @endphp

                @if ($notificationRoute)
                    <div x-data="{ openNotif: false }" class="relative">
                        <button type="button" class="relative inline-flex items-center justify-center w-9 h-9 rounded-full hover:bg-gray-100" x-on:click="openNotif = !openNotif">
                            <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 01-5.714 0c-1.009-.117-1.8-.9-1.928-1.913a17.716 17.716 0 01-.114-2.082c0-2.197.716-4.356 2.064-6.152A6.002 6.002 0 0112 3c1.6 0 3.125.63 4.236 1.76 1.348 1.796 2.064 3.955 2.064 6.152 0 .7-.038 1.401-.114 2.082-.128 1.013-.92 1.796-1.929 1.913z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17.75a2.25 2.25 0 004.5 0"/>
                            </svg>
                            @if ($unreadCount)
                                <span class="absolute -top-0.5 -right-0.5 inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-red-500 px-1.5 text-[10px] font-semibold text-white">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </button>
                        <div x-show="openNotif" x-cloak class="absolute right-0 mt-2 w-80 rounded-md border bg-white shadow-lg z-50">
                            <div class="p-3 border-b text-sm font-semibold text-gray-700">{{ __('Notifications') }}</div>
                            <div class="max-h-72 overflow-y-auto">
                                @if ($recentNotifications->isEmpty())
                                    <div class="p-4 text-sm text-gray-500">{{ __('No notifications yet.') }}</div>
                                @else
                                    @foreach ($recentNotifications as $notification)
                                        @php($data = $notification->data)
                                        <a href="{{ route($notificationRoute, $notification->id) }}" class="block px-4 py-3 text-sm hover:bg-gray-50 {{ $notification->read_at ? 'text-gray-600' : 'text-gray-800 bg-indigo-50' }}">
                                            @if (($data['type'] ?? '') === 'application_submitted')
                                                <p class="font-semibold">{{ __('New application') }} - {{ $data['job_title'] ?? __('Job') }}</p>
                                                <p class="text-xs text-gray-500">{{ __('Applicant: :name', ['name' => $data['applicant'] ?? __('Applicant')]) }}</p>
                                            @elseif (($data['type'] ?? '') === 'document_updated')
                                                <p class="font-semibold">{{ __('Document updated') }} - {{ strtoupper($data['document_type'] ?? '') }}</p>
                                                <p class="text-xs text-gray-500">{{ __('Applicant: :name', ['name' => $data['jobseeker'] ?? __('Applicant')]) }}</p>
                                            @elseif (($data['type'] ?? '') === 'document_update_requested')
                                                <p class="font-semibold">{{ __('Document update requested') }} - {{ strtoupper($data['document_type'] ?? '') }}</p>
                                                <p class="text-xs text-gray-500">{{ __('Please update your document.') }}</p>
                                            @else
                                                <p class="font-semibold">{{ __('Application Update') }}</p>
                                                <p class="text-xs text-gray-500">{{ $data['job_title'] ?? '' }}</p>
                                            @endif
                                            <p class="text-[10px] text-gray-400 mt-1">{{ $notification->created_at?->format('M d, Y H:i') }}</p>
                                        </a>
                                    @endforeach
                                @endif
                            </div>
                            <div class="p-3 border-t text-right">
                                <a href="{{ route($role === 'employer' ? 'employer.notifications' : 'jobseeker.notifications') }}" class="text-xs text-indigo-600 hover:text-indigo-900">
                                    {{ __('View all') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route($profileRoute)">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('profile.settings')">
                            {{ __('Account Settings') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if ($role === 'admin')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.users')" :active="request()->routeIs('admin.users')">
                    {{ __('Users') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.employers')" :active="request()->routeIs('admin.employers')">
                    {{ __('Employers') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.jobseekers')" :active="request()->routeIs('admin.jobseekers')">
                    {{ __('Jobseekers') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.job-posts')" :active="request()->routeIs('admin.job-posts')">
                    {{ __('Job Posts') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.applications')" :active="request()->routeIs('admin.applications')">
                    {{ __('Applications') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.documents')" :active="request()->routeIs('admin.documents')">
                    {{ __('Documents') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.digital-ids')" :active="request()->routeIs('admin.digital-ids')">
                    {{ __('Digital IDs') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.reports')" :active="request()->routeIs('admin.reports')">
                    {{ __('Reports') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.settings')" :active="request()->routeIs('admin.settings')">
                    {{ __('Settings') }}
                </x-responsive-nav-link>
            @elseif ($isEmployerOwner || $isEmployerSubUser)
                <x-responsive-nav-link :href="route('employer.dashboard')" :active="request()->routeIs('employer.dashboard')">
                    {{ __('Home') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('employer.job-posts.index')" :active="request()->routeIs('employer.job-posts.*')">
                    {{ __('Job Posting') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('employer.applicants')" :active="request()->routeIs('employer.applicants')">
                    {{ __('Applicants') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('employer.jobseekers.index')" :active="request()->routeIs('employer.jobseekers.*')">
                    {{ __('Jobseeker Directory') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('employer.digital-ids')" :active="request()->routeIs('employer.digital-ids')">
                    {{ __('Digital IDs') }}
                </x-responsive-nav-link>
                @if ($isEmployerOwner || $employerSubRole === 'admin')
                    <x-responsive-nav-link :href="route('employer.sub-users.index')" :active="request()->routeIs('employer.sub-users.*')">
                        {{ __('Sub-Users') }}
                    </x-responsive-nav-link>
                @endif
            @else
                <x-responsive-nav-link :href="route('jobseeker.dashboard')" :active="request()->routeIs('jobseeker.dashboard')">
                    {{ __('Home') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('jobseeker.jobs')" :active="request()->routeIs('jobseeker.jobs')">
                    {{ __('Jobs') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('jobseeker.documents')" :active="request()->routeIs('jobseeker.documents')">
                    {{ __('Docs') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('jobseeker.digital-id')" :active="request()->routeIs('jobseeker.digital-id')">
                    {{ __('Digital ID') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('jobseeker.history')" :active="request()->routeIs('jobseeker.history')">
                    {{ __('History') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route($profileRoute)">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.settings')">
                    {{ __('Account Settings') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
