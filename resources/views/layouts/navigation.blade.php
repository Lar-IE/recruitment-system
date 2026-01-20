<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                @php($role = Auth::user()->role?->value)
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
                    @elseif ($role === 'employer')
                        <x-nav-link :href="route('employer.dashboard')" :active="request()->routeIs('employer.dashboard')">
                            {{ __('Home') }}
                        </x-nav-link>
                        <x-nav-link :href="route('employer.job-posts.index')" :active="request()->routeIs('employer.job-posts.*')">
                            {{ __('Job Posting') }}
                        </x-nav-link>
                        <x-nav-link :href="route('employer.applicants')" :active="request()->routeIs('employer.applicants')">
                            {{ __('Applicants') }}
                        </x-nav-link>
                        <x-nav-link :href="route('employer.ats')" :active="request()->routeIs('employer.ats')">
                            {{ __('Applicant Tracking') }}
                        </x-nav-link>
                        <x-nav-link :href="route('employer.notifications')" :active="request()->routeIs('employer.notifications')">
                            {{ __('Notifications') }}
                            @if (Auth::user()->unreadNotifications()->count())
                                <span class="ml-1 inline-flex items-center rounded-full bg-red-500 px-2 py-0.5 text-xs font-semibold text-white">
                                    {{ Auth::user()->unreadNotifications()->count() }}
                                </span>
                            @endif
                        </x-nav-link>
                        <x-nav-link :href="route('employer.digital-ids')" :active="request()->routeIs('employer.digital-ids')">
                            {{ __('Digital IDs') }}
                        </x-nav-link>
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
                        <x-nav-link :href="route('jobseeker.notifications')" :active="request()->routeIs('jobseeker.notifications')">
                            {{ __('Notifications') }}
                            @if (Auth::user()->unreadNotifications()->count())
                                <span class="ml-1 inline-flex items-center rounded-full bg-red-500 px-2 py-0.5 text-xs font-semibold text-white">
                                    {{ Auth::user()->unreadNotifications()->count() }}
                                </span>
                            @endif
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
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
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
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
            @elseif ($role === 'employer')
                <x-responsive-nav-link :href="route('employer.dashboard')" :active="request()->routeIs('employer.dashboard')">
                    {{ __('Home') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('employer.job-posts.index')" :active="request()->routeIs('employer.job-posts.*')">
                    {{ __('Job Posting') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('employer.applicants')" :active="request()->routeIs('employer.applicants')">
                    {{ __('Applicants') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('employer.ats')" :active="request()->routeIs('employer.ats')">
                    {{ __('Applicant Tracking') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('employer.notifications')" :active="request()->routeIs('employer.notifications')">
                    {{ __('Notifications') }}
                    @if (Auth::user()->unreadNotifications()->count())
                        <span class="ml-1 inline-flex items-center rounded-full bg-red-500 px-2 py-0.5 text-xs font-semibold text-white">
                            {{ Auth::user()->unreadNotifications()->count() }}
                        </span>
                    @endif
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('employer.digital-ids')" :active="request()->routeIs('employer.digital-ids')">
                    {{ __('Digital IDs') }}
                </x-responsive-nav-link>
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
                <x-responsive-nav-link :href="route('jobseeker.notifications')" :active="request()->routeIs('jobseeker.notifications')">
                    {{ __('Notifications') }}
                    @if (Auth::user()->unreadNotifications()->count())
                        <span class="ml-1 inline-flex items-center rounded-full bg-red-500 px-2 py-0.5 text-xs font-semibold text-white">
                            {{ Auth::user()->unreadNotifications()->count() }}
                        </span>
                    @endif
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
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
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
