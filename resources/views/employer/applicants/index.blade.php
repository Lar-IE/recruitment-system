@php
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Applicants') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @php
                $user = Auth::user();
                $isEmployerOwner = request()->attributes->get('employer_owner', false);
                $isEmployerSubUser = $user instanceof \App\Models\EmployerSubUser;
                $employerSubRole = $isEmployerSubUser ? $user->role?->value : null;
                $canUpdateStatus = $isEmployerOwner || in_array($employerSubRole, ['admin', 'recruiter'], true);
                $currentSort = $sort ?? 'applied_at';
                $currentDir = $dir ?? 'desc';
            @endphp

            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-700">
                    <p class="font-semibold">{{ __('Please fix the errors below:') }}</p>
                    <ul class="mt-2 list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="GET" action="{{ route('employer.applicants') }}" class="bg-white p-4 rounded-lg shadow-sm flex flex-wrap gap-4 items-end">
                <div>
                    <x-input-label for="job_post_id" :value="__('Job Post')" />
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
                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" {{ ($filters['status'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[220px]">
                    <x-input-label for="search" :value="__('Search')" />
                    <x-text-input id="search" name="search" class="mt-1 block w-full" :value="old('search', $filters['search'] ?? '')" placeholder="{{ __('Name, email, phone, city, job') }}" />
                </div>
                <x-primary-button>{{ __('Filter') }}</x-primary-button>
            </form>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data>
                        <div class="p-6 text-gray-900">
                    @if ($applications->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No applicants found.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-[1400px] text-sm">
                                <thead class="text-left text-gray-500 whitespace-nowrap">
                                    <tr>
                                        @php
                                            $sortLink = function ($key) use ($filters, $currentSort, $currentDir) {
                                                $nextDir = ($currentSort === $key && $currentDir === 'asc') ? 'desc' : 'asc';
                                                return route('employer.applicants', array_merge($filters, ['sort' => $key, 'dir' => $nextDir]));
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
                                            <a href="{{ $sortLink('job_title') }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-gray-900">
                                                {{ __('Job Title') }} <span class="text-[10px]">{{ $sortIcon('job_title') }}</span>
                                            </a>
                                        </th>
                                        <th class="py-2 pr-4">
                                            <a href="{{ $sortLink('status') }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-gray-900">
                                                {{ __('Status') }} <span class="text-[10px]">{{ $sortIcon('status') }}</span>
                                            </a>
                                        </th>
                                        <th class="py-2 pr-4">
                                            <a href="{{ $sortLink('applied_at') }}" class="inline-flex items-center gap-1 text-gray-600 hover:text-gray-900">
                                                {{ __('Applied At') }} <span class="text-[10px]">{{ $sortIcon('applied_at') }}</span>
                                            </a>
                                        </th>
                                        <th class="py-2 pr-4 text-right">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    @foreach ($applications as $application)
                                                <tr class="border-t align-top">
                                            @php
                                                $jobseeker = $application->jobseeker;
                                                $education = Str::of($jobseeker?->education ?? '')
                                                    ->before("\n")
                                                    ->trim();
                                            @endphp
                                            <td class="py-3 pr-4">
                                                <a href="{{ route('employer.applicants.show', $application) }}" class="font-medium text-indigo-600 hover:text-indigo-900">
                                                    {{ $application->jobseeker->user->name ?? __('N/A') }}
                                                </a>
                                                <p class="text-xs text-gray-500">{{ $application->jobseeker->user->email ?? '' }}</p>
                                            </td>
                                            <td class="py-3 pr-4 whitespace-nowrap">{{ $jobseeker?->phone ?? '-' }}</td>
                                            <td class="py-3 pr-4 whitespace-nowrap">{{ $jobseeker?->city ?? '-' }}</td>
                                            <td class="py-3 pr-4">{{ $education->isNotEmpty() ? Str::limit($education->toString(), 40) : '-' }}</td>
                                            <td class="py-3 pr-4 whitespace-nowrap">{{ $jobseeker?->gender ? ucfirst($jobseeker->gender) : '-' }}</td>
                                            <td class="py-3 pr-4 whitespace-nowrap">{{ $jobseeker?->birth_date?->age ?? '-' }}</td>
                                            <td class="py-3 pr-4">{{ $application->jobPost->title ?? __('N/A') }}</td>
                                            <td class="py-3 pr-4 whitespace-nowrap">{{ $statuses[$application->current_status] ?? ucfirst($application->current_status) }}</td>
                                            <td class="py-3 pr-4 whitespace-nowrap">{{ $application->applied_at?->format('M d, Y') }}</td>
                                            <td class="py-3 pr-4 text-right space-x-2">
                                                <a href="{{ route('employer.applicants.show', $application) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                                    {{ __('View Profile') }}
                                                </a>
                                                @if ($canUpdateStatus)
                                                    <button type="button"
                                                        class="text-sm text-gray-600 hover:text-gray-900"
                                                        x-on:click="$dispatch('open-modal', 'status-{{ $application->id }}')">
                                                        {{ __('Change Status') }}
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($canUpdateStatus)
                            @foreach ($applications as $application)
                                <x-modal name="status-{{ $application->id }}" maxWidth="md">
                                    <div class="p-6">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Update Status') }}</h3>
                                            <button type="button" class="text-sm text-gray-500 hover:text-gray-700" x-on:click="$dispatch('close-modal', 'status-{{ $application->id }}')">
                                                {{ __('Close') }}
                                            </button>
                                        </div>
                                        <form method="POST" action="{{ route('employer.ats.status', $application) }}" class="mt-4 space-y-4" x-data="{ status: '{{ $application->current_status }}' }">
                                            @csrf
                                            <div>
                                                <x-input-label for="status-{{ $application->id }}" :value="__('Status')" />
                                                <select id="status-{{ $application->id }}" name="status" x-model="status" class="mt-1 block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                    @foreach ($statuses as $key => $label)
                                                        <option value="{{ $key }}" {{ $application->current_status === $key ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div x-show="status === 'interview_scheduled'" x-cloak>
                                                <x-input-label for="interview-at-{{ $application->id }}" :value="__('Interview Date & Time')" />
                                                <x-text-input id="interview-at-{{ $application->id }}" name="interview_at" type="datetime-local" class="mt-1 block w-full" x-bind:required="status === 'interview_scheduled'" />
                                            </div>
                                            <div x-show="status === 'interview_scheduled'" x-cloak>
                                                <x-input-label for="interview-link-{{ $application->id }}" :value="__('Interview Link (Zoom)')" />
                                                <x-text-input id="interview-link-{{ $application->id }}" name="interview_link" type="url" class="mt-1 block w-full" x-bind:required="status === 'interview_scheduled'" placeholder="https://zoom.us/j/..." />
                                            </div>
                                            <div>
                                                <x-input-label for="note-{{ $application->id }}" :value="__('Note (optional)')" />
                                                <textarea id="note-{{ $application->id }}" name="note" rows="3" class="mt-1 block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="{{ __('Add a note for this update') }}"></textarea>
                                            </div>
                                            <div class="flex items-center justify-end gap-3">
                                                <button type="button" class="text-sm text-gray-600 hover:text-gray-900" x-on:click="$dispatch('close-modal', 'status-{{ $application->id }}')">
                                                    {{ __('Cancel') }}
                                                </button>
                                                <x-primary-button class="text-xs">{{ __('Save') }}</x-primary-button>
                                            </div>
                                        </form>
                                    </div>
                                </x-modal>
                            @endforeach
                        @endif
                        <div class="mt-4">
                            {{ $applications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
