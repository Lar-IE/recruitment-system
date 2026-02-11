@php
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $profile?->company_name ?? $employer->company_name }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Company Profile -->
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row gap-6">
                        <!-- Logo -->
                        @if($employer->company_logo)
                            <div class="flex-shrink-0">
                                <img src="{{ asset('storage/' . $employer->company_logo) }}" alt="{{ $employer->company_name }}" class="h-24 w-24 sm:h-32 sm:w-32 object-cover rounded-lg border-2 border-gray-200">
                            </div>
                        @endif

                        <!-- Company Info -->
                        <div class="flex-grow space-y-4">
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $profile?->company_name ?? $employer->company_name }}</h1>
                                @if($profile?->industry || $employer->industry)
                                    <p class="text-gray-600 mt-1">{{ $profile?->industry ?? $employer->industry }}</p>
                                @endif
                            </div>

                            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                @if($profile?->company_size)
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <span>{{ $profile->company_size }} {{ __('employees') }}</span>
                                    </div>
                                @endif

                                @if($profile?->year_established)
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ __('Est. :year', ['year' => $profile->year_established]) }}</span>
                                    </div>
                                @endif

                                @if($profile?->website || $employer->website)
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                        </svg>
                                        <a href="{{ $profile?->website ?? $employer->website }}" target="_blank" class="text-indigo-600 hover:text-indigo-800">
                                            {{ __('Visit Website') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($profile?->description)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ __('About Us') }}</h3>
                            <p class="text-gray-700 whitespace-pre-line">{{ $profile->description }}</p>
                        </div>
                    @endif

                    <!-- Contact Information -->
                    @if($profile?->contact_email || $profile?->contact_number || $profile?->address)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ __('Contact Information') }}</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                @if($profile->contact_email)
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <div>
                                            <p class="font-medium text-gray-700">{{ __('Email') }}</p>
                                            <a href="mailto:{{ $profile->contact_email }}" class="text-indigo-600 hover:text-indigo-800">{{ $profile->contact_email }}</a>
                                        </div>
                                    </div>
                                @endif

                                @if($profile->contact_number)
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        <div>
                                            <p class="font-medium text-gray-700">{{ __('Phone') }}</p>
                                            <a href="tel:{{ $profile->contact_number }}" class="text-indigo-600 hover:text-indigo-800">{{ $profile->contact_number }}</a>
                                        </div>
                                    </div>
                                @endif

                                @if($profile->address)
                                    <div class="flex items-start gap-2 sm:col-span-2">
                                        <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <div>
                                            <p class="font-medium text-gray-700">{{ __('Address') }}</p>
                                            <p class="text-gray-600">{{ $profile->address }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Active Job Posts -->
            @if($jobPosts->count() > 0)
                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-6 sm:p-8">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6">{{ __('Open Positions') }}</h3>
                        <div class="space-y-4">
                            @foreach($jobPosts as $jobPost)
                                <div class="border border-gray-200 rounded-lg p-4 sm:p-6 hover:border-indigo-300 transition-colors">
                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                        <div class="flex-grow">
                                            <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ $jobPost->title }}</h4>
                                            <div class="flex flex-wrap gap-3 text-sm text-gray-600 mb-3">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    </svg>
                                                    {{ $jobPost->location ?? __('Remote') }}
                                                </span>
                                                <span>·</span>
                                                <span>{{ Str::of($jobPost->job_type)->replace('_', ' ')->title() }}</span>
                                                @if($jobPost->application_deadline)
                                                    <span>·</span>
                                                    <span>{{ __('Deadline: :date', ['date' => $jobPost->application_deadline->format('M d, Y')]) }}</span>
                                                @endif
                                            </div>
                                            @if($jobPost->description)
                                                <p class="text-sm text-gray-700 line-clamp-2">{{ Str::limit($jobPost->description, 200) }}</p>
                                            @endif
                                        </div>
                                        <div class="flex-shrink-0">
                                            @auth
                                                @if(auth()->user()->role?->value === 'jobseeker')
                                                    <a href="{{ route('jobseeker.jobs.show', $jobPost) }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                                                        {{ __('View Details') }}
                                                    </a>
                                                @else
                                                    <a href="{{ route('employer.job-posts.show', $jobPost) }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500">
                                                        {{ __('View') }}
                                                    </a>
                                                @endif
                                            @else
                                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                                                    {{ __('Login to Apply') }}
                                                </a>
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-6 sm:p-8 text-center text-gray-500">
                        {{ __('No open positions at the moment.') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
