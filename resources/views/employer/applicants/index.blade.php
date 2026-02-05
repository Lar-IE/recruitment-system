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
                <div class="min-w-[220px]">
                    <x-input-label for="search" :value="__('Search')" />
                    <x-text-input id="search" name="search" class="mt-1 block w-full" :value="old('search', $filters['search'] ?? '')" placeholder="{{ __('Name, email, phone, city, job') }}" />
                </div>
                <x-primary-button>{{ __('Search') }}</x-primary-button>
            </form>

            @if ($canUpdateStatus)
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <button id="openModal" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        {{ __('Manage Applicants') }}
                    </button>
                </div>
            @endif

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
                                            @endphp
                                            <td class="py-3 pr-4">
                                                <a href="{{ route('employer.applicants.show', $application) }}" class="font-medium text-indigo-600 hover:text-indigo-900">
                                                    {{ $application->jobseeker->full_name ?: ($application->jobseeker->user->name ?? __('N/A')) }}
                                                </a>
                                                <p class="text-xs text-gray-500">{{ $application->jobseeker->user->email ?? '' }}</p>
                                            </td>
                                            <td class="py-3 pr-4 whitespace-nowrap">
                                                @if ($jobseeker?->phone)
                                                    {{ str_starts_with($jobseeker->phone, '+63') ? $jobseeker->phone : '+63' . ltrim($jobseeker->phone, '0') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="py-3 pr-4 whitespace-nowrap">{{ $jobseeker?->city ?? '-' }}</td>
                                            <td class="py-3 pr-4 whitespace-nowrap">{{ $jobseeker?->educational_attainment ?? '-' }}</td>
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

    <!-- Import/Export Modal -->
    <div class="modal-overlay" id="modalOverlay" style="display: none;">
        <div class="modal-container">
            <!-- Modal Header -->
            <div class="modal-header">
                <h2 class="modal-title">{{ __('Manage Applicants') }}</h2>
                <button class="close-btn" id="closeModal">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <!-- Import Section -->
                <div class="section">
                    <h3 class="section-title">{{ __('Import Applicants') }}</h3>
                    <div class="import-area">
                        <form method="POST" action="{{ route('employer.applicants.import') }}" enctype="multipart/form-data" id="importForm">
                            @csrf
                            <div class="file-input-wrapper" id="fileDropArea">
                                <input type="file" id="fileInput" name="file" accept=".csv,.xlsx,.xls,.txt" required>
                                <div class="file-input-content">
                                    <svg class="file-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <div class="file-input-text">{{ __('Choose file or drag and drop') }}</div>
                                    <div class="file-input-subtext">{{ __('CSV, XLS, XLSX up to 10MB') }}</div>
                                </div>
                            </div>
                            
                            <div class="selected-file" id="selectedFile" style="display: none;">
                                <svg class="file-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span id="fileName">file.csv</span>
                                <button type="button" class="remove-file" id="removeFile">&times;</button>
                            </div>
                            
                            <div class="button-group">
                                <a href="{{ route('employer.applicants.template') }}" class="btn btn-secondary">
                                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    {{ __('Download Template') }}
                                </a>
                                <button type="submit" class="btn btn-primary" id="importBtn" disabled>
                                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    {{ __('Import') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Export Section -->
                <div class="section">
                    <h3 class="section-title">{{ __('Export Applicants') }}</h3>
                    <div class="export-controls">
                        <form method="GET" action="{{ route('employer.applicants.export') }}" id="exportForm" style="display: flex; flex-direction: column; gap: 16px;">
                            <div class="date-range">
                                <div class="input-group">
                                    <label for="dateFrom">{{ __('Date From') }}</label>
                                    <input type="date" id="dateFrom" name="date_from">
                                </div>
                                <div class="input-group">
                                    <label for="dateTo">{{ __('Date To') }}</label>
                                    <input type="date" id="dateTo" name="date_to">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" style="width: 100%; max-width: 600px; margin: 0 auto; justify-content: center;">
                                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                {{ __('Export') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 20px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.active {
            display: flex !important;
            opacity: 1;
        }

        .modal-container {
            background: white;
            border-radius: 16px;
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            transform: scale(0.9) translateY(20px);
            transition: all 0.3s ease;
        }

        .modal-overlay.active .modal-container {
            transform: scale(1) translateY(0);
        }

        .modal-header {
            padding: 24px 32px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .close-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: #666;
            display: flex;
            align-items: center;
            border-radius: 4px;
            transition: all 0.2s ease;
            font-size: 24px;
            width: 32px;
            height: 32px;
            justify-content: center;
        }

        .close-btn:hover {
            background: #f5f5f5;
            color: #1a1a1a;
        }

        .modal-body {
            padding: 32px;
            overflow-y: auto;
        }

        .section {
            padding: 24px 0;
        }

        .section:not(:last-child) {
            border-bottom: 1px solid #f0f0f0;
        }

        .section:first-child {
            padding-top: 0;
        }

        .section-title {
            font-size: 14px;
            font-weight: 500;
            color: #666;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .import-area {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .file-input-wrapper {
            position: relative;
            border: 2px dashed #e0e0e0;
            border-radius: 8px;
            padding: 32px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #fafafa;
        }

        .file-input-wrapper:hover {
            border-color: #2563eb;
            background: #f8faff;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            cursor: pointer;
        }

        .file-input-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            pointer-events: none;
        }

        .file-input-icon {
            width: 32px;
            height: 32px;
            color: #666;
        }

        .file-input-text {
            font-size: 14px;
            color: #666;
        }

        .file-input-subtext {
            font-size: 12px;
            color: #999;
        }

        .selected-file {
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background: #f8faff;
            border: 1px solid #e3e9ff;
            border-radius: 8px;
            font-size: 14px;
            color: #333;
        }

        .selected-file.show {
            display: flex !important;
        }

        .file-icon {
            width: 20px;
            height: 20px;
            color: #2563eb;
        }

        .remove-file {
            margin-left: auto;
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: #999;
            display: flex;
            align-items: center;
            font-size: 18px;
            width: 24px;
            height: 24px;
            justify-content: center;
        }

        .remove-file:hover {
            color: #ef4444;
        }

        .button-group {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: inherit;
            text-decoration: none;
        }

        .btn-icon {
            width: 16px;
            height: 16px;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover:not(:disabled) {
            background: #1d4ed8;
        }

        .btn-primary:disabled {
            background: #93b9f5;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: white;
            color: #666;
            border: 1px solid #e0e0e0;
        }

        .btn-secondary:hover {
            background: #fafafa;
            border-color: #ccc;
        }

        .export-controls {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .date-range {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .input-group label {
            font-size: 13px;
            color: #666;
            font-weight: 500;
        }

        .input-group input[type="date"] {
            padding: 10px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        .input-group input[type="date"]:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        @media (max-width: 640px) {
            .modal-container {
                border-radius: 16px 16px 0 0;
                max-height: 95vh;
            }

            .modal-header {
                padding: 20px 24px;
            }

            .modal-body {
                padding: 24px;
            }

            .date-range {
                grid-template-columns: 1fr;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal controls
            const modalOverlay = document.getElementById('modalOverlay');
            const openModalBtn = document.getElementById('openModal');
            const closeModalBtn = document.getElementById('closeModal');

            // Open modal
            if (openModalBtn) {
                openModalBtn.addEventListener('click', function() {
                    modalOverlay.classList.add('active');
                    document.body.style.overflow = 'hidden';
                });
            }

            // Close modal
            function closeModal() {
                modalOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            if (closeModalBtn) {
                closeModalBtn.addEventListener('click', closeModal);
            }

            // Close on overlay click
            modalOverlay.addEventListener('click', function(e) {
                if (e.target === modalOverlay) {
                    closeModal();
                }
            });

            // Close on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modalOverlay.classList.contains('active')) {
                    closeModal();
                }
            });

            // File handling
            const fileInput = document.getElementById('fileInput');
            const fileDropArea = document.getElementById('fileDropArea');
            const selectedFile = document.getElementById('selectedFile');
            const fileName = document.getElementById('fileName');
            const removeFile = document.getElementById('removeFile');
            const importBtn = document.getElementById('importBtn');

            // File selection handling
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    if (e.target.files.length > 0) {
                        fileName.textContent = e.target.files[0].name;
                        selectedFile.classList.add('show');
                        importBtn.disabled = false;
                    }
                });
            }

            // Remove file
            if (removeFile) {
                removeFile.addEventListener('click', function(e) {
                    e.stopPropagation();
                    fileInput.value = '';
                    selectedFile.classList.remove('show');
                    importBtn.disabled = true;
                });
            }

            // Drag and drop functionality
            if (fileDropArea) {
                fileDropArea.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    fileDropArea.style.borderColor = '#2563eb';
                    fileDropArea.style.background = '#f8faff';
                });

                fileDropArea.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    fileDropArea.style.borderColor = '#e0e0e0';
                    fileDropArea.style.background = '#fafafa';
                });

                fileDropArea.addEventListener('drop', function(e) {
                    e.preventDefault();
                    fileDropArea.style.borderColor = '#e0e0e0';
                    fileDropArea.style.background = '#fafafa';
                    
                    if (e.dataTransfer.files.length > 0) {
                        fileInput.files = e.dataTransfer.files;
                        fileName.textContent = e.dataTransfer.files[0].name;
                        selectedFile.classList.add('show');
                        importBtn.disabled = false;
                    }
                });
            }

            // Set today's date as default for date inputs
            const today = new Date().toISOString().split('T')[0];
            const dateTo = document.getElementById('dateTo');
            if (dateTo) {
                dateTo.value = today;
            }
        });
    </script>
</x-app-layout>
