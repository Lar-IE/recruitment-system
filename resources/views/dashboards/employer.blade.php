@php
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Employer Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="border rounded-lg p-4 bg-white">
                    <p class="text-sm text-gray-500">{{ __('Active Jobs') }}</p>
                    <p class="text-2xl font-bold">{{ $activeJobs }}</p>
                </div>
                <div class="border rounded-lg p-4 bg-white">
                    <p class="text-sm text-gray-500">{{ __('Total Applicants') }}</p>
                    <p class="text-2xl font-bold">{{ $totalApplicants }}</p>
                </div>
                <div class="border rounded-lg p-4 bg-white">
                    <p class="text-sm text-gray-500">{{ __('Hired') }}</p>
                    <p class="text-2xl font-bold">{{ $hired }}</p>
                </div>
                <div class="border rounded-lg p-4 bg-white">
                    <p class="text-sm text-gray-500">{{ __('Pending') }}</p>
                    <p class="text-2xl font-bold">{{ $pending }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold">{{ __('Recent Applications') }}</h3>
                    @if ($recentApplications->isEmpty())
                        <p class="text-sm text-gray-500 mt-2">{{ __('No applications yet.') }}</p>
                    @else
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left text-gray-500">
                                    <tr>
                                        <th class="py-2 pr-4">{{ __('Applicant') }}</th>
                                        <th class="py-2 pr-4">{{ __('Job Title') }}</th>
                                        <th class="py-2 pr-4">{{ __('Status') }}</th>
                                        <th class="py-2 pr-4">{{ __('Applied At') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    @foreach ($recentApplications as $application)
                                        <tr class="border-t">
                                            <td class="py-2 pr-4">{{ $application->jobseeker->user->name ?? __('N/A') }}</td>
                                            <td class="py-2 pr-4">{{ $application->jobPost->title ?? __('N/A') }}</td>
                                            <td class="py-2 pr-4">{{ Str::of($application->current_status)->replace('_', ' ')->title() }}</td>
                                            <td class="py-2 pr-4">{{ $application->applied_at?->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
