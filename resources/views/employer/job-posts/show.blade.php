@php
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $jobPost->title }}
            </h2>
            <a href="{{ route('employer.job-posts.edit', $jobPost) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                {{ __('Edit') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700">
                            {{ Str::of($jobPost->status)->title() }}
                        </span>
                        <span class="text-sm text-gray-500">
                            {{ __('Job Type: :type', ['type' => Str::of($jobPost->job_type)->replace('_', ' ')->title()]) }}
                        </span>
                        <span class="text-sm text-gray-500">
                            {{ __('Location: :location', ['location' => $jobPost->location ?? __('Remote')]) }}
                        </span>
                    </div>

                    <div>
                        <h3 class="font-semibold">{{ __('Description') }}</h3>
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $jobPost->description }}</p>
                    </div>

                    @if ($jobPost->responsibilities)
                        <div>
                            <h3 class="font-semibold">{{ __('Responsibilities') }}</h3>
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $jobPost->responsibilities }}</p>
                        </div>
                    @endif

                    @if ($jobPost->benefits)
                        <div>
                            <h3 class="font-semibold">{{ __('Benefits') }}</h3>
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $jobPost->benefits }}</p>
                        </div>
                    @endif

                    @if ($jobPost->requirements)
                        <div>
                            <h3 class="font-semibold">{{ __('Requirements') }}</h3>
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $jobPost->requirements }}</p>
                        </div>
                    @endif

                    @if ($jobPost->requiredSkills->isNotEmpty())
                        <div>
                            <h3 class="font-semibold">{{ __('Required Skills (for matching)') }}</h3>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($jobPost->requiredSkills as $skill)
                                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700">
                                        {{ $skill->skill_name }}
                                        @if ($skill->weight > 1)
                                            <span class="ml-1 text-indigo-500">(×{{ $skill->weight }})</span>
                                        @endif
                                        @if ($skill->min_proficiency !== null)
                                            <span class="ml-1 text-indigo-500">≥{{ $skill->min_proficiency }}%</span>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="text-sm text-gray-600">
                        @php
                            $salaryType = $jobPost->salary_type ?? 'salary_range';
                        @endphp
                        @if ($salaryType === 'daily_rate' && $jobPost->salary_daily !== null)
                            <p>{{ __('Rate per Day: :amount :currency', ['amount' => number_format($jobPost->salary_daily, 2), 'currency' => $jobPost->currency]) }}</p>
                        @elseif ($salaryType === 'fixed' && $jobPost->salary_monthly !== null)
                            <p>{{ __('Monthly Rate: :amount :currency', ['amount' => number_format($jobPost->salary_monthly, 2), 'currency' => $jobPost->currency]) }}</p>
                        @else
                            <p>{{ __('Salary: :min - :max :currency', ['min' => $jobPost->salary_min !== null ? number_format($jobPost->salary_min, 2) : '-', 'max' => $jobPost->salary_max !== null ? number_format($jobPost->salary_max, 2) : '-', 'currency' => $jobPost->currency]) }}</p>
                        @endif
                        <p>{{ __('Application Deadline: :date', ['date' => $jobPost->application_deadline?->format('M d, Y') ?? __('N/A')]) }}</p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        @if ($jobPost->status !== 'published')
                            <form method="POST" action="{{ route('employer.job-posts.publish', $jobPost) }}">
                                @csrf
                                <x-primary-button>{{ __('Publish') }}</x-primary-button>
                            </form>
                        @endif

                        @if ($jobPost->status !== 'closed')
                            <form method="POST" action="{{ route('employer.job-posts.close', $jobPost) }}">
                                @csrf
                                <x-secondary-button type="submit">{{ __('Close') }}</x-secondary-button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('employer.job-posts.duplicate', $jobPost) }}">
                            @csrf
                            <x-secondary-button type="submit">{{ __('Duplicate') }}</x-secondary-button>
                        </form>

                        <form method="POST" action="{{ route('employer.job-posts.destroy', $jobPost) }}" onsubmit="return confirm('{{ __('Delete this job post?') }}')">
                            @csrf
                            @method('DELETE')
                            <x-danger-button>{{ __('Delete') }}</x-danger-button>
                        </form>
                    </div>
                </div>
            </div>

            @if (!empty($candidateSuggestions))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Top Candidate Matches') }}</h3>
                        <p class="text-sm text-gray-600 mb-4">{{ __('Applicants ranked by skill match. Score = Σ (Job Skill Weight × Applicant Proficiency %).') }}</p>
                        <div class="space-y-4">
                            @foreach ($candidateSuggestions as $rank => $suggestion)
                                @php
                                    $app = $suggestion['application'];
                                    $jobseeker = $app->jobseeker;
                                    $user = $jobseeker->user;
                                @endphp
                                <div class="border rounded-lg p-4 hover:bg-gray-50/50 transition">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <a href="{{ route('employer.applicants.show', $app) }}" class="font-medium text-indigo-600 hover:text-indigo-900">
                                                {{ $jobseeker->full_name ?: $user->name }}
                                            </a>
                                            <p class="text-sm text-gray-500">{{ $user->email ?? '' }}</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-800">
                                                {{ __('Score: :score', ['score' => $suggestion['score']]) }}
                                            </span>
                                            <a href="{{ route('employer.applicants.show', $app) }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                                                {{ __('View profile') }}
                                            </a>
                                        </div>
                                    </div>
                                    @if (!empty($suggestion['matched_skills']))
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            @foreach ($suggestion['matched_skills'] as $ms)
                                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">
                                                    {{ $ms['skill_name'] }}: {{ $ms['proficiency'] }}% (+{{ $ms['contribution'] }})
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if (!empty($suggestedJobseekers))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Suggested Jobseekers to Invite') }}</h3>
                        <p class="text-sm text-gray-600 mb-4">{{ __('Jobseekers who match the required skills (and minimum proficiency) but have not applied yet.') }}</p>
                        <div class="space-y-4">
                            @foreach ($suggestedJobseekers as $suggestion)
                                @php
                                    $jobseeker = $suggestion['jobseeker'];
                                    $user = $jobseeker->user;
                                @endphp
                                <div class="border rounded-lg p-4 hover:bg-gray-50/50 transition">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <a href="{{ route('employer.jobseekers.show', $jobseeker) }}" class="font-medium text-indigo-600 hover:text-indigo-900">
                                                {{ $jobseeker->full_name ?: $user->name }}
                                            </a>
                                            <p class="text-sm text-gray-500">{{ $user->email ?? '' }}</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-800">
                                                {{ __('Score: :score', ['score' => $suggestion['score']]) }}
                                            </span>
                                            <a href="{{ route('employer.jobseekers.show', $jobseeker) }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                                                {{ __('View profile') }}
                                            </a>
                                        </div>
                                    </div>
                                    @if (!empty($suggestion['matched_skills']))
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            @foreach ($suggestion['matched_skills'] as $ms)
                                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">
                                                    {{ $ms['skill_name'] }}: {{ $ms['proficiency'] }}% (+{{ $ms['contribution'] }})
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div>
                <a href="{{ route('employer.applicants') }}?job_post_id={{ $jobPost->id }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                    {{ __('View all applicants') }}
                </a>
                <span class="mx-2 text-gray-300">|</span>
                <a href="{{ route('employer.job-posts.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    {{ __('Back to Job Posts') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
