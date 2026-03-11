@php
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 leading-tight">
            {{ __('Job Listing') }}
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ __('Create and manage job listings to attract qualified candidates. Edit job details, update job status, and monitor applications submitted for each job posting.') }}
        </p>
    </x-slot>

    <div class="space-y-6">
        <x-ui.toolbar>
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">{{ __('Your Job Posts') }}</h3>
                    <span class="inline-flex rounded-md shadow-sm isolate">
                        <a href="{{ route('employer.job-posts.index', ['status' => 'all']) }}" class="relative inline-flex items-center rounded-l-md border px-3 py-2 text-sm font-medium focus:z-10 {{ ($statusFilter ?? '') === 'all' ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">
                            {{ __('All') }}
                        </a>
                        <a href="{{ route('employer.job-posts.index', ['status' => 'published']) }}" class="relative -ml-px inline-flex items-center border px-3 py-2 text-sm font-medium focus:z-10 {{ ($statusFilter ?? 'published') === 'published' ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">
                            {{ __('Active') }}
                        </a>
                        <a href="{{ route('employer.job-posts.index', ['status' => 'draft']) }}" class="relative -ml-px inline-flex items-center border px-3 py-2 text-sm font-medium focus:z-10 {{ ($statusFilter ?? '') === 'draft' ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">
                            {{ __('Draft') }}
                        </a>
                        <a href="{{ route('employer.job-posts.index', ['status' => 'closed']) }}" class="relative -ml-px inline-flex items-center border px-3 py-2 text-sm font-medium focus:z-10 {{ ($statusFilter ?? '') === 'closed' ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">
                            {{ __('Closed') }}
                        </a>
                        <a href="{{ route('employer.job-posts.index', ['status' => 'archive']) }}" class="relative -ml-px inline-flex items-center rounded-r-md border px-3 py-2 text-sm font-medium focus:z-10 {{ ($statusFilter ?? '') === 'archive' ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">
                            {{ __('Archive') }}
                        </a>
                    </span>
                </div>
            </div>
            <a href="{{ route('employer.job-posts.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                {{ __('Create Job') }}
            </a>
        </x-ui.toolbar>

        @if (session('success'))
            <div class="rounded-xl bg-green-50 p-4 text-sm text-green-700 border border-green-100">
                {{ session('success') }}
            </div>
        @endif

        <x-ui.card>
            @if ($jobPosts->isEmpty())
                <p class="text-sm text-gray-500">{{ __('No job posts yet.') }}</p>
            @else
                <x-ui.table>
                    <thead class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50">
                        <tr>
                            <th class="py-3 px-4">{{ __('Title') }}</th>
                            <th class="py-3 px-4">{{ __('Type') }}</th>
                            <th class="py-3 px-4">{{ __('Location') }}</th>
                            <th class="py-3 px-4">{{ __('Status') }}</th>
                            <th class="py-3 px-4 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @foreach ($jobPosts as $jobPost)
                            <tr class="border-t hover:bg-gray-50/60">
                                <td class="py-3 px-4 font-semibold text-gray-900">{{ $jobPost->title }}</td>
                                <td class="py-3 px-4">{{ Str::of($jobPost->job_type)->replace('_', ' ')->title() }}</td>
                                <td class="py-3 px-4">{{ $jobPost->location ?? __('Remote') }}</td>
                                <td class="py-3 px-4">
                                    @if (($statusFilter ?? '') === 'archive' || $jobPost->trashed())
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold bg-amber-100 text-amber-800">
                                            {{ __('Archived') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold bg-gray-100 text-gray-700">
                                            {{ Str::of($jobPost->status)->title() }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('employer.job-posts.show', $jobPost) }}" class="inline-flex">
                                            <x-ui.icon-button variant="primary" size="sm" :label="__('View')">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12 18 19.5 12 19.5 2.25 12 2.25 12z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.75A3.75 3.75 0 1112 8.25a3.75 3.75 0 010 7.5z"/>
                                                </svg>
                                            </x-ui.icon-button>
                                        </a>
                                        <a href="{{ route('employer.job-posts.edit', $jobPost) }}" class="inline-flex">
                                            <x-ui.icon-button variant="ghost" size="sm" :label="__('Edit')">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125L16.875 4.5"/>
                                                </svg>
                                            </x-ui.icon-button>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>
                <div class="mt-4">
                    {{ $jobPosts->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
</x-app-layout>
