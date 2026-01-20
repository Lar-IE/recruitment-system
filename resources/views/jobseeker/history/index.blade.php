@php
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('History') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($applications->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No applications yet.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left text-gray-500">
                                    <tr>
                                        <th class="py-2 pr-4">{{ __('Job Title') }}</th>
                                        <th class="py-2 pr-4">{{ __('Status') }}</th>
                                        <th class="py-2 pr-4">{{ __('Applied At') }}</th>
                                        <th class="py-2 pr-4 text-right">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    @foreach ($applications as $application)
                                        <tr class="border-t">
                                            <td class="py-2 pr-4 font-medium">{{ $application->jobPost->title ?? __('N/A') }}</td>
                                            <td class="py-2 pr-4">
                                                {{ Str::of($application->current_status)->replace('_', ' ')->title() }}
                                            </td>
                                            <td class="py-2 pr-4">{{ $application->applied_at?->format('M d, Y') }}</td>
                                            <td class="py-2 pr-4 text-right">
                                                <a href="{{ route('jobseeker.history.show', $application) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                                    {{ __('View Details') }}
                                                </a>
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
