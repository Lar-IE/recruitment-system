<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Company Logo') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            @if ($isOwner)
                {{ __('Upload and manage your company logo. This logo will be displayed on all your job postings.') }}
            @else
                {{ __('View your company logo. Only the main employer can upload or update the logo.') }}
            @endif
        </p>
    </header>

    <div class="mt-6 space-y-6">
        @if ($employer->company_logo)
            <div>
                <p class="text-sm font-medium text-gray-700 mb-2">{{ __('Current Logo') }}</p>
                <img src="{{ asset('storage/' . $employer->company_logo) }}" 
                     alt="{{ $employer->company_name }}" 
                     class="h-24 w-auto object-contain border rounded-lg p-2 bg-gray-50">
            </div>
        @endif

        @if ($isOwner)
            @if ($employer->company_logo)
                <form method="POST" action="{{ route('employer.company-logo.destroy') }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <x-danger-button type="submit" onclick="return confirm('{{ __('Are you sure you want to remove the company logo?') }}')">
                        {{ __('Remove Logo') }}
                    </x-danger-button>
                </form>
            @endif

            <div @if($employer->company_logo) class="border-t pt-6" @endif>
                <form method="POST" action="{{ route('employer.company-logo.update') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="company_logo" :value="__($employer->company_logo ? 'Update Company Logo' : 'Upload Company Logo')" />
                        <input type="file" 
                               id="company_logo" 
                               name="company_logo" 
                               accept="image/jpeg,image/jpg,image/png,image/svg+xml"
                               class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer focus:outline-none focus:border-indigo-500"
                               required>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ __('Supported formats: JPG, JPEG, PNG, SVG. Maximum size: 2MB.') }}
                        </p>
                        <x-input-error class="mt-2" :messages="$errors->get('company_logo')" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Upload Logo') }}</x-primary-button>
                    </div>
                </form>
            </div>
        @else
            @if (!$employer->company_logo)
                <p class="text-sm text-gray-500">{{ __('No logo uploaded yet.') }}</p>
            @endif
            <p class="text-sm text-gray-500 italic">
                {{ __('Only the main employer can upload or update the company logo.') }}
            </p>
        @endif
    </div>
</section>
