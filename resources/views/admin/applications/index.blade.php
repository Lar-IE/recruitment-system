<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Applications') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($applications->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No applications found.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left text-gray-500">
                                    <tr>
                                        <th class="py-2 pr-4">{{ __('Applicant') }}</th>
                                        <th class="py-2 pr-4">{{ __('Job Title') }}</th>
                                        <th class="py-2 pr-4">{{ __('Status') }}</th>
                                        <th class="py-2 pr-4">{{ __('Applied At') }}</th>
                                        <th class="py-2 pr-4">{{ __('Latest Note') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    @foreach ($applications as $application)
                                        <tr class="border-t">
                                            <td class="py-3 pr-4">
                                                <p class="font-medium">{{ $application->jobseeker->user->name ?? __('N/A') }}</p>
                                                <p class="text-xs text-gray-500">{{ $application->jobseeker->user->email ?? '' }}</p>
                                            </td>
                                            <td class="py-3 pr-4">{{ $application->jobPost->title ?? __('N/A') }}</td>
                                            <td class="py-3 pr-4">{{ ucfirst($application->current_status) }}</td>
                                            <td class="py-3 pr-4">{{ $application->applied_at?->format('M d, Y') }}</td>
                                            <td class="py-3 pr-4">
                                                @if ($application->notes->isEmpty())
                                                    <span class="text-xs text-gray-500">{{ __('No notes') }}</span>
                                                @else
                                                    <div class="space-y-2">
                                                        @foreach ($application->notes->sortByDesc('created_at') as $note)
                                                            <div class="border rounded p-2">
                                                                <p class="text-sm text-gray-700">{{ $note->note }}</p>
                                                                <p class="text-xs text-gray-500">
                                                                    {{ __('By :name on :date', ['name' => $note->creator->name ?? __('Employer'), 'date' => $note->created_at?->format('M d, Y H:i')]) }}
                                                                </p>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $applications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
