<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>USM Pharmacy - POS Terminal & Dispensary</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* USM Pharmacy Theme Direct Styles to guarantee immediate rendering */
        :root {
            --usm-green-dark: #043c20;
            --usm-green: #064e2b;
            --usm-green-light: #0d7844;
            --usm-gold: #f59e0b;
            --usm-gold-hover: #fbbf24;
        }

        .bg-usm-dark { background-color: #043c20 !important; }
        .bg-usm-green { background-color: #064e2b !important; }
        .bg-usm-green-light { background-color: #0d7844 !important; }
        .border-usm-green { border-color: #064e2b !important; }
        .text-usm-gold { color: #f59e0b !important; }
        .bg-usm-gold { background-color: #f59e0b !important; }
        .border-usm-gold { border-color: #f59e0b !important; }

        /* Custom scrollbar for high-speed scrolling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #thermal-receipt,
            #thermal-receipt * {
                visibility: visible;
            }

            #thermal-receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 80mm !important;
                max-width: 80mm !important;
                padding: 10px !important;
                background: #ffffff !important;
                color: #000000 !important;
                box-shadow: none !important;
                font-family: monospace, sans-serif !important;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body class="font-sans antialiased h-screen overflow-hidden bg-slate-100 text-slate-900 select-none">

    <div x-data="posTerminal()" x-init="initData()" @keydown.window="handleShortcuts($event)" class="h-screen flex flex-col overflow-hidden bg-slate-100">

        <!-- ── Top Dispensary Header ─────────────────────────────────────────── -->
        <header
            class="h-16 bg-usm-dark text-white flex items-center justify-between px-5 gap-4 shrink-0 z-20 shadow-md border-b border-emerald-900">
            
            <!-- Left: Brand & Navigation -->
            <div class="flex items-center gap-4">
                <a href="{{ Auth::user()?->hasRole('admin') ? route('admin.dashboard') : route('dashboard') }}" title="{{ Auth::user()?->hasRole('admin') ? 'Back to Admin Portal (Esc)' : 'Back to Main Dashboard (Esc)' }}"
                    class="bg-emerald-900/80 hover:bg-emerald-800 text-emerald-100 hover:text-white px-3 py-2 rounded-xl transition-all flex items-center gap-2 border border-emerald-700/60 shadow-xs text-xs font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>{{ Auth::user()?->hasRole('admin') ? 'Admin Portal' : 'Back' }}</span>
                </a>

                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-amber-400 text-emerald-950 flex items-center justify-center font-black text-xl shadow-md border-2 border-amber-300">
                        ℞
                    </div>
                    <div class="leading-none">
                        <div class="flex items-center gap-2">
                            <span class="text-xl font-black text-amber-400 tracking-tight">USM</span>
                            <span class="text-emerald-500 text-lg font-bold">/</span>
                            <span class="text-base sm:text-lg font-bold text-white tracking-wide">Pharmacy / POS Module</span>
                        </div>
                        <p class="text-[11px] font-semibold text-emerald-300 uppercase tracking-widest mt-0.5">Dispensary & Point of Sale</p>
                    </div>
                </div>
            </div>

            <!-- Center/Right Quick Actions & Stats -->
            <div class="flex items-center gap-3">
                <!-- Consistent Risk Summary Pill -->
                <x-risk-summary-pill />

                <!-- Executive Sales Reports link -->
                <a href="{{ route('pos.reports') }}"
                    class="flex items-center gap-2 bg-emerald-900/90 hover:bg-emerald-800 text-emerald-100 hover:text-white border border-emerald-600/80 text-sm font-bold px-4 py-2 rounded-xl transition-all cursor-pointer shadow-sm">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    <span>Sales Reports</span>
                </a>


                <!-- Live Queue Counter Pill -->
                <button @click="goQueue()"
                    :class="queue.length > 0 ? 'bg-rose-600 hover:bg-rose-700 text-white shadow-rose-900/30' : 'bg-emerald-900/60 text-emerald-300 border border-emerald-700/60'"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-black transition-all cursor-pointer shadow-sm">
                    <span class="relative flex h-2.5 w-2.5" x-show="queue.length > 0">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-200 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-white"></span>
                    </span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span x-text="queue.length + ' in queue'"></span>
                </button>

                <!-- Pharmacist Profile Badge -->
                <div class="flex items-center gap-3 pl-3 border-l border-emerald-800/80">
                    <div
                        class="w-10 h-10 bg-emerald-800 rounded-xl flex items-center justify-center text-sm font-black text-amber-400 border border-emerald-600 shadow-inner">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="hidden sm:block leading-tight">
                        <p class="text-sm font-bold text-white">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-amber-300 font-semibold uppercase tracking-wider">Licensed Pharmacist</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- ── Flash notifications ───────────────────────────────────────────── -->
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show"
                class="bg-emerald-600 text-white text-sm font-bold px-6 py-3 flex items-center justify-between shadow-md transition-all">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-200 hover:text-white text-lg font-black">&times;</button>
            </div>
        @endif
        @if (session('error'))
            <div x-data="{ show: true }" x-show="show"
                class="bg-rose-600 text-white text-sm font-bold px-6 py-3 flex items-center justify-between shadow-md transition-all">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button @click="show = false" class="text-rose-200 hover:text-white text-lg font-black">&times;</button>
            </div>
        @endif

        <!-- ── Main Dispensary Workspace ────────────────────────────────────── -->
        <div class="flex-1 flex overflow-hidden">

            <!-- ── Left Dispensary Panel ──────────────────────────────────────── -->
            <div class="flex-1 flex flex-col overflow-hidden bg-white border-r border-slate-200">

                <!-- Streamlined Ergonomic Tab Strip -->
                <div class="flex items-stretch gap-0 border-b-2 border-slate-200 bg-slate-50/80 shrink-0 select-none overflow-x-auto">
                    
                    <!-- TAB 1: Prescription Queue (In-Patient / Nurse Routed) -->
                    <button @click="setMode('queue')"
                        :class="(mode === 'queue' || mode === 'processing') ?
                        'border-emerald-700 text-emerald-900 bg-white shadow-xs font-black' :
                        'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-bold'"
                        class="flex items-center gap-2 px-5 py-4 text-sm sm:text-base border-b-4 transition-all cursor-pointer whitespace-nowrap">
                        <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                        <span>Prescription Queue</span>
                        <span x-show="queue.length > 0"
                            :class="(mode === 'queue') ? 'bg-emerald-700 text-white' : 'bg-rose-600 text-white'"
                            class="text-xs font-black px-2 py-0.5 rounded-full shadow-xs" x-text="queue.length"></span>
                        <span class="text-[10px] font-mono font-bold text-slate-400 bg-slate-200/80 px-1.5 py-0.5 rounded">1</span>
                    </button>

                    <!-- TAB 2: Counter & OTC Sale -->
                    <button @click="goOtc()"
                        :class="mode === 'otc' ? 'border-emerald-700 text-emerald-900 bg-white shadow-xs font-black' :
                            'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-bold'"
                        class="flex items-center gap-2 px-5 py-4 text-sm sm:text-base border-b-4 transition-all cursor-pointer whitespace-nowrap">
                        <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <span>Counter &amp; OTC</span>
                        <span x-show="otcCart.length > 0"
                            class="text-xs font-black px-2 py-0.5 rounded-full bg-amber-400 text-emerald-950 shadow-xs"
                            x-text="otcCart.reduce((s, i) => s + i.qty, 0)"></span>
                        <span class="text-[10px] font-mono font-bold text-slate-400 bg-slate-200/80 px-1.5 py-0.5 rounded">2</span>
                    </button>

                    <!-- TAB 3: History / Dispensing Logs -->
                    <button @click="setMode('history')"
                        :class="mode === 'history' ? 'border-emerald-700 text-emerald-900 bg-white shadow-xs font-black' :
                            'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-bold'"
                        class="flex items-center gap-2 px-5 py-4 text-sm sm:text-base border-b-4 transition-all cursor-pointer whitespace-nowrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>History / Dispensing Logs</span>
                        <span class="text-[10px] font-mono font-bold text-slate-400 bg-slate-200/80 px-1.5 py-0.5 rounded">3</span>
                    </button>

                    <div class="ml-auto pr-6 flex items-center gap-2 text-xs font-bold text-slate-400">
                        <span class="hidden xl:inline">Shortcuts: 1-3 Switch tabs · Esc Back</span>
                    </div>
                </div>

                <!-- Left Content Area (Scrollable with rich padding and typography) -->
                <div class="flex-1 overflow-y-auto p-6 bg-slate-50/50">

                    <!-- ── MODE: RX QUEUE ─────────────────────────────────────── -->
                    <div x-show="mode === 'queue'" class="max-w-6xl mx-auto space-y-4">
                        
                        <!-- Empty Queue State -->
                        <template x-if="queue.length === 0">
                            <div class="flex flex-col items-center justify-center py-24 px-6 text-center bg-white rounded-3xl border-2 border-dashed border-slate-300 shadow-sm">
                                <div
                                    class="w-20 h-20 rounded-3xl bg-emerald-100 text-emerald-700 flex items-center justify-center mb-5 shadow-inner">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="font-black text-slate-900 text-2xl">Dispensary Queue is Clear!</h3>
                                <p class="text-base text-slate-500 max-w-md mt-2">All clinic-routed patient prescriptions have been safely fulfilled.</p>
                                <div class="mt-8">
                                    <button @click="goOtc()"
                                        class="flex items-center gap-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-base font-extrabold px-8 py-4 rounded-2xl shadow-md transition-all cursor-pointer active:scale-95">
                                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                            </path>
                                        </svg>
                                        <span>Start Counter &amp; OTC Sale</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <!-- Non-empty Queue List -->
                        <template x-if="queue.length > 0">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between px-1 pb-1">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-3 h-3 rounded-full bg-emerald-600 animate-pulse"></span>
                                        <p class="text-sm font-black text-slate-700 uppercase tracking-wider">
                                            Nurse-Routed Prescriptions · FIFO Priority
                                        </p>
                                    </div>
                                    <span class="text-sm font-extrabold text-emerald-800 bg-emerald-100/80 px-3.5 py-1 rounded-xl border border-emerald-200"
                                        x-text="queue.length + ' Pending Orders'"></span>
                                </div>

                                <template x-for="rx in queue" :key="rx.id">
                                    <div
                                        class="border-2 border-slate-200 rounded-3xl p-6 hover:border-emerald-600 hover:shadow-xl transition-all duration-150 bg-white relative group">
                                        
                                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                                            
                                            <!-- Patient & Clinical Details -->
                                            <div class="flex items-start gap-4 min-w-0">
                                                
                                                <!-- High-Visibility Wait Urgency Pill -->
                                                <div :class="getUrgencyClass(rx.routedAt)"
                                                    class="inline-flex flex-col items-center justify-center text-center px-3.5 py-2.5 rounded-2xl shrink-0 shadow-xs border">
                                                    <svg class="w-4 h-4 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span class="text-xs font-black leading-tight" x-text="getWaitTimeLabel(rx.routedAt)"></span>
                                                    <span class="text-[9px] uppercase tracking-wider font-extrabold opacity-80">Wait</span>
                                                </div>

                                                <div class="min-w-0 space-y-2">
                                                    <!-- Patient Name & Badges -->
                                                    <div class="flex items-center gap-3 flex-wrap">
                                                        <h3 class="font-black text-slate-900 text-xl lg:text-2xl truncate leading-snug"
                                                            x-text="rx.patient.name"></h3>
                                                        
                                                        <!-- Patient Classification Badge -->
                                                        <span class="text-xs font-black uppercase tracking-wider px-3 py-1 rounded-xl shadow-xs"
                                                            :class="getTypeBadgeClass(rx.patient.type)"
                                                            x-text="rx.patient.type"></span>

                                                        <!-- Patient Reference ID -->
                                                        <span class="font-mono text-xs font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2.5 py-1 rounded-lg"
                                                            x-text="rx.patient.id"></span>

                                                        <!-- Rx ID Tag -->
                                                        <span class="font-mono text-xs font-black text-emerald-800 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-lg"
                                                            x-text="rx.id"></span>

                                                        <!-- Bed & Room Number Badge -->
                                                        <template x-if="rx.room_bed_number">
                                                            <span class="inline-flex items-center gap-1.5 bg-indigo-50 border border-indigo-200 text-indigo-900 text-xs font-black px-3 py-1 rounded-xl">
                                                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                                                </svg>
                                                                <span x-text="'Bed/Room: ' + rx.room_bed_number"></span>
                                                            </span>
                                                        </template>

                                                        <!-- Preparation Status Badge -->
                                                        <template x-if="rx.status === 'prepared'">
                                                            <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-900 border border-emerald-300 text-xs font-black px-3 py-1 rounded-xl">
                                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                                </svg>
                                                                <span>Prepared / Ready</span>
                                                            </span>
                                                        </template>
                                                        <template x-if="rx.status !== 'prepared'">
                                                            <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-900 border border-amber-300 text-xs font-black px-3 py-1 rounded-xl">
                                                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                                                <span>Pending Preparation</span>
                                                            </span>
                                                        </template>
                                                    </div>

                                                    <!-- Prescribing Physician & Nurse Station -->
                                                    <div class="flex items-center gap-3 text-sm font-semibold text-slate-600 flex-wrap">
                                                        <div class="flex items-center gap-1.5">
                                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                            </svg>
                                                            <span x-text="'Prescribing: ' + rx.doctor"></span>
                                                        </div>
                                                        <span class="text-slate-300">·</span>
                                                        <span class="text-slate-500 text-xs" x-text="'Nurse Station: ' + (rx.nurse || 'Ward Nurse')"></span>
                                                    </div>

                                                    <!-- High-Legibility Medicines Chips -->
                                                    <div class="flex flex-wrap gap-2 pt-1">
                                                        <template x-for="(item, i) in rx.items" :key="i">
                                                            <div
                                                                class="inline-flex items-center gap-2 text-sm font-extrabold text-emerald-950 bg-emerald-50 border-2 border-emerald-200/80 px-3.5 py-1.5 rounded-xl shadow-2xs">
                                                                <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                                                    </path>
                                                                </svg>
                                                                <template x-if="item.code">
                                                                    <span class="font-mono text-[11px] font-bold text-emerald-800 bg-emerald-200/70 px-1.5 py-0.5 rounded" x-text="item.code"></span>
                                                                </template>
                                                                <span x-text="item.medicine"></span>
                                                                <span class="bg-emerald-200/80 text-emerald-950 text-xs font-black px-2 py-0.5 rounded-lg"
                                                                    x-text="'×' + item.qty"></span>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Actions: Mark Prepared + Process Order Button -->
                                            <div class="shrink-0 flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                                                <template x-if="rx.status !== 'prepared'">
                                                    <button type="button" @click="markOrderPrepared(rx)"
                                                        class="flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-800 border-2 border-slate-300 text-xs font-black px-5 py-3.5 rounded-2xl transition-all cursor-pointer shadow-xs">
                                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        <span>Mark Prepared</span>
                                                    </button>
                                                </template>
                                                <button @click="startProcess(rx.id)"
                                                    class="flex items-center justify-center gap-3 bg-emerald-700 hover:bg-emerald-800 text-white text-base font-black px-8 py-4 rounded-2xl transition-all shadow-md hover:shadow-lg hover:scale-105 active:scale-95 cursor-pointer">
                                                    <span>Process Order</span>
                                                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- ── MODE: PROCESSING (Prescription Dispense View) ──────── -->
                    <div x-show="mode === 'processing' && processRx" class="max-w-6xl mx-auto space-y-6">
                        
                        <!-- Top Breadcrumb / Action Bar -->
                        <div class="flex items-center justify-between bg-white p-4 rounded-2xl border-2 border-slate-200 shadow-xs">
                            <div class="flex items-center gap-4">
                                <button @click="cancelProcess()"
                                    class="flex items-center gap-2 text-sm font-black text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-4 py-2.5 rounded-xl transition-all cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    <span x-text="'Back to Prescription Queue'"></span>
                                </button>
                                <span class="text-slate-300 font-bold">/</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-black uppercase tracking-wider text-slate-400">Processing:</span>
                                    <span class="font-mono text-base font-black text-emerald-800 bg-emerald-100 px-3 py-1 rounded-xl" x-text="processRx?.id"></span>
                                    <span class="text-xs font-black text-blue-800 bg-blue-100 border border-blue-300 px-2.5 py-1 rounded-xl">In-Patient Order</span>
                                </div>
                            </div>
                        </div>

                        <!-- Patient Clinical Information Banner -->
                        <div class="border-2 border-slate-200 rounded-3xl p-6 bg-white shadow-sm space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <p class="text-xs font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>Patient Clinical Dossier</span>
                                </p>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-black uppercase px-3 py-1 rounded-xl bg-blue-700 text-white shadow-2xs">
                                        🏥 In-Patient
                                    </span>
                                    <span class="text-xs font-black uppercase px-3 py-1 rounded-xl shadow-2xs"
                                        :class="getTypeBadgeClass(processRx?.patient.type)"
                                        x-text="processRx?.patient.type"></span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200">
                                    <p class="text-[11px] text-slate-500 font-extrabold uppercase tracking-wider">Patient Full Name</p>
                                    <p class="text-lg font-black text-slate-900 mt-0.5 truncate" x-text="processRx?.patient.name"></p>
                                </div>
                                <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200">
                                    <p class="text-[11px] text-slate-500 font-extrabold uppercase tracking-wider"
                                        x-text="processRx?.room_bed_number ? 'Bed / Room Number' : 'Patient ID / Reference'"></p>
                                    <p class="font-mono text-base font-black text-slate-800 mt-0.5"
                                        x-text="processRx?.room_bed_number || processRx?.patient.id"></p>
                                </div>
                                <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200">
                                    <p class="text-[11px] text-slate-500 font-extrabold uppercase tracking-wider">Prescribing Physician</p>
                                    <p class="text-base font-bold text-slate-800 mt-0.5 truncate" x-text="processRx?.doctor"></p>
                                </div>
                                <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200">
                                    <p class="text-[11px] text-slate-500 font-extrabold uppercase tracking-wider">Routing Nurse</p>
                                    <p class="text-base font-bold text-slate-800 mt-0.5 truncate" x-text="processRx?.nurse || 'Nurse Station'"></p>
                                </div>
                            </div>

                            <template x-if="processRx?.notes">
                                <div
                                    class="bg-amber-50 border-2 border-amber-300 rounded-2xl p-4 text-amber-950 flex items-start gap-3 shadow-2xs">
                                    <svg class="w-6 h-6 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-xs font-black uppercase tracking-wider text-amber-800">Physician Clinical Instructions</p>
                                        <p class="text-sm font-bold mt-0.5" x-text="processRx.notes"></p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- FEFO Automated Allocation Notice -->
                        <div class="flex items-center gap-3 bg-emerald-50 border-2 border-emerald-300 rounded-2xl p-4 shadow-2xs">
                            <div class="w-10 h-10 rounded-xl bg-emerald-700 text-white flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-emerald-900">FEFO (First-Expired, First-Out) Smart Inventory Allocation</h4>
                                <p class="text-xs font-semibold text-emerald-800 mt-0.5">Batches expiring soonest have been pre-allocated to meet prescribed amounts. You can manually adjust quantities with the stepper buttons below.</p>
                            </div>
                        </div>

                        <!-- Medicine Batch Allocations -->
                        <template x-for="item in processRx?.items" :key="item.id">
                            <div class="border-2 border-slate-200 rounded-3xl overflow-hidden bg-white shadow-sm">
                                
                                <!-- Medicine Row Header -->
                                <div class="px-6 py-4 border-b-2 border-slate-100 flex items-center justify-between bg-slate-50">
                                    <div class="flex items-center gap-3.5">
                                        <div
                                            class="w-10 h-10 bg-emerald-800 text-amber-400 rounded-xl flex items-center justify-center font-black text-base shadow-sm">
                                            ℞
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2.5 flex-wrap">
                                                <h4 class="font-black text-slate-900 text-lg" x-text="item.medicine"></h4>
                                                <template x-if="item.code">
                                                    <span class="font-mono text-xs font-semibold text-gray-700 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200" x-text="item.code"></span>
                                                </template>
                                            </div>
                                            <p class="text-xs font-bold text-slate-500" x-text="item.dosage"></p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-black bg-emerald-100 text-emerald-950 border border-emerald-300 px-4 py-1.5 rounded-xl shadow-2xs"
                                        x-text="'Prescribed: ' + item.qty + ' units'"></span>
                                </div>

                                <!-- Out of stock alert -->
                                <template x-if="item.batches.length === 0">
                                    <div class="p-6 text-sm font-black text-rose-700 bg-rose-50 flex items-center gap-3">
                                        <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                            </path>
                                        </svg>
                                        <span>CRITICAL: No active stock batches found for this medicine! Prescription cannot be fulfilled.</span>
                                    </div>
                                </template>

                                <!-- Batches Table -->
                                <template x-if="item.batches.length > 0">
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-left">
                                            <thead>
                                                <tr class="bg-slate-100/80 text-xs font-black text-slate-600 uppercase tracking-wider border-b border-slate-200">
                                                    <th class="px-6 py-3.5">Batch Number</th>
                                                    <th class="px-6 py-3.5">Expiry Date</th>
                                                    <th class="px-6 py-3.5 text-center">Available Stock</th>
                                                    <th class="px-6 py-3.5">Unit Price</th>
                                                    <th class="px-6 py-3.5 text-center">Dispense Qty</th>
                                                    <th class="px-6 py-3.5 text-right">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                                <template x-for="b in item.batches" :key="b.batchNo">
                                                    <tr class="hover:bg-slate-50 transition-colors">
                                                        <td class="px-6 py-4 font-mono text-sm font-bold text-slate-800"
                                                            x-text="b.batchNo"></td>
                                                        <td class="px-6 py-4">
                                                            <span
                                                                :class="isExpiring90(b.expiry) ?
                                                                    'bg-rose-100 text-rose-800 border-2 border-rose-300 font-black' :
                                                                    'bg-slate-100 text-slate-700 border border-slate-200 font-bold'"
                                                                class="inline-flex items-center gap-1.5 text-xs px-3 py-1 rounded-xl">
                                                                <template x-if="isExpiring90(b.expiry)">
                                                                    <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                                        </path>
                                                                    </svg>
                                                                </template>
                                                                <span x-text="b.expiry"></span>
                                                            </span>

                                                            <template x-if="b.expiry_risk_score !== null && b.expiry_risk_score !== undefined">
                                                                <span
                                                                    :class="b.expiry_risk_category === 'high' ? 'bg-rose-100 text-rose-800 border-rose-300' : (b.expiry_risk_category === 'moderate' ? 'bg-amber-100 text-amber-900 border-amber-300' : 'bg-emerald-100 text-emerald-800 border-emerald-300')"
                                                                    class="inline-flex items-center gap-1 text-[10px] font-black px-2 py-0.5 rounded-lg border ml-2"
                                                                    :title="'Expiry Risk: ' + b.expiry_risk_score + '%'">
                                                                    <span x-text="b.expiry_risk_score + '% Er'"></span>
                                                                </span>
                                                            </template>
                                                        </td>
                                                        <td class="px-6 py-4 text-center font-bold text-sm text-slate-700"
                                                            x-text="b.qtyAvail + ' units'"></td>
                                                        <td class="px-6 py-4 text-sm font-black text-slate-800"
                                                            x-text="fmt(b.unitPrice)"></td>
                                                        <td class="px-6 py-4 text-center">
                                                            <div
                                                                class="inline-flex items-center border-2 border-slate-300 rounded-xl overflow-hidden bg-white shadow-2xs">
                                                                <button type="button"
                                                                    @click="adjustBatchQty(item.id, b.batchNo, getBatchQty(item.id, b.batchNo, b.qtyAllocated) - 1, b.qtyAvail)"
                                                                    class="w-10 h-10 flex items-center justify-center font-black text-lg text-slate-700 hover:bg-slate-100 active:bg-slate-200 transition-colors cursor-pointer">&minus;</button>
                                                                <input type="number"
                                                                    :value="getBatchQty(item.id, b.batchNo, b.qtyAllocated)"
                                                                    @input="adjustBatchQty(item.id, b.batchNo, parseInt($event.target.value) || 0, b.qtyAvail)"
                                                                    class="w-14 text-center text-base font-black py-1 border-0 focus:ring-0 outline-none p-0 text-emerald-950">
                                                                <button type="button"
                                                                    @click="adjustBatchQty(item.id, b.batchNo, getBatchQty(item.id, b.batchNo, b.qtyAllocated) + 1, b.qtyAvail)"
                                                                    class="w-10 h-10 flex items-center justify-center font-black text-lg text-slate-700 hover:bg-slate-100 active:bg-slate-200 transition-colors cursor-pointer">&plus;</button>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 font-black text-emerald-700 text-base text-right"
                                                            x-text="fmt(b.unitPrice * getBatchQty(item.id, b.batchNo, b.qtyAllocated))">
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- ── MODE: COUNTER & OTC SALE ───────────────────────────── -->
                    <div x-show="mode === 'otc'" class="max-w-6xl mx-auto space-y-5">
                        
                        <!-- High-Contrast Search Bar -->
                        <div
                            class="flex items-center gap-3 bg-white border-2 border-slate-200 rounded-2xl px-5 py-3.5 focus-within:border-emerald-600 focus-within:shadow-md transition-all">
                            <svg class="w-6 h-6 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input x-model="otcSearch" placeholder="Search medicines by item code (e.g. PAR-500-TAB), generic, brand, or barcode (F2)..."
                                class="bg-transparent text-base font-semibold outline-none w-full placeholder:text-slate-400 border-0 focus:ring-0 p-0 text-slate-900">
                            <button x-show="otcSearch" @click="otcSearch = ''"
                                class="text-sm font-black text-slate-400 hover:text-slate-600 px-2.5 py-1 bg-slate-100 rounded-lg cursor-pointer">Clear</button>
                        </div>

                        <!-- OTC Medicines Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            <template x-for="med in filteredMedicines" :key="med.id">
                                <button type="button" @click="tapMed(med)" :disabled="med.available_stock === 0"
                                    :class="getCartQty(med.name) > 0 ? 'border-2 border-emerald-600 bg-emerald-50/70 shadow-md ring-2 ring-emerald-500/20' :
                                        'border-2 border-slate-200 bg-white hover:border-emerald-500 hover:shadow-md'"
                                    class="text-left rounded-3xl p-5 transition-all active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer relative flex flex-col justify-between min-h-[170px]">
                                    
                                    <div>
                                        <div class="flex items-start justify-between mb-2">
                                            <div
                                                class="w-10 h-10 bg-emerald-800 text-amber-400 rounded-xl flex items-center justify-center font-black text-base shadow-sm">
                                                ℞
                                            </div>
                                            <div class="flex items-center gap-1.5">
                                                <template x-if="med.stockout_risk_score !== null && med.stockout_risk_score !== undefined">
                                                    <span
                                                        :class="med.stockout_risk_category === 'high' ? 'bg-rose-100 text-rose-800 border-rose-300' : (med.stockout_risk_category === 'moderate' ? 'bg-amber-100 text-amber-900 border-amber-300' : 'bg-emerald-100 text-emerald-800 border-emerald-300')"
                                                        class="text-[10px] font-black px-2 py-0.5 rounded-lg border"
                                                        :title="'Stockout Risk: ' + med.stockout_risk_score + '%'"
                                                        x-text="med.stockout_risk_score + '% Sr'"></span>
                                                </template>
                                                <template x-if="getCartQty(med.name) > 0">
                                                    <span
                                                        class="text-xs font-black bg-emerald-700 text-white px-3 py-1 rounded-xl shadow-xs"
                                                        x-text="'×' + getCartQty(med.name) + ' In Cart'"></span>
                                                </template>
                                            </div>
                                        </div>
                                        <template x-if="med.code">
                                            <div class="font-mono text-xs font-semibold text-gray-500 tracking-wider mb-1" x-text="med.code"></div>
                                        </template>
                                        <h4 class="text-base font-black text-slate-900 leading-snug line-clamp-2"
                                            x-text="med.name"></h4>
                                    </div>

                                    <div class="flex items-end justify-between mt-4 pt-3 border-t border-slate-100">
                                        <div>
                                            <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Unit Price</p>
                                            <p class="text-xl font-black text-emerald-800" x-text="fmt(med.unitPrice)"></p>
                                        </div>
                                        <span
                                            :class="med.available_stock < 20 ? 'bg-rose-100 text-rose-800 border-rose-300' : 'bg-slate-100 text-slate-700 border-slate-200'"
                                            class="text-xs font-black px-2.5 py-1 rounded-lg border"
                                            x-text="med.available_stock + ' in stock'"></span>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- ── MODE: TRANSACTION HISTORY ──────────────────────────── -->
                    <div x-show="mode === 'history'" class="max-w-6xl mx-auto bg-white border-2 border-slate-200 rounded-3xl overflow-hidden shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr
                                        class="bg-slate-100 text-xs font-black text-slate-600 uppercase tracking-wider sticky top-0 border-b border-slate-200">
                                        <th class="px-6 py-4">Date & Time</th>
                                        <th class="px-6 py-4">Patient Profile</th>
                                        <th class="px-6 py-4">Reference Type</th>
                                        <th class="px-6 py-4">Total Amount</th>
                                        <th class="px-6 py-4">Payment Tender</th>
                                        <th class="px-6 py-4 text-right">Receipt</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <template x-for="tx in transactions" :key="tx.id">
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-6 py-4 font-mono text-xs font-bold text-slate-600" x-text="tx.datetime">
                                            </td>
                                            <td class="px-6 py-4">
                                                <p class="font-black text-base text-slate-900" x-text="tx.patient"></p>
                                                <template x-if="tx.patientType">
                                                    <span class="text-xs font-black px-2 py-0.5 rounded-md mt-0.5 inline-block"
                                                        :class="getTypeBadgeClass(tx.patientType)"
                                                        x-text="tx.patientType"></span>
                                                </template>
                                            </td>
                                            <td class="px-6 py-4">
                                                <template x-if="!tx.rxId">
                                                    <span
                                                        class="text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300 px-3 py-1 rounded-xl">Counter Sale</span>
                                                </template>
                                                <template x-if="tx.rxId">
                                                    <span
                                                        class="text-xs font-black bg-blue-100 text-blue-800 border border-blue-300 px-3 py-1 rounded-xl"
                                                        x-text="tx.rxId"></span>
                                                </template>
                                            </td>
                                            <td class="px-6 py-4 font-black text-emerald-800 text-lg"
                                                x-text="fmt(tx.total)"></td>
                                            <td class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-600" x-text="tx.method"></td>
                                            <td class="px-6 py-4 text-right">
                                                <button type="button" @click="setCompletedTx(tx)"
                                                    class="inline-flex items-center gap-1.5 text-xs font-black text-emerald-800 bg-emerald-50 border border-emerald-300 hover:bg-emerald-100 px-3.5 py-2 rounded-xl transition-all cursor-pointer shadow-2xs">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                                        </path>
                                                    </svg>
                                                    <span>View Receipt</span>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <template x-if="transactions.length === 0">
                            <p class="text-center py-16 text-slate-400 text-base font-bold">No dispensary transactions recorded yet today.</p>
                        </template>
                    </div>

                </div>
            </div>

            <!-- ── Right Panel: POS Till, Checkout & Live Thermal Receipt ──────── -->
            <div class="w-84 lg:w-96 xl:w-[420px] flex flex-col bg-usm-dark text-white shrink-0 overflow-hidden shadow-2xl border-l border-emerald-900 select-none">

                <!-- High-Visibility Till Panel Header (shown when NOT viewing completed receipt) -->
                <template x-if="!completedTx">
                    <div class="px-6 py-4 bg-emerald-950/80 border-b border-emerald-800/80 shrink-0 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                            <h3 class="text-sm font-black uppercase tracking-widest text-emerald-200"
                                x-text="mode === 'processing' && processRx ? 'Dispensing: ' + processRx.id : (mode === 'otc' ? 'Counter & OTC Till' : 'Dispensary Till')">
                            </h3>
                        </div>
                    </div>
                </template>

                <!-- ── TILL STATE 1: COMPLETED TRANSACTION RECEIPT ── -->
                <template x-if="completedTx">
                    <div class="flex-1 flex flex-col overflow-hidden bg-usm-dark select-none">
                        
                        <!-- Panel Header: RECEIPT & Close × (Dark Green Till Style) -->
                        <div class="px-6 py-4 bg-emerald-950/90 border-b border-emerald-800/80 shrink-0 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                                <h3 class="text-sm font-black uppercase tracking-widest text-emerald-200">Receipt</h3>
                            </div>
                            <button type="button" @click="completedTx = null"
                                class="text-emerald-300 hover:text-white text-2xl font-black p-1 leading-none cursor-pointer transition-colors"
                                title="Close Receipt (Esc)">&times;</button>
                        </div>

                        <!-- Scrollable Till Area containing ONLY the White Receipt Ticket -->
                        <div class="flex-1 overflow-y-auto p-4 bg-usm-dark flex flex-col justify-start items-center">
                            
                            <!-- ── The White Receipt Ticket (Only this has bg-white) ── -->
                            <div id="thermal-receipt" class="w-full bg-white text-slate-900 rounded-2xl shadow-2xl p-5 sm:p-6 border border-slate-200 select-none">
                                
                                <!-- Check Circle Badge -->
                                <div class="flex flex-col items-center mb-4 text-center">
                                    <div class="w-12 h-12 bg-emerald-700 text-white rounded-full flex items-center justify-center shadow-md mb-2">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <h4 class="text-xs font-black text-emerald-800 tracking-wider uppercase">Sale Complete</h4>
                                    
                                    <!-- USM Health Services Pharmacy -->
                                    <div class="mt-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-emerald-800 text-amber-300 mx-auto flex items-center justify-center font-black text-xs shadow-xs mb-1">
                                            ℞
                                        </div>
                                        <h3 class="text-sm font-black text-slate-900 leading-snug">USM Health Services Pharmacy</h3>
                                        <p class="text-[11px] text-emerald-700 font-bold mt-0.5">Kabacan, Cotabato</p>
                                    </div>
                                </div>

                                <!-- Dashed Divider -->
                                <div class="border-b border-dashed border-slate-300 mb-3.5"></div>

                                <!-- Metadata: RECEIPT NO. & DATE & TIME -->
                                <div class="flex justify-between items-start text-xs mb-3.5">
                                    <div>
                                        <p class="text-slate-500 text-[10px] font-black uppercase tracking-wider">Receipt No.</p>
                                        <p class="font-mono font-black text-amber-600 text-sm sm:text-base mt-0.5" x-text="completedTx.id"></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-slate-500 text-[10px] font-black uppercase tracking-wider">Date &amp; Time</p>
                                        <p class="font-mono text-slate-700 text-xs font-bold mt-0.5" x-text="completedTx.datetime"></p>
                                        <p class="text-emerald-800 text-xs font-semibold mt-0.5" x-text="'Pharm. ' + (completedTx.cashier || 'Jose Reyes')"></p>
                                    </div>
                                </div>

                                <!-- Dashed Divider -->
                                <div class="border-b border-dashed border-slate-300 mb-3.5"></div>

                                <!-- Dispensed Items List -->
                                <div class="space-y-3 mb-4 text-xs">
                                    <template x-for="(item, i) in completedTx.items" :key="i">
                                        <div class="flex justify-between items-start gap-2.5">
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <p class="font-black text-slate-900 text-sm leading-tight" x-text="item.medicine"></p>
                                                    <template x-if="item.code">
                                                        <span class="font-mono text-[10px] font-bold text-slate-600 bg-slate-100 px-1.5 py-0.2 rounded border border-slate-200" x-text="item.code"></span>
                                                    </template>
                                                </div>
                                                <p class="text-emerald-700 font-mono text-[11px] font-bold mt-0.5" 
                                                    x-text="(item.batchNo && item.batchNo !== 'N/A' ? item.batchNo : 'BT-Standard') + (item.expiry && item.expiry !== 'N/A' ? ' · exp ' + item.expiry : '')"></p>
                                                <p class="text-slate-500 text-[11px] font-semibold mt-0.5" x-text="'×' + item.qty + ' @ ' + fmt(item.unitPrice)"></p>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <p class="text-slate-900 font-black text-sm sm:text-base" x-text="fmt(item.unitPrice * item.qty)"></p>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Dashed Divider -->
                                <div class="border-b border-dashed border-slate-300 mb-3.5"></div>

                                <!-- Total & Payment -->
                                <div class="space-y-1.5 mb-3.5">
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-slate-500 font-bold">Subtotal (Gross)</span>
                                        <span class="font-mono text-slate-800 font-bold" x-text="fmt(completedTx.subtotal || completedTx.total)"></span>
                                    </div>
                                    <template x-if="completedTx.vat_exempt_amount > 0">
                                        <div class="flex justify-between items-center text-xs text-emerald-700">
                                            <span>12% VAT Exemption</span>
                                            <span class="font-mono font-bold" x-text="'- ' + fmt(completedTx.vat_exempt_amount)"></span>
                                        </div>
                                    </template>
                                    <template x-if="completedTx.discount_amount > 0">
                                        <div class="flex justify-between items-center text-xs text-emerald-700">
                                            <span x-text="'Discount (' + (completedTx.discount_type === 'senior' ? 'Senior RA 9994' : (completedTx.discount_type === 'pwd' ? 'PWD RA 10754' : (completedTx.discount_type === 'student' ? 'Student 10%' : 'Discount'))) + ')'"></span>
                                            <span class="font-mono font-bold" x-text="'- ' + fmt(completedTx.discount_amount)"></span>
                                        </div>
                                    </template>
                                    <div class="flex justify-between items-center pt-1 border-t border-slate-200">
                                        <span class="text-sm font-bold text-slate-700">Net Total</span>
                                        <span class="text-2xl sm:text-3xl font-black text-amber-600 font-mono tracking-tight" x-text="fmt(completedTx.net_amount || completedTx.total)"></span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs pt-1">
                                        <span class="text-slate-500 font-semibold">Tender</span>
                                        <span class="font-black text-slate-900 text-sm" x-text="completedTx.method"></span>
                                    </div>
                                    <template x-if="completedTx.discount_id_number">
                                        <div class="flex justify-between items-center text-[11px] pt-0.5 text-slate-600">
                                            <span>Discount ID No:</span>
                                            <span class="font-mono font-bold text-slate-900" x-text="completedTx.discount_id_number"></span>
                                        </div>
                                    </template>
                                    <template x-if="completedTx.order_type === 'inpatient'">
                                        <div class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-xl text-center">
                                            <p class="text-[11px] font-black text-blue-900 uppercase">Hospital Ward Dispensation</p>
                                            <p class="text-[11px] text-blue-700 font-bold" x-text="completedTx.room_bed_number ? 'Bed/Room: ' + completedTx.room_bed_number : 'Charged to In-Patient Ledger'"></p>
                                        </div>
                                    </template>
                                </div>

                                <div class="text-center mt-4 pt-1">
                                    <p class="text-[11px] text-slate-400 font-medium">Thank you for your visit.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons: Print Receipt & New Sale (Dark Green Till Footer) -->
                        <div class="px-6 pb-6 pt-4 space-y-2.5 shrink-0 border-t border-emerald-800/80 bg-emerald-950/80 no-print">
                            <button type="button" @click="window.print()"
                                class="w-full flex items-center justify-center gap-2.5 border-2 border-emerald-600 hover:bg-emerald-900/90 text-white text-sm sm:text-base font-bold py-3 rounded-2xl transition-all cursor-pointer shadow-xs">
                                <svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                    </path>
                                </svg>
                                <span>Print Receipt</span>
                            </button>
                            <button type="button" @click="newSale()"
                                class="w-full flex items-center justify-center gap-2.5 bg-amber-400 hover:bg-amber-300 text-emerald-950 text-sm sm:text-base font-black py-3 rounded-2xl transition-all cursor-pointer shadow-md active:scale-98">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                <span>New Sale</span>
                            </button>
                        </div>
                    </div>
                </template>

                <!-- ── TILL STATE 2: ACTIVE COUNTER & OTC CART ── -->
                <template x-if="!completedTx && mode === 'otc'">
                    <div class="flex-1 flex flex-col overflow-hidden">
                        <div class="flex-1 overflow-y-auto px-5 py-4">
                            
                            <template x-if="otcCart.length === 0">
                                <div
                                    class="flex flex-col items-center justify-center h-full text-center py-12 text-emerald-400">
                                    <svg class="w-16 h-16 mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                    <p class="text-base font-black text-white">Till Cart is Empty</p>
                                    <p class="text-xs text-emerald-300 mt-1 max-w-xs leading-relaxed">Click or tap items in the medicine catalog to dispense for walk-ins or OTC.</p>
                                </div>
                            </template>

                            <div class="space-y-2.5">
                                <template x-for="item in otcCart" :key="item.id">
                                    <div
                                        class="flex items-center gap-3 bg-emerald-950/60 rounded-2xl p-3 border border-emerald-800/60">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <template x-if="item.code">
                                                    <span class="font-mono text-[10px] font-bold text-amber-300 bg-emerald-900/90 px-1.5 py-0.2 rounded border border-emerald-700/60" x-text="item.code"></span>
                                                </template>
                                                <p class="text-sm font-black text-white leading-snug truncate"
                                                    x-text="item.name"></p>
                                            </div>
                                            <p class="text-xs text-emerald-300 font-mono mt-0.5"
                                                x-text="fmt(item.unitPrice) + ' each'"></p>
                                        </div>
                                        <div class="flex items-center gap-1.5 shrink-0 bg-emerald-900/80 rounded-xl p-1 border border-emerald-700/60">
                                            <button type="button" @click="changeCartQty(item.id, -1)"
                                                class="w-7 h-7 rounded-lg bg-emerald-800 hover:bg-emerald-700 font-black flex items-center justify-center text-white cursor-pointer">&minus;</button>
                                            <span class="w-7 text-center text-sm font-black text-white"
                                                x-text="item.qty"></span>
                                            <button type="button" @click="changeCartQty(item.id, 1)"
                                                class="w-7 h-7 rounded-lg bg-emerald-800 hover:bg-emerald-700 font-black flex items-center justify-center text-white cursor-pointer">&plus;</button>
                                        </div>
                                        <div class="text-right shrink-0 w-20">
                                            <p class="text-sm font-black text-amber-400"
                                                x-text="fmt(item.unitPrice * item.qty)"></p>
                                            <button type="button" @click="removeFromCart(item.id)"
                                                class="text-rose-400 hover:text-rose-300 text-[11px] font-bold cursor-pointer mt-0.5">Remove</button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- High-Impact Checkout Footer -->
                        <div class="shrink-0 px-6 pb-6 pt-4 bg-emerald-950/90 border-t border-emerald-800/80 space-y-4">
                            
                            <!-- Dynamic Patient Type / Discount Selector -->
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-wider text-emerald-300 mb-2">Patient Type &amp; Statutory Discount</p>
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" @click="otcDiscountType = 'regular'"
                                        :class="otcDiscountType === 'regular' ? 'bg-amber-400 text-emerald-950 font-black border-amber-300 shadow-md' : 'border-emerald-700/80 text-emerald-100 hover:border-emerald-500 bg-emerald-900/40'"
                                        class="py-2 px-2 text-xs font-bold rounded-xl border transition-all cursor-pointer text-center">
                                        Regular (Standard)
                                    </button>
                                    <button type="button" @click="otcDiscountType = 'senior'"
                                        :class="otcDiscountType === 'senior' ? 'bg-amber-400 text-emerald-950 font-black border-amber-300 shadow-md' : 'border-emerald-700/80 text-emerald-100 hover:border-emerald-500 bg-emerald-900/40'"
                                        class="py-2 px-2 text-xs font-bold rounded-xl border transition-all cursor-pointer text-center">
                                        Senior (RA 9994)
                                    </button>
                                    <button type="button" @click="otcDiscountType = 'pwd'"
                                        :class="otcDiscountType === 'pwd' ? 'bg-amber-400 text-emerald-950 font-black border-amber-300 shadow-md' : 'border-emerald-700/80 text-emerald-100 hover:border-emerald-500 bg-emerald-900/40'"
                                        class="py-2 px-2 text-xs font-bold rounded-xl border transition-all cursor-pointer text-center">
                                        PWD (RA 10754)
                                    </button>
                                    <button type="button" @click="otcDiscountType = 'student'"
                                        :class="otcDiscountType === 'student' ? 'bg-amber-400 text-emerald-950 font-black border-amber-300 shadow-md' : 'border-emerald-700/80 text-emerald-100 hover:border-emerald-500 bg-emerald-900/40'"
                                        class="py-2 px-2 text-xs font-bold rounded-xl border transition-all cursor-pointer text-center">
                                        Student (10% Subsidy)
                                    </button>
                                </div>
                            </div>

                            <!-- Conditional Government ID / Student ID Input -->
                            <div x-show="otcDiscountType === 'senior' || otcDiscountType === 'pwd' || otcDiscountType === 'student'">
                                <label class="block text-[11px] font-black uppercase tracking-wider text-emerald-300 mb-1"
                                    x-text="otcDiscountType === 'student' ? 'Student ID Number' : (otcDiscountType === 'senior' ? 'Senior Citizen OSCA ID *' : 'PWD ID Number *')"></label>
                                <input type="text" x-model="otcDiscountId"
                                    :placeholder="otcDiscountType === 'student' ? 'Enter Student ID (e.g. 2024-00123)' : 'Enter Government ID (e.g. OSCA-12345)'"
                                    class="w-full text-xs font-bold px-3.5 py-2.5 rounded-xl border border-emerald-700 bg-emerald-950/80 text-white placeholder-emerald-500 focus:outline-hidden focus:border-amber-400 focus:ring-1 focus:ring-amber-400">
                            </div>

                            <!-- Financial Breakdown Summary -->
                            <div class="space-y-1.5 border-t border-emerald-800/60 pt-3 text-xs">
                                <div class="flex justify-between items-center text-emerald-300 font-bold">
                                    <span>Gross Subtotal</span>
                                    <span class="font-mono text-white" x-text="fmt(otcBreakdown.gross)"></span>
                                </div>
                                <template x-if="otcBreakdown.vatExempt > 0">
                                    <div class="flex justify-between items-center text-emerald-300">
                                        <span class="text-[11px]">12% VAT Exemption</span>
                                        <span class="font-mono text-amber-300 font-bold" x-text="'- ' + fmt(otcBreakdown.vatExempt)"></span>
                                    </div>
                                </template>
                                <template x-if="otcBreakdown.discount > 0">
                                    <div class="flex justify-between items-center text-emerald-300">
                                        <span class="text-[11px]" x-text="otcDiscountType === 'student' ? 'Student Subsidy (10%)' : 'Statutory Discount (20%)'"></span>
                                        <span class="font-mono text-amber-300 font-bold" x-text="'- ' + fmt(otcBreakdown.discount)"></span>
                                    </div>
                                </template>
                                <div class="flex justify-between items-center pt-2 border-t border-emerald-800/80">
                                    <span class="text-emerald-300 text-xs font-black uppercase tracking-wider">Net Total Due</span>
                                    <span class="text-3xl lg:text-4xl font-black text-amber-400 font-mono" x-text="fmt(otcBreakdown.net)"></span>
                                </div>
                            </div>

                            <!-- Payment Tender Selector -->
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-wider text-emerald-300 mb-2">Select Tender Method</p>
                                <div class="grid grid-cols-2 gap-2">
                                    <template x-for="pm in ['Cash', 'PhilHealth', 'HMO', 'Institutional']" :key="pm">
                                        <button type="button" @click="otcPayment = pm"
                                            :class="otcPayment === pm ? 'bg-amber-400 text-emerald-950 font-black border-amber-300 shadow-md scale-102' :
                                                'border-emerald-700/80 text-emerald-100 hover:border-emerald-500 bg-emerald-900/40'"
                                            class="py-2.5 text-xs font-bold rounded-xl border transition-all cursor-pointer text-center"
                                            x-text="pm">
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('pos.otc.store') }}" @submit="submitOtcForm($event)">
                                @csrf
                                <input type="hidden" name="payment_method" :value="mapPaymentMethod(otcPayment)">
                                <input type="hidden" name="discount_type" :value="otcDiscountType">
                                <input type="hidden" name="discount_id_number" :value="otcDiscountId">
                                <template x-for="(item, idx) in otcCart" :key="item.id">
                                    <div>
                                        <input type="hidden" :name="'items[' + idx + '][medicine_id]'" :value="item.id">
                                        <input type="hidden" :name="'items[' + idx + '][quantity]'" :value="item.qty">
                                    </div>
                                </template>

                                <button type="submit" :disabled="otcCart.length === 0"
                                    class="w-full bg-amber-400 hover:bg-amber-300 disabled:opacity-30 disabled:cursor-not-allowed text-emerald-950 font-black py-4 rounded-2xl transition-all text-base flex items-center justify-center gap-2.5 cursor-pointer shadow-xl active:scale-98">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>Complete Sale &amp; Dispense</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </template>

                <!-- ── TILL STATE 3: RX PROCESSING DISPENSE SUMMARY ── -->
                <template x-if="!completedTx && mode === 'processing' && processRx">
                    <div class="flex-1 flex flex-col overflow-hidden">
                        <div class="flex-1 overflow-y-auto px-5 py-4">
                            
                            <!-- In-Patient Ward Charge Alert Banner -->
                            <template x-if="processRx.order_type === 'inpatient'">
                                <div class="mb-4 bg-blue-900/60 border border-blue-500/80 rounded-2xl p-3.5 flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-blue-500 text-white flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-black text-blue-200 uppercase tracking-wider">In-Patient Ward Dispensation</p>
                                        <p class="text-[11px] font-bold text-blue-300 mt-0.5 truncate"
                                            x-text="processRx.room_bed_number ? 'Bed/Room: ' + processRx.room_bed_number : 'Charge Directly to Hospital Ledger'"></p>
                                    </div>
                                </div>
                            </template>

                            <div class="mb-4 pb-4 border-b border-emerald-800/80 bg-emerald-950/40 p-3.5 rounded-2xl border border-emerald-800/40">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-emerald-400 text-[10px] font-black uppercase tracking-wider">Patient Profile</p>
                                    <span class="text-[10px] font-mono font-bold text-amber-300" x-text="processRx.id"></span>
                                </div>
                                <p class="font-black text-white text-base leading-tight" x-text="processRx.patient.name"></p>
                                <div class="flex items-center gap-2 mt-1 text-xs text-emerald-200">
                                    <span x-text="'Dr. ' + processRx.doctor"></span>
                                    <template x-if="processRx.room_bed_number">
                                        <span class="text-blue-300 font-bold" x-text="'· ' + processRx.room_bed_number"></span>
                                    </template>
                                </div>
                            </div>

                            <!-- Itemized Batch Summary -->
                            <div class="space-y-2 mb-4">
                                <p class="text-[10px] font-black uppercase tracking-wider text-emerald-400">Allocated Batches (FEFO)</p>
                                <template x-for="item in processRx.items" :key="item.id">
                                    <template x-for="b in item.batches" :key="b.batchNo">
                                        <template x-if="getBatchQty(item.id, b.batchNo, b.qtyAllocated) > 0">
                                            <div
                                                class="flex items-start justify-between gap-3 bg-emerald-950/60 rounded-2xl p-3 border border-emerald-800/40">
                                                <div class="min-w-0">
                                                    <p class="text-xs font-bold text-white leading-tight truncate"
                                                        x-text="item.medicine"></p>
                                                    <p class="font-mono text-[11px] text-emerald-400 mt-0.5"
                                                        x-text="b.batchNo"></p>
                                                </div>
                                                <div class="text-right shrink-0">
                                                    <p class="text-sm font-black text-amber-400"
                                                        x-text="fmt(b.unitPrice * getBatchQty(item.id, b.batchNo, b.qtyAllocated))">
                                                    </p>
                                                    <p class="text-[11px] text-emerald-300 font-bold"
                                                        x-text="'×' + getBatchQty(item.id, b.batchNo, b.qtyAllocated) + ' units'">
                                                    </p>
                                                </div>
                                            </div>
                                        </template>
                                    </template>
                                </template>
                            </div>
                        </div>

                        <!-- Dispense Action Footer -->
                        <div class="shrink-0 px-6 pb-6 pt-4 bg-emerald-950/90 border-t border-emerald-800/80 space-y-4">
                            
                            <!-- Dynamic Patient Type / Discount Selector -->
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-wider text-emerald-300 mb-2">Prescription Discount Tier</p>
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" @click="processDiscountType = 'regular'"
                                        :class="processDiscountType === 'regular' ? 'bg-amber-400 text-emerald-950 font-black border-amber-300 shadow-md' : 'border-emerald-700/80 text-emerald-100 hover:border-emerald-500 bg-emerald-900/40'"
                                        class="py-2 px-2 text-xs font-bold rounded-xl border transition-all cursor-pointer text-center">
                                        Regular
                                    </button>
                                    <button type="button" @click="processDiscountType = 'senior'"
                                        :class="processDiscountType === 'senior' ? 'bg-amber-400 text-emerald-950 font-black border-amber-300 shadow-md' : 'border-emerald-700/80 text-emerald-100 hover:border-emerald-500 bg-emerald-900/40'"
                                        class="py-2 px-2 text-xs font-bold rounded-xl border transition-all cursor-pointer text-center">
                                        Senior (RA 9994)
                                    </button>
                                    <button type="button" @click="processDiscountType = 'pwd'"
                                        :class="processDiscountType === 'pwd' ? 'bg-amber-400 text-emerald-950 font-black border-amber-300 shadow-md' : 'border-emerald-700/80 text-emerald-100 hover:border-emerald-500 bg-emerald-900/40'"
                                        class="py-2 px-2 text-xs font-bold rounded-xl border transition-all cursor-pointer text-center">
                                        PWD (RA 10754)
                                    </button>
                                    <button type="button" @click="processDiscountType = 'student'"
                                        :class="processDiscountType === 'student' ? 'bg-amber-400 text-emerald-950 font-black border-amber-300 shadow-md' : 'border-emerald-700/80 text-emerald-100 hover:border-emerald-500 bg-emerald-900/40'"
                                        class="py-2 px-2 text-xs font-bold rounded-xl border transition-all cursor-pointer text-center">
                                        Student (10%)
                                    </button>
                                </div>
                            </div>

                            <!-- Conditional Government ID / Student ID Input -->
                            <div x-show="processDiscountType === 'senior' || processDiscountType === 'pwd' || processDiscountType === 'student'">
                                <label class="block text-[11px] font-black uppercase tracking-wider text-emerald-300 mb-1"
                                    x-text="processDiscountType === 'student' ? 'Student ID Number' : (processDiscountType === 'senior' ? 'Senior Citizen OSCA ID *' : 'PWD ID Number *')"></label>
                                <input type="text" x-model="processDiscountId"
                                    :placeholder="processDiscountType === 'student' ? 'Enter Student ID (e.g. 2024-00123)' : 'Enter Government ID (e.g. OSCA-12345)'"
                                    class="w-full text-xs font-bold px-3.5 py-2.5 rounded-xl border border-emerald-700 bg-emerald-950/80 text-white placeholder-emerald-500 focus:outline-hidden focus:border-amber-400 focus:ring-1 focus:ring-amber-400">
                            </div>

                            <!-- Financial Breakdown Summary -->
                            <div class="space-y-1.5 border-t border-emerald-800/60 pt-3 text-xs">
                                <div class="flex justify-between items-center text-emerald-300 font-bold">
                                    <span>Gross Prescription Bill</span>
                                    <span class="font-mono text-white" x-text="fmt(processBreakdown.gross)"></span>
                                </div>
                                <template x-if="processBreakdown.vatExempt > 0">
                                    <div class="flex justify-between items-center text-emerald-300">
                                        <span class="text-[11px]">12% VAT Exemption</span>
                                        <span class="font-mono text-amber-300 font-bold" x-text="'- ' + fmt(processBreakdown.vatExempt)"></span>
                                    </div>
                                </template>
                                <template x-if="processBreakdown.discount > 0">
                                    <div class="flex justify-between items-center text-emerald-300">
                                        <span class="text-[11px]" x-text="processDiscountType === 'student' ? 'Student Subsidy (10%)' : 'Statutory Discount (20%)'"></span>
                                        <span class="font-mono text-amber-300 font-bold" x-text="'- ' + fmt(processBreakdown.discount)"></span>
                                    </div>
                                </template>
                                <div class="flex justify-between items-center pt-2 border-t border-emerald-800/80">
                                    <span class="text-emerald-300 text-xs font-black uppercase tracking-wider">Net Amount Due</span>
                                    <span class="text-3xl lg:text-4xl font-black text-amber-400 font-mono" x-text="fmt(processBreakdown.net)"></span>
                                </div>
                            </div>

                            <!-- In-Patient Ledger Notice -->
                            <div class="bg-blue-950/80 border border-blue-600/70 rounded-2xl p-3 text-center">
                                <p class="text-[11px] font-black uppercase text-blue-200 tracking-wider">In-Patient Ward Order</p>
                                <p class="text-[11px] text-blue-300 font-semibold mt-0.5">Dispensation immediately deducts FEFO inventory and charges to the patient hospital ledger.</p>
                            </div>

                            <!-- Form submission to Laravel pos.dispense -->
                            <form method="POST" :action="'{{ url('/pos') }}/' + processRx.raw_id" @submit="submitDispenseForm($event)">
                                @csrf
                                <input type="hidden" name="payment_method" value="hospital_bill">
                                <input type="hidden" name="discount_type" :value="processDiscountType">
                                <input type="hidden" name="discount_id_number" :value="processDiscountId">
                                <template x-for="item in processRx.items" :key="item.id">
                                    <template x-for="b in item.batches" :key="b.batch_id">
                                        <input type="hidden"
                                            :name="'allocations[' + item.medicine_id + '][' + b.batch_id + ']'"
                                            :value="getBatchQty(item.id, b.batchNo, b.qtyAllocated)">
                                    </template>
                                </template>

                                <button type="submit"
                                    class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-black py-4 rounded-2xl transition-all text-base flex items-center justify-center gap-2.5 cursor-pointer shadow-xl active:scale-98">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>Dispense to Ward / Charge to Patient Ledger</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </template>

                <!-- ── TILL STATE 4: IDLE READY ── -->
                <template x-if="!completedTx && mode !== 'otc' && (mode !== 'processing' || !processRx)">
                    <div class="flex-1 flex flex-col items-center justify-center px-6 text-center gap-5">
                        <div
                            class="w-20 h-20 bg-emerald-800/80 rounded-3xl flex items-center justify-center shadow-inner border-2 border-emerald-600">
                            <svg class="w-10 h-10 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-white text-2xl"
                                x-text="mode === 'history' ? 'History & Dispensing Logs' : 'Prescription Queue Ready'"></h3>
                            <p class="text-emerald-200 text-sm mt-1.5 leading-relaxed"
                                x-text="mode === 'history' ? 'Click View Receipt on any completed transaction on the left to print or reprint receipts.' : 'Select a queued patient prescription on the left to review FEFO allocations, or start a direct counter sale for walk-ins.'">
                            </p>
                        </div>
                        
                        <div class="w-full space-y-3 mt-3">
                            <button type="button" @click="goOtc()"
                                class="w-full flex items-center justify-center gap-2.5 bg-amber-400 hover:bg-amber-300 text-emerald-950 font-black py-4 rounded-2xl transition-all text-base cursor-pointer shadow-lg active:scale-98">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                <span>Start Counter &amp; OTC Sale</span>
                            </button>
                        </div>
                    </div>
                </template>

            </div>
        </div>

    </div>

    <!-- ── Alpine.js Controller Data ────────────────────────────────────── -->
    <script>
        function posTerminal() {
            return {
                mode: 'queue',
                processRxId: null,
                completedTx: null,
                queue: @json($queueData),
                inpatientQueue: @json($inpatientQueueData),
                medicines: @json($medicinesData),
                transactions: @json($transactionsData),

                // OTC & Counter Sale state
                otcSearch: '',
                otcCart: [],
                otcPayment: 'Cash',
                otcDiscountType: 'regular',
                otcDiscountId: '',

                // Processing state
                batchEdits: {},
                processPayment: 'hospital_bill',
                processDiscountType: 'regular',
                processDiscountId: '',

                initData() {
                    // If receipt ID passed in query parameter, auto-open receipt
                    const urlParams = new URLSearchParams(window.location.search);
                    const receiptId = urlParams.get('receipt');
                    if (receiptId) {
                        const found = this.transactions.find(t => t.raw_id == receiptId || t.id == receiptId || t.id == '#' + String(receiptId).padStart(8, '0'));
                        if (found) {
                            this.completedTx = found;
                        }
                    }
                },

                // ── Statutory Discount Breakdown Calculator ──
                calculateBreakdown(gross, type) {
                    gross = Math.round((Number(gross) || 0) * 100) / 100;
                    if (type === 'senior' || type === 'pwd') {
                        // RA 9994 (Senior) & RA 10754 (PWD): Remove 12% VAT, then 20% discount on net of VAT
                        const netOfVat = Math.round((gross / 1.12) * 100) / 100;
                        const vatExempt = Math.round((gross - netOfVat) * 100) / 100;
                        const discount = Math.round((netOfVat * 0.20) * 100) / 100;
                        const net = Math.round((netOfVat - discount) * 100) / 100;
                        return { gross, vatExempt, discount, net };
                    }
                    if (type === 'student') {
                        // Institutional Student Subsidy: 10% discount on retail
                        const discount = Math.round((gross * 0.10) * 100) / 100;
                        const net = Math.round((gross - discount) * 100) / 100;
                        return { gross, vatExempt: 0, discount, net };
                    }
                    return { gross, vatExempt: 0, discount: 0, net: gross };
                },

                get otcBreakdown() {
                    return this.calculateBreakdown(this.otcTotal, this.otcDiscountType);
                },

                get processBreakdown() {
                    return this.calculateBreakdown(this.processTotal, this.processDiscountType);
                },

                // ── Asynchronous in-page checkout & dispensing (zero page reload) ──
                submitOtcForm(e) {
                    e.preventDefault();

                    // Validation for Senior / PWD statutory compliance
                    if ((this.otcDiscountType === 'senior' || this.otcDiscountType === 'pwd') && !this.otcDiscountId.trim()) {
                        alert('Compliance Requirement: Please enter the Senior Citizen OSCA ID or PWD ID Number before completing the sale.');
                        return;
                    }

                    const form = e.target;
                    const formData = new FormData(form);

                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success && data.transaction) {
                            this.transactions.unshift(data.transaction);
                            this.completedTx = data.transaction;
                            this.otcCart = [];
                            this.otcDiscountType = 'regular';
                            this.otcDiscountId = '';
                        } else if (data.error) {
                            alert('Error: ' + data.error);
                        } else if (data.message) {
                            alert(data.message);
                        }
                    })
                    .catch(() => {
                        form.submit();
                    });
                },

                submitDispenseForm(e) {
                    e.preventDefault();

                    // Validation for Senior / PWD statutory compliance
                    if ((this.processDiscountType === 'senior' || this.processDiscountType === 'pwd') && !this.processDiscountId.trim()) {
                        alert('Compliance Requirement: Please enter the Senior Citizen OSCA ID or PWD ID Number before dispensing.');
                        return;
                    }

                    const form = e.target;
                    const formData = new FormData(form);

                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success && data.transaction) {
                            this.transactions.unshift(data.transaction);
                            this.completedTx = data.transaction;

                            if (this.processRxId) {
                                const rxId = this.processRxId;
                                const rawId = this.processRx?.raw_id;
                                this.queue = this.queue.filter(q => q.id !== rxId && q.raw_id != rawId);
                            }
                            this.processRxId = null;
                            this.mode = 'queue';
                        } else if (data.error) {
                            alert('Error: ' + data.error);
                        } else if (data.message) {
                            alert(data.message);
                        }
                    })
                    .catch(() => {
                        form.submit();
                    });
                },

                // ── Ward preparation ──
                markOrderPrepared(rx) {
                    const token = document.querySelector('meta[name="csrf-token"]')?.content;
                    fetch(`{{ url('/pos/prescriptions') }}/${rx.raw_id}/prepare`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': token,
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            rx.status = 'prepared';
                        } else if (data.error) {
                            alert('Error: ' + data.error);
                        }
                    })
                    .catch(() => {
                        alert('Failed to update preparation status.');
                    });
                },

                // ── Keyboard ergonomics for Flow State ──
                handleShortcuts(e) {
                    if (['input', 'textarea', 'select'].includes(e.target.tagName.toLowerCase())) {
                        if (e.key === 'Escape') {
                            e.target.blur();
                        }
                        return;
                    }

                    if (e.key === '1') { this.goQueue(); }
                    else if (e.key === '2') { this.goOtc(); }
                    else if (e.key === '3') { this.setMode('history'); }
                    else if (e.key === 'Escape') {
                        if (this.completedTx) { this.completedTx = null; }
                        else if (this.mode === 'processing') { this.cancelProcess(); }
                    }
                },

                // ── Mode navigation ──
                setMode(m) {
                    this.mode = m;
                    if (m === 'queue' || m === 'history') {
                        this.processRxId = null;
                    }
                },
                goOtc() {
                    this.completedTx = null;
                    this.mode = 'otc';
                },
                goQueue() {
                    this.setMode('queue');
                },
                newSale() {
                    this.completedTx = null;
                    this.otcCart = [];
                    this.otcDiscountType = 'regular';
                    this.otcDiscountId = '';
                    this.goQueue();
                },
                startProcess(rxId) {
                    this.processRxId = rxId;
                    this.batchEdits = {};
                    const rx = this.processRx;

                    // Auto-select discount tier based on linked patient profile
                    if (rx && rx.patient) {
                        const pType = (rx.patient.type || '').toLowerCase();
                        if (pType.includes('student')) {
                            this.processDiscountType = 'student';
                            this.processDiscountId = rx.patient.id || '';
                        } else if (pType.includes('senior')) {
                            this.processDiscountType = 'senior';
                            this.processDiscountId = rx.patient.id || '';
                        } else if (pType.includes('pwd')) {
                            this.processDiscountType = 'pwd';
                            this.processDiscountId = rx.patient.id || '';
                        } else {
                            this.processDiscountType = 'regular';
                            this.processDiscountId = '';
                        }
                    } else {
                        this.processDiscountType = 'regular';
                        this.processDiscountId = '';
                    }

                    // Default to hospital bill for in-patient prescriptions
                    this.processPayment = 'hospital_bill';

                    this.mode = 'processing';
                    this.completedTx = null;
                },
                cancelProcess() {
                    this.processRxId = null;
                    this.mode = 'queue';
                },
                setCompletedTx(tx) {
                    this.completedTx = tx;
                },

                // ── Computed & Helpers ──
                get processRx() {
                    return this.queue.find(r => r.id === this.processRxId) || null;
                },

                get filteredMedicines() {
                    if (!this.otcSearch.trim()) return this.medicines;
                    const q = this.otcSearch.toLowerCase();
                    return this.medicines.filter(m => 
                        (m.name && m.name.toLowerCase().includes(q)) ||
                        (m.code && m.code.toLowerCase().includes(q)) ||
                        (m.barcode && m.barcode.includes(q))
                    );
                },

                get otcTotal() {
                    return this.otcCart.reduce((sum, item) => sum + (item.unitPrice * item.qty), 0);
                },

                get processTotal() {
                    if (!this.processRx) return 0;
                    let sum = 0;
                    for (const item of this.processRx.items) {
                        for (const b of item.batches) {
                            const q = this.getBatchQty(item.id, b.batchNo, b.qtyAllocated);
                            sum += b.unitPrice * q;
                        }
                    }
                    return sum;
                },

                // ── Batch allocation management ──
                getBatchQty(itemId, batchNo, defaultQty) {
                    if (this.batchEdits[itemId] && this.batchEdits[itemId][batchNo] !== undefined) {
                        return this.batchEdits[itemId][batchNo];
                    }
                    return defaultQty;
                },

                adjustBatchQty(itemId, batchNo, newQty, maxAvail) {
                    const bounded = Math.max(0, Math.min(newQty, maxAvail));
                    if (!this.batchEdits[itemId]) {
                        this.batchEdits[itemId] = {};
                    }
                    this.batchEdits[itemId][batchNo] = bounded;
                },

                // ── OTC Cart Helpers ──
                tapMed(med) {
                    const existing = this.otcCart.find(i => i.id === med.id);
                    if (existing) {
                        if (existing.qty < med.available_stock) {
                            existing.qty += 1;
                        }
                    } else {
                        this.otcCart.push({
                            id: med.id,
                            code: med.code,
                            name: med.name,
                            unitPrice: med.unitPrice,
                            qty: 1
                        });
                    }
                },

                getCartQty(name) {
                    const found = this.otcCart.find(i => i.name === name);
                    return found ? found.qty : 0;
                },

                changeCartQty(id, delta) {
                    const idx = this.otcCart.findIndex(i => i.id === id);
                    if (idx > -1) {
                        const med = this.medicines.find(m => m.id === id);
                        const newQty = this.otcCart[idx].qty + delta;
                        if (newQty <= 0) {
                            this.otcCart.splice(idx, 1);
                        } else if (med && newQty <= med.available_stock) {
                            this.otcCart[idx].qty = newQty;
                        }
                    }
                },

                removeFromCart(id) {
                    this.otcCart = this.otcCart.filter(i => i.id !== id);
                },

                // ── Formatting & styling helpers ──
                fmt(n) {
                    return '₱' + (parseFloat(n) || 0).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                },

                isExpiring90(dateStr) {
                    if (!dateStr) return false;
                    const diffDays = (new Date(dateStr).getTime() - Date.now()) / (1000 * 60 * 60 * 24);
                    return diffDays < 90;
                },

                getWaitTimeLabel(dateStr) {
                    if (!dateStr) return 'Just now';
                    const m = Math.floor((Date.now() - new Date(dateStr.replace(' ', 'T')).getTime()) / 60000);
                    if (isNaN(m) || m < 1) return 'Just now';
                    if (m < 60) return m + 'm ago';
                    const h = Math.floor(m / 60);
                    if (h < 24) return h + 'h ago';
                    return Math.floor(h / 24) + 'd ago';
                },

                getUrgencyClass(dateStr) {
                    if (!dateStr) return 'bg-emerald-100 text-emerald-900 border-emerald-300';
                    const m = Math.floor((Date.now() - new Date(dateStr.replace(' ', 'T')).getTime()) / 60000);
                    if (m < 15) return 'bg-emerald-100 text-emerald-900 border-emerald-300';
                    if (m < 45) return 'bg-amber-100 text-amber-950 border-amber-300';
                    return 'bg-rose-100 text-rose-900 border-rose-300 font-black';
                },

                getTypeBadgeClass(type) {
                    const map = {
                        'Student': 'bg-indigo-600 text-white',
                        'Faculty': 'bg-blue-600 text-white',
                        'Community': 'bg-amber-600 text-white',
                        'Walk-in': 'bg-teal-700 text-white'
                    };
                    return map[type] || 'bg-slate-700 text-white';
                },

                mapPaymentMethod(pm) {
                    if (pm === 'PhilHealth' || pm === 'HMO' || pm === 'Institutional') {
                        return 'insurance';
                    }
                    return pm.toLowerCase();
                }
            };
        }
    </script>

    <x-dual-risk-engine-modal />

</body>

</html>
