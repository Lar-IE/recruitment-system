@php
    $genderOptions = ['Male', 'Female', 'Other', 'Prefer not to say'];
    $countryOptions = ['Philippines'];
    $selectedCountry = old('country', $jobseeker->country ?: 'Philippines');
    $selectedRegion = old('region', $jobseeker->region);
@endphp

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Resume Details') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Update the information employers see on your profile.') }}
        </p>
    </header>

    @if (session('success'))
        <div class="mt-4 rounded-md bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (! $jobseeker)
        <p class="mt-4 text-sm text-gray-500">{{ __('Resume profile is not available.') }}</p>
    @else
        <form method="POST" action="{{ route('jobseeker.profile.update') }}" class="mt-6 space-y-6">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="phone" :value="__('Phone')" />
                    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $jobseeker->phone)" />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>
                <div>
                    <x-input-label for="birth_date" :value="__('Birth Date')" />
                    <x-text-input id="birth_date" name="birth_date" type="date" class="mt-1 block w-full" :value="old('birth_date', $jobseeker->birth_date?->format('Y-m-d'))" />
                    <x-input-error class="mt-2" :messages="$errors->get('birth_date')" />
                </div>
                <div>
                    <x-input-label for="region" :value="__('Region')" />
                    <select id="region" name="region" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" data-current-value="{{ $selectedRegion }}">
                        <option value="">{{ __('Select region') }}</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('region')" />
                </div>
                <div>
                    <x-input-label for="gender" :value="__('Gender')" />
                    <select id="gender" name="gender" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('Select gender') }}</option>
                        @foreach ($genderOptions as $option)
                            <option value="{{ $option }}" @selected(old('gender', $jobseeker->gender) === $option)>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                </div>
                <div>
                    <x-input-label for="country" :value="__('Country')" />
                    <select id="country" name="country" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('Select country') }}</option>
                        @foreach ($countryOptions as $option)
                            <option value="{{ $option }}" @selected($selectedCountry === $option)>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('country')" />
                </div>
                <div>
                    <x-input-label for="province" :value="__('Province')" />
                    <select id="province" name="province" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" data-current-value="{{ old('province', $jobseeker->province) }}" disabled>
                        <option value="">{{ __('Select province') }}</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('province')" />
                </div>
                <div>
                    <x-input-label for="city" :value="__('City')" />
                    <select id="city" name="city" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" data-current-value="{{ old('city', $jobseeker->city) }}">
                        <option value="">{{ __('Select city/municipality') }}</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('city')" />
                </div>
                <div>
                    <x-input-label for="barangay" :value="__('Barangay')" />
                    <select id="barangay" name="barangay" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" data-current-value="{{ old('barangay', $jobseeker->barangay) }}">
                        <option value="">{{ __('Select barangay') }}</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('barangay')" />
                </div>
                <div class="md:col-span-2">
                    <x-input-label for="address" :value="__('Address')" />
                    <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $jobseeker->address)" />
                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                </div>
            </div>

            <div class="border-t pt-6">
                <h3 class="text-base font-semibold text-gray-900">{{ __('Resume Content') }}</h3>
                <p class="text-sm text-gray-600">{{ __('Add highlights that employers see when viewing your profile.') }}</p>
            </div>

            <div>
                <x-input-label for="bio" :value="__('Professional Summary')" />
                <textarea id="bio" name="bio" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="{{ __('A short summary about you') }}">{{ old('bio', $jobseeker->bio) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('bio')" />
            </div>

            <!-- Education Section -->
            <div>
                <div class="flex justify-between items-center mb-3">
                    <x-input-label :value="__('Education')" />
                    <button type="button" id="addEducation" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        + {{ __('Add Education') }}
                    </button>
                </div>
                <div id="educationContainer" class="space-y-4">
                    @php
                        $educations = old('education', $jobseeker->educations ?? []);
                        if (empty($educations) || count($educations) == 0) {
                            $educations = [null]; // At least one empty field
                        }
                    @endphp
                    @foreach($educations as $index => $education)
                        <div class="education-item border border-gray-300 rounded-lg p-4 bg-gray-50">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-semibold text-sm text-gray-700">{{ __('Education') }} #{{ $index + 1 }}</h4>
                                @if($index > 0 || count($educations) > 1)
                                    <button type="button" class="remove-education text-red-600 hover:text-red-800 text-sm font-medium">{{ __('Remove') }}</button>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="md:col-span-2">
                                    <x-input-label :value="__('Institution/School Name')" />
                                    <x-text-input type="text" name="education[{{ $index }}][institution]" class="mt-1 block w-full" :value="old('education.'.$index.'.institution', $education->institution ?? '')" placeholder="{{ __('e.g., ABC University') }}" />
                                    <x-input-error class="mt-2" :messages="$errors->get('education.'.$index.'.institution')" />
                                </div>
                                <div>
                                    <x-input-label :value="__('Degree/Level')" />
                                    <x-text-input type="text" name="education[{{ $index }}][degree]" class="mt-1 block w-full" :value="old('education.'.$index.'.degree', $education->degree ?? '')" placeholder="{{ __('e.g., Bachelor of Science') }}" />
                                    <x-input-error class="mt-2" :messages="$errors->get('education.'.$index.'.degree')" />
                                </div>
                                <div>
                                    <x-input-label :value="__('Field of Study')" />
                                    <x-text-input type="text" name="education[{{ $index }}][field_of_study]" class="mt-1 block w-full" :value="old('education.'.$index.'.field_of_study', $education->field_of_study ?? '')" placeholder="{{ __('e.g., Computer Science') }}" />
                                    <x-input-error class="mt-2" :messages="$errors->get('education.'.$index.'.field_of_study')" />
                                </div>
                                <div>
                                    <x-input-label :value="__('Start Date')" />
                                    <x-text-input type="date" name="education[{{ $index }}][start_date]" class="mt-1 block w-full" :value="old('education.'.$index.'.start_date', $education->start_date?->format('Y-m-d') ?? '')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('education.'.$index.'.start_date')" />
                                </div>
                                <div>
                                    <x-input-label :value="__('End Date')" />
                                    <x-text-input type="date" name="education[{{ $index }}][end_date]" class="mt-1 block w-full" :value="old('education.'.$index.'.end_date', $education->end_date?->format('Y-m-d') ?? '')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('education.'.$index.'.end_date')" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label :value="__('Description (Optional)')" />
                                    <textarea name="education[{{ $index }}][description]" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="{{ __('e.g., Awards, achievements, relevant coursework') }}">{{ old('education.'.$index.'.description', $education->description ?? '') }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('education.'.$index.'.description')" />
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Work Experience Section -->
            <div>
                <div class="flex justify-between items-center mb-3">
                    <x-input-label :value="__('Work Experience')" />
                    <button type="button" id="addWorkExperience" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        + {{ __('Add Work Experience') }}
                    </button>
                </div>
                <div id="workExperienceContainer" class="space-y-4">
                    @php
                        $workExperiences = old('work_experience', $jobseeker->workExperiences ?? []);
                        if (empty($workExperiences) || count($workExperiences) == 0) {
                            $workExperiences = [null]; // At least one empty field
                        }
                    @endphp
                    @foreach($workExperiences as $index => $experience)
                        <div class="work-experience-item border border-gray-300 rounded-lg p-4 bg-gray-50">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-semibold text-sm text-gray-700">{{ __('Work Experience') }} #{{ $index + 1 }}</h4>
                                @if($index > 0 || count($workExperiences) > 1)
                                    <button type="button" class="remove-work-experience text-red-600 hover:text-red-800 text-sm font-medium">{{ __('Remove') }}</button>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <x-input-label :value="__('Company Name')" />
                                    <x-text-input type="text" name="work_experience[{{ $index }}][company]" class="mt-1 block w-full" :value="old('work_experience.'.$index.'.company', $experience->company ?? '')" placeholder="{{ __('e.g., XYZ Corporation') }}" />
                                    <x-input-error class="mt-2" :messages="$errors->get('work_experience.'.$index.'.company')" />
                                </div>
                                <div>
                                    <x-input-label :value="__('Position/Job Title')" />
                                    <x-text-input type="text" name="work_experience[{{ $index }}][position]" class="mt-1 block w-full" :value="old('work_experience.'.$index.'.position', $experience->position ?? '')" placeholder="{{ __('e.g., Software Developer') }}" />
                                    <x-input-error class="mt-2" :messages="$errors->get('work_experience.'.$index.'.position')" />
                                </div>
                                <div>
                                    <x-input-label :value="__('Start Date')" />
                                    <x-text-input type="date" name="work_experience[{{ $index }}][start_date]" class="mt-1 block w-full" :value="old('work_experience.'.$index.'.start_date', $experience->start_date?->format('Y-m-d') ?? '')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('work_experience.'.$index.'.start_date')" />
                                </div>
                                <div>
                                    <x-input-label :value="__('End Date')" />
                                    <x-text-input type="date" name="work_experience[{{ $index }}][end_date]" class="mt-1 block w-full end-date-input" :value="old('work_experience.'.$index.'.end_date', $experience->end_date?->format('Y-m-d') ?? '')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('work_experience.'.$index.'.end_date')" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="work_experience[{{ $index }}][is_current]" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 current-job-checkbox" {{ old('work_experience.'.$index.'.is_current', $experience->is_current ?? false) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-600">{{ __('I currently work here') }}</span>
                                    </label>
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label :value="__('Description (Optional)')" />
                                    <textarea name="work_experience[{{ $index }}][description]" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="{{ __('e.g., Key responsibilities and achievements') }}">{{ old('work_experience.'.$index.'.description', $experience->description ?? '') }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('work_experience.'.$index.'.description')" />
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <x-input-label for="skills" :value="__('Skills')" />
                <textarea id="skills" name="skills" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="{{ __('One skill per line') }}">{{ old('skills', $jobseeker->skills) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('skills')" />
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('Save Resume') }}</x-primary-button>
            </div>
        </form>
    @endif
</section>

<script>
    (function () {
        const regionSelect = document.getElementById('region');
        const provinceSelect = document.getElementById('province');
        const citySelect = document.getElementById('city');
        const barangaySelect = document.getElementById('barangay');

        if (!regionSelect || !provinceSelect || !citySelect || !barangaySelect) {
            return;
        }

        const currentRegion = regionSelect.dataset.currentValue || '';
        const currentProvince = provinceSelect.dataset.currentValue || '';
        const currentCity = citySelect.dataset.currentValue || '';
        const currentBarangay = barangaySelect.dataset.currentValue || '';

        const apiBase = 'https://psgc.cloud/api';

        const placeholders = {
            region: @json(__('Select region')),
            province: @json(__('Select province')),
            city: @json(__('Select city/municipality')),
            barangay: @json(__('Select barangay')),
        };

        const resetSelect = (select, placeholder) => {
            select.innerHTML = '';
            const option = document.createElement('option');
            option.value = '';
            option.textContent = placeholder;
            select.appendChild(option);
        };

        const populateSelect = (select, items, selectedValue, getLabel) => {
            items.forEach((item) => {
                const option = document.createElement('option');
                option.value = item.name;
                option.textContent = getLabel ? getLabel(item) : item.name;
                option.dataset.code = item.code;
                if (item.type) {
                    option.dataset.type = item.type;
                }
                if (selectedValue && item.name === selectedValue) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        };

        const fetchJson = async (url) => {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`Request failed: ${response.status}`);
            }
            return response.json();
        };

        const loadRegions = async () => {
            resetSelect(regionSelect, placeholders.region);
            resetSelect(provinceSelect, placeholders.province);
            resetSelect(citySelect, placeholders.city);
            resetSelect(barangaySelect, placeholders.barangay);

            const data = await fetchJson(`${apiBase}/regions`);
            const regions = data.sort((a, b) => a.name.localeCompare(b.name));
            populateSelect(regionSelect, regions, currentRegion);

            if (currentRegion) {
                const selected = Array.from(regionSelect.options).find((opt) => opt.value === currentRegion);
                if (selected?.dataset.code) {
                    await loadProvinces(selected.dataset.code, currentProvince);
                }
            } else {
                provinceSelect.disabled = true;
            }
        };

        const loadProvinces = async (regionCode, selectedProvince = '') => {
            resetSelect(provinceSelect, placeholders.province);
            resetSelect(citySelect, placeholders.city);
            resetSelect(barangaySelect, placeholders.barangay);

            const data = await fetchJson(`${apiBase}/regions/${regionCode}/provinces`);
            const provinces = data.sort((a, b) => a.name.localeCompare(b.name));
            provinceSelect.disabled = provinces.length === 0;

            if (provinces.length === 0) {
                await loadCitiesMunicipalitiesByRegion(regionCode, currentCity);
                return;
            }

            populateSelect(provinceSelect, provinces, selectedProvince);

            if (selectedProvince) {
                const selected = Array.from(provinceSelect.options).find((opt) => opt.value === selectedProvince);
                if (selected?.dataset.code) {
                    await loadCitiesMunicipalitiesByProvince(selected.dataset.code, currentCity);
                }
            }
        };

        const loadCitiesMunicipalitiesByRegion = async (regionCode, selectedCity = '') => {
            resetSelect(citySelect, placeholders.city);
            resetSelect(barangaySelect, placeholders.barangay);

            const data = await fetchJson(`${apiBase}/regions/${regionCode}/cities-municipalities`);
            const cities = data.sort((a, b) => a.name.localeCompare(b.name));
            populateSelect(citySelect, cities, selectedCity, (item) => `${item.name} (${item.type})`);

            if (selectedCity) {
                const selected = Array.from(citySelect.options).find((opt) => opt.value === selectedCity);
                if (selected?.dataset.code && selected?.dataset.type) {
                    await loadBarangays(selected.dataset.code, selected.dataset.type, currentBarangay);
                }
            }
        };

        const loadCitiesMunicipalitiesByProvince = async (provinceCode, selectedCity = '') => {
            resetSelect(citySelect, placeholders.city);
            resetSelect(barangaySelect, placeholders.barangay);

            const data = await fetchJson(`${apiBase}/provinces/${provinceCode}/cities-municipalities`);
            const cities = data.sort((a, b) => a.name.localeCompare(b.name));
            populateSelect(citySelect, cities, selectedCity, (item) => `${item.name} (${item.type})`);

            if (selectedCity) {
                const selected = Array.from(citySelect.options).find((opt) => opt.value === selectedCity);
                if (selected?.dataset.code && selected?.dataset.type) {
                    await loadBarangays(selected.dataset.code, selected.dataset.type, currentBarangay);
                }
            }
        };

        const loadBarangays = async (cityCode, cityType, selectedBarangay = '') => {
            resetSelect(barangaySelect, placeholders.barangay);
            let resource = 'municipalities';
            if (cityType === 'City') {
                resource = 'cities';
            } else if (cityType === 'SubMun') {
                resource = 'sub-municipalities';
            }
            const data = await fetchJson(`${apiBase}/${resource}/${cityCode}/barangays`);
            const barangays = data.sort((a, b) => a.name.localeCompare(b.name));
            populateSelect(barangaySelect, barangays, selectedBarangay);
        };

        regionSelect.addEventListener('change', async () => {
            const regionCode = regionSelect.selectedOptions[0]?.dataset.code;
            if (!regionCode) {
                provinceSelect.disabled = true;
                resetSelect(provinceSelect, placeholders.province);
                resetSelect(citySelect, placeholders.city);
                resetSelect(barangaySelect, placeholders.barangay);
                return;
            }
            await loadProvinces(regionCode);
        });

        provinceSelect.addEventListener('change', async () => {
            const provinceCode = provinceSelect.selectedOptions[0]?.dataset.code;
            if (!provinceCode) {
                resetSelect(citySelect, placeholders.city);
                resetSelect(barangaySelect, placeholders.barangay);
                return;
            }
            await loadCitiesMunicipalitiesByProvince(provinceCode);
        });

        citySelect.addEventListener('change', async () => {
            const selected = citySelect.selectedOptions[0];
            const code = selected?.dataset.code;
            const type = selected?.dataset.type;
            if (!code || !type) {
                resetSelect(barangaySelect, placeholders.barangay);
                return;
            }
            await loadBarangays(code, type);
        });

        loadRegions().catch(() => {
            resetSelect(regionSelect, placeholders.region);
            resetSelect(provinceSelect, placeholders.province);
            resetSelect(citySelect, placeholders.city);
            resetSelect(barangaySelect, placeholders.barangay);
        });
    })();

    // Dynamic Education Fields
    (function() {
        const addEducationBtn = document.getElementById('addEducation');
        const educationContainer = document.getElementById('educationContainer');
        let educationIndex = educationContainer.querySelectorAll('.education-item').length;

        const createEducationItem = (index) => {
            const template = `
                <div class="education-item border border-gray-300 rounded-lg p-4 bg-gray-50">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-semibold text-sm text-gray-700">{{ __('Education') }} #${index + 1}</h4>
                        <button type="button" class="remove-education text-red-600 hover:text-red-800 text-sm font-medium">{{ __('Remove') }}</button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="md:col-span-2">
                            <label class="block font-medium text-sm text-gray-700">{{ __('Institution/School Name') }}</label>
                            <input type="text" name="education[${index}][institution]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="{{ __('e.g., ABC University') }}" />
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">{{ __('Degree/Level') }}</label>
                            <input type="text" name="education[${index}][degree]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="{{ __('e.g., Bachelor of Science') }}" />
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">{{ __('Field of Study') }}</label>
                            <input type="text" name="education[${index}][field_of_study]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="{{ __('e.g., Computer Science') }}" />
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">{{ __('Start Date') }}</label>
                            <input type="date" name="education[${index}][start_date]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">{{ __('End Date') }}</label>
                            <input type="date" name="education[${index}][end_date]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block font-medium text-sm text-gray-700">{{ __('Description (Optional)') }}</label>
                            <textarea name="education[${index}][description]" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="{{ __('e.g., Awards, achievements, relevant coursework') }}"></textarea>
                        </div>
                    </div>
                </div>
            `;
            return template;
        };

        addEducationBtn.addEventListener('click', function() {
            const newItem = document.createElement('div');
            newItem.innerHTML = createEducationItem(educationIndex);
            educationContainer.appendChild(newItem.firstElementChild);
            educationIndex++;
            updateEducationNumbers();
        });

        educationContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-education')) {
                e.target.closest('.education-item').remove();
                updateEducationNumbers();
            }
        });

        function updateEducationNumbers() {
            const items = educationContainer.querySelectorAll('.education-item');
            items.forEach((item, index) => {
                item.querySelector('h4').textContent = '{{ __('Education') }} #' + (index + 1);
            });
        }
    })();

    // Dynamic Work Experience Fields
    (function() {
        const addWorkExperienceBtn = document.getElementById('addWorkExperience');
        const workExperienceContainer = document.getElementById('workExperienceContainer');
        let workExperienceIndex = workExperienceContainer.querySelectorAll('.work-experience-item').length;

        const createWorkExperienceItem = (index) => {
            const template = `
                <div class="work-experience-item border border-gray-300 rounded-lg p-4 bg-gray-50">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-semibold text-sm text-gray-700">{{ __('Work Experience') }} #${index + 1}</h4>
                        <button type="button" class="remove-work-experience text-red-600 hover:text-red-800 text-sm font-medium">{{ __('Remove') }}</button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">{{ __('Company Name') }}</label>
                            <input type="text" name="work_experience[${index}][company]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="{{ __('e.g., XYZ Corporation') }}" />
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">{{ __('Position/Job Title') }}</label>
                            <input type="text" name="work_experience[${index}][position]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="{{ __('e.g., Software Developer') }}" />
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">{{ __('Start Date') }}</label>
                            <input type="date" name="work_experience[${index}][start_date]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">{{ __('End Date') }}</label>
                            <input type="date" name="work_experience[${index}][end_date]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm end-date-input" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="work_experience[${index}][is_current]" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 current-job-checkbox">
                                <span class="ml-2 text-sm text-gray-600">{{ __('I currently work here') }}</span>
                            </label>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block font-medium text-sm text-gray-700">{{ __('Description (Optional)') }}</label>
                            <textarea name="work_experience[${index}][description]" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="{{ __('e.g., Key responsibilities and achievements') }}"></textarea>
                        </div>
                    </div>
                </div>
            `;
            return template;
        };

        addWorkExperienceBtn.addEventListener('click', function() {
            const newItem = document.createElement('div');
            newItem.innerHTML = createWorkExperienceItem(workExperienceIndex);
            workExperienceContainer.appendChild(newItem.firstElementChild);
            workExperienceIndex++;
            updateWorkExperienceNumbers();
            attachCurrentJobCheckboxHandler();
        });

        workExperienceContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-work-experience')) {
                e.target.closest('.work-experience-item').remove();
                updateWorkExperienceNumbers();
            }
        });

        function updateWorkExperienceNumbers() {
            const items = workExperienceContainer.querySelectorAll('.work-experience-item');
            items.forEach((item, index) => {
                item.querySelector('h4').textContent = '{{ __('Work Experience') }} #' + (index + 1);
            });
        }

        // Handle "I currently work here" checkbox
        function attachCurrentJobCheckboxHandler() {
            document.querySelectorAll('.current-job-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const endDateInput = this.closest('.work-experience-item').querySelector('.end-date-input');
                    if (this.checked) {
                        endDateInput.value = '';
                        endDateInput.disabled = true;
                    } else {
                        endDateInput.disabled = false;
                    }
                });
            });
        }

        // Initialize on page load
        attachCurrentJobCheckboxHandler();
    })();
</script>
