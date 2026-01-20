@php
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $jobseeker->user->name ?? __('Applicant') }}
            </h2>
            <a href="{{ route('employer.documents') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('Back to Applicants') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    <div>
                        <p class="text-sm text-gray-500">{{ $jobseeker->user->email ?? '' }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($types as $key => $label)
                            @php($document = $documents[$key] ?? null)
                            <div class="border rounded-lg p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-semibold">{{ $label }}</h4>
                                    @if ($document)
                                        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                            {{ $document->status === 'rejected' ? __('Needs Update') : ($document->status === 'pending' ? __('Updated') : Str::of($document->status)->title()) }}
                                        </span>
                                    @else
                                        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                            {{ __('Missing') }}
                                        </span>
                                    @endif
                                </div>

                                @if ($document && $document->remarks)
                                    <p class="text-xs text-gray-500">{{ __('Remarks: :remarks', ['remarks' => $document->remarks]) }}</p>
                                @endif

                                @if ($document)
                                    <a href="{{ asset('storage/'.$document->file_path) }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-900">
                                        {{ __('View File') }}
                                    </a>

                                    <form method="POST" action="{{ route('employer.documents.request-update', $document) }}" class="space-y-2 mt-2">
                                        @csrf
                                        <textarea name="remarks" rows="2" class="block w-full text-xs border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="{{ __('Reason for update request') }}" required></textarea>
                                        <div class="flex justify-end">
                                            <x-danger-button class="text-xs">{{ __('Request Update') }}</x-danger-button>
                                        </div>
                                    </form>
                                @else
                                    <p class="text-xs text-gray-500">{{ __('No file uploaded yet.') }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
