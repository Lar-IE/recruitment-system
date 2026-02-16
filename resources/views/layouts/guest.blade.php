@props(['fullWidth' => false])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="font-sans text-primary antialiased">
        @if($fullWidth)
            <div class="min-h-screen w-full" style="width: 100%; max-width: 100vw; overflow-x: hidden;">
                {{ $slot }}
            </div>
        @else
            <div class="min-h-screen flex flex-col sm:justify-center items-center px-4 py-8 bg-gray-100">
                <div class="w-full sm:max-w-md px-6 py-6 bg-white shadow-md overflow-hidden sm:rounded-lg">
                    {{ $slot }}
                </div>
            </div>
        @endif
    </body>
</html>
