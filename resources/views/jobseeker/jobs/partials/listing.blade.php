@php
    use Illuminate\Support\Str;
    $searchTerm = $searchTerm ?? '';
    $employerIdsAppliedRecently = $employerIdsAppliedRecently ?? [];
    $highlightSearch = function ($text) use ($searchTerm) {
        if ($searchTerm === '' || $text === null || $text === '') {
            return e((string) $text);
        }
        $escaped = e((string) $text);
        $pattern = '/'.preg_quote(e($searchTerm), '/').'/iu';
        return preg_replace($pattern, '<mark class="bg-amber-200 dark:bg-amber-400/40 rounded px-0.5">$0</mark>', $escaped);
    };
@endphp
<div class="space-y-4">
    @foreach ($jobPosts as $jobPost)
        @php
            $application = $applications[$jobPost->id] ?? null;
        @endphp
        <div class="border rounded-lg p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-start gap-4">
                @if ($jobPost->employer && $jobPost->employer->company_logo)
                    <img src="{{ asset('storage/' . $jobPost->employer->company_logo) }}"
                         alt="{{ $jobPost->employer->companyProfile?->company_name ?? $jobPost->employer->company_name }}"
                         class="h-12 w-12 object-contain flex-shrink-0 border rounded p-1">
                @endif
                <div>
                    <h3 class="text-lg font-semibold">{!! $highlightSearch($jobPost->title) !!}</h3>
                    <p class="text-sm text-gray-500">
                        {!! $highlightSearch($jobPost->employer->companyProfile?->company_name ?? $jobPost->employer->company_name ?? __('N/A')) !!}
                    </p>
                    <p class="text-sm text-gray-500">
                        {!! $highlightSearch($jobPost->location ?? __('Remote')) !!} · {!! $highlightSearch(Str::of($jobPost->job_type)->replace('_', ' ')->title()) !!}
                    </p>
                    <p class="text-sm font-medium text-gray-700 mt-1">
                        @php
                            $salaryType = $jobPost->salary_type ?? 'salary_range';
                        @endphp
                        @if ($salaryType === 'daily_rate' && $jobPost->salary_daily !== null)
                            {!! $highlightSearch(__('Rate per Day: :amount :currency', ['amount' => number_format($jobPost->salary_daily, 0), 'currency' => $jobPost->currency])) !!}
                        @elseif ($salaryType === 'fixed' && $jobPost->salary_monthly !== null)
                            {!! $highlightSearch(__('Monthly: :amount :currency', ['amount' => number_format($jobPost->salary_monthly, 0), 'currency' => $jobPost->currency])) !!}
                        @elseif (($jobPost->salary_min !== null || $jobPost->salary_max !== null))
                            {!! $highlightSearch(__('Salary: :min - :max :currency', ['min' => $jobPost->salary_min !== null ? number_format($jobPost->salary_min, 0) : '-', 'max' => $jobPost->salary_max !== null ? number_format($jobPost->salary_max, 0) : '-', 'currency' => $jobPost->currency])) !!}
                        @else
                            <span class="text-gray-500">{!! $highlightSearch(__('Salary not specified')) !!}</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @if ($application)
                    <span class="text-xs px-2 py-1 rounded-full bg-indigo-50 text-indigo-700">
                        {!! $highlightSearch(Str::of($application->current_status)->replace('_', ' ')->title()) !!}
                    </span>
                    <a href="{{ route('jobseeker.jobs.show', $jobPost) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-indigo-600 bg-indigo-50 hover:bg-indigo-100">{{ __('View') }}</a>
                @elseif ($jobPost->employer_id && in_array($jobPost->employer_id, $employerIdsAppliedRecently))
                    <span class="text-xs px-2 py-1 rounded-full bg-amber-50 text-amber-700" title="{{ __('You applied to this company within the last 6 months.') }}">
                        {{ __('Applied to company recently') }}
                    </span>
                    <a href="{{ route('jobseeker.jobs.show', $jobPost) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">{{ __('View Details') }}</a>
                @else
                    <form method="POST" action="{{ route('jobseeker.jobs.apply', $jobPost) }}" class="inline-block">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                            {{ __('Apply Now') }}
                        </button>
                    </form>
                    <a href="{{ route('jobseeker.jobs.show', $jobPost) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">{{ __('View Details') }}</a>
                @endif
            </div>
        </div>
    @endforeach
</div>
