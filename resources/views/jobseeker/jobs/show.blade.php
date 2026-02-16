@php
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $jobPost->title }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->has('job'))
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-700">
                    {{ $errors->first('job') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    @if ($jobPost->employer && $jobPost->employer->company_logo)
                        <div class="flex items-center gap-4 pb-4 border-b">
                            <img src="{{ asset('storage/' . $jobPost->employer->company_logo) }}" 
                                 alt="{{ $jobPost->employer->companyProfile?->company_name ?? $jobPost->employer->company_name }}" 
                                 class="h-16 w-16 object-contain border rounded-lg p-2">
                            <div>
                                <h3 class="font-semibold text-lg">{{ $jobPost->employer->companyProfile?->company_name ?? $jobPost->employer->company_name }}</h3>
                                @if ($jobPost->employer->companyProfile?->industry ?? $jobPost->employer->industry)
                                    <p class="text-sm text-gray-500">{{ $jobPost->employer->companyProfile?->industry ?? $jobPost->employer->industry }}</p>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="pb-4 border-b">
                            <h3 class="font-semibold text-lg">{{ $jobPost->employer->companyProfile?->company_name ?? $jobPost->employer->company_name ?? __('N/A') }}</h3>
                            @if ($jobPost->employer && ($jobPost->employer->companyProfile?->industry ?? $jobPost->employer->industry))
                                <p class="text-sm text-gray-500">{{ $jobPost->employer->companyProfile?->industry ?? $jobPost->employer->industry }}</p>
                            @endif
                        </div>
                    @endif

                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500">
                        <span>{{ $jobPost->location ?? __('Remote') }}</span>
                        <span>·</span>
                        <span>{{ Str::of($jobPost->job_type)->replace('_', ' ')->title() }}</span>
                        @if ($jobPost->application_deadline)
                            <span>·</span>
                            <span>{{ __('Deadline: :date', ['date' => $jobPost->application_deadline->format('M d, Y')]) }}</span>
                        @endif
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
                    </div>

                    <div class="border-t pt-4">
                        @if ($application)
                            <p class="text-sm text-gray-600">
                                {{ __('You already applied. Status: :status', ['status' => Str::of($application->current_status)->replace('_', ' ')->title()]) }}
                            </p>
                        @elseif ($appliedToEmployerWithinSixMonths ?? false)
                            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                                <p class="font-medium">{{ __('You have already applied to this company within the last 6 months.') }}</p>
                                <p class="mt-1 text-amber-700">{{ __('You can apply again after 6 months from your last application to this company.') }}</p>
                            </div>
                        @else
                            <form method="POST" action="{{ route('jobseeker.jobs.apply', $jobPost) }}" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div>
                                    <x-input-label for="cover_letter" :value="__('Cover Letter (optional)')" />
                                    <p class="text-xs text-gray-500 mb-1">{{ __('You can type your cover letter below and/or upload a PDF or Word file.') }}</p>
                                    <textarea id="cover_letter" name="cover_letter" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="{{ __('Type your cover letter here...') }}">{{ old('cover_letter') }}</textarea>
                                    <x-input-error :messages="$errors->get('cover_letter')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="cover_letter_file" :value="__('Or upload cover letter (optional)')" />
                                    <input type="file" id="cover_letter_file" name="cover_letter_file" accept=".pdf,.doc,.docx" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    <p class="mt-1 text-xs text-gray-500">{{ __('Accepted: PDF, .doc, .docx (max 5 MB)') }}</p>
                                    <x-input-error :messages="$errors->get('cover_letter_file')" class="mt-2" />
                                </div>
                                <x-primary-button>{{ __('Apply Now') }}</x-primary-button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div>
                <a href="{{ route('jobseeker.jobs') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    {{ __('Back to Jobs') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
