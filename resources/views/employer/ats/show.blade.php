@php
    $user = $jobseeker->user;
    $age = $jobseeker->birth_date?->age;
    $firstEducation = $jobseeker->educations->first();
    $educationPrimaryLine = '';
    $educationSchoolLine = '';
    if ($firstEducation) {
        $educationPrimaryLine = trim(implode(' in ', array_filter([
            $firstEducation->degree,
            $firstEducation->field_of_study,
        ])));
        if ($educationPrimaryLine === '') {
            $educationPrimaryLine = $firstEducation->field_of_study ?: ($firstEducation->degree ?: '');
        }
        $educationSchoolLine = (string) ($firstEducation->institution ?? '');
    }
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Profile Details') }}
            </h2>
            <a href="{{ route('employer.ats') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('Back to Talent Pool') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold">{{ __('Profile Overview') }}</h3>
                    <div class="mt-4 grid grid-cols-1 gap-4 text-sm text-gray-700 md:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <p class="text-xs font-bold text-gray-800">{{ __('Name') }}</p>
                            <p>{{ $jobseeker->full_name ?: ($user->name ?? '-') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-800">{{ __('Email') }}</p>
                            <p>{{ $user->email ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-800">{{ __('Contact number') }}</p>
                            <p>
                                @if ($jobseeker->phone)
                                    {{ str_starts_with($jobseeker->phone, '+63') ? $jobseeker->phone : '+63' . ltrim($jobseeker->phone, '0') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-800">{{ __('City Location') }}</p>
                            <p>{{ $jobseeker->city ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-800">{{ __('Educational Attainment') }}</p>
                            <p>{{ $jobseeker->educational_attainment ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-800">{{ __('Education Details') }}</p>
                            @if ($educationPrimaryLine !== '' || $educationSchoolLine !== '')
                                <p>{{ $educationPrimaryLine !== '' ? $educationPrimaryLine : '-' }}</p>
                                <p class="text-xs text-gray-500">{{ $educationSchoolLine !== '' ? $educationSchoolLine : '-' }}</p>
                            @else
                                <p>-</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-800">{{ __('Gender') }}</p>
                            <p>{{ $jobseeker->gender ? ucfirst($jobseeker->gender) : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-800">{{ __('Age') }}</p>
                            <p>{{ $age ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-800">{{ __('Current/Recent Job') }}</p>
                            @php
                                $firstWe = $jobseeker->workExperiences->first();
                                $currentJob = $firstWe?->position ?: $firstWe?->company ?: 'N/A';
                            @endphp
                            <p>{{ $currentJob }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <h3 class="text-lg font-semibold">{{ __('Resume Details') }}</h3>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ __('Professional Summary') }}</p>
                        @if ($jobseeker->bio)
                            <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $jobseeker->bio }}</p>
                        @else
                            <p class="mt-2 text-sm text-gray-500">{{ __('No summary provided.') }}</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ __('Education') }}</p>
                            @if ($jobseeker->educations->isEmpty())
                                <p class="mt-2 text-sm text-gray-500">{{ __('No education entries.') }}</p>
                            @else
                                <div class="mt-2 space-y-4">
                                    @foreach ($jobseeker->educations as $education)
                                        <div class="border-l-4 border-indigo-500 pl-3 py-1">
                                            <p class="font-medium text-sm text-gray-900">{{ $education->institution }}</p>
                                            @if ($education->degree || $education->field_of_study)
                                                <p class="text-sm text-gray-700">
                                                    {{ trim(implode(' in ', array_filter([$education->degree, $education->field_of_study]))) }}
                                                </p>
                                            @endif
                                            @if ($education->start_date || $education->end_date)
                                                <p class="text-xs text-gray-500">
                                                    {{ $education->start_date?->format('M Y') ?? 'N/A' }} - {{ $education->end_date?->format('M Y') ?? 'Present' }}
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ __('Work Experience') }}</p>
                            @if ($jobseeker->workExperiences->isEmpty())
                                <p class="mt-2 text-sm text-gray-500">{{ __('No experience entries.') }}</p>
                            @else
                                <div class="mt-2 space-y-4">
                                    @foreach ($jobseeker->workExperiences as $experience)
                                        <div class="border-l-4 border-green-500 pl-3 py-1">
                                            <p class="font-medium text-sm text-gray-900">{{ $experience->position ?: __('N/A') }}</p>
                                            <p class="text-sm text-gray-700">{{ $experience->company ?: __('N/A') }}</p>
                                            @if ($experience->start_date || $experience->end_date || $experience->is_current)
                                                <p class="text-xs text-gray-500">
                                                    {{ $experience->start_date?->format('M Y') ?? 'N/A' }} -
                                                    @if ($experience->is_current)
                                                        {{ __('Present') }}
                                                    @else
                                                        {{ $experience->end_date?->format('M Y') ?? 'N/A' }}
                                                    @endif
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="md:col-span-2">
                            <p class="text-sm font-semibold text-gray-800">{{ __('Skills') }}</p>
                            @if ($jobseeker->skillsList->isEmpty())
                                <p class="mt-2 text-sm text-gray-500">{{ __('No skills listed.') }}</p>
                            @else
                                <div class="mt-2 space-y-3">
                                    @foreach ($jobseeker->skillsList as $skill)
                                        <div>
                                            <div class="flex items-center justify-between text-sm mb-1">
                                                <span class="font-medium text-gray-800">{{ $skill->skill_name }}</span>
                                                @if ($skill->proficiency_percentage !== null)
                                                    <span class="text-gray-500">{{ $skill->proficiency_percentage }}%</span>
                                                @endif
                                            </div>
                                            @if ($skill->proficiency_percentage !== null)
                                                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                                    <div class="h-full bg-indigo-600 rounded-full transition-all" style="width: {{ min(100, max(0, $skill->proficiency_percentage)) }}%"></div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

