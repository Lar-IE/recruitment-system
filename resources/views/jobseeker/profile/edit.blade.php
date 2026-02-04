<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Resume Profile') }}
            </h2>
            <a href="{{ route('jobseeker.profile.show') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('Back to Profile') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @include('profile.partials.jobseeker-resume-form', ['jobseeker' => $jobseeker])
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
