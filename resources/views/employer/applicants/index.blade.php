<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Applicants') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="GET" action="{{ route('employer.applicants') }}" class="bg-white p-4 rounded-lg shadow-sm flex flex-wrap gap-4 items-end">
                <div>
                    <x-input-label for="job_post_id" :value="__('Job Post')" />
                    <select id="job_post_id" name="job_post_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($jobPosts as $jobPost)
                            <option value="{{ $jobPost->id }}" {{ ($filters['job_post_id'] ?? '') == $jobPost->id ? 'selected' : '' }}>{{ $jobPost->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" {{ ($filters['status'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <x-primary-button>{{ __('Filter') }}</x-primary-button>
            </form>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($applications->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No applicants found.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left text-gray-500">
                                    <tr>
                                        <th class="py-2 pr-4">{{ __('Applicant') }}</th>
                                        <th class="py-2 pr-4">{{ __('Job Title') }}</th>
                                        <th class="py-2 pr-4">{{ __('Status') }}</th>
                                        <th class="py-2 pr-4">{{ __('Applied At') }}</th>
                                        <th class="py-2 pr-4 text-right">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    @foreach ($applications as $application)
                                        <tr class="border-t">
                                            <td class="py-3 pr-4">
                                                <p class="font-medium">{{ $application->jobseeker->user->name ?? __('N/A') }}</p>
                                                <p class="text-xs text-gray-500">{{ $application->jobseeker->user->email ?? '' }}</p>
                                            </td>
                                            <td class="py-3 pr-4">{{ $application->jobPost->title ?? __('N/A') }}</td>
                                            <td class="py-3 pr-4">{{ $statuses[$application->current_status] ?? ucfirst($application->current_status) }}</td>
                                            <td class="py-3 pr-4">{{ $application->applied_at?->format('M d, Y') }}</td>
                                            <td class="py-3 pr-4 text-right">
                                                <a href="{{ route('employer.applicants.show', $application) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                                    {{ __('View Profile') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $applications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
