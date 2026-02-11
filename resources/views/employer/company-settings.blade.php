@php
    use Illuminate\Support\Facades\Storage;
@endphp
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

            <!-- Company Profile Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Company Profile') }}</h3>
                    
                    @if ($isOwner)
                        <form method="POST" action="{{ route('employer.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">{{ __('Company Name') }}</label>
                                    <input name="company_name" value="{{ old('company_name', $profile?->company_name ?? $employer->company_name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    @error('company_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">{{ __('Description') }}</label>
                                    <textarea name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $profile?->description) }}</textarea>
                                    @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                    <p class="mt-1 text-xs text-gray-500">{{ __('Tell jobseekers about your company') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ __('Industry') }}</label>
                                    <input name="industry" value="{{ old('industry', $profile?->industry ?? $employer->industry) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('industry') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ __('Company Size') }}</label>
                                    <select name="company_size" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">{{ __('Select size') }}</option>
                                        <option value="1-10" @selected(old('company_size', $profile?->company_size) === '1-10')>1-10 {{ __('employees') }}</option>
                                        <option value="11-50" @selected(old('company_size', $profile?->company_size) === '11-50')>11-50 {{ __('employees') }}</option>
                                        <option value="51-200" @selected(old('company_size', $profile?->company_size) === '51-200')>51-200 {{ __('employees') }}</option>
                                        <option value="201-500" @selected(old('company_size', $profile?->company_size) === '201-500')>201-500 {{ __('employees') }}</option>
                                        <option value="501-1000" @selected(old('company_size', $profile?->company_size) === '501-1000')>501-1000 {{ __('employees') }}</option>
                                        <option value="1000+" @selected(old('company_size', $profile?->company_size) === '1000+')>1000+ {{ __('employees') }}</option>
                                    </select>
                                    @error('company_size') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ __('Year Established') }}</label>
                                    <input name="year_established" type="number" min="1800" max="{{ date('Y') }}" value="{{ old('year_established', $profile?->year_established) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('year_established') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ __('Website') }}</label>
                                    <input name="website" type="url" value="{{ old('website', $profile?->website ?? $employer->website) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://example.com">
                                    @error('website') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ __('Contact Email') }}</label>
                                    <input name="contact_email" type="email" value="{{ old('contact_email', $profile?->contact_email ?? $employer->company_email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('contact_email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ __('Contact Number') }}</label>
                                    <input name="contact_number" type="tel" value="{{ old('contact_number', $profile?->contact_number ?? $employer->phone) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('contact_number') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">{{ __('Address') }}</label>
                                    <textarea name="address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $profile?->address ?? $employer->address) }}</textarea>
                                    @error('address') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3 pt-4 border-t border-gray-200">
                                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ __('Save Company Profile') }}
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">{{ __('Company Name') }}</p>
                                <p class="font-medium">{{ $profile?->company_name ?? $employer->company_name }}</p>
                            </div>
                            @if ($profile?->industry || $employer->industry)
                                <div>
                                    <p class="text-gray-600">{{ __('Industry') }}</p>
                                    <p class="font-medium">{{ $profile?->industry ?? $employer->industry }}</p>
                                </div>
                            @endif
                            @if ($profile?->company_size)
                                <div>
                                    <p class="text-gray-600">{{ __('Company Size') }}</p>
                                    <p class="font-medium">{{ $profile->company_size }}</p>
                                </div>
                            @endif
                            @if ($profile?->year_established)
                                <div>
                                    <p class="text-gray-600">{{ __('Year Established') }}</p>
                                    <p class="font-medium">{{ $profile->year_established }}</p>
                                </div>
                            @endif
                            @if ($profile?->website || $employer->website)
                                <div>
                                    <p class="text-gray-600">{{ __('Website') }}</p>
                                    <p class="font-medium">{{ $profile?->website ?? $employer->website }}</p>
                                </div>
                            @endif
                            @if ($profile?->contact_email || $employer->company_email)
                                <div>
                                    <p class="text-gray-600">{{ __('Contact Email') }}</p>
                                    <p class="font-medium">{{ $profile?->contact_email ?? $employer->company_email }}</p>
                                </div>
                            @endif
                            @if ($profile?->contact_number || $employer->phone)
                                <div>
                                    <p class="text-gray-600">{{ __('Contact Number') }}</p>
                                    <p class="font-medium">{{ $profile?->contact_number ?? $employer->phone }}</p>
                                </div>
                            @endif
                            @if ($profile?->address || $employer->address)
                                <div class="md:col-span-2">
                                    <p class="text-gray-600">{{ __('Address') }}</p>
                                    <p class="font-medium">{{ $profile?->address ?? $employer->address }}</p>
                                </div>
                            @endif
                            @if ($profile?->description)
                                <div class="md:col-span-2">
                                    <p class="text-gray-600">{{ __('Description') }}</p>
                                    <p class="font-medium">{{ $profile->description }}</p>
                                </div>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 italic mt-4">
                            {{ __('Only the main employer can edit the company profile.') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
