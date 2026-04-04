<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @yield('page-styles')
    @livewireStyles
</head>
<body class="bg-light">

    {{-- Navbar --}}
    @include('layouts.navigation')

    {{-- Page Heading --}}
    @isset($header)
        <header class="bg-white shadow-sm mb-4">
            <div class="container py-3">
                {{ $header }}
            </div>
        </header>
    @endisset

    {{-- Page Content --}}
    <main class="container">
        @yield('content')
    </main>

    @yield('page-scripts')
    @livewireScripts
</body>
</html>