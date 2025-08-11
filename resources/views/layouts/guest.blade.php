<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

        <title>@yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('img/wri.png') }}">
        <!-- Fonts -->
        <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-8 sm:pt-0">

             <div>
                <!-- <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a> -->
            </div> 

            <div class="w-full sm:max-w-md mt-6 px-6 py-4  sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>

        
    </body>
</html>
