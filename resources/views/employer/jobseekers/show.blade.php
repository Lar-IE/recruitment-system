@php
    use Illuminate\Support\Str;

    $user = $jobseeker->user;
    $age = $jobseeker->birth_date?->age;
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Jobseeker Profile') }}
            </h2>
            <a href="{{ route('employer.jobseekers.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('Back to Directory') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold">{{ __('Profile Overview') }}</h3>
                    <div class="mt-4 grid grid-cols-1 gap-4 text-sm text-gray-700 md:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <p class="text-xs font-semibold text-gray-500">{{ __('Name') }}</p>
                            <p>{{ $user->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500">{{ __('Email') }}</p>
                            <p>{{ $user->email ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500">{{ __('Contact number') }}</p>
                            <p>{{ $jobseeker->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500">{{ __('City Location') }}</p>
                            <p>{{ $jobseeker->city ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500">{{ __('Educational Attainment') }}</p>
                            <p>{{ $jobseeker->education ? Str::of($jobseeker->education)->before("\n")->trim() : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500">{{ __('Gender') }}</p>
                            <p>{{ $jobseeker->gender ? ucfirst($jobseeker->gender) : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500">{{ __('Age') }}</p>
                            <p>{{ $age ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500">{{ __('Status') }}</p>
                            <p>{{ ucfirst($jobseeker->status) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
