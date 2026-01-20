<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Document Details') }}
            </h2>
            <a href="{{ route('jobseeker.documents') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('Back to Documents') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold">{{ $typeLabel }}</h3>
                        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                            {{ $document->status === 'rejected' ? __('Needs Update') : ($document->status === 'pending' ? __('Updated') : ucfirst($document->status)) }}
                        </span>
                    </div>

                    <div class="text-sm text-gray-600 space-y-1">
                        <p>{{ __('Uploaded: :date', ['date' => $document->created_at?->format('M d, Y') ?? '-']) }}</p>
                        <p>{{ __('Last Reviewed: :date', ['date' => $document->reviewed_at?->format('M d, Y') ?? '-']) }}</p>
                    </div>

                    @if ($document->remarks)
                        @php
                            $lines = explode(' Reason: ', $document->remarks, 2);
                        @endphp
                        <div class="rounded-md bg-yellow-50 p-4 text-sm text-yellow-800 space-y-1">
                            <p>{{ $lines[0] }}</p>
                            @if (isset($lines[1]))
                                <p>{{ __('Reason: :reason', ['reason' => $lines[1]]) }}</p>
                            @endif
                        </div>
                    @endif

                    <div class="flex items-center gap-3">
                        <a href="{{ route('jobseeker.documents.download', $document) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                            {{ __('Download File') }}
                        </a>
                        <a href="{{ asset('storage/'.$document->file_path) }}" target="_blank" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('View File') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
