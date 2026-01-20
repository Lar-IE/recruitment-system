<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Digital IDs') }}
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
                    @if ($digitalIds->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No digital IDs found.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left text-gray-500">
                                    <tr>
                                        <th class="py-2 pr-4">{{ __('Jobseeker') }}</th>
                                        <th class="py-2 pr-4">{{ __('Employer') }}</th>
                                        <th class="py-2 pr-4">{{ __('Job Title') }}</th>
                                        <th class="py-2 pr-4">{{ __('Status') }}</th>
                                        <th class="py-2 pr-4">{{ __('Issued') }}</th>
                                        <th class="py-2 pr-4 text-right">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    @foreach ($digitalIds as $digitalId)
                                        <tr class="border-t">
                                            <td class="py-2 pr-4">{{ $digitalId->jobseeker->user->name ?? __('N/A') }}</td>
                                            <td class="py-2 pr-4">{{ $digitalId->employer->company_name ?? ($digitalId->employer->user->name ?? __('N/A')) }}</td>
                                            <td class="py-2 pr-4">{{ $digitalId->job_title }}</td>
                                            <td class="py-2 pr-4">
                                                <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                                    {{ ucfirst($digitalId->status) }}
                                                </span>
                                            </td>
                                            <td class="py-2 pr-4">{{ $digitalId->issue_date?->format('M d, Y') }}</td>
                                            <td class="py-2 pr-4 text-right space-x-2">
                                                <a href="{{ route('admin.digital-ids.show', $digitalId) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">{{ __('Preview') }}</a>
                                                @if ($digitalId->status === 'active')
                                                    <form method="POST" action="{{ route('admin.digital-ids.revoke', $digitalId) }}" class="inline">
                                                        @csrf
                                                        <x-danger-button class="text-xs">{{ __('Revoke') }}</x-danger-button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $digitalIds->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
