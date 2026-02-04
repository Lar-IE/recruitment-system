@php
    use Illuminate\Support\Str;

    $user = Auth::user();
    
    // Get the first education entry for summary
    $firstEducation = $jobseeker->educations->first();
    $educationSummary = $firstEducation 
        ? ($firstEducation->degree ? $firstEducation->degree . ' - ' : '') . $firstEducation->institution
        : '';
    
    $skillItems = collect(preg_split("/\r\n|\r|\n/", $jobseeker->skills ?? ''))
        ->map(fn ($item) => trim($item))
        ->filter();
    $age = $jobseeker->birth_date?->age;
    $location = collect([$jobseeker->barangay, $jobseeker->city, $jobseeker->province, $jobseeker->region, $jobseeker->country])
        ->filter()
        ->implode(', ');
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Profile Overview') }}
            </h2>
            <a href="{{ route('jobseeker.profile.edit') }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md text-xs font-semibold text-white uppercase tracking-widest hover:bg-indigo-500">
                {{ __('Edit Profile') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif
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
                            <p>{{ $educationSummary->isNotEmpty() ? $educationSummary->toString() : '-' }}</p>
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
                            <p class="text-xs font-semibold text-gray-500">{{ __('Birth Date') }}</p>
                            <p>{{ $jobseeker->birth_date?->format('M d, Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500">{{ __('Address') }}</p>
                            <p>{{ $jobseeker->address ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500">{{ __('Barangay') }}</p>
                            <p>{{ $jobseeker->barangay ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500">{{ __('Province') }}</p>
                            <p>{{ $jobseeker->province ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500">{{ __('Region') }}</p>
                            <p>{{ $jobseeker->region ?? '-' }}</p>
                        </div>
                        <div class="lg:col-span-2">
                            <p class="text-xs font-semibold text-gray-500">{{ __('Full Location') }}</p>
                            <p>{{ $location ?: '-' }}</p>
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
                                            @if ($education->degree)
                                                <p class="text-sm text-gray-700">{{ $education->degree }}@if($education->field_of_study), {{ $education->field_of_study }}@endif</p>
                                            @endif
                                            @if ($education->start_date || $education->end_date)
                                                <p class="text-xs text-gray-500">
                                                    {{ $education->start_date?->format('M Y') ?? 'N/A' }} - {{ $education->end_date?->format('M Y') ?? 'Present' }}
                                                </p>
                                            @endif
                                            @if ($education->description)
                                                <p class="mt-1 text-xs text-gray-600">{{ $education->description }}</p>
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
                                            <p class="font-medium text-sm text-gray-900">{{ $experience->position }}</p>
                                            <p class="text-sm text-gray-700">{{ $experience->company }}</p>
                                            @if ($experience->start_date || $experience->end_date || $experience->is_current)
                                                <p class="text-xs text-gray-500">
                                                    {{ $experience->start_date?->format('M Y') ?? 'N/A' }} - 
                                                    @if($experience->is_current)
                                                        {{ __('Present') }}
                                                    @else
                                                        {{ $experience->end_date?->format('M Y') ?? 'N/A' }}
                                                    @endif
                                                </p>
                                            @endif
                                            @if ($experience->description)
                                                <p class="mt-1 text-xs text-gray-600">{{ $experience->description }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-sm font-semibold text-gray-800">{{ __('Skills') }}</p>
                            @if ($skillItems->isEmpty())
                                <p class="mt-2 text-sm text-gray-500">{{ __('No skills listed.') }}</p>
                            @else
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach ($skillItems as $item)
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700">{{ $item }}</span>
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
