@props(['title' => 'USM Pharmacy — Admin Portal', 'active' => 'dashboard'])

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

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-100 text-slate-900 h-screen overflow-hidden" 
      x-data="{ sidebarOpen: true, mobileSidebar: false }">

@php
    $pendingRxCount = \App\Models\Prescription::pending()->count();
    $lowStockCount = \App\Models\Medicine::with(['stockBatches' => function ($q) {
        $q->where('status', 'received')
            ->where('expiry_date', '>=', now()->toDateString())
            ->where('quantity_remaining', '>', 0);
    }])->get()->filter(fn ($m) => $m->available_stock <= $m->reorder_level)->count();
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

    <!-- ── Sidebar ── -->
    <aside :class="{'translate-x-0': mobileSidebar, '-translate-x-full': !mobileSidebar, 'w-64': sidebarOpen, 'w-20': !sidebarOpen}"
           class="fixed inset-y-0 left-0 z-50 flex flex-col bg-[#064e2b] text-white transition-all duration-300 ease-in-out lg:static lg:translate-x-0 border-r border-[#043c20] shadow-xl h-screen shrink-0">

        <!-- Logo & Branding -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-[#0b5c35] bg-[#064e2b] shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 overflow-hidden">
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
            </a>

            <!-- Mobile Close Button -->
            <button @click="mobileSidebar = false" class="lg:hidden text-emerald-300 hover:text-white p-1 rounded-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-3 py-4 space-y-5 overflow-y-auto">

            <!-- Section 1: Admin Governance -->
            <div>
                <div x-show="sidebarOpen" class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-yellow-400/80 mb-2">
                    Admin Governance
                </div>

                <div class="space-y-1">
                    <!-- Overview Dashboard -->
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

                    <!-- Staff & Users -->
                    @php $isUsers = request()->routeIs('admin.users.*'); @endphp
                    <a href="{{ route('admin.users.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ $isUsers ? 'bg-yellow-500 text-green-950 shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}"
                       :title="!sidebarOpen ? 'Staff & Users' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ $isUsers ? 'text-green-950' : 'text-emerald-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span x-show="sidebarOpen" class="truncate">Staff &amp; Users</span>
                    </a>

                    <!-- Audit Logs -->
                    @php $isAudit = request()->routeIs('admin.audit-logs'); @endphp
                    <a href="{{ route('admin.audit-logs') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ $isAudit ? 'bg-yellow-500 text-green-950 shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}"
                       :title="!sidebarOpen ? 'Audit Logs' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ $isAudit ? 'text-green-950' : 'text-emerald-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span x-show="sidebarOpen" class="truncate">Compliance Logs</span>
                    </a>

                    <!-- Analytics & Reports -->
                    @php $isReports = request()->routeIs('admin.reports'); @endphp
                    <a href="{{ route('admin.reports') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ $isReports ? 'bg-yellow-500 text-green-950 shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}"
                       :title="!sidebarOpen ? 'Analytics' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ $isReports ? 'text-green-950' : 'text-emerald-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span x-show="sidebarOpen" class="truncate">Clinic Analytics</span>
                    </a>

                    <!-- Operational Settings -->
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

            <!-- Section 2: Clinical Modules (Super-Admin Access) -->
            <div>
                <div x-show="sidebarOpen" class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-300/70 mb-2">
                    Clinical Operations
                </div>

                <div class="space-y-1">
                    <!-- Prescriptions -->
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
                        @if($pendingRxCount > 0)
                            <span x-show="sidebarOpen" class="w-5 h-5 rounded-full {{ $isPrescriptions ? 'bg-green-950 text-yellow-400' : 'bg-yellow-400 text-green-950' }} font-bold text-[10px] flex items-center justify-center shrink-0 shadow-xs">
                                {{ $pendingRxCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Warehouse Inventory -->
                    @php $isInventory = request()->routeIs('prescriptions.inventory') || request()->routeIs('inventory.index') || request()->routeIs('inventory.medicines.*'); @endphp
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

        </nav>

        <!-- Bottom User Profile & Sign Out -->
        <div class="p-3 border-t border-[#0b5c35] bg-[#064e2b] shrink-0 space-y-3">
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
        </div>
    </aside>

    <!-- ── Main Content Area ── -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">

        <!-- Top Header Bar -->
        <header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-30 shadow-xs shrink-0">
            <div class="flex items-center gap-3">
                <!-- Mobile Open Toggle -->
                <button @click="mobileSidebar = true" class="lg:hidden p-2 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <!-- Desktop Collapse Toggle -->
                <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:flex p-2 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/>
                    </svg>
                </button>

                <!-- Page Heading -->
                <div>
                    @isset($header)
                        {{ $header }}
                    @elseif(isset($heading))
                        {{ $heading }}
                    @else
                        <div class="flex items-center gap-2">
                            <h1 class="text-base font-extrabold text-slate-800">
                                @if(request()->routeIs('admin.dashboard')) Admin Control Center
                                @elseif(request()->routeIs('admin.users.*')) Staff &amp; User Accounts
                                @elseif(request()->routeIs('admin.audit-logs')) Compliance Audit Trail
                                @elseif(request()->routeIs('admin.reports')) Clinic &amp; Pharmacy Analytics
                                @elseif(request()->routeIs('admin.settings.*')) Operational Settings
                                @else Admin Portal
                                @endif
                            </h1>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                Super-Admin
                            </span>
                        </div>
                    @endisset
                </div>
            </div>

            <!-- Top Right Action Controls & Status Cluster -->
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
        </header>

        <!-- Scrollable Main Content -->
        <main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
            {{ $slot }}
        </main>
    </div>

</div>

<!-- Dual-Risk Engine Global Drawer Modal -->
<x-dual-risk-engine-modal />

<!-- Sign Out Confirmation Modal UI -->
<x-signout-modal />

</body>
</html>
