<x-public-layout>
    <div class="bg-white shadow-sm rounded-lg p-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">{{ __('Digital ID Verification') }}</h1>
                <p class="text-sm text-gray-500">{{ __('Verify employment details and uploaded documents.') }}</p>
            </div>
            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium bg-gray-100 text-gray-700">
                {{ ucfirst($digitalId->status) }}
            </span>
        </div>

        @if ($digitalId->status !== 'active')
            <div class="rounded-md bg-amber-50 p-4 text-sm text-amber-800">
                {{ __('This digital ID is inactive. Please contact the issuing employer for clarification.') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700">
            <div class="space-y-2">
                <h2 class="text-sm font-semibold text-gray-900">{{ __('Applicant Details') }}</h2>
                <p><span class="font-semibold text-gray-800">{{ __('Name:') }}</span> {{ $digitalId->jobseeker->user->name ?? __('N/A') }}</p>
                <p><span class="font-semibold text-gray-800">{{ __('Employer:') }}</span> {{ $digitalId->employer->company_name ?? $digitalId->employer->user->name ?? __('N/A') }}</p>
                <p><span class="font-semibold text-gray-800">{{ __('Job Title:') }}</span> {{ $digitalId->job_title }}</p>
                <p><span class="font-semibold text-gray-800">{{ __('Employee ID:') }}</span> {{ $digitalId->employee_identifier }}</p>
                <p><span class="font-semibold text-gray-800">{{ __('Issue Date:') }}</span> {{ $digitalId->issue_date?->format('M d, Y') ?? __('N/A') }}</p>
            </div>
            <div class="space-y-2">
                <h2 class="text-sm font-semibold text-gray-900">{{ __('Job Details') }}</h2>
                <p><span class="font-semibold text-gray-800">{{ __('Company:') }}</span> {{ $digitalId->company_name }}</p>
                <p><span class="font-semibold text-gray-800">{{ __('Job Post:') }}</span> {{ $digitalId->jobPost->title ?? __('N/A') }}</p>
                <p><span class="font-semibold text-gray-800">{{ __('Status:') }}</span> {{ ucfirst($digitalId->status) }}</p>
            </div>
        </div>

        <div class="border-t pt-5 space-y-3">
            <h2 class="text-sm font-semibold text-gray-900">{{ __('Uploaded Documents') }}</h2>
            <div class="space-y-6 text-sm text-gray-700">
                @foreach (['sss','pagibig','philhealth','psa'] as $type)
                    @php
                        $document = $documents[$type] ?? null;
                    @endphp
                    @if ($document)
                        @php
                            $label = \Illuminate\Support\Str::of($type)->replace('_', ' ')->title();
                            $statusLabel = $document->status === 'pending' ? __('Updated') : ($document->status === 'rejected' ? __('Needs Update') : ucfirst($document->status));
                        @endphp
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-gray-800">
                                <span class="font-semibold">{{ $label }}</span>
                                <span class="text-xs text-gray-500">
                                    {{ __(':status', ['status' => $statusLabel]) }}
                                </span>
                            </div>
                            <a href="{{ $downloadUrls[$type] ?? '#' }}" class="text-xs text-indigo-600 hover:text-indigo-900">
                                {{ __('Download securely') }}
                            </a>
                        </div>
                    @else
                        <div class="flex items-center gap-2 text-gray-500">
                            <span class="font-semibold text-gray-700">{{ \Illuminate\Support\Str::of($type)->replace('_', ' ')->title() }}</span>
                            <span class="text-xs">{{ __('Missing') }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <p class="text-xs text-gray-500">
            {{ __('This verification page is generated from the Digital ID and uses expiring signed links for document downloads.') }}
        </p>
    </div>
</x-public-layout>
