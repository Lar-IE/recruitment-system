@php
    use Illuminate\Support\Str;

    $jobseeker = $application->jobseeker;
    $user = $jobseeker?->user;
    $location = collect([$jobseeker->barangay, $jobseeker->city, $jobseeker->province, $jobseeker->region, $jobseeker->country])
        ->filter()
        ->implode(', ');
    // Legacy text fields (fallback when no structured education/experience records)
    $educationItemsLegacy = collect(preg_split("/\r\n|\r|\n/", $jobseeker->education ?? ''))
        ->map(fn ($item) => trim($item))
        ->filter();
    $experienceItemsLegacy = collect(preg_split("/\r\n|\r|\n/", $jobseeker->experience ?? ''))
        ->map(fn ($item) => trim($item))
        ->filter();
    $skillItems = collect(preg_split("/\r\n|\r|\n/", $jobseeker->skills ?? ''))
        ->map(fn ($item) => trim($item))
        ->filter();
    $skillsWithProficiency = $jobseeker->skillsList->isNotEmpty()
        ? $jobseeker->skillsList
        : $skillItems->map(fn ($name) => (object)['skill_name' => $name, 'proficiency_percentage' => null]);

    $statusStyles = [
        'new' => 'bg-blue-100 text-blue-700 ring-blue-200',
        'for_review' => 'bg-amber-100 text-amber-700 ring-amber-200',
        'schedule_interview' => 'bg-indigo-100 text-indigo-700 ring-indigo-200',
        'shortlisted' => 'bg-purple-100 text-purple-700 ring-purple-200',
        'hired' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
        'for_pooling' => 'bg-rose-100 text-rose-700 ring-rose-200',
        'on_hold' => 'bg-slate-100 text-slate-700 ring-slate-200',
    ];
    $statusKey = $application->current_status;
    $statusClass = $statusStyles[$statusKey] ?? 'bg-gray-100 text-gray-700 ring-gray-200';
    $firstEducation = $jobseeker->educations->first();
    $educationSummary = $firstEducation
        ? ($firstEducation->degree ? $firstEducation->degree . ' - ' : '') . $firstEducation->institution
        : Str::of($jobseeker->education ?? '')->before("\n")->trim();
    $age = $jobseeker->birth_date?->age;
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Applicant Profile') }}
            </h2>
            <a href="{{ route('employer.applicants') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('Back to Applicants') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-xl font-semibold">{{ $user->name ?? __('N/A') }}</h3>
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">
                                    {{ $statuses[$statusKey] ?? ucfirst($statusKey) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">
                                <span>{{ $user->email ?? '-' }}</span>
                                <span class="mx-2 text-gray-300">|</span>
                                <span>
                                    @if ($jobseeker->phone)
                                        {{ str_starts_with($jobseeker->phone, '+63') ? $jobseeker->phone : '+63' . ltrim($jobseeker->phone, '0') }}
                                    @else
                                        -
                                    @endif
                                </span>
                                @if ($location)
                                    <span class="mx-2 text-gray-300">|</span>
                                    <span>{{ $location }}</span>
                                @endif
                            </p>
                        </div>
                        @if ($resume)
                            <a href="{{ asset('storage/'.$resume->file_path) }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-900">
                                {{ __('View Resume') }}
                            </a>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2 space-y-4">
                            <div class="border rounded-lg p-4 bg-gray-50">
                                <h4 class="text-sm font-semibold text-gray-800">{{ __('Professional Summary') }}</h4>
                                @if ($jobseeker->bio)
                                    <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $jobseeker->bio }}</p>
                                @else
                                    <p class="mt-2 text-sm text-gray-500">{{ __('No summary provided.') }}</p>
                                @endif
                            </div>

                            <div class="border rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-800">{{ __('Resume Details') }}</h4>
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ __('Education') }}</p>
                                        @if ($jobseeker->educations->isNotEmpty())
                                            <ul class="mt-2 space-y-3 list-none pl-0">
                                                @foreach ($jobseeker->educations as $edu)
                                                    <li class="border-l-2 border-gray-200 pl-3">
                                                        <p class="font-medium text-gray-800">{{ $edu->institution }}</p>
                                                        @if ($edu->degree || $edu->field_of_study)
                                                            <p class="text-gray-600">{{ trim(implode(', ', array_filter([$edu->degree, $edu->field_of_study]))) }}</p>
                                                        @endif
                                                        @if ($edu->start_date || $edu->end_date)
                                                            <p class="text-xs text-gray-500">
                                                                {{ $edu->start_date?->format('M Y') ?? '?' }} – {{ $edu->end_date?->format('M Y') ?? '?' }}
                                                            </p>
                                                        @endif
                                                        @if ($edu->description)
                                                            <p class="mt-1 text-gray-600 whitespace-pre-line">{{ $edu->description }}</p>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @elseif ($educationItemsLegacy->isNotEmpty())
                                            <ul class="mt-2 list-disc list-inside space-y-1">
                                                @foreach ($educationItemsLegacy as $item)
                                                    <li>{{ $item }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="mt-1 text-sm text-gray-500">{{ __('No education entries.') }}</p>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ __('Work Experience') }}</p>
                                        @if ($jobseeker->workExperiences->isNotEmpty())
                                            <ul class="mt-2 space-y-3 list-none pl-0">
                                                @foreach ($jobseeker->workExperiences as $exp)
                                                    <li class="border-l-2 border-gray-200 pl-3">
                                                        <p class="font-medium text-gray-800">{{ $exp->company }}</p>
                                                        @if ($exp->position)
                                                            <p class="text-gray-600">{{ $exp->position }}</p>
                                                        @endif
                                                        @if ($exp->start_date || $exp->end_date)
                                                            <p class="text-xs text-gray-500">
                                                                {{ $exp->start_date?->format('M Y') ?? '?' }} – {{ $exp->is_current ? __('Present') : ($exp->end_date?->format('M Y') ?? '?') }}
                                                            </p>
                                                        @endif
                                                        @if ($exp->description)
                                                            <p class="mt-1 text-gray-600 whitespace-pre-line">{{ $exp->description }}</p>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @elseif ($experienceItemsLegacy->isNotEmpty())
                                            <ul class="mt-2 list-disc list-inside space-y-1">
                                                @foreach ($experienceItemsLegacy as $item)
                                                    <li>{{ $item }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="mt-1 text-sm text-gray-500">{{ __('No experience entries.') }}</p>
                                        @endif
                                    </div>
                                    <div class="md:col-span-2">
                                        <p class="font-semibold text-gray-800">{{ __('Skills') }}</p>
                                        @if ($skillsWithProficiency->isEmpty())
                                            <p class="mt-1 text-sm text-gray-500">{{ __('No skills listed.') }}</p>
                                        @else
                                            <div class="mt-2 space-y-3">
                                                @foreach ($skillsWithProficiency as $skill)
                                                    <div>
                                                        <div class="flex items-center justify-between text-sm mb-1">
                                                            <span class="font-medium text-gray-800">{{ is_object($skill) ? $skill->skill_name : $skill }}</span>
                                                            @if (is_object($skill) && $skill->proficiency_percentage !== null)
                                                                <span class="text-gray-500">{{ $skill->proficiency_percentage }}%</span>
                                                            @endif
                                                        </div>
                                                        @if (is_object($skill) && $skill->proficiency_percentage !== null)
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

                        <div class="border rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-gray-800">{{ __('Basic Information') }}</h4>
                            <div class="mt-3 space-y-2 text-sm text-gray-600">
                            <p><span class="font-semibold text-gray-800">{{ __('Contact Number:') }}</span> 
                                @if ($jobseeker->phone)
                                    {{ str_starts_with($jobseeker->phone, '+63') ? $jobseeker->phone : '+63' . ltrim($jobseeker->phone, '0') }}
                                @else
                                    -
                                @endif
                            </p>
                                <p><span class="font-semibold text-gray-800">{{ __('Birth Date:') }}</span> {{ $jobseeker->birth_date?->format('M d, Y') ?? '-' }}</p>
                            <p><span class="font-semibold text-gray-800">{{ __('Age:') }}</span> {{ $age ?? '-' }}</p>
                                <p><span class="font-semibold text-gray-800">{{ __('Gender:') }}</span> {{ $jobseeker->gender ? ucfirst($jobseeker->gender) : '-' }}</p>
                                <p><span class="font-semibold text-gray-800">{{ __('Address:') }}</span> {{ $jobseeker->address ?? '-' }}</p>
                                <p><span class="font-semibold text-gray-800">{{ __('Barangay:') }}</span> {{ $jobseeker->barangay ?? '-' }}</p>
                            <p><span class="font-semibold text-gray-800">{{ __('City Location:') }}</span> {{ $jobseeker->city ?? '-' }}</p>
                                <p><span class="font-semibold text-gray-800">{{ __('Province:') }}</span> {{ $jobseeker->province ?? '-' }}</p>
                                <p><span class="font-semibold text-gray-800">{{ __('Region:') }}</span> {{ $jobseeker->region ?? '-' }}</p>
                                <p><span class="font-semibold text-gray-800">{{ __('Country:') }}</span> {{ $jobseeker->country ?? '-' }}</p>
                            <p><span class="font-semibold text-gray-800">{{ __('Educational Attainment:') }}</span> {{ $jobseeker->educational_attainment ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 space-y-4">
                        <h4 class="text-lg font-semibold">{{ __('Applied Job/Position') }}</h4>
                        <p class="text-sm text-gray-600">
                            {{ __('Job: :title', ['title' => $application->jobPost->title ?? __('N/A')]) }}
                        </p>
                        <p class="text-sm text-gray-600">
                            {{ __('Applied At: :date', ['date' => $application->applied_at?->format('M d, Y') ?? '-']) }}
                        </p>
                        @if ($application->cover_letter)
                            <div class="text-sm text-gray-700 whitespace-pre-line border rounded-lg p-3 bg-gray-50">
                                {{ $application->cover_letter }}
                            </div>
                        @endif
                        @if ($application->cover_letter_file)
                            <p class="text-sm mt-2">
                                <a href="{{ asset('storage/' . $application->cover_letter_file) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-indigo-600 hover:text-indigo-900 font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    {{ __('Download cover letter (PDF/Word)') }}
                                </a>
                            </p>
                        @endif
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 space-y-4">
                        <h4 class="text-lg font-semibold">{{ __('Uploaded Documents') }}</h4>
                        <div class="space-y-2 text-sm text-gray-700">
                            @foreach (['sss','pagibig','philhealth','psa'] as $type)
                                @php($doc = $application->jobseeker->documents->firstWhere('type', $type))
                                @if ($doc)
                                    <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-900">
                                        <span class="font-semibold">{{ Str::of($type)->replace('_', ' ')->title() }}</span>
                                        <span class="text-xs text-gray-500">
                                            {{ __(':status', ['status' => $doc->status === 'pending' ? __('Updated') : ($doc->status === 'rejected' ? __('Needs Update') : ucfirst($doc->status))]) }}
                                        </span>
                                    </a>
                                @else
                                    <div class="flex items-center gap-2 text-gray-500">
                                        <span class="font-semibold text-gray-700">{{ Str::of($type)->replace('_', ' ')->title() }}</span>
                                        <span class="text-xs">{{ __('Missing') }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <div class="mt-4" x-data="{ openRequest: false }">
                            <button type="button" class="text-sm text-indigo-600 hover:text-indigo-900" x-on:click="openRequest = true">
                                {{ __('Request Update') }}
                            </button>

                            <div x-show="openRequest" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
                                <div class="absolute inset-0 bg-black/50" x-on:click="openRequest = false"></div>
                                <div class="relative w-full max-w-4xl rounded-lg bg-white p-5 shadow-lg">
                                    <div class="flex items-center justify-between">
                                        <h5 class="text-base font-semibold text-gray-800">{{ __('Request Updates') }}</h5>
                                        <button type="button" class="text-sm text-gray-500 hover:text-gray-700" x-on:click="openRequest = false">
                                            {{ __('Close') }}
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">{{ __('Select a document and add a reason.') }}</p>
                                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3 max-h-[70vh] overflow-y-auto">
                                        @foreach (['sss','pagibig','philhealth','psa'] as $type)
                                            @php($doc = $application->jobseeker->documents->firstWhere('type', $type))
                                            <div class="border rounded-lg p-4">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        @if ($doc)
                                                            <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="font-semibold text-sm text-indigo-600 hover:text-indigo-900">
                                                                {{ Str::of($type)->replace('_', ' ')->title() }}
                                                            </a>
                                                            <p class="text-xs text-gray-500">{{ __('Status: :status', ['status' => $doc->status === 'pending' ? __('Updated') : ($doc->status === 'rejected' ? __('Needs Update') : ucfirst($doc->status))]) }}</p>
                                                        @else
                                                            <p class="font-semibold text-sm">{{ Str::of($type)->replace('_', ' ')->title() }}</p>
                                                            <p class="text-xs text-gray-500">{{ __('Missing') }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if ($doc)
                                                    <form method="POST" action="{{ route('employer.documents.request-update', $doc) }}" class="mt-3 space-y-2">
                                                        @csrf
                                                        <textarea name="remarks" rows="2" class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="{{ __('Reason for update request') }}" required></textarea>
                                                        <div class="flex justify-end">
                                                            <x-danger-button class="text-xs">{{ __('Send Request') }}</x-danger-button>
                                                        </div>
                                                    </form>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h4 class="text-lg font-semibold mb-4">{{ __('Status Timeline') }}</h4>
                    @if ($application->statuses->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No status updates yet.') }}</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($application->statuses->sortByDesc('created_at') as $status)
                                <div class="border rounded-lg p-4">
                                    <div class="flex items-center justify-between text-sm text-gray-600">
                                        <span class="font-semibold">
                                            {{ Str::of($status->status)->replace('_', ' ')->title() }}
                                        </span>
                                        <span>{{ $status->created_at?->format('M d, Y H:i') }}</span>
                                    </div>
                                    @if ($status->note)
                                        <p class="mt-2 text-sm text-gray-700">{{ $status->note }}</p>
                                    @endif
                                    @if ($status->interview_at)
                                        <p class="mt-2 text-sm text-gray-700">
                                            {{ __('Interview schedule: :date', ['date' => $status->interview_at->format('M d, Y h:i A')]) }}
                                        </p>
                                    @endif
                                    @if ($status->interview_link)
                                        <p class="mt-1 text-sm">
                                            <a href="{{ $status->interview_link }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                                {{ __('Join Interview') }}
                                            </a>
                                        </p>
                                    @endif
                                    @if ($status->setBy)
                                        <p class="mt-2 text-xs text-gray-500">
                                            {{ __('Updated by: :name', ['name' => $status->setBy->name]) }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 space-y-4">
                        <h4 class="text-lg font-semibold">{{ __('Employer Notes') }}</h4>

                        <form method="POST" action="{{ route('employer.applicants.notes.store', $application) }}" class="space-y-3">
                            @csrf
                            <textarea name="note" rows="3" class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="{{ __('Add a note for this applicant') }}" required></textarea>
                            <x-primary-button class="text-xs">{{ __('Add Note') }}</x-primary-button>
                        </form>

                        @if ($application->notes->isEmpty())
                            <p class="text-sm text-gray-500">{{ __('No notes yet.') }}</p>
                        @else
                            <div class="space-y-3">
                                @foreach ($application->notes->sortByDesc('created_at') as $note)
                                    <div class="border rounded-lg p-3" x-data="{ editing: false }">
                                        <div class="flex items-center justify-between">
                                            <p class="text-xs text-gray-500">
                                                {{ __('By :name on :date', ['name' => $note->creatorSubUser->name ?? $note->creator->name ?? __('Employer'), 'date' => $note->created_at?->format('M d, Y H:i')]) }}
                                            </p>
                                            <div class="flex items-center gap-2 text-xs">
                                                <button type="button" class="text-indigo-600 hover:text-indigo-900" x-on:click="editing = !editing">
                                                    <span x-show="!editing">{{ __('Edit') }}</span>
                                                    <span x-show="editing">{{ __('Cancel') }}</span>
                                                </button>
                                                <form method="POST" action="{{ route('employer.applicants.notes.destroy', [$application, $note]) }}" onsubmit="return confirm('{{ __('Delete this note?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-700">{{ __('Delete') }}</button>
                                                </form>
                                            </div>
                                        </div>

                                        <p class="text-sm text-gray-700 mt-2" x-show="!editing">{{ $note->note }}</p>

                                        <form method="POST" action="{{ route('employer.applicants.notes.update', [$application, $note]) }}" class="mt-2 space-y-2" x-show="editing">
                                            @csrf
                                            @method('PUT')
                                            <textarea name="note" rows="3" class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ $note->note }}</textarea>
                                            <div class="flex justify-end">
                                                <x-primary-button class="text-xs">{{ __('Save') }}</x-primary-button>
                                            </div>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h4 class="text-lg font-semibold mb-4">{{ __('Application History (Employer)') }}</h4>
                        @if ($otherApplications->isEmpty())
                            <p class="text-sm text-gray-500">{{ __('No other applications found.') }}</p>
                        @else
                            <div class="overflow-x-auto -mx-6 sm:mx-0 lg:mx-0 lg:overflow-visible">
                                <table class="w-full min-w-[1000px] lg:min-w-0 text-sm">
                                    <thead class="text-left text-gray-500 whitespace-nowrap bg-gray-50/80">
                                        <tr>
                                            <th class="py-3 px-4">{{ __('Job Title') }}</th>
                                            <th class="py-3 px-4">{{ __('Status') }}</th>
                                            <th class="py-3 px-4">{{ __('Applied At') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700">
                                        @foreach ($otherApplications as $other)
                                            <tr class="border-t align-top">
                                                <td class="py-3 px-4">{{ $other->jobPost->title ?? __('N/A') }}</td>
                                                <td class="py-3 px-4 whitespace-nowrap">{{ $statuses[$other->current_status] ?? Str::of($other->current_status)->replace('_', ' ')->title() }}</td>
                                                <td class="py-3 px-4 whitespace-nowrap">{{ $other->applied_at?->format('M d, Y') }}</td>
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
    </div>
</x-app-layout>
