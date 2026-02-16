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
                    @php
                        $searchTerm = $filters['search'] ?? '';
                        $highlightSearch = function ($text) use ($searchTerm) {
                            if ($searchTerm === '' || $text === null || $text === '') {
                                return e((string) $text);
                            }
                            $escaped = e((string) $text);
                            $pattern = '/'.preg_quote(e($searchTerm), '/').'/iu';
                            return preg_replace($pattern, '<mark class="bg-amber-200 dark:bg-amber-400/40 rounded px-0.5">$0</mark>', $escaped);
                        };
                    @endphp
                    @if (!empty($searchTerm))
                        <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                            @if ($jobPosts->isEmpty())
                                {{ __('No results found for') }} "<strong>{{ e($searchTerm) }}</strong>".
                            @else
                                {{ $jobPosts->total() }} {{ $jobPosts->total() === 1 ? __('result') : __('results') }} {{ __('found for') }} "<strong>{{ e($searchTerm) }}</strong>".
                            @endif
                        </div>
                    @endif
                    @if ($jobPosts->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No jobs found.') }}</p>
                    @else
                        @include('jobseeker.jobs.partials.listing', ['jobPosts' => $jobPosts, 'applications' => $applications, 'searchTerm' => $searchTerm, 'employerIdsAppliedRecently' => $employerIdsAppliedRecently ?? []])
                        <div class="mt-4">
                            {{ $jobPosts->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
