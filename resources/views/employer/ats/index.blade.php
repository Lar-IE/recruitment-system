@php
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Talent Pool') }}
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ __('Browse and search qualified jobseekers who are available for opportunities. Filter candidates based on skills, experience, and availability.') }}
        </p>
    </x-slot>

    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            @php
                $currentSort = $sort ?? 'applied_at';
                $currentDir = $dir ?? 'desc';
            @endphp
            <form method="GET" action="{{ route('employer.ats') }}" class="bg-white p-4 rounded-lg shadow-sm flex flex-wrap gap-4 items-end">
                <div>
                    <x-input-label for="city" :value="__('City Location')" />
                    <select id="city" name="city" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city }}" {{ ($filters['city'] ?? '') === $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="gender" :value="__('Gender')" />
                    <select id="gender" name="gender" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($genders as $gender)
                            <option value="{{ $gender }}" {{ ($filters['gender'] ?? '') === $gender ? 'selected' : '' }}>{{ ucfirst($gender) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="educational_attainment" :value="__('Educational Attainment')" />
                    <select id="educational_attainment" name="educational_attainment" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($educationalAttainments as $attainment)
                            <option value="{{ $attainment }}" {{ ($filters['educational_attainment'] ?? '') === $attainment ? 'selected' : '' }}>{{ $attainment }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" {{ ($filters['status'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="age_range" :value="__('Age')" />
                    <select id="age_range" name="age_range" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('All') }}</option>
                        <option value="18-24" {{ ($filters['age_range'] ?? '') === '18-24' ? 'selected' : '' }}>18–24</option>
                        <option value="25-34" {{ ($filters['age_range'] ?? '') === '25-34' ? 'selected' : '' }}>25–34</option>
                        <option value="35-44" {{ ($filters['age_range'] ?? '') === '35-44' ? 'selected' : '' }}>35–44</option>
                        <option value="45-54" {{ ($filters['age_range'] ?? '') === '45-54' ? 'selected' : '' }}>45–54</option>
                        <option value="55+" {{ ($filters['age_range'] ?? '') === '55+' ? 'selected' : '' }}>55+</option>
                    </select>
                </div>
                <div class="min-w-[220px]">
                    <x-input-label for="search" :value="__('Search')" />
                    <x-text-input id="search" name="search" class="mt-1 block w-full" :value="old('search', $filters['search'] ?? '')" placeholder="{{ __('Name, email, phone, city, current job') }}" />
                </div>
                <div class="flex items-center gap-2">
                    <x-primary-button>{{ __('Search') }}</x-primary-button>
                    <a href="{{ route('employer.ats') }}" class="text-sm text-gray-600 hover:text-gray-900">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($applications->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No candidates found.') }}</p>
                    @else
                        <div class="overflow-x-auto -mx-6 sm:mx-0 lg:mx-0 lg:overflow-visible">
                            <table class="w-full min-w-[900px] lg:min-w-0 text-xs">
                                <thead class="text-left text-gray-500 whitespace-nowrap bg-gray-50/80">
                                    <tr>
                                        @php
                                            $sortLink = function ($key) use ($filters, $currentSort, $currentDir) {
                                                $nextDir = ($currentSort === $key && $currentDir === 'asc') ? 'desc' : 'asc';
                                                return route('employer.ats', array_merge($filters, ['sort' => $key, 'dir' => $nextDir]));
                                            };
                                            $sortIcon = function ($key) use ($currentSort, $currentDir) {
                                                if ($currentSort !== $key) {
                                                    return '';
                                                }
                                                return $currentDir === 'asc' ? '▲' : '▼';
                                            };
                                        @endphp
                                        <th class="py-2 px-3"><a href="{{ $sortLink('name') }}" class="inline-flex items-center gap-1">{{ __('Name') }} <span class="text-[10px]">{{ $sortIcon('name') }}</span></a></th>
                                        <th class="py-2 px-3"><a href="{{ $sortLink('contact') }}" class="inline-flex items-center gap-1">{{ __('Contact Number') }} <span class="text-[10px]">{{ $sortIcon('contact') }}</span></a></th>
                                        <th class="py-2 px-3"><a href="{{ $sortLink('city') }}" class="inline-flex items-center gap-1">{{ __('City Location') }} <span class="text-[10px]">{{ $sortIcon('city') }}</span></a></th>
                                        <th class="py-2 px-3"><a href="{{ $sortLink('education') }}" class="inline-flex items-center gap-1">{{ __('Educational Attainment') }} <span class="text-[10px]">{{ $sortIcon('education') }}</span></a></th>
                                        <th class="py-2 px-3"><a href="{{ $sortLink('gender') }}" class="inline-flex items-center gap-1">{{ __('Gender') }} <span class="text-[10px]">{{ $sortIcon('gender') }}</span></a></th>
                                        <th class="py-2 px-3"><a href="{{ $sortLink('age') }}" class="inline-flex items-center gap-1">{{ __('Age') }} <span class="text-[10px]">{{ $sortIcon('age') }}</span></a></th>
                                        <th class="py-2 px-3"><a href="{{ $sortLink('current_job') }}" class="inline-flex items-center gap-1">{{ __('Current/Recent Job') }} <span class="text-[10px]">{{ $sortIcon('current_job') }}</span></a></th>
                                        <th class="py-2 px-3"><a href="{{ $sortLink('status') }}" class="inline-flex items-center gap-1">{{ __('Status') }} <span class="text-[10px]">{{ $sortIcon('status') }}</span></a></th>
                                        <th class="py-2 px-3 text-right">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    @foreach ($applications as $application)
                                        @php
                                            $jobseeker = $application->jobseeker;
                                            $firstWe = $jobseeker?->workExperiences->first();
                                            $currentJob = $firstWe?->position ?: $firstWe?->company ?: 'N/A';
                                        @endphp
                                        <tr class="border-t align-top">
                                            <td class="py-2 px-3">
                                                <p class="font-medium">{{ $jobseeker->full_name ?: ($jobseeker->user->name ?? __('N/A')) }}</p>
                                                <p class="text-xs text-gray-500">{{ $jobseeker->user->email ?? '' }}</p>
                                            </td>
                                            <td class="py-2 px-3 whitespace-nowrap">
                                                @if ($jobseeker?->phone)
                                                    {{ str_starts_with($jobseeker->phone, '+63') ? $jobseeker->phone : '+63' . ltrim($jobseeker->phone, '0') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="py-2 px-3 whitespace-nowrap">{{ $jobseeker?->city ?? '-' }}</td>
                                            <td class="py-2 px-3 whitespace-nowrap">{{ $jobseeker?->educational_attainment ?? '-' }}</td>
                                            <td class="py-2 px-3 whitespace-nowrap">{{ $jobseeker?->gender ? ucfirst($jobseeker->gender) : '-' }}</td>
                                            <td class="py-2 px-3 whitespace-nowrap">{{ $jobseeker?->birth_date?->age ?? '-' }}</td>
                                            <td class="py-2 px-3">{{ $currentJob }}</td>
                                            <td class="py-2 px-3 whitespace-nowrap">{{ $statuses[$application->current_status] ?? Str::of($application->current_status)->replace('_', ' ')->title() }}</td>
                                            <td class="py-2 px-3 text-right">
                                                <a href="{{ route('employer.ats.show', $application) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition" title="{{ __('View Profile') }}" aria-label="{{ __('View Profile') }}">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $applications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
