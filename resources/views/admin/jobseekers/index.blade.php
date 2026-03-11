<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 leading-tight">
            {{ __('Jobseekers') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl bg-green-50 p-4 text-sm text-green-700 border border-green-100">
                {{ session('success') }}
            </div>
        @endif

        <x-ui.card>
            @if ($jobseekers->isEmpty())
                <p class="text-sm text-gray-500">{{ __('No jobseekers found.') }}</p>
            @else
                <x-ui.table>
                    <thead class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50">
                        <tr>
                            <th class="py-3 px-4">{{ __('Jobseeker') }}</th>
                            <th class="py-3 px-4">{{ __('Status') }}</th>
                            <th class="py-3 px-4 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @foreach ($jobseekers as $jobseeker)
                            <tr class="border-t hover:bg-gray-50/60">
                                <td class="py-3 px-4">
                                    <p class="font-semibold text-gray-900">{{ $jobseeker->user->name ?? __('N/A') }}</p>
                                    <p class="text-xs text-gray-500">{{ $jobseeker->user->email ?? '' }}</p>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                        {{ $jobseeker->status === 'active' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-red-50 text-red-700 ring-1 ring-red-100' }}">
                                        {{ ucfirst($jobseeker->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center justify-end gap-2">
                                        @if ($jobseeker->status === 'active')
                                            <form method="POST" action="{{ route('admin.jobseekers.suspend', $jobseeker) }}" class="inline">
                                                @csrf
                                                <x-danger-button class="!py-1.5 !text-xs">{{ __('Suspend') }}</x-danger-button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.jobseekers.activate', $jobseeker) }}" class="inline">
                                                @csrf
                                                <x-secondary-button type="submit" class="!py-1.5 !text-xs">{{ __('Activate') }}</x-secondary-button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>
                <div class="mt-4">
                    {{ $jobseekers->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
</x-app-layout>
