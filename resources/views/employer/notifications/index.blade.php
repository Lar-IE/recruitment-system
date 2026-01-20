@php
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Notifications') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($notifications->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No notifications yet.') }}</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($notifications as $notification)
                                @php($data = $notification->data)
                                <div class="border rounded-lg p-4 {{ $notification->read_at ? 'bg-white' : 'bg-indigo-50' }}">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            @if (($data['type'] ?? '') === 'application_submitted')
                                                <p class="text-sm font-semibold">
                                                    {{ __('New application') }} - {{ $data['job_title'] ?? __('Job') }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ __('Applicant: :name', ['name' => $data['applicant'] ?? __('Applicant')]) }}
                                                </p>
                                            @else
                                                <p class="text-sm font-semibold">
                                                    {{ __('Document updated') }} - {{ Str::of($data['document_type'] ?? '')->upper() }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ __('Applicant: :name', ['name' => $data['jobseeker'] ?? __('Applicant')]) }}
                                                </p>
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $notification->created_at?->format('M d, Y H:i') }}</span>
                                    </div>

                                    <div class="mt-3">
                                        <a href="{{ route('employer.notifications.read', $notification->id) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                            {{ __('View') }}
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
