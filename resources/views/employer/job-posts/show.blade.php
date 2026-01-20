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

                    @if ($jobPost->requirements)
                        <div>
                            <h3 class="font-semibold">{{ __('Requirements') }}</h3>
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $jobPost->requirements }}</p>
                        </div>
                    @endif

                    <div class="text-sm text-gray-600">
                        <p>{{ __('Salary: :min - :max :currency', ['min' => $jobPost->salary_min ?? '-', 'max' => $jobPost->salary_max ?? '-', 'currency' => $jobPost->currency]) }}</p>
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
                                <x-secondary-button>{{ __('Close') }}</x-secondary-button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('employer.job-posts.duplicate', $jobPost) }}">
                            @csrf
                            <x-secondary-button>{{ __('Duplicate') }}</x-secondary-button>
                        </form>

                        <form method="POST" action="{{ route('employer.job-posts.destroy', $jobPost) }}" onsubmit="return confirm('{{ __('Delete this job post?') }}')">
                            @csrf
                            @method('DELETE')
                            <x-danger-button>{{ __('Delete') }}</x-danger-button>
                        </form>
                    </div>
                </div>
            </div>

            <div>
                <a href="{{ route('employer.job-posts.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    {{ __('Back to Job Posts') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
