@php
    use Illuminate\Support\Str;
    $recommendedJobs = $recommendedJobs ?? [];
    $applicationsByJob = $applicationsByJob ?? collect();
    $employerIdsAppliedRecently = $employerIdsAppliedRecently ?? [];
    $filters = $filters ?? [];
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jobseeker Home') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-700">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-lg font-semibold">
                        {{ __('Welcome, :name', ['name' => Auth::user()->name]) }}
                    </p>

                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="border rounded-lg p-4">
                            <p class="text-sm text-gray-500">{{ __('Applied Jobs') }}</p>
                            <p class="text-2xl font-bold">{{ $appliedJobs }}</p>
                        </div>
                        <div class="border rounded-lg p-4">
                            <p class="text-sm text-gray-500">{{ __('Interviews') }}</p>
                            <p class="text-2xl font-bold">{{ $interviews }}</p>
                        </div>
                        <div class="border rounded-lg p-4">
                            <p class="text-sm text-gray-500">{{ __('Hired') }}</p>
                            <p class="text-2xl font-bold">{{ $hired }}</p>
                        </div>
                    </div>

                    {{-- Filters for recommended jobs --}}
                    <form method="GET" action="{{ route('jobseeker.dashboard') }}" class="mt-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ __('Filter Recommended Jobs') }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                            <div>
                                <x-input-label for="location" :value="__('Location')" class="text-xs" />
                                <x-text-input id="location" name="location" class="mt-1 block w-full text-sm" :value="$filters['location'] ?? ''" placeholder="{{ __('e.g., Manila') }}" />
                            </div>
                            <div>
                                <x-input-label for="job_type" :value="__('Job Type')" class="text-xs" />
                                <select id="job_type" name="job_type" class="mt-1 block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">{{ __('All types') }}</option>
                                    @foreach (['full_time' => 'Full Time', 'part_time' => 'Part Time', 'contract' => 'Contract', 'temporary' => 'Temporary', 'internship' => 'Internship'] as $key => $label)
                                        <option value="{{ $key }}" {{ ($filters['job_type'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="min_match" :value="__('Min. Match %')" class="text-xs" />
                                <select id="min_match" name="min_match" class="mt-1 block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @php $selectedMatch = (int)($filters['min_match'] ?? 0); @endphp
                                    <option value="0" {{ $selectedMatch === 0 ? 'selected' : '' }}>{{ __('All matches') }}</option>
                                    @foreach ([20, 40, 60, 80] as $pct)
                                        <option value="{{ $pct }}" {{ $selectedMatch === $pct ? 'selected' : '' }}>≥{{ $pct }}%</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-end gap-2">
                                <x-primary-button class="text-sm">{{ __('Apply') }}</x-primary-button>
                                <a href="{{ route('jobseeker.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 pb-2">{{ __('Reset') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Recommended Jobs (skill-matched) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold">{{ __('Jobs Matching Your Skills') }}</h3>

                    @if (empty($recommendedJobs))
                        <div class="mt-6 p-6 rounded-lg border-2 border-dashed border-gray-200 bg-gray-50/50 text-center">
                            <p class="text-gray-600">
                                @if (!$jobseeker || ($jobseeker->skillsList ?? collect())->isEmpty())
                                    {{ __('Add skills to your profile to see job recommendations tailored to you.') }}
                                @else
                                    {{ __('No jobs match your skills at the selected threshold. Try lowering the minimum match % or updating your profile skills.') }}
                                @endif
                            </p>
                            <a href="{{ route('jobseeker.profile.edit') }}" class="inline-flex items-center mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                {{ __('Edit profile skills') }}
                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    @else
                        <div class="mt-6 space-y-4">
                            @foreach ($recommendedJobs as $item)
                                @php
                                    $jobPost = $item['job_post'];
                                    $matchPct = $item['match_percentage'];
                                    $matchedSkills = $item['matched_skills'] ?? [];
                                    $application = $applicationsByJob[$jobPost->id] ?? null;
                                    $appliedToEmployerRecently = $jobPost->employer_id && in_array($jobPost->employer_id, $employerIdsAppliedRecently);
                                @endphp
                                <div class="border rounded-lg p-4 hover:bg-gray-50/50 transition">
                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <a href="{{ route('jobseeker.jobs.show', $jobPost) }}" class="text-lg font-semibold text-indigo-600 hover:text-indigo-900">
                                                    {{ $jobPost->title }}
                                                </a>
                                                <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold {{ $matchPct >= 90 ? 'bg-emerald-100 text-emerald-800' : ($matchPct >= 80 ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800') }}" title="{{ __('Your skills match :pct% of this job\'s requirements.', ['pct' => $matchPct]) }}">
                                                    {{ $matchPct }}% {{ __('match') }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-500 mt-0.5">
                                                {{ $jobPost->employer->companyProfile?->company_name ?? $jobPost->employer->company_name ?? __('N/A') }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {{ $jobPost->location ?? __('Remote') }} · {{ Str::of($jobPost->job_type)->replace('_', ' ')->title() }}
                                            </p>

                                            {{-- Matched skills with proficiency --}}
                                            @if (!empty($matchedSkills))
                                                <div class="mt-3 flex flex-wrap gap-2">
                                                    @foreach ($matchedSkills as $ms)
                                                        <span class="inline-flex items-center rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-medium text-emerald-800" title="{{ __('Your proficiency: :pct%', ['pct' => $ms['proficiency']]) }}">
                                                            {{ $ms['skill_name'] }}: {{ $ms['proficiency'] }}%
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif

                                            {{-- Job's required skills (with min proficiency) --}}
                                            @if ($jobPost->requiredSkills->isNotEmpty())
                                                <p class="mt-2 text-xs text-gray-500">{{ __('Required:') }}
                                                    @foreach ($jobPost->requiredSkills as $rs)
                                                        <span class="inline text-gray-600">{{ $rs->skill_name }}{{ $rs->min_proficiency ? ' ≥'.$rs->min_proficiency.'%' : '' }}</span>@if (!$loop->last), @endif
                                                    @endforeach
                                                </p>
                                            @endif
                                        </div>

                                        <div class="flex flex-wrap items-center gap-2 sm:flex-shrink-0">
                                            @if ($application)
                                                <span class="text-xs px-2 py-1 rounded-full bg-indigo-50 text-indigo-700">
                                                    {{ Str::of($application->current_status)->replace('_', ' ')->title() }}
                                                </span>
                                                <a href="{{ route('jobseeker.jobs.show', $jobPost) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-indigo-600 bg-indigo-50 hover:bg-indigo-100">
                                                    {{ __('View') }}
                                                </a>
                                            @elseif ($appliedToEmployerRecently)
                                                <span class="text-xs px-2 py-1 rounded-full bg-amber-50 text-amber-700" title="{{ __('You applied to this company within the last 6 months.') }}">
                                                    {{ __('Applied to company recently') }}
                                                </span>
                                                <a href="{{ route('jobseeker.jobs.show', $jobPost) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                                    {{ __('View Details') }}
                                                </a>
                                            @else
                                                <a href="{{ route('jobseeker.jobs.show', $jobPost) }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                                    {{ __('Apply Now') }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Match score progress bar --}}
                                    <div class="mt-3">
                                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full transition-all {{ $matchPct >= 90 ? 'bg-emerald-500' : ($matchPct >= 80 ? 'bg-indigo-500' : 'bg-amber-500') }}" style="width: {{ min(100, $matchPct) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Recent Applications --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold">{{ __('Recent Applications') }}</h3>
                    @if (($recentApplications ?? collect())->isEmpty())
                        <p class="text-sm text-gray-500 mt-2">{{ __('No applications yet.') }}</p>
                    @else
                        <ul class="mt-3 space-y-2 text-sm text-gray-700">
                            @foreach ($recentApplications as $application)
                                <li class="flex justify-between items-center border-b pb-2">
                                    <a href="{{ route('jobseeker.history.show', $application) }}" class="font-medium text-indigo-600 hover:text-indigo-900">
                                        {{ $application->jobPost->title ?? __('N/A') }}
                                    </a>
                                    <span class="text-gray-500">{{ Str::of($application->current_status)->replace('_', ' ')->title() }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('jobseeker.history') }}" class="inline-block mt-3 text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                            {{ __('View all application history') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
