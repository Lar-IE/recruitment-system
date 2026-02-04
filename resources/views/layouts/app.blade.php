<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @php
                $maintenanceEnabled = \App\Models\Setting::getValue('maintenance_mode', '0') === '1';
                $isAdmin = auth()->check() && auth()->user()->role?->value === 'admin';
            @endphp

            @if ($isAdmin && $maintenanceEnabled)
                <div class="bg-amber-500 text-white">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div class="text-sm font-medium">
                            {{ __('Maintenance mode is currently enabled.') }}
                        </div>
                        <form method="POST" action="{{ route('admin.settings.toggle-maintenance') }}">
                            @csrf
                            <button type="submit" class="text-xs font-semibold bg-white/20 hover:bg-white/30 px-3 py-1 rounded">
                                {{ __('Disable now') }}
                            </button>
                        </form>
                    </div>
                </div>
            @elseif ($isAdmin && ! $maintenanceEnabled)
                <div class="bg-indigo-600 text-white">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div class="text-sm font-medium">
                            {{ __('Maintenance mode is off.') }}
                        </div>
                        <form method="POST" action="{{ route('admin.settings.toggle-maintenance') }}">
                            @csrf
                            <button type="submit" class="text-xs font-semibold bg-white/20 hover:bg-white/30 px-3 py-1 rounded">
                                {{ __('Enable now') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
