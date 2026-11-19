<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admission & Bed Management — USM Hospital' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-100 text-slate-900 h-screen overflow-hidden" x-data="{ sidebarOpen: true, mobileSidebar: false }">

@php
    $availBeds = \App\Models\Bed::where('occupancy_status', 'available')->count();
    $pendingEndorsements = \App\Models\PatientEndorsement::where('acknowledgment_status', 'pending')->count();
@endphp

<div class="h-screen flex flex-col lg:flex-row overflow-hidden">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="mobileSidebar"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-black/60 lg:hidden"
         @click="mobileSidebar = false"
         style="display: none;">
    </div>

    <!-- Sidebar -->
    <aside :class="{'translate-x-0': mobileSidebar, '-translate-x-full': !mobileSidebar, 'w-64': sidebarOpen, 'w-20': !sidebarOpen}"
           class="fixed inset-y-0 left-0 z-50 flex flex-col bg-[#064e2b] text-white transition-all duration-300 ease-in-out lg:static lg:translate-x-0 border-r border-[#043c20] shadow-xl h-screen shrink-0">

        <!-- Logo & Branding -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-[#0b5c35] bg-[#064e2b] shrink-0">
            <a href="{{ route('admission.index') }}" class="flex items-center gap-3 overflow-hidden">
                <div class="w-9 h-9 rounded-lg bg-emerald-800/90 border border-amber-400/40 flex items-center justify-center text-amber-300 font-bold text-base shadow-sm shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="transition-opacity duration-200" x-show="sidebarOpen" x-transition>
                    <div class="text-sm font-bold tracking-tight text-amber-400 leading-tight">USM Hospital</div>
                    <div class="text-[11px] font-semibold text-emerald-200 flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        Admission Staff
                    </div>
                </div>
            </a>

            <button @click="mobileSidebar = false" class="lg:hidden text-emerald-300 hover:text-white p-1 rounded-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-3 py-4 space-y-2 overflow-y-auto">
            <div x-show="sidebarOpen" class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-300/80 mb-2">
                Clinical Intake & Wards
            </div>

            <!-- Admission Dashboard -->
            <a href="{{ route('admission.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admission.index') ? 'bg-amber-400 text-[#064e2b] shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}"
               :title="!sidebarOpen ? 'Admission Hub' : ''">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span x-show="sidebarOpen" class="truncate">Admission Dashboard</span>
            </a>

            <!-- Bed & Ward Visual Board -->
            <a href="{{ route('admission.beds') }}"
               class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admission.beds') ? 'bg-amber-400 text-[#064e2b] shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}"
               :title="!sidebarOpen ? 'Ward & Bed Visual Board' : ''">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <span x-show="sidebarOpen" class="truncate">Ward & Bed Board</span>
                </div>
                <span x-show="sidebarOpen" class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-800 text-emerald-200 border border-emerald-600">
                    {{ $availBeds }} Free
                </span>
            </a>

            <!-- Patient Registry & Intake -->
            <a href="{{ route('admission.patients') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admission.patients') ? 'bg-amber-400 text-[#064e2b] shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}"
               :title="!sidebarOpen ? 'Patient Registry' : ''">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span x-show="sidebarOpen" class="truncate">Patient Registry</span>
            </a>

            @if(Auth::user()?->hasRole('admin'))
                <div class="pt-4 border-t border-[#0b5c35] mt-4">
                    <div x-show="sidebarOpen" class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-amber-300/80 mb-2">
                        Admin Switch
                    </div>
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-emerald-200 hover:bg-white/10 hover:text-white transition-all">
                        <svg class="w-4 h-4 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span x-show="sidebarOpen" class="truncate">Back to Admin Hub</span>
                    </a>
                </div>
            @endif
        </nav>

        <!-- User Profile Strip & Signout -->
        <div class="p-3 border-t border-[#0b5c35] bg-[#053e22] shrink-0" x-data="{ userMenu: false }">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5 overflow-hidden">
                    <div class="w-8 h-8 rounded-full bg-amber-400 text-green-950 font-bold flex items-center justify-center text-xs shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="truncate text-left" x-show="sidebarOpen">
                        <p class="text-xs font-bold text-white truncate leading-tight">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-emerald-300 capitalize truncate">Admission Officer</p>
                    </div>
                </div>

                <div x-show="sidebarOpen">
                    <button type="button" @click="$dispatch('open-signout-modal')"
                            class="p-1.5 rounded-lg text-emerald-300 hover:text-white hover:bg-white/10 transition-colors"
                            title="Sign Out">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">

        <!-- Top Header Bar -->
        <header class="h-16 bg-white border-b border-slate-200 shadow-xs flex items-center justify-between px-4 sm:px-6 shrink-0 z-10">
            <div class="flex items-center gap-3">
                <button @click="mobileSidebar = true" class="lg:hidden text-slate-600 hover:text-slate-900 p-2 rounded-lg hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:flex text-slate-500 hover:text-slate-800 p-2 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                </button>

                <div>
                    <h1 class="text-sm font-bold text-slate-800 sm:text-base leading-tight">{{ $header ?? 'Admission Management Portal' }}</h1>
                    <p class="text-[11px] text-slate-500 hidden sm:block">USM Hospital Management System &bull; Patient Intake, Bed Allotment & Nurse Endorsement</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Free beds badge -->
                <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-800">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>{{ $availBeds }} Beds Available</span>
                </div>

                <!-- Clock / Date indicator -->
                <div class="text-xs font-medium text-slate-500 hidden xl:block">
                    {{ now()->format('M d, Y | h:i A') }}
                </div>
            </div>
        </header>

        <!-- Flash Notifications -->
        @if (session('success'))
            <div class="mx-4 sm:mx-6 mt-4 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-medium flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div class="mx-4 sm:mx-6 mt-4 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-medium flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800">&times;</button>
            </div>
        @endif

        <!-- Scrollable Page Content -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 min-w-0">
            {{ $slot }}
        </main>
    </div>
</div>

<x-signout-modal />

</body>
</html>
