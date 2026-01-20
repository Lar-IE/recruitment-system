<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="border rounded-lg p-4 bg-white">
                    <p class="text-sm text-gray-500">{{ __('Total Users') }}</p>
                    <p class="text-2xl font-bold">{{ $totalUsers }}</p>
                </div>
                <div class="border rounded-lg p-4 bg-white">
                    <p class="text-sm text-gray-500">{{ __('Employers') }}</p>
                    <p class="text-2xl font-bold">{{ $totalEmployers }}</p>
                </div>
                <div class="border rounded-lg p-4 bg-white">
                    <p class="text-sm text-gray-500">{{ __('Jobseekers') }}</p>
                    <p class="text-2xl font-bold">{{ $totalJobseekers }}</p>
                </div>
                <div class="border rounded-lg p-4 bg-white">
                    <p class="text-sm text-gray-500">{{ __('Job Posts') }}</p>
                    <p class="text-2xl font-bold">{{ $totalJobPosts }}</p>
                </div>
                <div class="border rounded-lg p-4 bg-white">
                    <p class="text-sm text-gray-500">{{ __('Applications') }}</p>
                    <p class="text-2xl font-bold">{{ $totalApplications }}</p>
                </div>
                <div class="border rounded-lg p-4 bg-white">
                    <p class="text-sm text-gray-500">{{ __('Hired') }}</p>
                    <p class="text-2xl font-bold">{{ $totalHired }}</p>
                </div>
                <div class="border rounded-lg p-4 bg-white">
                    <p class="text-sm text-gray-500">{{ __('Pending Employers') }}</p>
                    <p class="text-2xl font-bold">{{ $pendingEmployers }}</p>
                </div>
                <div class="border rounded-lg p-4 bg-white">
                    <p class="text-sm text-gray-500">{{ __('Pending Documents') }}</p>
                    <p class="text-2xl font-bold">{{ $pendingDocuments }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold">{{ __('Applications Per Month') }}</h3>
                        @php($maxCount = $chartApplications->max('count') ?: 1)
                        <div class="mt-4 space-y-3">
                            @foreach ($chartApplications as $row)
                                <div>
                                    <div class="flex justify-between text-sm text-gray-600">
                                        <span>{{ $row['label'] }}</span>
                                        <span>{{ $row['count'] }}</span>
                                    </div>
                                    <div class="h-2 bg-gray-100 rounded">
                                        <div class="h-2 bg-indigo-500 rounded" style="width: {{ ($row['count'] / $maxCount) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold">{{ __('Hiring Rate (Last 6 Months)') }}</h3>
                        <div class="mt-4">
                            <p class="text-3xl font-bold">{{ $hiringRate }}%</p>
                            <div class="h-2 bg-gray-100 rounded mt-2">
                                <div class="h-2 bg-emerald-500 rounded" style="width: {{ $hiringRate }}%"></div>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">{{ __('Based on hired applications over total applications.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
