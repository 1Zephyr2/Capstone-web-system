<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>FURCARE | {{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('furcare.ico') }}">

        <!-- Scripts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
    <style>html { font-size: 112%; }</style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    },
                }
            }
        }
    </script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col">

        <nav class="w-full bg-white border-b border-gray-200 shadow-sm">
            <div class="container mx-auto px-4 sm:px-6 py-4 flex items-center justify-center">
                <a href="/" class="text-lg font-bold flex items-center gap-2 text-emerald-700">
                    <img src="{{ asset('paw-icon.png') }}" class="w-7 h-7" alt="Logo"> FURCARE
                </a>
            </div>
        </nav>

        <main class="flex-grow flex flex-col items-center justify-center py-12 px-6">
            <div class="w-full sm:max-w-md bg-white border border-gray-200 shadow-sm rounded-2xl px-6 py-8 sm:px-8">
                {{ $slot }}
            </div>
        </main>

        <footer class="py-8 text-center border-t border-gray-100">
            <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} FURCARE. All rights reserved.</p>
        </footer>
    </body>
</html>
