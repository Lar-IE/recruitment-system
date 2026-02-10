<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Company Settings') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
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

            <!-- Company Logo Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Company Logo') }}</h3>
                    
                    @if ($isOwner)
                        <div class="space-y-4">
                            @if ($employer->company_logo)
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600 mb-2">{{ __('Current Logo') }}</p>
                                    <img src="{{ asset('storage/' . $employer->company_logo) }}" 
                                         alt="{{ $employer->company_name }}" 
                                         class="h-24 w-auto object-contain border rounded-lg p-2">
                                </div>
                                
                                <form method="POST" action="{{ route('employer.company-logo.destroy') }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button type="submit" onclick="return confirm('{{ __('Are you sure you want to remove the company logo?') }}')">
                                        {{ __('Remove Logo') }}
                                    </x-danger-button>
                                </form>
                                
                                <div class="border-t pt-4 mt-4">
                                    <p class="text-sm text-gray-600 mb-2">{{ __('Update Logo') }}</p>
                            @else
                                <p class="text-sm text-gray-600 mb-4">{{ __('No logo uploaded yet.') }}</p>
                            @endif
                            
                            <form method="POST" action="{{ route('employer.company-logo.update') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="space-y-3">
                                    <div>
                                        <x-input-label for="company_logo" :value="__('Upload Company Logo')" />
                                        <input type="file" 
                                               id="company_logo" 
                                               name="company_logo" 
                                               accept="image/jpeg,image/jpg,image/png,image/svg+xml"
                                               class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer focus:outline-none focus:border-indigo-500"
                                               required>
                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ __('Supported formats: JPG, JPEG, PNG, SVG. Maximum size: 2MB.') }}
                                        </p>
                                        <x-input-error :messages="$errors->get('company_logo')" class="mt-2" />
                                    </div>
                                    <x-primary-button>
                                        {{ __('Upload Logo') }}
                                    </x-primary-button>
                                </div>
                            </form>
                            
                            @if ($employer->company_logo)
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="space-y-4">
                            @if ($employer->company_logo)
                                <div>
                                    <p class="text-sm text-gray-600 mb-2">{{ __('Current Logo') }}</p>
                                    <img src="{{ asset('storage/' . $employer->company_logo) }}" 
                                         alt="{{ $employer->company_name }}" 
                                         class="h-24 w-auto object-contain border rounded-lg p-2">
                                </div>
                            @else
                                <p class="text-sm text-gray-600">{{ __('No logo uploaded yet.') }}</p>
                            @endif
                            <p class="text-sm text-gray-500 italic">
                                {{ __('Only the main employer can upload or update the company logo.') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Company Information Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Company Information') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">{{ __('Company Name') }}</p>
                            <p class="font-medium">{{ $employer->company_name }}</p>
                        </div>
                        @if ($employer->company_email)
                            <div>
                                <p class="text-gray-600">{{ __('Email') }}</p>
                                <p class="font-medium">{{ $employer->company_email }}</p>
                            </div>
                        @endif
                        @if ($employer->phone)
                            <div>
                                <p class="text-gray-600">{{ __('Phone') }}</p>
                                <p class="font-medium">{{ $employer->phone }}</p>
                            </div>
                        @endif
                        @if ($employer->website)
                            <div>
                                <p class="text-gray-600">{{ __('Website') }}</p>
                                <p class="font-medium">{{ $employer->website }}</p>
                            </div>
                        @endif
                        @if ($employer->industry)
                            <div>
                                <p class="text-gray-600">{{ __('Industry') }}</p>
                                <p class="font-medium">{{ $employer->industry }}</p>
                            </div>
                        @endif
                        @if ($employer->company_size)
                            <div>
                                <p class="text-gray-600">{{ __('Company Size') }}</p>
                                <p class="font-medium">{{ $employer->company_size }}</p>
                            </div>
                        @endif
                        @if ($employer->address)
                            <div class="md:col-span-2">
                                <p class="text-gray-600">{{ __('Address') }}</p>
                                <p class="font-medium">{{ $employer->address }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
