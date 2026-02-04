@php
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jobseeker Directory') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @php
                $currentSort = $sort ?? 'created_at';
                $currentDir = $dir ?? 'desc';
            @endphp
            <form method="GET" action="{{ route('employer.jobseekers.index') }}" class="bg-white p-4 rounded-lg shadow-sm flex flex-wrap gap-4 items-end">
                <div>
                    <x-input-label for="job_post_id" :value="__('Job Title')" />
                    <select id="job_post_id" name="job_post_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($jobPosts as $jobPost)
                            <option value="{{ $jobPost->id }}" {{ ($filters['job_post_id'] ?? '') == $jobPost->id ? 'selected' : '' }}>{{ $jobPost->title }}</option>
                        @endforeach
                    </select>
                </div>
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
                <div class="min-w-[260px]">
                    <x-input-label for="search" :value="__('Search')" />
                    <x-text-input id="search" name="search" class="mt-1 block w-full" :value="old('search', $filters['search'] ?? '')" placeholder="{{ __('Name, email, phone, city, education') }}" />
                </div>
                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('All') }}</option>
                        <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="suspended" {{ ($filters['status'] ?? '') === 'suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
                    </select>
                </div>
                <x-primary-button>{{ __('Search') }}</x-primary-button>
            </form>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($jobseekers->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No jobseekers found.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-[1200px] text-sm">
                                <thead class="text-left text-gray-500 whitespace-nowrap">
                                    <tr>
                                        @php
                                            $sortLink = function ($key) use ($filters, $currentSort, $currentDir) {
                                                $nextDir = ($currentSort === $key && $currentDir === 'asc') ? 'desc' : 'asc';
                                                return route('employer.jobseekers.index', array_merge($filters, ['sort' => $key, 'dir' => $nextDir]));
                                            };
                                            $sortIcon = function ($key) use ($currentSort, $currentDir) {
                                                if ($currentSort !== $key) {
                                                    return '';
                                                }
                                                return $currentDir === 'asc' ? '▲' : '▼';
                                            };
                                        @endphp
                                        <th class="py-2 pr-4">
                                            <a href="{{ $sortLink('name') }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-gray-900">
                                                {{ __('Name') }} <span class="text-[10px]">{{ $sortIcon('name') }}</span>
                                            </a>
                                        </th>
                                        <th class="py-2 pr-4">
                                            <a href="{{ $sortLink('contact') }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-gray-900">
                                                {{ __('Contact number') }} <span class="text-[10px]">{{ $sortIcon('contact') }}</span>
                                            </a>
                                        </th>
                                        <th class="py-2 pr-4">
                                            <a href="{{ $sortLink('city') }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-gray-900">
                                                {{ __('City Location') }} <span class="text-[10px]">{{ $sortIcon('city') }}</span>
                                            </a>
                                        </th>
                                        <th class="py-2 pr-4">
                                            <a href="{{ $sortLink('education') }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-gray-900">
                                                {{ __('Educational Attainment') }} <span class="text-[10px]">{{ $sortIcon('education') }}</span>
                                            </a>
                                        </th>
                                        <th class="py-2 pr-4">
                                            <a href="{{ $sortLink('gender') }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-gray-900">
                                                {{ __('Gender') }} <span class="text-[10px]">{{ $sortIcon('gender') }}</span>
                                            </a>
                                        </th>
                                        <th class="py-2 pr-4">
                                            <a href="{{ $sortLink('age') }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-gray-900">
                                                {{ __('Age') }} <span class="text-[10px]">{{ $sortIcon('age') }}</span>
                                            </a>
                                        </th>
                                        <th class="py-2 pr-4">
                                            <a href="{{ $sortLink('status') }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-gray-900">
                                                {{ __('Status') }} <span class="text-[10px]">{{ $sortIcon('status') }}</span>
                                            </a>
                                        </th>
                                        <th class="py-2 pr-4 text-right">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    @foreach ($jobseekers as $jobseeker)
                                        @php
                                            $education = Str::of($jobseeker->education ?? '')
                                                ->before("\n")
                                                ->trim();
                                        @endphp
                                        <tr class="border-t align-top">
                                            <td class="py-3 pr-4">
                                                <a href="{{ route('employer.jobseekers.show', $jobseeker) }}" class="font-medium text-indigo-600 hover:text-indigo-900">
                                                    {{ $jobseeker->user->name ?? __('N/A') }}
                                                </a>
                                                <p class="text-xs text-gray-500">{{ $jobseeker->user->email ?? '' }}</p>
                                            </td>
                                            <td class="py-3 pr-4 whitespace-nowrap">{{ $jobseeker->phone ?? '-' }}</td>
                                            <td class="py-3 pr-4 whitespace-nowrap">{{ $jobseeker->city ?? '-' }}</td>
                                            <td class="py-3 pr-4">{{ $education->isNotEmpty() ? Str::limit($education->toString(), 40) : '-' }}</td>
                                            <td class="py-3 pr-4 whitespace-nowrap">{{ $jobseeker->gender ? ucfirst($jobseeker->gender) : '-' }}</td>
                                            <td class="py-3 pr-4 whitespace-nowrap">{{ $jobseeker->birth_date?->age ?? '-' }}</td>
                                            <td class="py-3 pr-4 whitespace-nowrap">{{ ucfirst($jobseeker->status) }}</td>
                                            <td class="py-3 pr-4 text-right">
                                                <a href="{{ route('employer.jobseekers.show', $jobseeker) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                                    {{ __('View Profile') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $jobseekers->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
