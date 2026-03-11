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

        @if ($errors->any())
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700 border border-red-100">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <x-ui.card>
            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-3 text-sm">
                    <span class="font-semibold text-gray-900">
                        {{ $event->employer->companyProfile?->company_name ?? $event->employer->company_name ?? __('Company') }}
                    </span>
                    <span class="text-gray-400">·</span>
                    <span class="text-gray-500">{{ __('Date: :date', ['date' => $event->date->format('M d, Y')]) }}</span>
                    <span class="text-gray-400">·</span>
                    <span class="text-gray-500">{{ __('Time: :start - :end', ['start' => \Carbon\Carbon::createFromTimeString($event->start_time)->format('h:i A'), 'end' => \Carbon\Carbon::createFromTimeString($event->end_time)->format('h:i A')]) }}</span>
                    <span class="text-gray-400">·</span>
                    <span class="text-gray-500">{{ __('Platform: :platform', ['platform' => $event->platform]) }}</span>
                </div>

                @if ($event->description)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('Description') }}</h3>
                        <p class="mt-1 text-sm text-gray-700 whitespace-pre-line">{{ $event->description }}</p>
                    </div>
                @endif

                @if ($event->registration_deadline)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('Registration Deadline') }}</h3>
                        <p class="mt-1 text-sm text-gray-700">{{ $event->registration_deadline->format('M d, Y h:i A') }}</p>
                    </div>
                @endif

                @if ($isRegistered)
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                        <p class="text-sm font-semibold text-emerald-800">{{ __('You are registered for this event.') }}</p>
                        @if ($meetingLinkAvailable && !empty($event->meeting_link))
                            <div class="mt-3">
                                <h4 class="text-sm font-semibold text-emerald-900 mb-2">{{ __('Event / Meeting Link') }}</h4>
                                <a href="{{ $event->meeting_link }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg {{ $canJoin ? 'bg-emerald-600 text-white hover:bg-emerald-500' : 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' }} text-sm font-semibold shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                                    {{ $canJoin ? __('Join Now') : __('View Event Link') }}
                                </a>
                                @if (!$canJoin)
                                    <p class="text-sm text-emerald-700 mt-2">{{ __('Event starts soon. Save this link to join.') }}</p>
                                @endif
                            </div>
                        @elseif (!empty($event->meeting_link))
                            <p class="text-sm text-emerald-700 mt-2">{{ __('The meeting link will be available 1 hour before the event starts.') }}</p>
                        @else
                            <p class="text-sm text-emerald-700 mt-2">{{ __('The meeting link will be available 1 hour before the event starts.') }}</p>
                        @endif
                    </div>
                @else
                    @if ($canRegister)
                        <form method="POST" action="{{ route('jobseeker.virtual-events.register', $event) }}" class="pt-2">
                            @csrf
                            <x-primary-button>{{ __('Register for this Event') }}</x-primary-button>
                        </form>
                    @else
                        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                            <p class="text-sm text-amber-800">
                                @if ($event->isCancelled())
                                    {{ __('This event has been cancelled.') }}
                                @elseif ($event->isCompleted())
                                    {{ __('This event has been completed.') }}
                                @elseif ($event->registration_deadline && now()->isAfter($event->registration_deadline))
                                    {{ __('Registration deadline has passed.') }}
                                @else
                                    {{ __('Registration is not available at this time.') }}
                                @endif
                            </p>
                        </div>
                    @endif
                @endif

                <div class="pt-2 border-t border-gray-100">
                    <a href="{{ route('jobseeker.virtual-events.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                        {{ __('Back to Virtual Events') }}
                    </a>
                </div>
            </div>
        </x-ui.card>
    </div>
</x-app-layout>
