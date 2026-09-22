@props(['active' => 'dashboard', 'title' => 'USM Pharmacy — Patient Portal'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-100 text-slate-900 h-screen overflow-hidden"
      x-data="{ sidebarOpen: true, mobileSidebar: false }">

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

    <!-- ── Left Sidebar ── -->
    <aside :class="{'translate-x-0': mobileSidebar, '-translate-x-full': !mobileSidebar, 'w-64': sidebarOpen, 'w-20': !sidebarOpen}"
           class="fixed inset-y-0 left-0 z-50 flex flex-col bg-[#064e2b] text-white transition-all duration-300 ease-in-out lg:static lg:translate-x-0 border-r border-[#043c20] shadow-xl h-screen shrink-0">

        <!-- Logo & Branding -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-[#0b5c35] bg-[#064e2b] shrink-0">
            <a href="{{ route('patient.dashboard') }}" class="flex items-center gap-3 overflow-hidden">
                <div class="w-9 h-9 rounded-lg bg-yellow-500 text-green-950 flex items-center justify-center font-bold text-lg shadow-sm border border-yellow-300 shrink-0">
                    <svg viewBox="0 0 32 32" class="w-6 h-6 text-green-950" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 3L4 8v8c0 6.627 5.373 12 12 12s12-5.373 12-12V8L16 3z" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
                        <path d="M11 16l3 3 7-7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="transition-opacity duration-200" x-show="sidebarOpen" x-transition>
                    <div class="text-sm font-bold tracking-tight text-yellow-400 leading-tight">USM Pharmacy</div>
                    <div class="text-[11px] font-semibold text-emerald-300/90 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span>
                        Patient Portal
                    </div>
                </div>
            </a>

            <!-- Mobile Close Button -->
            <button @click="mobileSidebar = false" class="lg:hidden text-emerald-300 hover:text-white p-1 rounded-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-3 py-4 space-y-4 overflow-y-auto">
            <div>
                <div x-show="sidebarOpen" class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-yellow-400/80 mb-2">
                    Patient Care
                </div>

                <div class="space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('patient.dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ $active === 'dashboard' ? 'bg-yellow-500 text-green-950 shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}"
                       :title="!sidebarOpen ? 'Dashboard' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ $active === 'dashboard' ? 'text-green-950' : 'text-emerald-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect width="7" height="9" x="3" y="3" rx="1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <rect width="7" height="5" x="14" y="3" rx="1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <rect width="7" height="9" x="14" y="12" rx="1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <rect width="7" height="5" x="3" y="16" rx="1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span x-show="sidebarOpen" class="truncate">Dashboard</span>
                    </a>

                    <!-- Prescriptions -->
                    <a href="{{ route('patient.prescriptions') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ in_array($active, ['prescriptions', 'prescription-detail']) ? 'bg-yellow-500 text-green-950 shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}"
                       :title="!sidebarOpen ? 'Prescriptions' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ in_array($active, ['prescriptions', 'prescription-detail']) ? 'text-green-950' : 'text-emerald-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect width="8" height="4" x="8" y="2" rx="1" ry="1" stroke-width="2"/>
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 11h4M12 16h4M8 11h.01M8 16h.01" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span x-show="sidebarOpen" class="truncate">Prescriptions</span>
                    </a>

                    <!-- Pharmacy Storefront -->
                    <a href="{{ route('medicines') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ $active === 'storefront' ? 'bg-yellow-500 text-green-950 shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}"
                       :title="!sidebarOpen ? 'Pharmacy Store' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ $active === 'storefront' ? 'text-green-950' : 'text-emerald-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <line x1="3" y1="6" x2="21" y2="6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16 10a4 4 0 01-8 0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span x-show="sidebarOpen" class="truncate">Pharmacy Store</span>
                    </a>

                    <!-- AI Assistant -->
                    <a href="{{ route('patient.dashboard') }}#chat"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ $active === 'chat' ? 'bg-yellow-500 text-green-950 shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}"
                       :title="!sidebarOpen ? 'AI Assistant' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ $active === 'chat' ? 'text-green-950' : 'text-emerald-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 8V4m0 0a2 2 0 100-4 2 2 0 000 4z" stroke-width="2" stroke-linecap="round"/>
                            <rect width="16" height="12" x="4" y="8" rx="2" stroke-width="2"/>
                            <path d="M2 14h2M20 14h2M9 13v2M15 13v2" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span x-show="sidebarOpen" class="truncate">AI Assistant</span>
                    </a>

                    <!-- Profile -->
                    <a href="{{ route('patient.profile') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ $active === 'profile' ? 'bg-yellow-500 text-green-950 shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}"
                       :title="!sidebarOpen ? 'My Profile' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ $active === 'profile' ? 'text-green-950' : 'text-emerald-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="4" stroke-width="2"/>
                            <path d="M18 20a6 6 0 00-12 0" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span x-show="sidebarOpen" class="truncate">My Profile</span>
                    </a>
                </div>
            </div>
        </nav>

        <!-- Bottom User Profile & Sign Out Block -->
        <div class="p-3 border-t border-[#0b5c35] bg-[#064e2b] shrink-0 space-y-3">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-full bg-yellow-500 text-green-950 flex items-center justify-center font-bold text-xs shrink-0 shadow-sm">
                    {{ strtoupper(substr(Auth::user()->name ?? 'PT', 0, 2)) }}
                </div>
                <div class="min-w-0 flex-1" x-show="sidebarOpen">
                    <div class="text-xs font-bold text-white truncate">{{ Auth::user()->name ?? 'Patient' }}</div>
                    <div class="text-[10px] text-yellow-300 font-semibold truncate">
                        {{ Auth::user()->patient?->id_number ?? 'Student / Outpatient' }}
                    </div>
                </div>
            </div>

            <button type="button"
                    @click="$dispatch('open-signout-modal')"
                    x-show="sidebarOpen"
                    class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-emerald-300 hover:bg-rose-900/40 hover:text-rose-200 transition-colors cursor-pointer"
                    title="Sign out of Patient Portal">
                <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Sign Out</span>
            </button>
        </div>
    </aside>

    <!-- ── Main Content Area ── -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">

        <!-- Top Header Bar -->
        <header class="h-16 bg-white border-b border-slate-200/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between sticky top-0 z-30 shadow-xs shrink-0">
            <div class="flex items-center gap-3">
                <!-- Mobile Open Toggle -->
                <button @click="mobileSidebar = true" class="lg:hidden p-2 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <!-- Desktop Collapse Toggle -->
                <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:flex p-2 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"></path>
                    </svg>
                </button>

                <!-- Page Heading / Breadcrumbs -->
                <div>
                    @isset($header)
                        {{ $header }}
                    @else
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-md">
                                Patient Portal
                            </span>
                            <h1 class="text-base sm:text-lg font-bold text-slate-800 leading-tight">
                                @if($active === 'dashboard') Medical Dashboard
                                @elseif($active === 'prescriptions') Prescription Records
                                @elseif($active === 'profile') My Medical Profile
                                @elseif($active === 'storefront') University Dispensary
                                @elseif($active === 'chat') AI Health Assistant
                                @else Patient Portal
                                @endif
                            </h1>
                        </div>
                    @endisset
                </div>
            </div>

            <!-- Top Date Badge -->
            <div class="flex items-center gap-3">
                <div class="hidden md:flex items-center gap-1 text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ now()->format('M d, Y') }}</span>
                </div>
            </div>
        </header>

        <!-- Main Scrollable Content -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-50/70">
            {{ $slot }}
        </main>
    </div>

</div>

<!-- Sign Out Confirmation Modal UI -->
<x-signout-modal />

</body>
</html>
