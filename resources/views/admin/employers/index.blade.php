<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Employers') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($employers->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No employers found.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left text-gray-500">
                                    <tr>
                                        <th class="py-2 pr-4">{{ __('Company') }}</th>
                                        <th class="py-2 pr-4">{{ __('Owner') }}</th>
                                        <th class="py-2 pr-4">{{ __('Status') }}</th>
                                        <th class="py-2 pr-4 text-right">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    @foreach ($employers as $employer)
                                        <tr class="border-t">
                                            <td class="py-3 pr-4">{{ $employer->company_name }}</td>
                                            <td class="py-3 pr-4">
                                                <p class="font-medium">{{ $employer->user->name ?? __('N/A') }}</p>
                                                <p class="text-xs text-gray-500">{{ $employer->user->email ?? '' }}</p>
                                            </td>
                                            <td class="py-3 pr-4">{{ ucfirst($employer->status) }}</td>
                                            <td class="py-3 pr-4 text-right space-x-2">
                                                @if ($employer->status === 'pending')
                                                    <form method="POST" action="{{ route('admin.employers.approve', $employer) }}" class="inline">
                                                        @csrf
                                                        <x-primary-button class="text-xs">{{ __('Approve') }}</x-primary-button>
                                                    </form>
                                                @endif
                                                @if ($employer->status !== 'suspended')
                                                    <form method="POST" action="{{ route('admin.employers.suspend', $employer) }}" class="inline">
                                                        @csrf
                                                        <x-danger-button class="text-xs">{{ __('Suspend') }}</x-danger-button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('admin.employers.activate', $employer) }}" class="inline">
                                                        @csrf
                                                        <x-secondary-button type="submit" class="text-xs">{{ __('Activate') }}</x-secondary-button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $employers->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
