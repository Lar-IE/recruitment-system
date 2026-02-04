<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Settings') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="site_name" :value="__('Site Name')" />
                            <x-text-input id="site_name" name="site_name" class="mt-1 block w-full" :value="old('site_name', $settings['site_name'] ?? 'Recruitment System')" required />
                            <x-input-error :messages="$errors->get('site_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="support_email" :value="__('Support Email')" />
                            <x-text-input id="support_email" name="support_email" class="mt-1 block w-full" :value="old('support_email', $settings['support_email'] ?? 'support@example.com')" required />
                            <x-input-error :messages="$errors->get('support_email')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-3">
                            <input id="allow_employer_registration" name="allow_employer_registration" type="checkbox" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('allow_employer_registration', $settings['allow_employer_registration'] ?? '0') === '1' ? 'checked' : '' }}>
                            <x-input-label for="allow_employer_registration" :value="__('Allow employer self-registration')" />
                        </div>

                        <div class="flex items-center gap-3">
                            <input id="maintenance_mode" name="maintenance_mode" type="checkbox" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('maintenance_mode', $settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' }}>
                            <x-input-label for="maintenance_mode" :value="__('Enable maintenance mode')" />
                        </div>

                        <div class="flex justify-end">
                            <x-primary-button>{{ __('Save Settings') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
