@props(['title' => 'USM Pharmacy — Inventory & Stock Management', 'active' => 'dashboard'])

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
    $medicinesList = \App\Models\Medicine::with(['stockBatches' => function ($q) {
        $q->where('status', 'received')
            ->where('expiry_date', '>=', now()->toDateString())
            ->where('quantity_remaining', '>', 0);
    }])->get();

    $lowStockCount = $medicinesList->filter(fn ($m) => $m->available_stock <= $m->reorder_level)->count();
    $outOfStockCount = $medicinesList->filter(fn ($m) => $m->available_stock === 0)->count();
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
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden">
                <div class="w-9 h-9 rounded-lg bg-yellow-500 text-green-950 flex items-center justify-center font-bold text-lg shadow-sm border border-yellow-300 shrink-0">
                    📦
                </div>
                <div class="transition-opacity duration-200" x-show="sidebarOpen" x-transition>
                    <div class="text-sm font-bold tracking-tight text-yellow-400 leading-tight">USM Pharmacy</div>
                    <div class="text-[11px] font-semibold text-emerald-300/90 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span>
                        Inventory Portal
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

            <!-- Section 1: Core Warehouse Operations -->
            <div>
                <div x-show="sidebarOpen" class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-yellow-400/80 mb-2">
                    Warehouse &amp; Stock
                </div>

                <div class="space-y-1">
                    <!-- Dashboard -->
                    @php $isDash = request()->routeIs('dashboard'); @endphp
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ $isDash ? 'bg-yellow-500 text-green-950 shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}"
                       :title="!sidebarOpen ? 'Dashboard' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ $isDash ? 'text-green-950' : 'text-emerald-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect width="7" height="9" x="3" y="3" rx="1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <rect width="7" height="5" x="14" y="3" rx="1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <rect width="7" height="9" x="14" y="12" rx="1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <rect width="7" height="5" x="3" y="16" rx="1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span x-show="sidebarOpen" class="truncate">Dashboard</span>
                    </a>

                    <!-- Inventory Formulary -->
                    @php $isInv = request()->routeIs('inventory.index') || request()->routeIs('inventory.medicines.show') || request()->routeIs('inventory.medicines.edit'); @endphp
                    <a href="{{ route('inventory.index') }}"
                       class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ $isInv ? 'bg-yellow-500 text-green-950 shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}"
                       :title="!sidebarOpen ? 'Inventory Formulary' : ''">
                        <div class="flex items-center gap-3 min-w-0">
                            <svg class="w-5 h-5 shrink-0 {{ $isInv ? 'text-green-950' : 'text-emerald-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <span x-show="sidebarOpen" class="truncate">Inventory Formulary</span>
                        </div>
                        @if($lowStockCount > 0)
                            <span x-show="sidebarOpen" class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $isInv ? 'bg-green-950 text-amber-300' : 'bg-amber-400 text-green-950' }}">
                                {{ $lowStockCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Movements & Deliveries -->
                    @php $isMovements = request()->routeIs('inventory.movements'); @endphp
                    <a href="{{ route('inventory.movements') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ $isMovements ? 'bg-yellow-500 text-green-950 shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}"
                       :title="!sidebarOpen ? 'Delivery & Stock Logs' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ $isMovements ? 'text-green-950' : 'text-emerald-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <span x-show="sidebarOpen" class="truncate">Delivery &amp; Logs</span>
                    </a>

                    <!-- Dual-Risk Prediction Engine Dropdown -->
                    @php 
                        $isRisk = request()->routeIs('inventory.risk-engine*');
                        $currentTab = request('tab', 'overview');
                    @endphp
                    <div x-data="{ riskOpen: {{ $isRisk ? 'true' : 'false' }} }" class="space-y-1">
                        <button type="button"
                                @click="if(!sidebarOpen) { sidebarOpen = true; riskOpen = true; } else { riskOpen = !riskOpen; }"
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer {{ $isRisk ? 'bg-emerald-800/90 text-yellow-300 shadow-2xs' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}"
                                :title="!sidebarOpen ? 'Dual-Risk Engine' : ''">
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-5 h-5 shrink-0 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span x-show="sidebarOpen" class="truncate">Dual-Risk Engine</span>
                            </div>
                            <div class="flex items-center gap-1.5" x-show="sidebarOpen">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="riskOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </button>

                        <!-- Dropdown Sub-menu Items -->
                        <div x-show="riskOpen && sidebarOpen" x-transition class="ml-4 pl-3 border-l-2 border-emerald-600/60 space-y-1 py-1">
                            <!-- 1. Overview -->
                            <a href="{{ route('inventory.risk-engine', ['tab' => 'overview']) }}"
                               class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all {{ $isRisk && $currentTab === 'overview' ? 'bg-yellow-500 text-green-950 font-bold shadow-xs' : 'text-emerald-200/90 hover:bg-white/10 hover:text-white' }}">
                                <span>📊</span>
                                <span class="truncate">Overview</span>
                            </a>

                            <!-- 2. Stockout Risk -->
                            <a href="{{ route('inventory.risk-engine', ['tab' => 'stockout']) }}"
                               class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all {{ $isRisk && $currentTab === 'stockout' ? 'bg-yellow-500 text-green-950 font-bold shadow-xs' : 'text-emerald-200/90 hover:bg-white/10 hover:text-white' }}">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span>⚡</span>
                                    <span class="truncate">Stockout Risk</span>
                                </div>
                                @if($lowStockCount > 0)
                                    <span class="px-1.5 py-0.2 text-[9px] font-extrabold rounded-full bg-rose-500 text-white">
                                        {{ $lowStockCount }}
                                    </span>
                                @endif
                            </a>

                            <!-- 3. Expiry Risk -->
                            <a href="{{ route('inventory.risk-engine', ['tab' => 'expiry']) }}"
                               class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all {{ $isRisk && $currentTab === 'expiry' ? 'bg-yellow-500 text-green-950 font-bold shadow-xs' : 'text-emerald-200/90 hover:bg-white/10 hover:text-white' }}">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span>⏳</span>
                                    <span class="truncate">Expiry Risk</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Quick Inbound Actions -->
            <div>
                <div x-show="sidebarOpen" class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-300/70 mb-2">
                    Quick Actions
                </div>

                <div class="space-y-1">
                    @can('create', App\Models\Delivery::class)
                        @php $isDeliveryCreate = request()->routeIs('inventory.deliveries.create'); @endphp
                        <a href="{{ route('inventory.deliveries.create') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ $isDeliveryCreate ? 'bg-emerald-700/80 text-yellow-300 font-bold' : 'text-emerald-200/90 hover:bg-white/10 hover:text-white' }}"
                           :title="!sidebarOpen ? 'Request Delivery' : ''">
                            <svg class="w-4 h-4 shrink-0 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span x-show="sidebarOpen" class="truncate">Request Delivery</span>
                        </a>
                    @endcan

                    @can('create', App\Models\Medicine::class)
                        @php $isMedCreate = request()->routeIs('inventory.medicines.create'); @endphp
                        <a href="{{ route('inventory.medicines.create') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ $isMedCreate ? 'bg-emerald-700/80 text-yellow-300 font-bold' : 'text-emerald-200/90 hover:bg-white/10 hover:text-white' }}"
                           :title="!sidebarOpen ? 'Add New Medicine' : ''">
                            <svg class="w-4 h-4 shrink-0 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <span x-show="sidebarOpen" class="truncate">Add New Medicine</span>
                        </a>
                    @endcan

                    @php $isExport = request()->routeIs('inventory.export-pdf'); @endphp
                    <a href="{{ route('inventory.export-pdf') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all text-emerald-200/90 hover:bg-white/10 hover:text-white"
                       :title="!sidebarOpen ? 'Export PDF' : ''">
                        <svg class="w-4 h-4 shrink-0 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span x-show="sidebarOpen" class="truncate">Export Inventory PDF</span>
                    </a>

                    @php $isProfile = request()->routeIs('profile.edit'); @endphp
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ $isProfile ? 'bg-emerald-700/80 text-yellow-300 font-bold' : 'text-emerald-200/90 hover:bg-white/10 hover:text-white' }}"
                       :title="!sidebarOpen ? 'Account Settings' : ''">
                        <svg class="w-4 h-4 shrink-0 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span x-show="sidebarOpen" class="truncate">Account Settings</span>
                    </a>
                </div>
            </div>
        </nav>

        <!-- User Profile & Sign Out Footer -->
        <div class="p-3 border-t border-[#0b5c35] bg-[#064e2b] shrink-0 space-y-3">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-full bg-yellow-500 text-green-950 flex items-center justify-center font-bold text-xs shrink-0 shadow-sm">
                    {{ strtoupper(substr(Auth::user()->name ?? 'SK', 0, 2)) }}
                </div>
                <div class="min-w-0 flex-1" x-show="sidebarOpen">
                    <div class="text-xs font-bold text-white truncate">{{ Auth::user()->name ?? 'Stock Keeper' }}</div>
                    <div class="text-[10px] text-yellow-300 font-semibold truncate">Stock Manager / Custodian</div>
                </div>
            </div>

            <button type="button"
                    @click="$dispatch('open-signout-modal')"
                    x-show="sidebarOpen"
                    class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-emerald-300 hover:bg-rose-900/40 hover:text-rose-200 transition-colors cursor-pointer"
                    title="Sign out of Stock Keeper Portal">
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
        <header class="h-16 bg-white border-b border-slate-200/80 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-30 shadow-xs shrink-0 gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <!-- Mobile Open Toggle -->
                <button @click="mobileSidebar = true" class="lg:hidden p-2 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 shrink-0" aria-label="Open sidebar menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <!-- Desktop Collapse Toggle -->
                <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:flex p-2 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 shrink-0" aria-label="Toggle sidebar collapse">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"></path>
                    </svg>
                </button>

                <!-- Page Heading / Breadcrumbs -->
                <div class="min-w-0">
                    @isset($header)
                        {{ $header }}
                    @elseif(isset($heading))
                        {{ $heading }}
                    @else
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-md shrink-0">
                                Stock Management
                            </span>
                            <h1 class="text-base sm:text-lg font-bold text-slate-800 leading-tight truncate">
                                @if(request()->routeIs('dashboard')) Stock Overview
                                @elseif(request()->routeIs('inventory.index')) Inventory Formulary
                                @elseif(request()->routeIs('inventory.movements')) Stock Deliveries &amp; Movements
                                @elseif(request()->routeIs('inventory.risk-engine')) Risk Forecasting
                                @elseif(request()->routeIs('inventory.medicines.create')) Add New Medicine
                                @elseif(request()->routeIs('inventory.deliveries.create')) Request Delivery
                                @elseif(request()->routeIs('profile.*')) Account Settings
                                @else Hospital Inventory
                                @endif
                            </h1>
                        </div>
                    @endisset
                </div>
            </div>

            <!-- Top Actions & Unified Status Cluster (Far Right) -->
            <div class="flex items-center gap-2.5 sm:gap-3 shrink-0">
                @isset($actions)
                    {{ $actions }}
                    <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>
                @endisset

                <!-- Consistent Risk Summary Pill -->
                <x-risk-summary-pill />

                <span class="hidden md:inline-flex items-center gap-1.5 text-xs text-slate-400 font-medium whitespace-nowrap select-none">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ now()->format('M d, Y') }}</span>
                </span>
            </div>
        </header>

        <!-- Main Scrollable Content -->
        <main class="flex-1 overflow-y-auto p-3 sm:p-5 lg:p-6 bg-slate-50/70">
            {{ $slot }}
        </main>
    </div>

</div>

<!-- Global Sign Out Confirmation Modal UI -->
<x-signout-modal />

<!-- Dual-Risk Prediction Engine Modal -->
<x-dual-risk-engine-modal />

</body>
</html>
