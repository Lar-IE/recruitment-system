@php
    use Illuminate\Support\Str;
    $companyName = $jobPost->employer->companyProfile?->company_name ?? $jobPost->employer->company_name ?? __('Company');
    $publicUrl = \App\Support\SocialShare::publicJobUrl($jobPost);
    $ogDescription = \App\Support\SocialShare::ogDescription($jobPost);
    $pageTitle = $jobPost->title . ' – ' . $companyName;
    $shortDescription = trim((string) ($jobPost->description ?? ''));
    $shortDescription = $shortDescription !== '' ? Str::limit(preg_replace('/\s+/', ' ', $shortDescription), 300) : '';
@endphp
<x-app-layout>
    <x-slot name="title">{{ $pageTitle }}</x-slot>
    <x-slot name="meta">
        @php $canonicalUrl = url($publicUrl); @endphp
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ $canonicalUrl }}">
        <meta property="og:title" content="{{ $pageTitle }}">
        <meta property="og:description" content="{{ $ogDescription }}">
        <meta property="og:site_name" content="{{ config('app.name') }}">
        <link rel="canonical" href="{{ $canonicalUrl }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $pageTitle }}">
        <meta name="twitter:description" content="{{ $ogDescription }}">
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $jobPost->title }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    {{-- Public preview: Job Title, Company Name, Short Description, Basic Info only --}}
                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500">
                        <span class="font-medium text-gray-700">
                            {{ $jobPost->employer->companyProfile?->company_name ?? $jobPost->employer->company_name ?? __('N/A') }}
                        </span>
                        <span>·</span>
                        <span>{{ $jobPost->location ?? __('Remote') }}</span>
                        <span>·</span>
                        <span>{{ Str::of($jobPost->job_type)->replace('_', ' ')->title() }}</span>
                        @if ($jobPost->application_deadline)
                            <span>·</span>
                            <span>{{ __('Deadline: :date', ['date' => $jobPost->application_deadline->format('M d, Y')]) }}</span>
                        @endif
                    </div>

                    @if ($shortDescription !== '')
                        <div>
                            <h3 class="font-semibold">{{ __('Description') }}</h3>
                            <p class="text-sm text-gray-700">{{ $shortDescription }}</p>
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
                        @elseif ($jobPost->salary_min !== null || $jobPost->salary_max !== null)
                            <p>{{ __('Salary: :min - :max :currency', ['min' => $jobPost->salary_min !== null ? number_format($jobPost->salary_min, 2) : '-', 'max' => $jobPost->salary_max !== null ? number_format($jobPost->salary_max, 2) : '-', 'currency' => $jobPost->currency]) }}</p>
                        @endif
                    </div>

                    @guest
                        <p class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-md px-3 py-2">
                            {{ __('Please login or create an account to view full details and apply.') }}
                        </p>
                    @endguest

                    <div class="flex flex-wrap gap-3 pt-2">
                        <a href="{{ route('jobseeker.jobs.show', $jobPost) }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                            {{ __('View / Apply') }}
                        </a>
                        <a href="{{ route('company.show', $jobPost->employer_id) }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            {{ __('View Company') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

