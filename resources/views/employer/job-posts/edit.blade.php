<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Job Post') }}
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
                    <form method="POST" action="{{ route('employer.job-posts.update', $jobPost) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="title" :value="__('Job Title')" />
                            <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title', $jobPost->title)" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="location" :value="__('Location')" />
                                <x-text-input id="location" name="location" class="mt-1 block w-full" :value="old('location', $jobPost->location)" />
                                <x-input-error :messages="$errors->get('location')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="job_type" :value="__('Job Type')" />
                                <select id="job_type" name="job_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    @foreach ($jobTypes as $key => $label)
                                        <option value="{{ $key }}" {{ old('job_type', $jobPost->job_type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('job_type')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="5" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('description', $jobPost->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="responsibilities" :value="__('Responsibilities')" />
                            <textarea id="responsibilities" name="responsibilities" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('responsibilities', $jobPost->responsibilities) }}</textarea>
                            <x-input-error :messages="$errors->get('responsibilities')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="benefits" :value="__('Benefits')" />
                            <textarea id="benefits" name="benefits" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('benefits', $jobPost->benefits) }}</textarea>
                            <x-input-error :messages="$errors->get('benefits')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="requirements" :value="__('Requirements')" />
                            <textarea id="requirements" name="requirements" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('requirements', $jobPost->requirements) }}</textarea>
                            <x-input-error :messages="$errors->get('requirements')" class="mt-2" />
                        </div>

                        @php
                            $existingSkills = old('required_skills', $jobPost->requiredSkills->map(fn ($s) => [
                                'skill_name' => $s->skill_name,
                                'weight' => $s->weight,
                                'min_proficiency' => $s->min_proficiency,
                            ])->toArray());
                            $skillsInput = array_values(is_array($existingSkills) ? $existingSkills : []);
                            if (count($skillsInput) === 0) {
                                $skillsInput[] = ['skill_name' => '', 'weight' => 1, 'min_proficiency' => ''];
                            }
                            if (count($skillsInput) < 5) {
                                $skillsInput = array_merge(
                                    $skillsInput,
                                    array_fill(0, 5 - count($skillsInput), ['skill_name' => '', 'weight' => 1, 'min_proficiency' => ''])
                                );
                            }
                        @endphp
                        <div>
                            <x-input-label :value="__('Required Skills (for candidate matching)')" />
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Specify skills and importance (1–3): 1 = not important, 2 = important, 3 = very important. Candidates with matching skills will be ranked by proficiency.') }}
                            </p>
                            @foreach ($skillsInput as $index => $skill)
                                <div class="mt-3 flex flex-wrap gap-3 items-end p-3 rounded-lg border border-gray-200 bg-gray-50/50">
                                    <div class="flex-1 min-w-[140px]">
                                        <input type="text" name="required_skills[{{ $index }}][skill_name]" value="{{ $skill['skill_name'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="{{ __('Skill name') }}" />
                                    </div>
                                    <div class="w-24">
                                        <input
                                            type="number"
                                            name="required_skills[{{ $index }}][weight]"
                                            value="{{ $skill['weight'] ?? 1 }}"
                                            min="1"
                                            max="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                            placeholder="{{ __('1–3') }}"
                                            title="{{ __('1 = not important, 2 = important, 3 = very important') }}"
                                        />
                                    </div>
                                    <div class="w-28">
                                        <input type="number" name="required_skills[{{ $index }}][min_proficiency]" value="{{ $skill['min_proficiency'] ?? '' }}" min="0" max="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="{{ __('Min %') }}" title="{{ __('Optional min proficiency %') }}" />
                                    </div>
                                </div>
                            @endforeach
                            <p class="mt-2 text-xs text-gray-500">{{ __('Up to 5 skills can be entered.') }}</p>
                        </div>

                        @php
                            $salaryType = old('salary_type', $jobPost->salary_type ?? 'salary_range');
                        @endphp
                        <div class="space-y-4" x-data="{ salaryType: '{{ $salaryType }}' }">
                            <div>
                                <x-input-label for="salary_type" :value="__('Salary Type')" />
                                <select id="salary_type" name="salary_type" x-model="salaryType" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="daily_rate" {{ $salaryType === 'daily_rate' ? 'selected' : '' }}>{{ __('Daily Rate') }}</option>
                                    <option value="fixed" {{ $salaryType === 'fixed' ? 'selected' : '' }}>{{ __('Fixed') }}</option>
                                    <option value="salary_range" {{ $salaryType === 'salary_range' ? 'selected' : '' }}>{{ __('Salary Range') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('salary_type')" class="mt-2" />
                            </div>
                            <div x-show="salaryType === 'daily_rate'" x-cloak>
                                <x-input-label for="salary_daily" :value="__('Rate per Day')" />
                                <x-text-input id="salary_daily" name="salary_daily" type="number" step="0.01" class="mt-1 block w-full" :value="old('salary_daily', $jobPost->salary_daily)" />
                                <x-input-error :messages="$errors->get('salary_daily')" class="mt-2" />
                            </div>
                            <div x-show="salaryType === 'fixed'" x-cloak>
                                <x-input-label for="salary_monthly" :value="__('Monthly Rate')" />
                                <x-text-input id="salary_monthly" name="salary_monthly" type="number" step="0.01" class="mt-1 block w-full" :value="old('salary_monthly', $jobPost->salary_monthly)" />
                                <x-input-error :messages="$errors->get('salary_monthly')" class="mt-2" />
                            </div>
                            <div x-show="salaryType === 'salary_range'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="salary_min" :value="__('Salary Min')" />
                                    <x-text-input id="salary_min" name="salary_min" type="number" step="0.01" class="mt-1 block w-full" :value="old('salary_min', $jobPost->salary_min)" />
                                    <x-input-error :messages="$errors->get('salary_min')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="salary_max" :value="__('Salary Max')" />
                                    <x-text-input id="salary_max" name="salary_max" type="number" step="0.01" class="mt-1 block w-full" :value="old('salary_max', $jobPost->salary_max)" />
                                    <x-input-error :messages="$errors->get('salary_max')" class="mt-2" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="currency" :value="__('Currency')" />
                                <x-text-input id="currency" name="currency" class="mt-1 block w-full max-w-xs" :value="old('currency', $jobPost->currency)" />
                                <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="application_deadline" :value="__('Application Deadline')" />
                                <x-text-input id="application_deadline" name="application_deadline" type="date" class="mt-1 block w-full" :value="old('application_deadline', optional($jobPost->application_deadline)->format('Y-m-d'))" />
                                <x-input-error :messages="$errors->get('application_deadline')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="draft" {{ old('status', $jobPost->status) === 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                                    <option value="published" {{ old('status', $jobPost->status) === 'published' ? 'selected' : '' }}>{{ __('Published') }}</option>
                                    <option value="closed" {{ old('status', $jobPost->status) === 'closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('employer.job-posts.show', $jobPost) }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Cancel') }}</a>
                            <x-primary-button>{{ __('Update Job Post') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
