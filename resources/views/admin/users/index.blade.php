<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 leading-tight">
            {{ __('Users') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl bg-green-50 p-4 text-sm text-green-700 border border-green-100">
                {{ session('success') }}
            </div>
        @endif

        <x-ui.card>
            @if ($users->isEmpty())
                <p class="text-sm text-gray-500">{{ __('No users found.') }}</p>
            @else
                <x-ui.table>
                    <thead class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50">
                        <tr>
                            <th class="py-3 px-4">{{ __('Name') }}</th>
                            <th class="py-3 px-4">{{ __('Email') }}</th>
                            <th class="py-3 px-4">{{ __('Role') }}</th>
                            <th class="py-3 px-4">{{ __('Status') }}</th>
                            <th class="py-3 px-4 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @foreach ($users as $user)
                            <tr class="border-t hover:bg-gray-50/60">
                                <td class="py-3 px-4 font-semibold text-gray-900">{{ $user->name }}</td>
                                <td class="py-3 px-4">{{ $user->email }}</td>
                                <td class="py-3 px-4">
                                    <form method="POST" action="{{ route('admin.users.update-role', $user) }}">
                                        @csrf
                                        <select name="role" class="text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" onchange="this.form.submit()">
                                            @foreach ($roles as $value => $label)
                                                <option value="{{ $value }}" {{ $user->role?->value === $value ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-amber-50 text-amber-700 ring-1 ring-amber-100' }}">
                                        {{ $user->is_active ? __('Active') : __('Suspended') }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                                            @csrf
                                            <x-ui.icon-button variant="{{ $user->is_active ? 'danger' : 'primary' }}" size="sm" :label="$user->is_active ? __('Suspend') : __('Activate')">
                                                @if ($user->is_active)
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728"/>
                                                    </svg>
                                                @else
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                                    </svg>
                                                @endif
                                            </x-ui.icon-button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" onsubmit="return confirm('Reset password for {{ $user->email }}?')">
                                            @csrf
                                            <x-ui.icon-button variant="ghost" size="sm" :label="__('Reset Password')">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992h.001m0 0a8.25 8.25 0 0113.862-5.633l.154.162m-14.017 5.471a8.25 8.25 0 0013.862 5.633l.154-.162m0 0v4.992h-.001m0 0h-4.992"/>
                                                </svg>
                                            </x-ui.icon-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
</x-app-layout>
