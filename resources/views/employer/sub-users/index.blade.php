<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Employer Sub-Users') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">{{ __('Team Members') }}</h3>
                <a href="{{ route('employer.sub-users.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                    {{ __('Add Sub-User') }}
                </a>
            </div>

            @if (session('success'))
                <div class="rounded-md bg-green-50 border border-green-200 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">{{ __('Name') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ __('Email') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ __('Role') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($subUsers as $subUser)
                            <tr>
                                <td class="px-4 py-3 text-gray-800">{{ $subUser->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $subUser->email }}</td>
                                <td class="px-4 py-3 text-gray-600 capitalize">{{ $subUser->role?->value }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $subUser->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($subUser->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="{{ route('employer.sub-users.edit', $subUser) }}" class="text-indigo-600 hover:text-indigo-800">
                                        {{ __('Edit') }}
                                    </a>
                                    <form action="{{ route('employer.sub-users.toggle-status', $subUser) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="text-gray-600 hover:text-gray-900">
                                            {{ $subUser->status === 'active' ? __('Deactivate') : __('Activate') }}
                                        </button>
                                    </form>
                                    <form action="{{ route('employer.sub-users.destroy', $subUser) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Delete this sub-user?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:text-red-800">
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                    {{ __('No sub-users found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $subUsers->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
