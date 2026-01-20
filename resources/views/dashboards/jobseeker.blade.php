@php
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jobseeker Home') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-lg font-semibold">
                        {{ __('Welcome, :name', ['name' => Auth::user()->name]) }}
                    </p>

                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="border rounded-lg p-4">
                            <p class="text-sm text-gray-500">{{ __('Applied Jobs') }}</p>
                            <p class="text-2xl font-bold">{{ $appliedJobs }}</p>
                        </div>
                        <div class="border rounded-lg p-4">
                            <p class="text-sm text-gray-500">{{ __('Interviews') }}</p>
                            <p class="text-2xl font-bold">{{ $interviews }}</p>
                        </div>
                        <div class="border rounded-lg p-4">
                            <p class="text-sm text-gray-500">{{ __('Hired') }}</p>
                            <p class="text-2xl font-bold">{{ $hired }}</p>
                        </div>
                    </div>

                    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="border rounded-lg p-4">
                            <h3 class="text-lg font-semibold">{{ __('Active Jobs') }}</h3>
                            @if ($activeJobs->isEmpty())
                                <p class="text-sm text-gray-500 mt-2">{{ __('No job posts yet.') }}</p>
                            @else
                                <ul class="mt-3 space-y-2 text-sm text-gray-700">
                                    @foreach ($activeJobs as $job)
                                        <li class="flex justify-between border-b pb-2">
                                            <span>{{ $job->title }}</span>
                                            <span class="text-gray-500">{{ $job->location ?? __('Remote') }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <div class="border rounded-lg p-4">
                            <h3 class="text-lg font-semibold">{{ __('Recent Applications') }}</h3>
                            @if ($recentApplications->isEmpty())
                                <p class="text-sm text-gray-500 mt-2">{{ __('No applications yet.') }}</p>
                            @else
                                <ul class="mt-3 space-y-2 text-sm text-gray-700">
                                    @foreach ($recentApplications as $application)
                                        <li class="flex justify-between border-b pb-2">
                                            <span>{{ $application->jobPost->title ?? __('N/A') }}</span>
                                            <span class="text-gray-500">{{ Str::of($application->current_status)->replace('_', ' ')->title() }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
