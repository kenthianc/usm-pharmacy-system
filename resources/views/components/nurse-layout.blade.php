<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'USM Pharmacy') }} - {{ Auth::user()?->hasRole('admin') ? 'Admin Control' : 'Nurse Portal' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-100 text-slate-900 h-screen overflow-hidden" x-data="{ sidebarOpen: true, mobileSidebar: false, showNewModal: {{ ((isset($errors) && $errors->any() && old('_is_modal')) || request('new') || request()->routeIs('prescriptions.create')) ? 'true' : 'false' }} }" @open-new-prescription.window="showNewModal = true">

@php
    $pendingCount = \App\Models\Prescription::pending()->count();
    $lowStockCount = Auth::user()?->hasRole('admin') ? \App\Models\Medicine::with(['stockBatches' => function ($q) {
        $q->where('status', 'received')
            ->where('expiry_date', '>=', now()->toDateString())
            ->where('quantity_remaining', '>', 0);
    }])->get()->filter(fn ($m) => $m->available_stock <= $m->reorder_level)->count() : 0;
    $currentRoute = request()->route()->getName();
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
        <!-- Logo & Branding -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-[#0b5c35] bg-[#064e2b] shrink-0">
            <a href="{{ Auth::user()?->hasRole('admin') ? route('admin.dashboard') : route('dashboard') }}" class="flex items-center gap-3 overflow-hidden">
                @if(Auth::user()?->hasRole('admin'))
                    <div class="w-9 h-9 rounded-lg bg-yellow-500 text-green-950 flex items-center justify-center font-bold text-lg shadow-sm border border-yellow-300 shrink-0">
                        ℞
                    </div>
                    <div class="transition-opacity duration-200" x-show="sidebarOpen" x-transition>
                        <div class="text-sm font-bold tracking-tight text-yellow-400 leading-tight">USM Pharmacy</div>
                        <div class="text-[11px] font-semibold text-emerald-300/90 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span>
                            Admin Control
                        </div>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center shrink-0">
                        <div class="w-9 h-9 rounded-lg bg-emerald-800/80 border border-emerald-500/40 flex items-center justify-center text-amber-400 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                        </div>
                        <span class="text-[9px] font-extrabold tracking-wider text-emerald-200 uppercase mt-0.5">USM</span>
                    </div>
                    <div class="transition-opacity duration-200" x-show="sidebarOpen" x-transition>
                        <div class="text-sm font-bold tracking-tight text-amber-400 leading-tight">USM Pharmacy</div>
                        <div class="text-xs font-medium text-emerald-300/90 mt-0.5">Nurse Portal</div>
                    </div>
                @endif
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
            @if(Auth::user()?->hasRole('admin'))
                <!-- Section 1: Admin Governance (Consistent with Admin Layout) -->
                <div>
                    <div x-show="sidebarOpen" class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-yellow-400/80 mb-2">
                        Admin Governance
                    </div>

                    <div class="space-y-1">
                        @php $isDash = request()->routeIs('admin.dashboard'); @endphp
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ $isDash ? 'bg-yellow-500 text-green-950 shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}"
                           :title="!sidebarOpen ? 'Admin Hub' : ''">
                            <svg class="w-5 h-5 shrink-0 {{ $isDash ? 'text-green-950' : 'text-emerald-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect width="7" height="9" x="3" y="3" rx="1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <rect width="7" height="5" x="14" y="3" rx="1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <rect width="7" height="9" x="14" y="12" rx="1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <rect width="7" height="5" x="3" y="16" rx="1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span x-show="sidebarOpen" class="truncate">Overview Dashboard</span>
                        </a>

                        @php $isUsers = request()->routeIs('admin.users.*'); @endphp
                        <a href="{{ route('admin.users.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ $isUsers ? 'bg-yellow-500 text-green-950 shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}"
                           :title="!sidebarOpen ? 'Staff & Users' : ''">
                            <svg class="w-5 h-5 shrink-0 {{ $isUsers ? 'text-green-950' : 'text-emerald-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span x-show="sidebarOpen" class="truncate">Staff &amp; Users</span>
                        </a>

                        @php $isAudit = request()->routeIs('admin.audit-logs'); @endphp
                        <a href="{{ route('admin.audit-logs') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ $isAudit ? 'bg-yellow-500 text-green-950 shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}"
                           :title="!sidebarOpen ? 'Audit Logs' : ''">
                            <svg class="w-5 h-5 shrink-0 {{ $isAudit ? 'text-green-950' : 'text-emerald-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span x-show="sidebarOpen" class="truncate">Compliance Logs</span>
                        </a>

                        @php $isReports = request()->routeIs('admin.reports'); @endphp
                        <a href="{{ route('admin.reports') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ $isReports ? 'bg-yellow-500 text-green-950 shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}"
                           :title="!sidebarOpen ? 'Analytics' : ''">
                            <svg class="w-5 h-5 shrink-0 {{ $isReports ? 'text-green-950' : 'text-emerald-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span x-show="sidebarOpen" class="truncate">Clinic Analytics</span>
                        </a>

                        @php $isSettings = request()->routeIs('admin.settings.*'); @endphp
                        <a href="{{ route('admin.settings.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ $isSettings ? 'bg-yellow-500 text-green-950 shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}"
                           :title="!sidebarOpen ? 'Settings' : ''">
                            <svg class="w-5 h-5 shrink-0 {{ $isSettings ? 'text-green-950' : 'text-emerald-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span x-show="sidebarOpen" class="truncate">Clinic Settings</span>
                        </a>
                    </div>
                </div>

                <!-- Section 2: Clinical Operations -->
                <div>
                    <div x-show="sidebarOpen" class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-300/70 mb-2">
                        Clinical Operations
                    </div>

                    <div class="space-y-1">
                        @php
                            $isPrescriptions = request()->routeIs('prescriptions.index')
                                || request()->routeIs('prescriptions.create')
                                || request()->routeIs('prescriptions.show');
                        @endphp
                        <a href="{{ route('prescriptions.index') }}"
                           class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ $isPrescriptions ? 'bg-yellow-500 text-green-950 shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}"
                           :title="!sidebarOpen ? 'Prescriptions' : ''">
                            <div class="flex items-center gap-3 truncate">
                                <svg class="w-4 h-4 shrink-0 {{ $isPrescriptions ? 'text-green-950' : 'text-emerald-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <rect width="8" height="4" x="8" y="2" rx="1" ry="1" stroke-width="2"/>
                                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span x-show="sidebarOpen" class="truncate">Prescriptions</span>
                            </div>
                            @if ($pendingCount > 0)
                                <span x-show="sidebarOpen" class="w-5 h-5 rounded-full {{ $isPrescriptions ? 'bg-green-950 text-yellow-400' : 'bg-yellow-400 text-green-950' }} font-bold text-[10px] flex items-center justify-center shrink-0 shadow-xs">
                                    {{ $pendingCount }}
                                </span>
                            @endif
                        </a>

                        @php $isInventory = request()->routeIs('prescriptions.inventory') || request()->routeIs('inventory.index'); @endphp
                        <a href="{{ route('inventory.index') }}"
                           class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold {{ $isInventory ? 'bg-yellow-500 text-green-950 font-bold shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }} transition-all"
                           :title="!sidebarOpen ? 'Warehouse Inventory' : ''">
                            <div class="flex items-center gap-3 truncate">
                                <svg class="w-4 h-4 shrink-0 {{ $isInventory ? 'text-green-950' : 'text-emerald-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <span x-show="sidebarOpen" class="truncate">Inventory</span>
                            </div>
                            @if($lowStockCount > 0)
                                <span x-show="sidebarOpen" class="bg-rose-500 text-white text-[10px] font-extrabold px-1.5 py-0.2 rounded-full">
                                    {{ $lowStockCount }}
                                </span>
                            @endif
                        </a>
                    </div>
                </div>
            @else
                <!-- Standard Nurse Navigation Menu -->
                @php $isDashboard = request()->routeIs('dashboard'); @endphp
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-bold transition-all {{ $isDashboard ? 'bg-amber-500 text-[#064e2b] shadow-sm' : 'text-emerald-100/90 hover:bg-white/10 hover:text-white' }}"
                   :title="!sidebarOpen ? 'Dashboard' : ''">
                    <svg class="w-5 h-5 shrink-0 {{ $isDashboard ? 'text-[#064e2b]' : 'text-emerald-200' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="truncate">Dashboard</span>
                </a>

                @php
                    $isPrescriptions = (request()->routeIs('prescriptions.index')
                        || request()->routeIs('prescriptions.create')
                        || request()->routeIs('prescriptions.show'))
                        && ! request()->routeIs('prescriptions.inventory')
                        && ! request()->routeIs('prescriptions.patients');
                @endphp
                <a href="{{ route('prescriptions.index') }}"
                   class="flex items-center justify-between px-3.5 py-3 rounded-xl text-sm font-bold transition-all {{ $isPrescriptions ? 'bg-amber-500 text-[#064e2b] shadow-sm' : 'text-emerald-100/90 hover:bg-white/10 hover:text-white' }}"
                   :title="!sidebarOpen ? 'Prescriptions' : ''">
                    <div class="flex items-center gap-3 min-w-0">
                        <svg class="w-5 h-5 shrink-0 {{ $isPrescriptions ? 'text-[#064e2b]' : 'text-emerald-200' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span x-show="sidebarOpen" class="truncate">Prescriptions</span>
                    </div>
                    @if ($pendingCount > 0)
                        <span x-show="sidebarOpen" class="w-5 h-5 rounded-full {{ $isPrescriptions ? 'bg-[#064e2b] text-amber-400' : 'bg-amber-500 text-[#064e2b]' }} font-bold text-xs flex items-center justify-center shrink-0 shadow-xs">
                            {{ $pendingCount }}
                        </span>
                        <span x-show="!sidebarOpen" class="w-2.5 h-2.5 rounded-full {{ $isPrescriptions ? 'bg-[#064e2b]' : 'bg-amber-500' }} absolute right-2 top-3"></span>
                    @endif
                </a>

                @php $isInventory = request()->routeIs('prescriptions.inventory'); @endphp
                <a href="{{ route('prescriptions.inventory') }}"
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-bold transition-all {{ $isInventory ? 'bg-amber-500 text-[#064e2b] shadow-sm' : 'text-emerald-100/90 hover:bg-white/10 hover:text-white' }}"
                   :title="!sidebarOpen ? 'Inventory Check' : ''">
                    <svg class="w-5 h-5 shrink-0 {{ $isInventory ? 'text-[#064e2b]' : 'text-emerald-200' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="truncate">Inventory Check</span>
                </a>

                @php $isPatients = request()->routeIs('prescriptions.patients') || request()->routeIs('patients.*'); @endphp
                <a href="{{ route('prescriptions.patients') }}"
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-bold transition-all {{ $isPatients ? 'bg-amber-500 text-[#064e2b] shadow-sm' : 'text-emerald-100/90 hover:bg-white/10 hover:text-white' }}"
                   :title="!sidebarOpen ? 'Patients' : ''">
                    <svg class="w-5 h-5 shrink-0 {{ $isPatients ? 'text-[#064e2b]' : 'text-emerald-200' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="truncate">Patients</span>
                </a>

                @php $isProfile = request()->routeIs('profile.*'); @endphp
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-bold transition-all {{ $isProfile ? 'bg-amber-500 text-[#064e2b] shadow-sm' : 'text-emerald-100/90 hover:bg-white/10 hover:text-white' }}"
                   :title="!sidebarOpen ? 'Settings' : ''">
                    <svg class="w-5 h-5 shrink-0 {{ $isProfile ? 'text-[#064e2b]' : 'text-emerald-200' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="truncate">Settings</span>
                </a>
            @endif
        </nav>

        <!-- Bottom User Profile & Sign Out Block -->
        <div class="p-3 border-t border-[#0b5c35] bg-[#064e2b] shrink-0 space-y-3">
            @if(Auth::user()?->hasRole('admin'))
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-full bg-yellow-500 text-green-950 flex items-center justify-center font-bold text-xs shrink-0 shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-1" x-show="sidebarOpen">
                        <div class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] text-yellow-300 font-semibold truncate">Chief Pharmacist / Admin</div>
                    </div>
                </div>

                <button type="button"
                        @click="$dispatch('open-signout-modal')"
                        x-show="sidebarOpen"
                        class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-emerald-300 hover:bg-rose-900/40 hover:text-rose-200 transition-colors cursor-pointer"
                        title="Sign out of Admin Portal">
                    <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Sign Out</span>
                </button>
            @else
                <!-- Nurse Profile -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-amber-500 text-[#064e2b] flex items-center justify-center font-bold shrink-0 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1" x-show="sidebarOpen">
                        <div class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-emerald-300 truncate">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <button type="button"
                        @click="$dispatch('open-signout-modal')"
                        x-show="sidebarOpen"
                        class="flex items-center gap-3 text-emerald-300 hover:text-white font-semibold text-sm transition-colors group cursor-pointer pt-1"
                        title="Sign out of Nurse Portal">
                    <svg class="w-5 h-5 text-emerald-300 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Log Out</span>
                </button>
            @endif
        </div>
    </aside>

    <!-- Main Content Area -->
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

                <!-- Page Heading / Breadcrumb -->
                <div>
                    @isset($heading)
                        {{ $heading }}
                    @else
                        <h1 class="text-base sm:text-lg font-bold text-slate-800 leading-tight">
                            @if(request()->routeIs('dashboard')) Clinic Dashboard
                            @elseif(request()->routeIs('prescriptions.create')) Encode Prescription
                            @elseif(request()->routeIs('prescriptions.show')) Prescription Details
                            @elseif(request()->routeIs('prescriptions.inventory')) Inventory Check
                            @elseif(request()->routeIs('prescriptions.patients')) Patient Directory
                            @elseif(request()->routeIs('prescriptions.*')) Prescription Module
                            @elseif(request()->routeIs('medicines')) Pharmacy Catalog
                            @elseif(request()->routeIs('profile.*')) Account Settings
                            @else {{ config('app.name') }}
                            @endif
                        </h1>
                    @endisset
                </div>
            </div>

            <!-- Top Actions & Notifications -->
            <div class="flex items-center gap-3">
                @isset($actions)
                    {{ $actions }}
                @else
                    @can('create', App\Models\Prescription::class)
                        @if(!request()->routeIs('prescriptions.create'))
                            <button type="button"
                               @click="$dispatch('open-new-prescription')"
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-sm shadow-emerald-700/20 transition-all transform active:scale-98 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Encode Prescription</span>
                            </button>
                        @endif
                    @endcan
                @endisset

                <!-- Pending Notifications Bell -->
                <a href="{{ route('prescriptions.index', ['status' => 'pending']) }}"
                   class="relative p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors"
                   title="{{ $pendingCount }} Pending Prescriptions">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    @if ($pendingCount > 0)
                        <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-amber-500 rounded-full ring-2 ring-white animate-pulse"></span>
                    @endif
                </a>
            </div>
        </header>

        <!-- Page Content (Only Content Area Scrolls) -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            {{ $slot }}
        </main>
    </div>

</div>

    <!-- Global Floating Modal for Creating Prescriptions -->
    @can('create', App\Models\Prescription::class)
        @include('prescriptions.partials.modal-create')
    @endcan

    <!-- Sign Out Confirmation Modal UI -->
    <x-signout-modal />

</body>
</html>