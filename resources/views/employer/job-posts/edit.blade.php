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
                        @endphp
                        <div x-data="requiredSkillsManager(@js($existingSkills))">
                            <x-input-label :value="__('Required Skills (for candidate matching)')" />
                            <p class="mt-1 text-sm text-gray-600">{{ __('Specify skills and optional weight (1–10). Candidates with matching skills will be ranked by proficiency.') }}</p>
                            <template x-for="(skill, index) in skills" :key="index">
                                <div class="mt-3 flex flex-wrap gap-3 items-end p-3 rounded-lg border border-gray-200 bg-gray-50/50">
                                    <div class="flex-1 min-w-[140px]">
                                        <input type="text" x-model="skill.skill_name" :name="'required_skills[' + index + '][skill_name]'" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="{{ __('Skill name') }}" />
                                    </div>
                                    <div class="w-24">
                                        <input type="number" x-model.number="skill.weight" :name="'required_skills[' + index + '][weight]'" min="1" max="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="{{ __('Weight') }}" title="{{ __('1–10') }}" />
                                    </div>
                                    <div class="w-28">
                                        <input type="number" x-model.number="skill.min_proficiency" :name="'required_skills[' + index + '][min_proficiency]'" min="0" max="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="{{ __('Min %') }}" title="{{ __('Optional min proficiency %') }}" />
                                    </div>
                                    <button type="button" @click="removeSkill(index)" class="p-2 text-red-600 hover:bg-red-50 rounded-md">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </template>
                            <button type="button" @click="addSkill()" class="mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                + {{ __('Add required skill') }}
                            </button>
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
