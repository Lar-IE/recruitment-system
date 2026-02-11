<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Company Profile') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-md bg-green-50 border border-green-200 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-4 sm:p-6">
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

                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Company Logo') }}</label>
                            @if($profile?->logo)
                                <div class="mt-2 mb-3">
                                    <img src="{{ Storage::url($profile->logo) }}" alt="Company Logo" class="h-24 w-24 object-cover rounded border border-gray-200">
                                </div>
                            @endif
                            <input name="logo" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            @error('logo') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">{{ __('Max file size: 2MB') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Cover Photo') }}</label>
                            @if($profile?->cover_photo)
                                <div class="mt-2 mb-3">
                                    <img src="{{ Storage::url($profile->cover_photo) }}" alt="Cover Photo" class="h-24 w-full object-cover rounded border border-gray-200">
                                </div>
                            @endif
                            <input name="cover_photo" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            @error('cover_photo') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">{{ __('Max file size: 5MB') }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3 pt-4 border-t border-gray-200">
                        <a href="{{ route('employer.dashboard') }}" class="text-center sm:text-left text-gray-600 hover:text-gray-900 py-2 sm:py-0">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Save Profile') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
