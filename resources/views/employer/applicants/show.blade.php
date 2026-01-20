@php
    use Illuminate\Support\Str;
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
                <div class="p-6 text-gray-900 space-y-4">
                    <h3 class="text-lg font-semibold">{{ $application->jobseeker->user->name ?? __('N/A') }}</h3>
                    <div class="text-sm text-gray-600">
                        <p>{{ $application->jobseeker->user->email ?? '' }}</p>
                        <p>{{ $application->jobseeker->phone ?? '-' }}</p>
                        <p>{{ $application->jobseeker->address ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 space-y-4">
                        <h4 class="text-lg font-semibold">{{ __('Application Details') }}</h4>
                        <p class="text-sm text-gray-600">
                            {{ __('Job: :title', ['title' => $application->jobPost->title ?? __('N/A')]) }}
                        </p>
                        <p class="text-sm text-gray-600">
                            {{ __('Status: :status', ['status' => $statuses[$application->current_status] ?? ucfirst($application->current_status)]) }}
                        </p>
                        <p class="text-sm text-gray-600">
                            {{ __('Applied At: :date', ['date' => $application->applied_at?->format('M d, Y') ?? '-']) }}
                        </p>
                        @if ($application->cover_letter)
                            <div class="text-sm text-gray-700 whitespace-pre-line border rounded-lg p-3 bg-gray-50">
                                {{ $application->cover_letter }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 space-y-4">
                        <h4 class="text-lg font-semibold">{{ __('Resume') }}</h4>
                        @if ($resume)
                            <a href="{{ asset('storage/'.$resume->file_path) }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-900">
                                {{ __('View Resume') }}
                            </a>
                        @else
                            <p class="text-sm text-gray-500">{{ __('No resume uploaded.') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <h4 class="text-lg font-semibold">{{ __('Uploaded Documents') }}</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach (['resume','sss','pagibig','philhealth','psa'] as $type)
                            @php($doc = $application->jobseeker->documents->firstWhere('type', $type))
                            <div class="border rounded-lg p-4">
                                <p class="font-semibold text-sm">{{ Str::of($type)->replace('_', ' ')->title() }}</p>
                                @if ($doc)
                                    <p class="text-xs text-gray-500">{{ __('Status: :status', ['status' => $doc->status === 'pending' ? __('Updated') : ($doc->status === 'rejected' ? __('Needs Update') : ucfirst($doc->status))]) }}</p>
                                    <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-900">
                                        {{ __('View') }}
                                    </a>
                                @else
                                    <p class="text-xs text-gray-500">{{ __('Missing') }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6" x-data="{ openRequest: false }">
                        <x-danger-button type="button" class="text-xs" x-on:click="openRequest = true">
                            {{ __('Request Update') }}
                        </x-danger-button>

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
                                    @foreach (['resume','sss','pagibig','philhealth','psa'] as $type)
                                        @php($doc = $application->jobseeker->documents->firstWhere('type', $type))
                                        <div class="border rounded-lg p-4">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="font-semibold text-sm">{{ Str::of($type)->replace('_', ' ')->title() }}</p>
                                                    @if ($doc)
                                                        <p class="text-xs text-gray-500">{{ __('Status: :status', ['status' => $doc->status === 'pending' ? __('Updated') : ($doc->status === 'rejected' ? __('Needs Update') : ucfirst($doc->status))]) }}</p>
                                                    @else
                                                        <p class="text-xs text-gray-500">{{ __('Missing') }}</p>
                                                    @endif
                                                </div>
                                                @if ($doc)
                                                    <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-900">
                                                        {{ __('View') }}
                                                    </a>
                                                @endif
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
                                                {{ __('By :name on :date', ['name' => $note->creator->name ?? __('Employer'), 'date' => $note->created_at?->format('M d, Y H:i')]) }}
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
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="text-left text-gray-500">
                                        <tr>
                                            <th class="py-2 pr-4">{{ __('Job Title') }}</th>
                                            <th class="py-2 pr-4">{{ __('Status') }}</th>
                                            <th class="py-2 pr-4">{{ __('Applied At') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700">
                                        @foreach ($otherApplications as $other)
                                            <tr class="border-t">
                                                <td class="py-2 pr-4">{{ $other->jobPost->title ?? __('N/A') }}</td>
                                                <td class="py-2 pr-4">{{ $statuses[$other->current_status] ?? ucfirst($other->current_status) }}</td>
                                                <td class="py-2 pr-4">{{ $other->applied_at?->format('M d, Y') }}</td>
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
