@php
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jobs') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="GET" action="{{ route('jobseeker.jobs') }}" class="bg-white p-6 rounded-lg shadow-sm space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="search" :value="__('Search')" />
                        <x-text-input id="search" name="search" class="mt-1 block w-full" :value="$filters['search'] ?? ''" />
                    </div>
                    <div>
                        <x-input-label for="location" :value="__('Location')" />
                        <x-text-input id="location" name="location" class="mt-1 block w-full" :value="$filters['location'] ?? ''" />
                    </div>
                    <div>
                        <x-input-label for="job_type" :value="__('Job Type')" />
                        <select id="job_type" name="job_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">{{ __('All types') }}</option>
                            @foreach (['full_time' => 'Full Time', 'part_time' => 'Part Time', 'contract' => 'Contract', 'temporary' => 'Temporary', 'internship' => 'Internship'] as $key => $label)
                                <option value="{{ $key }}" {{ ($filters['job_type'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <x-primary-button>{{ __('Filter') }}</x-primary-button>
                    <a href="{{ route('jobseeker.jobs') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Reset') }}</a>
                </div>
            </form>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($jobPosts->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No jobs found.') }}</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($jobPosts as $jobPost)
                                @php($application = $applications[$jobPost->id] ?? null)
                                <div class="border rounded-lg p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    <div>
                                        <h3 class="text-lg font-semibold">{{ $jobPost->title }}</h3>
                                        <p class="text-sm text-gray-500">
                                            {{ $jobPost->location ?? __('Remote') }} · {{ Str::of($jobPost->job_type)->replace('_', ' ')->title() }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        @if ($application)
                                            <span class="text-xs px-2 py-1 rounded-full bg-indigo-50 text-indigo-700">
                                                {{ Str::of($application->current_status)->replace('_', ' ')->title() }}
                                            </span>
                                            <a href="{{ route('jobseeker.jobs.show', $jobPost) }}" class="text-sm text-indigo-600 hover:text-indigo-900">{{ __('View') }}</a>
                                        @else
                                            <a href="{{ route('jobseeker.jobs.show', $jobPost) }}" class="text-sm text-indigo-600 hover:text-indigo-900">{{ __('View Details') }}</a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $jobPosts->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
