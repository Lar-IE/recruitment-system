@php
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Job Posting') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">{{ __('Your Job Posts') }}</h3>
                <a href="{{ route('employer.job-posts.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                    {{ __('Create Job') }}
                </a>
            </div>

            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($jobPosts->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No job posts yet.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left text-gray-500">
                                    <tr>
                                        <th class="py-2 pr-4">{{ __('Title') }}</th>
                                        <th class="py-2 pr-4">{{ __('Type') }}</th>
                                        <th class="py-2 pr-4">{{ __('Location') }}</th>
                                        <th class="py-2 pr-4">{{ __('Status') }}</th>
                                        <th class="py-2 pr-4 text-right">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    @foreach ($jobPosts as $jobPost)
                                        <tr class="border-t">
                                            <td class="py-2 pr-4 font-medium">{{ $jobPost->title }}</td>
                                            <td class="py-2 pr-4">{{ Str::of($jobPost->job_type)->replace('_', ' ')->title() }}</td>
                                            <td class="py-2 pr-4">{{ $jobPost->location ?? __('Remote') }}</td>
                                            <td class="py-2 pr-4">
                                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700">
                                                    {{ Str::of($jobPost->status)->title() }}
                                                </span>
                                            </td>
                                            <td class="py-2 pr-4 text-right space-x-2">
                                                <a href="{{ route('employer.job-posts.show', $jobPost) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('View') }}</a>
                                                <a href="{{ route('employer.job-posts.edit', $jobPost) }}" class="text-gray-600 hover:text-gray-900">{{ __('Edit') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $jobPosts->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
