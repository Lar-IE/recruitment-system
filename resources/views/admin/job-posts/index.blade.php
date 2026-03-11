<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 leading-tight">
            {{ __('Job Posts') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <x-ui.card>
            <p class="text-sm text-gray-600">{{ __('Job post approvals and status controls will appear here.') }}</p>
        </x-ui.card>
    </div>
</x-app-layout>
