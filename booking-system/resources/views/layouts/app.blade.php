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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">

            @include('layouts.navigation')

            <div style="display:flex; min-height:calc(100vh - 64px);">

                <aside style="width:240px; background:#111827; color:white; padding:20px;">
                    <h3 style="font-size:14px; color:#9ca3af; margin-bottom:12px;">
                        Menu
                    </h3>

                    <a href="{{ route('dashboard') }}" style="display:block; color:white; padding:10px; text-decoration:none;">
                        Dashboard
                    </a>

                    <a href="{{ route('appointments.calendar') }}" style="display:block; color:white; padding:10px; text-decoration:none;">
                        Calendar
                    </a>
                    
                    <a href="{{ route('appointments.index') }}" style="display:block; color:white; padding:10px; text-decoration:none;">
                        Appointments
                    </a>

                    <a href="{{ route('staff.index') }}" style="display:block; color:white; padding:10px; text-decoration:none;">
                        Staff
                    </a>

                    <a href="{{ route('customers.index') }}" style="display:block; color:white; padding:10px; text-decoration:none;">
                        Customers
                    </a>

                    <a href="{{ route('services.index') }}" style="display:block; color:white; padding:10px; text-decoration:none;">
                        Services
                    </a>

                    <a href="{{ route('service-categories.index') }}" style="display:block; color:white; padding:10px; text-decoration:none;">
                        Service Categories
                    </a>

                    <a href="{{ route('booking.form') }}" target="_blank" style="display:block; color:white; padding:10px; text-decoration:none;">
                        Public Booking Page
                    </a>
                </aside>

                <main style="flex:1;">
                    @isset($header)
                        <header class="bg-white shadow">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    {{ $slot }}
                </main>

            </div>
        </div>
    </body>
</html>
