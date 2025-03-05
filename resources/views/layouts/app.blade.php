<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    <style>
        .highlight {
    background-color: yellow; /* Color for completed words */
    font-weight: bold;
}

        .selected {
            background-color: lightgreen; /* Color for selected words */
            font-weight: bold;
        }
    </style>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900 antialiased">
    {{ $slot }}
    @livewireScripts
</body>
</html> 