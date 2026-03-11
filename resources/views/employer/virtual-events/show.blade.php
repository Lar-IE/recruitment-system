@php
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 leading-tight">
            {{ $event->title }}
        </h2>
    </x-slot>

    <div class="space-y-6 max-w-4xl">
        @if (session('success'))
            <div class="rounded-xl bg-green-50 p-4 text-sm text-green-700 border border-green-100">
                {{ session('success') }}
            </div>
        @endif

        <x-ui.card>
            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                        {{ $event->status === 'upcoming' ? 'bg-blue-50 text-blue-700 ring-1 ring-blue-100' : '' }}
                        {{ $event->status === 'ongoing' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : '' }}
                        {{ $event->status === 'completed' ? 'bg-gray-100 text-gray-700' : '' }}
                        {{ $event->status === 'cancelled' ? 'bg-red-50 text-red-700 ring-1 ring-red-100' : '' }}">
                        {{ Str::of($event->status)->title() }}
                    </span>
                    <span class="text-sm text-gray-500">{{ __('Date: :date', ['date' => $event->date->format('M d, Y')]) }}</span>
                    <span class="text-sm text-gray-500">{{ __('Time: :start - :end', ['start' => \Carbon\Carbon::createFromTimeString($event->start_time)->format('h:i A'), 'end' => \Carbon\Carbon::createFromTimeString($event->end_time)->format('h:i A')]) }}</span>
                    <span class="text-sm text-gray-500">{{ __('Platform: :platform', ['platform' => $event->platform]) }}</span>
                </div>

                @if ($event->description)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('Description') }}</h3>
                        <p class="mt-1 text-sm text-gray-700 whitespace-pre-line">{{ $event->description }}</p>
                    </div>
                @endif

                <div>
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Meeting Link') }}</h3>
                    <p class="mt-1 text-sm text-gray-700 break-all">{{ $event->meeting_link }}</p>
                </div>

                @if ($event->registration_deadline)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('Registration Deadline') }}</h3>
                        <p class="mt-1 text-sm text-gray-700">{{ $event->registration_deadline->format('M d, Y h:i A') }}</p>
                    </div>
                @endif

                <div class="flex flex-wrap items-center gap-3 pt-2 border-t border-gray-100">
                    <a href="{{ route('employer.virtual-events.edit', $event) }}" class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                        {{ __('Edit') }}
                    </a>
                    @if (!$event->isCancelled() && !$event->isCompleted())
                        <form method="POST" action="{{ route('employer.virtual-events.cancel', $event) }}" onsubmit="return confirm('{{ __('Cancel this virtual event? Registered jobseekers will no longer be able to join.') }}')" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg border border-red-300 bg-white text-sm font-semibold text-red-700 shadow-sm hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                                {{ __('Cancel Event') }}
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('employer.virtual-events.destroy', $event) }}" onsubmit="return confirm('{{ __('Delete this virtual event?') }}')" class="inline">
                        @csrf
                        @method('DELETE')
                        <x-danger-button>{{ __('Delete') }}</x-danger-button>
                    </form>
                    <a href="{{ route('employer.virtual-events.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                        {{ __('Back to Virtual Events') }}
                    </a>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h3 class="text-base font-semibold text-gray-900 mb-4">{{ __('Registered Jobseekers') }} ({{ $event->registrations->count() }})</h3>
            @if ($event->registrations->isEmpty())
                <p class="text-sm text-gray-500">{{ __('No registrations yet.') }}</p>
            @else
                <div class="space-y-3">
                    @foreach ($event->registrations as $registration)
                        <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $registration->jobseeker->full_name ?: $registration->jobseeker->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $registration->jobseeker->user->email }}</p>
                                </div>
                                <span class="text-sm text-gray-500">{{ __('Registered: :date', ['date' => $registration->registered_at->format('M d, Y h:i A')]) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>
    </div>
</x-app-layout>
