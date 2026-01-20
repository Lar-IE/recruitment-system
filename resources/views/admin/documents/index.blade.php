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

            <form method="GET" action="{{ route('admin.documents') }}" class="bg-white p-4 rounded-lg shadow-sm flex flex-wrap gap-4 items-end">
                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('All') }}</option>
                        @foreach (['pending' => 'Updated', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $key => $label)
                            <option value="{{ $key }}" {{ ($filters['status'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="type" :value="__('Type')" />
                    <select id="type" name="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($types as $key => $label)
                            <option value="{{ $key }}" {{ ($filters['type'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <x-primary-button>{{ __('Filter') }}</x-primary-button>
            </form>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($documents->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No documents found.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left text-gray-500">
                                    <tr>
                                        <th class="py-2 pr-4">{{ __('Jobseeker') }}</th>
                                        <th class="py-2 pr-4">{{ __('Type') }}</th>
                                        <th class="py-2 pr-4">{{ __('Status') }}</th>
                                        <th class="py-2 pr-4">{{ __('Uploaded') }}</th>
                                        <th class="py-2 pr-4">{{ __('Remarks') }}</th>
                                        <th class="py-2 pr-4 text-right">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    @foreach ($documents as $document)
                                        <tr class="border-t align-top">
                                            <td class="py-3 pr-4">
                                                <p class="font-medium">{{ $document->jobseeker->user->name ?? __('N/A') }}</p>
                                                <p class="text-xs text-gray-500">{{ $document->jobseeker->user->email ?? '' }}</p>
                                            </td>
                                            <td class="py-3 pr-4">{{ $types[$document->type] ?? Str::of($document->type)->title() }}</td>
                                            <td class="py-3 pr-4">
                                                <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                                    {{ $document->status === 'pending' ? __('Updated') : Str::of($document->status)->title() }}
                                                </span>
                                            </td>
                                            <td class="py-3 pr-4">{{ $document->created_at?->format('M d, Y') }}</td>
                                            <td class="py-3 pr-4">{{ $document->remarks ?? '-' }}</td>
                                            <td class="py-3 pr-4 text-right">
                                                <a href="{{ asset('storage/'.$document->file_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                                    {{ __('View File') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $documents->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
