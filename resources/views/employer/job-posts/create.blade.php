<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Job Post') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('employer.job-posts.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="title" :value="__('Job Title')" />
                            <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title')" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="location" :value="__('Location')" />
                                <x-text-input id="location" name="location" class="mt-1 block w-full" :value="old('location')" />
                                <x-input-error :messages="$errors->get('location')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="job_type" :value="__('Job Type')" />
                                <select id="job_type" name="job_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="" disabled {{ old('job_type') ? '' : 'selected' }}>{{ __('Select type') }}</option>
                                    @foreach ($jobTypes as $key => $label)
                                        <option value="{{ $key }}" {{ old('job_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('job_type')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="5" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="responsibilities" :value="__('Responsibilities')" />
                            <textarea id="responsibilities" name="responsibilities" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('responsibilities') }}</textarea>
                            <x-input-error :messages="$errors->get('responsibilities')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="requirements" :value="__('Requirements')" />
                            <textarea id="requirements" name="requirements" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('requirements') }}</textarea>
                            <x-input-error :messages="$errors->get('requirements')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="salary_min" :value="__('Salary Min')" />
                                <x-text-input id="salary_min" name="salary_min" type="number" step="0.01" class="mt-1 block w-full" :value="old('salary_min')" />
                                <x-input-error :messages="$errors->get('salary_min')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="salary_max" :value="__('Salary Max')" />
                                <x-text-input id="salary_max" name="salary_max" type="number" step="0.01" class="mt-1 block w-full" :value="old('salary_max')" />
                                <x-input-error :messages="$errors->get('salary_max')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="currency" :value="__('Currency')" />
                                <x-text-input id="currency" name="currency" class="mt-1 block w-full" :value="old('currency', 'PHP')" />
                                <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="application_deadline" :value="__('Application Deadline')" />
                                <x-text-input id="application_deadline" name="application_deadline" type="date" class="mt-1 block w-full" :value="old('application_deadline')" />
                                <x-input-error :messages="$errors->get('application_deadline')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="draft" {{ old('status', 'published') === 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                                    <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>{{ __('Published') }}</option>
                                    <option value="closed" {{ old('status', 'published') === 'closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">{{ __('Published jobs are visible to jobseekers.') }}</p>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('employer.job-posts.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Cancel') }}</a>
                            <x-primary-button>{{ __('Save Job Post') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
