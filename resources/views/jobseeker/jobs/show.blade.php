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

                    @if ($jobPost->requirements)
                        <div>
                            <h3 class="font-semibold">{{ __('Requirements') }}</h3>
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $jobPost->requirements }}</p>
                        </div>
                    @endif

                    <div class="text-sm text-gray-600">
                        <p>{{ __('Salary: :min - :max :currency', ['min' => $jobPost->salary_min ?? '-', 'max' => $jobPost->salary_max ?? '-', 'currency' => $jobPost->currency]) }}</p>
                    </div>

                    <div class="border-t pt-4">
                        @if ($application)
                            <p class="text-sm text-gray-600">
                                {{ __('You already applied. Status: :status', ['status' => Str::of($application->current_status)->replace('_', ' ')->title()]) }}
                            </p>
                        @else
                            <form method="POST" action="{{ route('jobseeker.jobs.apply', $jobPost) }}" class="space-y-4">
                                @csrf
                                <div>
                                    <x-input-label for="cover_letter" :value="__('Cover Letter (optional)')" />
                                    <textarea id="cover_letter" name="cover_letter" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('cover_letter') }}</textarea>
                                    <x-input-error :messages="$errors->get('cover_letter')" class="mt-2" />
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
