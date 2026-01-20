<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Digital ID Issuance') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    <h3 class="text-lg font-semibold">{{ __('Issue Digital ID') }}</h3>
                    <form method="POST" action="{{ route('employer.digital-ids.store') }}" class="space-y-4" x-data>
                        @csrf
                        <div>
                            <x-input-label for="jobseeker_id" :value="__('Hired Applicant')" />
                            <select id="jobseeker_id" name="jobseeker_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required x-on:change="document.getElementById('job_post_id').value = $event.target.selectedOptions[0].dataset.job">
                                <option value="" disabled selected>{{ __('Select applicant') }}</option>
                                @foreach ($hiredApplications as $application)
                                    <option value="{{ $application->jobseeker_id }}" data-job="{{ $application->job_post_id }}">
                                        {{ $application->jobseeker->user->name ?? __('N/A') }} - {{ $application->jobPost->title ?? __('Job') }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('jobseeker_id')" class="mt-2" />
                            <input type="hidden" name="job_post_id" id="job_post_id" value="">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="company_name" :value="__('Company Name')" />
                                <x-text-input id="company_name" name="company_name" class="mt-1 block w-full" :value="old('company_name')" required />
                                <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="job_title" :value="__('Job Title')" />
                                <x-text-input id="job_title" name="job_title" class="mt-1 block w-full" :value="old('job_title')" required />
                                <x-input-error :messages="$errors->get('job_title')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="employee_identifier" :value="__('Employee ID')" />
                                <x-text-input id="employee_identifier" name="employee_identifier" class="mt-1 block w-full" :value="old('employee_identifier')" required />
                                <x-input-error :messages="$errors->get('employee_identifier')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="issue_date" :value="__('Issue Date')" />
                                <x-text-input id="issue_date" name="issue_date" type="date" class="mt-1 block w-full" :value="old('issue_date')" required />
                                <x-input-error :messages="$errors->get('issue_date')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <x-primary-button>{{ __('Issue ID') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Issued Digital IDs') }}</h3>
                    @if ($issuedIds->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No digital IDs issued yet.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left text-gray-500">
                                    <tr>
                                        <th class="py-2 pr-4">{{ __('Applicant') }}</th>
                                        <th class="py-2 pr-4">{{ __('Job Title') }}</th>
                                        <th class="py-2 pr-4">{{ __('Status') }}</th>
                                        <th class="py-2 pr-4">{{ __('Issued') }}</th>
                                        <th class="py-2 pr-4 text-right">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    @foreach ($issuedIds as $digitalId)
                                        <tr class="border-t">
                                            <td class="py-2 pr-4">
                                                {{ $digitalId->jobseeker->user->name ?? __('N/A') }}
                                            </td>
                                            <td class="py-2 pr-4">{{ $digitalId->job_title }}</td>
                                            <td class="py-2 pr-4">
                                                <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                                    {{ ucfirst($digitalId->status) }}
                                                </span>
                                            </td>
                                            <td class="py-2 pr-4">{{ $digitalId->issue_date?->format('M d, Y') }}</td>
                                            <td class="py-2 pr-4 text-right space-x-2">
                                                <a href="{{ route('employer.digital-ids.show', $digitalId) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">{{ __('Preview') }}</a>
                                                <form method="POST" action="{{ route('employer.digital-ids.toggle', $digitalId) }}" class="inline">
                                                    @csrf
                                                    <x-secondary-button type="submit" class="text-xs">
                                                        {{ $digitalId->status === 'active' ? __('Deactivate') : __('Activate') }}
                                                    </x-secondary-button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $issuedIds->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
