<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 leading-tight">
            {{ __('Employers') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl bg-green-50 p-4 text-sm text-green-700 border border-green-100">
                {{ session('success') }}
            </div>
        @endif

        <x-ui.card>
            @if ($employers->isEmpty())
                <p class="text-sm text-gray-500">{{ __('No employers found.') }}</p>
            @else
                <x-ui.table>
                    <thead class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50">
                        <tr>
                            <th class="py-3 px-4">{{ __('Company') }}</th>
                            <th class="py-3 px-4">{{ __('Owner') }}</th>
                            <th class="py-3 px-4">{{ __('Status') }}</th>
                            <th class="py-3 px-4">{{ __('Jobseeker Directory') }}</th>
                            <th class="py-3 px-4 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @foreach ($employers as $employer)
                            <tr class="border-t hover:bg-gray-50/60">
                                <td class="py-3 px-4 font-semibold text-gray-900">{{ $employer->company_name }}</td>
                                <td class="py-3 px-4">
                                    <p class="font-medium text-gray-900">{{ $employer->user->name ?? __('N/A') }}</p>
                                    <p class="text-xs text-gray-500">{{ $employer->user->email ?? '' }}</p>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                        {{ $employer->status === 'pending' ? 'bg-amber-50 text-amber-700 ring-1 ring-amber-100' : '' }}
                                        {{ $employer->status === 'active' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : '' }}
                                        {{ $employer->status === 'suspended' ? 'bg-red-50 text-red-700 ring-1 ring-red-100' : '' }}">
                                        {{ ucfirst($employer->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                        {{ $employer->jobseeker_directory_access ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-gray-100 text-gray-700 ring-1 ring-gray-200' }}">
                                        {{ $employer->jobseeker_directory_access ? __('Enabled') : __('Disabled') }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center justify-end gap-2 flex-wrap">
                                        @if ($employer->status === 'pending')
                                            <form method="POST" action="{{ route('admin.employers.approve', $employer) }}" class="inline">
                                                @csrf
                                                <x-primary-button class="!py-1.5 !text-xs">{{ __('Approve') }}</x-primary-button>
                                            </form>
                                        @endif
                                        @if ($employer->status !== 'suspended')
                                            <form method="POST" action="{{ route('admin.employers.suspend', $employer) }}" class="inline">
                                                @csrf
                                                <x-danger-button class="!py-1.5 !text-xs">{{ __('Suspend') }}</x-danger-button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.employers.activate', $employer) }}" class="inline">
                                                @csrf
                                                <x-secondary-button type="submit" class="!py-1.5 !text-xs">{{ __('Activate') }}</x-secondary-button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.employers.jobseeker-directory-access', $employer) }}" class="inline">
                                            @csrf
                                            <x-secondary-button type="submit" class="!py-1.5 !text-xs">
                                                {{ $employer->jobseeker_directory_access ? __('Disable Directory') : __('Enable Directory') }}
                                            </x-secondary-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>
                <div class="mt-4">
                    {{ $employers->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
</x-app-layout>
