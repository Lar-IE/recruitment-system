<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $siteName }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">
    <div class="min-h-screen flex items-center justify-center px-6">
        <div class="max-w-md text-center space-y-4">
            <h1 class="text-2xl font-semibold">{{ $siteName }}</h1>
            <p class="text-gray-600">{{ __('We are currently under maintenance. Please try again later.') }}</p>
            <p class="text-sm text-gray-500">{{ __('Need help? Contact :email', ['email' => $supportEmail]) }}</p>
        </div>
    </div>
</body>
</html>
