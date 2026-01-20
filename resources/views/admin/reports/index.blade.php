<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <h3 class="text-lg font-semibold">{{ __('Export Reports (CSV)') }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <a href="{{ route('admin.reports.applications') }}" class="inline-flex items-center justify-center rounded-md border px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            {{ __('Applications Report') }}
                        </a>
                        <a href="{{ route('admin.reports.users') }}" class="inline-flex items-center justify-center rounded-md border px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            {{ __('Users Report') }}
                        </a>
                        <a href="{{ route('admin.reports.hiring') }}" class="inline-flex items-center justify-center rounded-md border px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            {{ __('Hiring Summary') }}
                        </a>
                    </div>
                    <p class="text-xs text-gray-500">{{ __('CSV exports can be opened in Excel or Google Sheets.') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
