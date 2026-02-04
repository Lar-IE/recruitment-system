<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Employer Approval Pending') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 space-y-4">
                    <p class="text-lg font-semibold">{{ __('Thanks for registering!') }}</p>
                    <p class="text-sm text-gray-600">
                        {{ __('Your employer account is currently pending approval. An admin will review and activate your account soon.') }}
                    </p>
                    <p class="text-sm text-gray-600">
                        {{ __('Need help? Contact :email', ['email' => config('mail.from.address', 'support@example.com')]) }}
                    </p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-danger-button type="submit">{{ __('Log Out') }}</x-danger-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
