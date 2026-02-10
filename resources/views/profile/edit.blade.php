<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-md bg-red-50 p-4">
                    <ul class="list-disc list-inside text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            @if ($user->role?->value === 'jobseeker')
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-3xl">
                        @include('profile.partials.jobseeker-resume-form', ['jobseeker' => $jobseeker])
                    </div>
                </div>
            @endif

            @if (($user->role?->value === 'employer' || $user instanceof \App\Models\EmployerSubUser) && $employer)
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.employer-company-logo-form', ['employer' => $employer, 'isOwner' => $isOwner])
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Company Information') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Your company details as registered in the system.') }}
                            </p>
                        </header>

                        <div class="mt-6 grid grid-cols-1 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">{{ __('Company Name') }}</p>
                                <p class="font-medium text-gray-900">{{ $employer->company_name }}</p>
                            </div>
                            @if ($employer->company_email)
                                <div>
                                    <p class="text-gray-600">{{ __('Company Email') }}</p>
                                    <p class="font-medium text-gray-900">{{ $employer->company_email }}</p>
                                </div>
                            @endif
                            @if ($employer->phone)
                                <div>
                                    <p class="text-gray-600">{{ __('Phone') }}</p>
                                    <p class="font-medium text-gray-900">{{ $employer->phone }}</p>
                                </div>
                            @endif
                            @if ($employer->website)
                                <div>
                                    <p class="text-gray-600">{{ __('Website') }}</p>
                                    <p class="font-medium text-gray-900">{{ $employer->website }}</p>
                                </div>
                            @endif
                            @if ($employer->industry)
                                <div>
                                    <p class="text-gray-600">{{ __('Industry') }}</p>
                                    <p class="font-medium text-gray-900">{{ $employer->industry }}</p>
                                </div>
                            @endif
                            @if ($employer->company_size)
                                <div>
                                    <p class="text-gray-600">{{ __('Company Size') }}</p>
                                    <p class="font-medium text-gray-900">{{ $employer->company_size }}</p>
                                </div>
                            @endif
                            @if ($employer->address)
                                <div>
                                    <p class="text-gray-600">{{ __('Address') }}</p>
                                    <p class="font-medium text-gray-900">{{ $employer->address }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
