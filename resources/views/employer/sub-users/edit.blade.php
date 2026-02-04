<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Employer Sub-User') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6">
                <form method="POST" action="{{ route('employer.sub-users.update', $subUser) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
                        <input name="name" value="{{ old('name', $subUser->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                        <input name="email" type="email" value="{{ old('email', $subUser->email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>
                        <input name="password" type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="{{ __('Leave blank to keep current') }}">
                        @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Role') }}</label>
                        <select name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            @foreach ($roles as $value => $label)
                                <option value="{{ $value }}" @selected(old('role', $subUser->role?->value) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('role') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            <option value="active" @selected(old('status', $subUser->status) === 'active')>{{ __('Active') }}</option>
                            <option value="inactive" @selected(old('status', $subUser->status) === 'inactive')>{{ __('Inactive') }}</option>
                        </select>
                        @error('status') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('employer.sub-users.index') }}" class="text-gray-600 hover:text-gray-900">
                            {{ __('Cancel') }}
                        </a>
                        <button class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                            {{ __('Save Changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
