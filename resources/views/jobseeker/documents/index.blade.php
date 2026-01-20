@php
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Documents') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    <div x-data="{ open: false }" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold">{{ __('Upload Required Documents') }}</h3>
                            <button type="button" class="text-sm text-indigo-600 hover:text-indigo-900" x-on:click="open = !open" x-bind:aria-expanded="open">
                                <span x-show="!open">{{ __('Bulk Upload') }}</span>
                                <span x-show="open">{{ __('Hide Bulk Upload') }}</span>
                            </button>
                        </div>

                        <div x-show="open" class="mt-4">
                            <form method="POST" action="{{ route('jobseeker.documents.store-all') }}" enctype="multipart/form-data" class="border rounded-lg p-4 space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach ($types as $key => $label)
                                        <div>
                                            <x-input-label :value="$label" />
                                            <input type="file" name="files[{{ $key }}]" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                        </div>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('files')" class="mt-1" />
                                <x-primary-button>{{ __('Upload All') }}</x-primary-button>
                            </form>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($types as $key => $label)
                            @php($document = $documents[$key] ?? null)
                            <div class="border rounded-lg p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-semibold">{{ $label }}</h4>
                                    @if ($document)
                                        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                            {{ $document->status === 'pending' ? __('Updated') : Str::of($document->status)->title() }}
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

                                <form method="POST" action="{{ route('jobseeker.documents.store') }}" enctype="multipart/form-data" class="space-y-2">
                                    @csrf
                                    <input type="hidden" name="type" value="{{ $key }}">
                                    <input type="file" name="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                                    <x-input-error :messages="$errors->get('file')" class="mt-1" />
                                    <x-input-error :messages="$errors->get('type')" class="mt-1" />
                                    <x-primary-button class="w-full justify-center">{{ $document ? __('Replace') : __('Upload') }}</x-primary-button>
                                </form>

                                @if ($document)
                                    <div class="flex items-center justify-between">
                                        <a href="{{ route('jobseeker.documents.show', $document) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                            {{ __('View Details') }}
                                        </a>
                                        <a href="{{ route('jobseeker.documents.download', $document) }}" class="text-sm text-gray-600 hover:text-gray-900">
                                            {{ __('Download') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500">{{ __('Accepted formats: PDF, JPG, PNG. Max size 5MB.') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
