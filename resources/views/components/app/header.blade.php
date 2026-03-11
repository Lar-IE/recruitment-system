@php
    use App\Helpers\CmsHelper;
    $user = Auth::user();
    $isEmployerOwner = $user && $user instanceof \App\Models\User && $user->role?->value === 'employer';
    $isEmployerSubUser = $user && $user instanceof \App\Models\EmployerSubUser;
    $role = $user?->role?->value ?? null;
    $profileRoute = null;
    if ($user) {
        if ($role === 'jobseeker') {
            $profileRoute = 'jobseeker.profile.show';
        } elseif ($isEmployerOwner || $isEmployerSubUser) {
            $profileRoute = 'employer.company-settings';
        } else {
            $profileRoute = 'profile.edit';
        }
    }

    $notificationRoute = null;
    if ($role === 'employer') {
        $notificationRoute = 'employer.notifications.read';
    } elseif ($role === 'jobseeker') {
        $notificationRoute = 'jobseeker.notifications.read';
    }

    $unreadCount = 0;
    $recentNotifications = collect();
    if ($notificationRoute && \Illuminate\Support\Facades\Schema::hasTable('notifications') && Auth::user()) {
        $unreadCount = Auth::user()->unreadNotifications()->count();
        $recentNotifications = Auth::user()->notifications()->latest()->take(5)->get();
    }
@endphp

<div class="h-16 px-4 sm:px-6 lg:px-8">
    <div class="h-full flex items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <button
                type="button"
                class="lg:hidden inline-flex items-center justify-center w-10 h-10 rounded-md hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                x-on:click="sidebarOpen = true"
                aria-label="{{ __('Open menu') }}"
            >
                <svg class="h-6 w-6 text-gray-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/>
                </svg>
            </button>

            <button
                type="button"
                class="hidden lg:inline-flex items-center justify-center w-9 h-9 rounded-md hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                x-on:click="sidebarCollapsed = !sidebarCollapsed"
                x-bind:aria-label="sidebarCollapsed ? 'Show navigation' : 'Hide navigation'"
            >
                <svg class="h-5 w-5 text-gray-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10"/>
                </svg>
            </button>

            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0">
                @if(CmsHelper::logo())
                    <img src="{{ CmsHelper::logo() }}" alt="{{ CmsHelper::siteName() }}" class="h-9 w-auto">
                @else
                    <span class="text-sm font-semibold text-gray-900 truncate">{{ CmsHelper::siteName() }}</span>
                @endif
            </a>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            @auth
                @if ($notificationRoute)
                    <div x-data="{ openNotif: false }" class="relative" x-on:click.outside="openNotif = false">
                        <button
                            type="button"
                            class="relative inline-flex items-center justify-center w-10 h-10 rounded-full hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                            x-on:click="openNotif = !openNotif"
                            aria-label="{{ __('Notifications') }}"
                        >
                            <svg class="h-5 w-5 text-gray-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 01-5.714 0c-1.009-.117-1.8-.9-1.928-1.913a17.716 17.716 0 01-.114-2.082c0-2.197.716-4.356 2.064-6.152A6.002 6.002 0 0112 3c1.6 0 3.125.63 4.236 1.76 1.348 1.796 2.064 3.955 2.064 6.152 0 .7-.038 1.401-.114 2.082-.128 1.013-.92 1.796-1.929 1.913z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17.75a2.25 2.25 0 004.5 0"/>
                            </svg>
                            @if ($unreadCount)
                                <span class="absolute -top-0.5 -right-0.5 inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-red-500 px-1.5 text-[10px] font-semibold text-white">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </button>

                        <div x-show="openNotif" x-cloak class="absolute right-0 mt-2 w-80 rounded-xl border bg-white shadow-lg z-50 overflow-hidden">
                            <div class="p-3 border-b text-sm font-semibold text-gray-800">{{ __('Notifications') }}</div>
                            <div class="max-h-80 overflow-y-auto">
                                @if ($recentNotifications->isEmpty())
                                    <div class="p-4 text-sm text-gray-500">{{ __('No notifications yet.') }}</div>
                                @else
                                    @foreach ($recentNotifications as $notification)
                                        @php
                                            $data = is_array($notification->data) ? $notification->data : (is_string($notification->data) ? json_decode($notification->data, true) : []);
                                            $notificationType = $data['type'] ?? '';
                                        @endphp
                                        <a href="{{ route($notificationRoute, $notification->id) }}" class="block px-4 py-3 text-sm hover:bg-gray-50 {{ $notification->read_at ? 'text-gray-600' : 'text-gray-900 bg-indigo-50/40' }}">
                                            @if ($notificationType === 'application_submitted')
                                                <p class="font-semibold">{{ __('New application') }} - {{ $data['job_title'] ?? __('Job') }}</p>
                                                <p class="text-xs text-gray-500">{{ __('Applicant: :name', ['name' => $data['applicant'] ?? __('Applicant')]) }}</p>
                                            @elseif ($notificationType === 'document_updated')
                                                <p class="font-semibold">{{ __('Document updated') }} - {{ strtoupper($data['document_type'] ?? '') }}</p>
                                                <p class="text-xs text-gray-500">{{ __('Applicant: :name', ['name' => $data['jobseeker'] ?? __('Applicant')]) }}</p>
                                            @elseif ($notificationType === 'document_update_requested')
                                                <p class="font-semibold">{{ __('Document update requested') }} - {{ strtoupper($data['document_type'] ?? '') }}</p>
                                                <p class="text-xs text-gray-500">{{ __('Please update your document.') }}</p>
                                            @elseif ($notificationType === 'virtual_event_created')
                                                <p class="font-semibold">{{ __('New Virtual Event') }} - {{ $data['title'] ?? __('Event') }}</p>
                                                <p class="text-xs text-gray-500">{{ __('Date: :date', ['date' => $data['date'] ?? '']) }} | {{ __('Platform: :platform', ['platform' => $data['platform'] ?? '']) }}</p>
                                            @else
                                                <p class="font-semibold">{{ __('Notification') }}</p>
                                                <p class="text-xs text-gray-500">{{ $data['job_title'] ?? ($data['title'] ?? '') }}</p>
                                            @endif
                                            <p class="text-[10px] text-gray-400 mt-1">{{ $notification->created_at?->format('M d, Y H:i') }}</p>
                                        </a>
                                    @endforeach
                                @endif
                            </div>
                            <div class="p-3 border-t text-right">
                                <a href="{{ route($role === 'employer' ? 'employer.notifications' : 'jobseeker.notifications') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-900">
                                    {{ __('View all') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-2 py-2 rounded-md hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                            <span class="hidden sm:block text-sm font-medium text-gray-700 max-w-[18ch] truncate">{{ Auth::user()->name }}</span>
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-indigo-50 text-indigo-700 text-sm font-semibold">
                                {{ strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route($profileRoute)">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('profile.settings')">
                            {{ __('Account Settings') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            @else
                <a href="{{ route('login') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50">
                    {{ __('Log in') }}
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-flex items-center px-3 py-2 text-sm font-semibold rounded-md text-white bg-indigo-600 hover:bg-indigo-500">
                        {{ __('Register') }}
                    </a>
                @endif
            @endauth
        </div>
    </div>
</div>

