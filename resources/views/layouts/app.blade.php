@if(Auth::check() && Auth::user()->hasRole('admin'))
    <x-admin-layout>
        @isset($header)
            <x-slot name="header">
                {{ $header }}
            </x-slot>
        @endisset
        @isset($actions)
            <x-slot name="actions">
                {{ $actions }}
            </x-slot>
        @endisset

        {{ $slot }}
    </x-admin-layout>
@elseif(Auth::check() && Auth::user()->hasRole('stock_manager'))
    <x-stock-layout>
        @isset($header)
            <x-slot name="header">
                {{ $header }}
            </x-slot>
        @endisset
        @isset($actions)
            <x-slot name="actions">
                {{ $actions }}
            </x-slot>
        @endisset

        {{ $slot }}
    </x-stock-layout>
@elseif(Auth::check() && Auth::user()->hasRole('nurse'))
    <x-nurse-layout>
        @isset($header)
            <x-slot name="heading">
                {{ $header }}
            </x-slot>
        @endisset

        {{ $slot }}
    </x-nurse-layout>
@else
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

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white border-b border-slate-200/80 shadow-xs">
                    <div class="max-w-7xl mx-auto py-3.5 px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
                        <div class="min-w-0">
                            {{ $header }}
                        </div>
                        <div class="flex items-center gap-2.5 sm:gap-3 shrink-0">
                            @isset($actions)
                                {{ $actions }}
                                <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>
                            @endisset

                            <!-- Consistent Risk Summary Pill -->
                            <x-risk-summary-pill />

                            <!-- Current Date Badge -->
                            <span class="hidden md:inline-flex items-center gap-1.5 text-xs text-slate-400 font-medium whitespace-nowrap select-none">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>{{ now()->format('M d, Y') }}</span>
                            </span>
                        </div>
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <!-- Global Sign Out Confirmation Modal UI -->
        <x-signout-modal />

        <!-- Dual-Risk Prediction Engine Drawer Modal -->
        <x-dual-risk-engine-modal />
    </body>
</html>
@endif
