@php
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 leading-tight">
            {{ __('Virtual Talent Connect') }}
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ __('Engage with potential candidates through virtual recruitment activities. Connect with jobseekers, conduct interviews, and participate in online hiring events.') }}
        </p>
    </x-slot>

    <div class="space-y-6">
        <x-ui.toolbar>
            <div class="flex flex-wrap items-center gap-3">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900">{{ __('Virtual Events') }}</h3>
                <span class="inline-flex rounded-lg border border-gray-200 bg-white shadow-sm isolate">
                    <a href="{{ route('employer.virtual-events.index') }}" class="relative inline-flex items-center rounded-l-lg border-0 px-3 py-2 text-sm font-medium focus:z-10 {{ !$statusFilter ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        {{ __('All') }}
                    </a>
                    <a href="{{ route('employer.virtual-events.index', ['status' => 'upcoming']) }}" class="relative -ml-px inline-flex items-center border-0 border-l border-gray-200 px-3 py-2 text-sm font-medium focus:z-10 {{ $statusFilter === 'upcoming' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        {{ __('Upcoming') }}
                    </a>
                    <a href="{{ route('employer.virtual-events.index', ['status' => 'ongoing']) }}" class="relative -ml-px inline-flex items-center border-0 border-l border-gray-200 px-3 py-2 text-sm font-medium focus:z-10 {{ $statusFilter === 'ongoing' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        {{ __('Ongoing') }}
                    </a>
                    <a href="{{ route('employer.virtual-events.index', ['status' => 'completed']) }}" class="relative -ml-px inline-flex items-center border-0 border-l border-gray-200 px-3 py-2 text-sm font-medium focus:z-10 {{ $statusFilter === 'completed' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        {{ __('Completed') }}
                    </a>
                    <a href="{{ route('employer.virtual-events.index', ['status' => 'cancelled']) }}" class="relative -ml-px inline-flex items-center rounded-r-lg border-0 border-l border-gray-200 px-3 py-2 text-sm font-medium focus:z-10 {{ $statusFilter === 'cancelled' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        {{ __('Cancelled') }}
                    </a>
                </span>
            </div>
            <a href="{{ route('employer.virtual-events.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold shadow-sm hover:bg-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                {{ __('Create Event') }}
            </a>
        </x-ui.toolbar>

        @if (session('success'))
            <div class="rounded-xl bg-green-50 p-4 text-sm text-green-700 border border-green-100">
                {{ session('success') }}
            </div>
        @endif

        <x-ui.card>
            @if ($events->isEmpty())
                <p class="text-sm text-gray-500">{{ __('No virtual events yet.') }}</p>
            @else
                <x-ui.table>
                    <thead class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50">
                        <tr>
                            <th class="py-3 px-4">{{ __('Title') }}</th>
                            <th class="py-3 px-4">{{ __('Date') }}</th>
                            <th class="py-3 px-4">{{ __('Time') }}</th>
                            <th class="py-3 px-4">{{ __('Platform') }}</th>
                            <th class="py-3 px-4">{{ __('Registrations') }}</th>
                            <th class="py-3 px-4">{{ __('Status') }}</th>
                            <th class="py-3 px-4 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @foreach ($events as $event)
                            <tr class="border-t hover:bg-gray-50/60">
                                <td class="py-3 px-4 font-semibold text-gray-900">{{ $event->title }}</td>
                                <td class="py-3 px-4">{{ $event->date->format('M d, Y') }}</td>
                                <td class="py-3 px-4">{{ \Carbon\Carbon::createFromTimeString($event->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::createFromTimeString($event->end_time)->format('h:i A') }}</td>
                                <td class="py-3 px-4">{{ $event->platform }}</td>
                                <td class="py-3 px-4">{{ $event->registrations_count }}</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                        {{ $event->status === 'upcoming' ? 'bg-blue-50 text-blue-700 ring-1 ring-blue-100' : '' }}
                                        {{ $event->status === 'ongoing' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : '' }}
                                        {{ $event->status === 'completed' ? 'bg-gray-100 text-gray-700' : '' }}
                                        {{ $event->status === 'cancelled' ? 'bg-red-50 text-red-700 ring-1 ring-red-100' : '' }}">
                                        {{ Str::of($event->status)->title() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('employer.virtual-events.show', $event) }}" class="inline-flex">
                                            <x-ui.icon-button variant="primary" size="sm" :label="__('View')">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12 18 19.5 12 19.5 2.25 12 2.25 12z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.75A3.75 3.75 0 1112 8.25a3.75 3.75 0 010 7.5z"/>
                                                </svg>
                                            </x-ui.icon-button>
                                        </a>
                                        <a href="{{ route('employer.virtual-events.edit', $event) }}" class="inline-flex">
                                            <x-ui.icon-button variant="ghost" size="sm" :label="__('Edit')">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125L16.875 4.5"/>
                                                </svg>
                                            </x-ui.icon-button>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>
                <div class="mt-4">
                    {{ $events->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
</x-app-layout>
