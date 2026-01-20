<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Users') }}
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
                    @if ($users->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No users found.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left text-gray-500">
                                    <tr>
                                        <th class="py-2 pr-4">{{ __('Name') }}</th>
                                        <th class="py-2 pr-4">{{ __('Email') }}</th>
                                        <th class="py-2 pr-4">{{ __('Role') }}</th>
                                        <th class="py-2 pr-4">{{ __('Status') }}</th>
                                        <th class="py-2 pr-4 text-right">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    @foreach ($users as $user)
                                        <tr class="border-t">
                                            <td class="py-3 pr-4">{{ $user->name }}</td>
                                            <td class="py-3 pr-4">{{ $user->email }}</td>
                                            <td class="py-3 pr-4">
                                                <form method="POST" action="{{ route('admin.users.update-role', $user) }}">
                                                    @csrf
                                                    <select name="role" class="text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" onchange="this.form.submit()">
                                                        @foreach ($roles as $value => $label)
                                                            <option value="{{ $value }}" {{ $user->role?->value === $value ? 'selected' : '' }}>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            </td>
                                            <td class="py-3 pr-4">
                                                <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                                    {{ $user->is_active ? __('Active') : __('Suspended') }}
                                                </span>
                                            </td>
                                            <td class="py-3 pr-4 text-right">
                                                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                                                    @csrf
                                                    <x-secondary-button type="submit" class="text-xs">
                                                        {{ $user->is_active ? __('Suspend') : __('Activate') }}
                                                    </x-secondary-button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
