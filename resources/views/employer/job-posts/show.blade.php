@php
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $jobPost->title }}
            </h2>
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
                        @if ($jobPost->status !== 'published')
                            <span class="text-sm text-amber-600">{{ __('Publish this job so jobseekers can see it in their recommendations.') }}</span>
                        @endif
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

                    <div class="flex flex-wrap items-center justify-end gap-3">
                        @if ($jobPost->status === 'published')
                            @php
                                $sharePayload = \App\Support\SocialShare::sharePayload($jobPost);
                                $shareIconPlatforms = \App\Support\SocialShare::shareIcons($jobPost);
                            @endphp
                            <button type="button"
                                    id="shareJobBtn"
                                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                    title="{{ __('Share this job') }}"
                                    aria-label="{{ __('Share this job') }}">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                </svg>
                                {{ __('Share') }}
                            </button>
                        @endif

                        @if ($jobPost->status !== 'published')
                            <form method="POST" action="{{ route('employer.job-posts.publish', $jobPost) }}" class="inline">
                                @csrf
                                <x-primary-button>{{ __('Publish') }}</x-primary-button>
                            </form>
                        @endif

                        <a href="{{ route('employer.job-posts.edit', $jobPost) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            {{ __('Edit') }}
                        </a>

                        @if ($jobPost->status !== 'closed')
                            <form method="POST" action="{{ route('employer.job-posts.close', $jobPost) }}" class="inline">
                                @csrf
                                <x-secondary-button type="submit">{{ __('Close') }}</x-secondary-button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('employer.job-posts.duplicate', $jobPost) }}" class="inline">
                            @csrf
                            <x-secondary-button type="submit">{{ __('Duplicate') }}</x-secondary-button>
                        </form>

                        <form method="POST" action="{{ route('employer.job-posts.destroy', $jobPost) }}" class="inline" onsubmit="return confirm('{{ __('Delete this job post?') }}')">
                            @csrf
                            @method('DELETE')
                            <x-danger-button>{{ __('Delete') }}</x-danger-button>
                        </form>
                    </div>
                    @if ($jobPost->status === 'published')
                        {{-- Share modal: job data and platform URLs from database (sharePayload + shareIcons) --}}
                        <div id="shareModal" class="fixed inset-0 z-50 hidden" aria-modal="true" aria-labelledby="shareModalTitle" role="dialog">
                            <div class="fixed inset-0 bg-black/50" id="shareModalBackdrop"></div>
                            <div class="fixed inset-0 flex items-center justify-center p-4">
                                <div class="bg-white rounded-lg shadow-xl max-w-lg w-full max-h-[90vh] overflow-hidden flex flex-col">
                                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                                        <h3 id="shareModalTitle" class="text-lg font-semibold text-gray-900">{{ __('Share this job') }}</h3>
                                        <button type="button" id="shareModalClose" class="p-1 rounded-md text-gray-400 hover:text-gray-600 text-2xl leading-none" aria-label="{{ __('Close') }}">&times;</button>
                                    </div>
                                    <div class="px-6 py-4 overflow-y-auto flex-1 space-y-3 text-sm">
                                        <p class="font-medium text-gray-900">{{ $sharePayload['title'] }}</p>
                                        <p class="text-gray-600">{{ $sharePayload['company_name'] }}</p>
                                        @if ($sharePayload['description_short'])
                                            <p class="text-gray-700 whitespace-pre-line">{{ $sharePayload['description_short'] }}</p>
                                        @endif
                                        @if ($sharePayload['salary_line'])
                                            <p class="text-gray-600">{{ $sharePayload['salary_line'] }}</p>
                                        @endif
                                        <p class="text-gray-500 break-all"><a href="{{ $sharePayload['public_url'] }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:underline">{{ $sharePayload['public_url'] }}</a></p>
                                    </div>
                                    <div class="px-6 py-4 border-t border-gray-200 space-y-2">
                                        <p class="text-xs text-gray-500">
                                            {{ __('When you click Facebook, job details are copied to your clipboard. Paste them (Ctrl+V or Cmd+V) in the Facebook post, then click Post.') }}
                                        </p>
                                        <div class="flex flex-wrap items-center gap-2">
                                        <button type="button" id="shareModalCopyLink" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50" data-share-url="{{ e($sharePayload['public_url']) }}">
                                            {{ __('Copy link') }}
                                        </button>
                                        @foreach ($shareIconPlatforms as $p)
                                            @if ($p['intent_url'] && $p['key'] === 'facebook')
                                                <button type="button"
                                                        class="js-share-facebook inline-flex items-center justify-center w-9 h-9 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 border border-transparent hover:border-gray-300 transition"
                                                        title="{{ $p['label'] }}"
                                                        aria-label="{{ $p['label'] }}"
                                                        data-share-text="{{ e($sharePayload['share_text']) }}"
                                                        data-intent-url="{{ $p['intent_url'] }}">
                                                    @include('employer.job-posts.partials.share-icon', ['platform' => $p['key']])
                                                </button>
                                            @elseif ($p['intent_url'])
                                                <a href="{{ $p['intent_url'] }}"
                                                   target="_blank"
                                                   rel="noopener noreferrer"
                                                   class="inline-flex items-center justify-center w-9 h-9 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 border border-transparent hover:border-gray-300 transition"
                                                   title="{{ $p['label'] }}"
                                                   aria-label="{{ $p['label'] }}">
                                                    @include('employer.job-posts.partials.share-icon', ['platform' => $p['key']])
                                                </a>
                                            @else
                                                <button type="button"
                                                        class="js-share-copy-modal inline-flex items-center justify-center w-9 h-9 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 border border-transparent hover:border-gray-300 transition"
                                                        title="{{ $p['label'] }}"
                                                        aria-label="{{ $p['label'] }}"
                                                        data-share-text="{{ e($sharePayload['share_text']) }}"
                                                        data-share-url="{{ e($sharePayload['public_url']) }}">
                                                    @include('employer.job-posts.partials.share-icon', ['platform' => $p['key']])
                                                </button>
                                            @endif
                                        @endforeach
                                        </div>
                                        <p id="shareModalFeedback" class="w-full text-sm text-gray-600 hidden" role="status" aria-live="polite"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script>
                            (function () {
                                var shareBtn = document.getElementById('shareJobBtn');
                                var modal = document.getElementById('shareModal');
                                var backdrop = document.getElementById('shareModalBackdrop');
                                var closeBtn = document.getElementById('shareModalClose');
                                var copyLinkBtn = document.getElementById('shareModalCopyLink');
                                var feedbackEl = document.getElementById('shareModalFeedback');

                                function showFeedback(msg) {
                                    if (!feedbackEl) return;
                                    feedbackEl.textContent = msg;
                                    feedbackEl.classList.remove('hidden');
                                    setTimeout(function () { feedbackEl.classList.add('hidden'); }, 3500);
                                }

                                function openModal() {
                                    if (modal) modal.classList.remove('hidden');
                                }

                                function closeModal() {
                                    if (modal) modal.classList.add('hidden');
                                }

                                function copyToClipboard(text) {
                                    if (!navigator.clipboard || !navigator.clipboard.writeText) return Promise.resolve(false);
                                    return navigator.clipboard.writeText(text).then(function () { return true; }).catch(function () { return false; });
                                }

                                if (shareBtn) shareBtn.addEventListener('click', openModal);
                                if (backdrop) backdrop.addEventListener('click', closeModal);
                                if (closeBtn) closeBtn.addEventListener('click', closeModal);
                                if (copyLinkBtn) {
                                    copyLinkBtn.addEventListener('click', function () {
                                        var url = copyLinkBtn.getAttribute('data-share-url') || '';
                                        copyToClipboard(url).then(function (ok) {
                                            showFeedback(ok ? '{{ __('Link copied to clipboard.') }}' : '{{ __('Copy failed. Please copy the link manually.') }}');
                                        });
                                    });
                                }

                                document.addEventListener('click', function (e) {
                                    var target = e.target.closest('.js-share-copy-modal');
                                    if (!target) return;
                                    e.preventDefault();
                                    var text = target.getAttribute('data-share-text') || '';
                                    var url = target.getAttribute('data-share-url') || '';
                                    copyToClipboard(text || url).then(function (ok) {
                                        showFeedback(ok ? '{{ __('Copied. Paste into the app to share.') }}' : '{{ __('Copy failed. Please copy the link manually.') }}');
                                    });
                                });

                                document.addEventListener('click', function (e) {
                                    var target = e.target.closest('.js-share-facebook');
                                    if (!target) return;
                                    e.preventDefault();
                                    var text = target.getAttribute('data-share-text') || '';
                                    var intentUrl = target.getAttribute('data-intent-url') || '';
                                    copyToClipboard(text).then(function (ok) {
                                        showFeedback(ok ? '{{ __('Copied. Paste in the Facebook post, then click Post.') }}' : '{{ __('Copy failed. Please copy the text manually.') }}');
                                        if (intentUrl) window.open(intentUrl, '_blank', 'noopener,noreferrer');
                                    });
                                });
                            })();
                        </script>
                    @endif
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
                        @if ($jobPost->status !== 'published')
                            <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                                {{ __('This job is not published yet. Jobseekers will only see this job in their "Jobs Matching Your Skills" section after you publish it.') }}
                                <a href="{{ route('employer.job-posts.edit', $jobPost) }}" class="font-medium underline hover:no-underline">{{ __('Publish this job') }}</a>
                            </div>
                        @endif
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
