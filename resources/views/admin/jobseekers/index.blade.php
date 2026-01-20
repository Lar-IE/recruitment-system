<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jobseekers') }}
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
                    @if ($jobseekers->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No jobseekers found.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left text-gray-500">
                                    <tr>
                                        <th class="py-2 pr-4">{{ __('Jobseeker') }}</th>
                                        <th class="py-2 pr-4">{{ __('Status') }}</th>
                                        <th class="py-2 pr-4 text-right">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    @foreach ($jobseekers as $jobseeker)
                                        <tr class="border-t">
                                            <td class="py-3 pr-4">
                                                <p class="font-medium">{{ $jobseeker->user->name ?? __('N/A') }}</p>
                                                <p class="text-xs text-gray-500">{{ $jobseeker->user->email ?? '' }}</p>
                                            </td>
                                            <td class="py-3 pr-4">{{ ucfirst($jobseeker->status) }}</td>
                                            <td class="py-3 pr-4 text-right">
                                                @if ($jobseeker->status === 'active')
                                                    <form method="POST" action="{{ route('admin.jobseekers.suspend', $jobseeker) }}">
                                                        @csrf
                                                        <x-danger-button class="text-xs">{{ __('Suspend') }}</x-danger-button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('admin.jobseekers.activate', $jobseeker) }}">
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
                            {{ $jobseekers->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
