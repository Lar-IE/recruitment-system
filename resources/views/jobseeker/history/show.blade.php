@php
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Application Details') }}
            </h2>
            <a href="{{ request()->query('from') === 'notifications' ? route('jobseeker.notifications') : route('jobseeker.history') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ request()->query('from') === 'notifications' ? __('Back to Notifications') : __('Back to History') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold">{{ $application->jobPost->title ?? __('N/A') }}</h3>
                        <p class="text-sm text-gray-500">{{ $application->jobPost->location ?? __('Remote') }}</p>
                    </div>

                    <div class="text-sm text-gray-600">
                        <p>{{ __('Current Status: :status', ['status' => Str::of($application->current_status)->replace('_', ' ')->title()]) }}</p>
                        <p>{{ __('Applied At: :date', ['date' => $application->applied_at?->format('M d, Y') ?? '-']) }}</p>
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
        </div>
    </div>
</x-app-layout>
