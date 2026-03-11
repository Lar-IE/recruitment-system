<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 leading-tight">
            {{ __('Virtual Talent Connect') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl bg-green-50 p-4 text-sm text-green-700 border border-green-100">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700 border border-red-100">
                {{ session('error') }}
            </div>
        @endif

        <x-ui.card>
            <h3 class="text-base font-semibold text-gray-900 mb-4">{{ __('Upcoming Virtual Events') }}</h3>
            @if ($events->isEmpty())
                <p class="text-sm text-gray-500">{{ __('No upcoming virtual events at this time.') }}</p>
            @else
                <div class="space-y-4">
                    @foreach ($events as $event)
                        <div class="rounded-xl border border-gray-200 bg-gray-50/30 p-4 hover:bg-gray-50/60 transition">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-900">{{ $event->title }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ $event->employer->companyProfile?->company_name ?? $event->employer->company_name ?? __('Company') }}
                                    </p>
                                    @if ($event->description)
                                        <p class="text-sm text-gray-700 mt-2">{{ \Illuminate\Support\Str::limit($event->description, 150) }}</p>
                                    @endif
                                    <div class="mt-3 flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                        <span>{{ __('Date: :date', ['date' => $event->date->format('M d, Y')]) }}</span>
                                        <span>{{ __('Time: :start - :end', ['start' => \Carbon\Carbon::createFromTimeString($event->start_time)->format('h:i A'), 'end' => \Carbon\Carbon::createFromTimeString($event->end_time)->format('h:i A')]) }}</span>
                                        <span>{{ __('Platform: :platform', ['platform' => $event->platform]) }}</span>
                                        @if ($event->registration_deadline)
                                            <span class="text-amber-600">{{ __('Registration deadline: :deadline', ['deadline' => $event->registration_deadline->format('M d, Y h:i A')]) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    @if (in_array($event->id, $registeredEventIds))
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-100">
                                            {{ __('Registered') }}
                                        </span>
                                    @endif
                                    <a href="{{ route('jobseeker.virtual-events.show', $event) }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                                        {{ __('View Details') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    {{ $events->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
</x-app-layout>
