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
                                            <p class="text-sm font-semibold">
                                                {{ __('Application Update') }} - {{ $data['job_title'] ?? __('Job') }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ __('Employer: :name', ['name' => $data['employer'] ?? __('Employer')]) }}
                                            </p>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $notification->created_at?->format('M d, Y H:i') }}</span>
                                    </div>

                                    @if (($data['type'] ?? '') === 'document_update_requested')
                                        <p class="mt-2 text-sm text-gray-700">
                                            {{ __('Document update requested: :type', ['type' => Str::of($data['document_type'] ?? '')->replace('_', ' ')->title()]) }}
                                        </p>
                                        @if (! empty($data['remarks']))
                                            <p class="mt-2 text-sm text-gray-600">{{ $data['remarks'] }}</p>
                                        @endif
                                    @else
                                        <p class="mt-2 text-sm text-gray-700">
                                            {{ __('Status: :status', ['status' => Str::of($data['status'] ?? '')->replace('_', ' ')->title()]) }}
                                        </p>
                                        @if (! empty($data['note']))
                                            <p class="mt-2 text-sm text-gray-600">{{ $data['note'] }}</p>
                                        @endif
                                    @endif

                                    <div class="mt-3">
                                        <a href="{{ route('jobseeker.notifications.read', $notification->id) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                            {{ ($data['type'] ?? '') === 'document_update_requested' ? __('View Documents') : __('View Application') }}
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
