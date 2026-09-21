<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>USM Pharmacy — Patient Portal</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900 min-h-screen flex flex-col">

@php
    $patientData = [
        'name' => $patient->user->name,
        'type' => ucfirst($patient->patient_type),
        'id' => $patient->id_number,
        'email' => $patient->user->email,
        'contact' => $patient->contact_number ?? '09171234567',
        'allergies' => $patient->allergies ?? 'Penicillin',
        'conditions' => $patient->medical_notes ?? 'Asthmatic',
    ];

    $formattedPrescriptions = $allPrescriptions->map(function ($rx) {
        $statusMap = [
            'pending' => 'Pending',
            'routed' => 'At Pharmacy',
            'dispensed' => 'Dispensed',
            'cancelled' => 'Cancelled',
        ];

        $notesMap = [
            'pending' => 'Allergic rhinitis with mild wheeze. Routine review scheduled.',
            'routed' => 'Mild pain management for musculoskeletal complaint.',
            'dispensed' => 'Patient presented with fever and sore throat. Completed dispensing.',
            'cancelled' => 'Cancelled per patient request or doctor recommendation.',
        ];

        return [
            'id' => 'RX-' . date('Y') . '-' . str_pad($rx->id, 3, '0', STR_PAD_LEFT),
            'db_id' => $rx->id,
            'status' => $statusMap[$rx->status] ?? ucfirst($rx->status),
            'doctor' => 'Dr. ' . ($rx->encodedBy->name ?? 'Ana Reyes'),
            'date' => $rx->created_at->format('M d, Y'),
            'notes' => $notesMap[$rx->status] ?? 'General clinical consultation prescription.',
            'items' => $rx->items->map(function ($item) {
                return [
                    'name' => $item->medicine->name,
                    'generic' => $item->medicine->generic_name,
                    'qty' => $item->quantity,
                    'dosage' => $item->dosage_instructions ?: '1 tab OD as directed',
                ];
            })->values()->all(),
        ];
    })->values()->all();

    $products = [
        ['id' => 1,  'name' => 'Normal Saline 0.9% 1L',       'brand' => 'Baxter',      'category' => 'IV Fluids',        'price' => 85,  'originalPrice' => null, 'unit' => 'bag',     'stock' => 300, 'rx' => true,  'badge' => null,   'rating' => 4.9, 'sold' => 540],
        ['id' => 2,  'name' => 'D5W (Dextrose 5%) 1L',         'brand' => 'Baxter',      'category' => 'IV Fluids',        'price' => 80,  'originalPrice' => null, 'unit' => 'bag',     'stock' => 280, 'rx' => true,  'badge' => null,   'rating' => 4.8, 'sold' => 410],
        ['id' => 3,  'name' => "Lactated Ringer's 1L",         'brand' => 'Baxter',      'category' => 'IV Fluids',        'price' => 95,  'originalPrice' => null, 'unit' => 'bag',     'stock' => 200, 'rx' => true,  'badge' => null,   'rating' => 4.9, 'sold' => 320],
        ['id' => 4,  'name' => 'D5NM (Dextrose in NaCl) 1L',  'brand' => 'Otsuka',      'category' => 'IV Fluids',        'price' => 90,  'originalPrice' => null, 'unit' => 'bag',     'stock' => 150, 'rx' => true,  'badge' => null,   'rating' => 4.7, 'sold' => 210],
        ['id' => 5,  'name' => 'Ceftriaxone 1g IV',            'brand' => 'Mediphil',    'category' => 'Injectables',      'price' => 120, 'originalPrice' => 145,  'unit' => 'vial',    'stock' => 180, 'rx' => true,  'badge' => 'SALE', 'rating' => 4.9, 'sold' => 290],
        ['id' => 6,  'name' => 'Ampicillin 500mg IV',          'brand' => 'Mediphil',    'category' => 'Injectables',      'price' => 55,  'originalPrice' => null, 'unit' => 'vial',    'stock' => 160, 'rx' => true,  'badge' => null,   'rating' => 4.7, 'sold' => 175],
        ['id' => 7,  'name' => 'Hydrocortisone 100mg IV',      'brand' => 'Pharex',      'category' => 'Injectables',      'price' => 95,  'originalPrice' => null, 'unit' => 'vial',    'stock' => 90,  'rx' => true,  'badge' => null,   'rating' => 4.8, 'sold' => 130],
        ['id' => 8,  'name' => 'Metoclopramide 10mg IV',       'brand' => 'Pharex',      'category' => 'Injectables',      'price' => 38,  'originalPrice' => null, 'unit' => 'amp',     'stock' => 220, 'rx' => true,  'badge' => null,   'rating' => 4.6, 'sold' => 340],
        ['id' => 9,  'name' => 'Epinephrine 1mg/mL',           'brand' => 'Hospira',     'category' => 'Injectables',      'price' => 75,  'originalPrice' => null, 'unit' => 'amp',     'stock' => 60,  'rx' => true,  'badge' => 'CRIT', 'rating' => 4.9, 'sold' => 80],
        ['id' => 10, 'name' => 'Atropine Sulfate 1mg/mL',      'brand' => 'Hospira',     'category' => 'Injectables',      'price' => 65,  'originalPrice' => null, 'unit' => 'amp',     'stock' => 55,  'rx' => true,  'badge' => 'CRIT', 'rating' => 4.8, 'sold' => 65],
        ['id' => 11, 'name' => 'Furosemide 20mg IV',           'brand' => 'Pharex',      'category' => 'Injectables',      'price' => 42,  'originalPrice' => null, 'unit' => 'amp',     'stock' => 140, 'rx' => true,  'badge' => null,   'rating' => 4.7, 'sold' => 195],
        ['id' => 12, 'name' => 'Amoxicillin 500mg Cap',        'brand' => 'Pharex',      'category' => 'Oral Medications', 'price' => 12,  'originalPrice' => null, 'unit' => 'capsule', 'stock' => 500, 'rx' => true,  'badge' => null,   'rating' => 4.8, 'sold' => 620],
        ['id' => 13, 'name' => 'Paracetamol 500mg Tab',        'brand' => 'Biogesic',    'category' => 'Oral Medications', 'price' => 5,   'originalPrice' => 7,    'unit' => 'tablet',  'stock' => 900, 'rx' => false, 'badge' => 'SALE', 'rating' => 4.9, 'sold' => 1400],
        ['id' => 14, 'name' => 'Omeprazole 20mg Cap',          'brand' => 'Pharex',      'category' => 'Oral Medications', 'price' => 18,  'originalPrice' => null, 'unit' => 'capsule', 'stock' => 350, 'rx' => false, 'badge' => null,   'rating' => 4.7, 'sold' => 480],
        ['id' => 15, 'name' => 'Amlodipine 5mg Tab',           'brand' => 'Norvasc',     'category' => 'Oral Medications', 'price' => 22,  'originalPrice' => null, 'unit' => 'tablet',  'stock' => 270, 'rx' => true,  'badge' => null,   'rating' => 4.7, 'sold' => 310],
        ['id' => 16, 'name' => 'Metformin 500mg Tab',          'brand' => 'Glucophage',  'category' => 'Oral Medications', 'price' => 15,  'originalPrice' => null, 'unit' => 'tablet',  'stock' => 400, 'rx' => true,  'badge' => null,   'rating' => 4.8, 'sold' => 390],
        ['id' => 17, 'name' => 'Losartan 50mg Tab',            'brand' => 'Cozaar',      'category' => 'Oral Medications', 'price' => 28,  'originalPrice' => null, 'unit' => 'tablet',  'stock' => 0,   'rx' => true,  'badge' => null,   'rating' => 4.6, 'sold' => 210],
        ['id' => 18, 'name' => 'Dexamethasone 4mg Tab',        'brand' => 'Pharex',      'category' => 'Oral Medications', 'price' => 20,  'originalPrice' => null, 'unit' => 'tablet',  'stock' => 180, 'rx' => true,  'badge' => null,   'rating' => 4.7, 'sold' => 175],
        ['id' => 19, 'name' => 'Betadine Solution 60mL',       'brand' => 'Mundipharma', 'category' => 'Wound Care',       'price' => 95,  'originalPrice' => 110,  'unit' => 'bottle',  'stock' => 120, 'rx' => false, 'badge' => 'SALE', 'rating' => 4.8, 'sold' => 340],
        ['id' => 20, 'name' => 'Sterile Gauze Pads 4x4',       'brand' => 'Mediline',    'category' => 'Wound Care',       'price' => 15,  'originalPrice' => null, 'unit' => 'pack',    'stock' => 400, 'rx' => false, 'badge' => null,   'rating' => 4.8, 'sold' => 520],
        ['id' => 21, 'name' => 'Adhesive Bandage Strips',      'brand' => 'Johnson',     'category' => 'Wound Care',       'price' => 55,  'originalPrice' => null, 'unit' => 'box',     'stock' => 200, 'rx' => false, 'badge' => null,   'rating' => 4.7, 'sold' => 410],
        ['id' => 22, 'name' => 'Surgical Tape 1" x 10yd',     'brand' => '3M',          'category' => 'Wound Care',       'price' => 65,  'originalPrice' => null, 'unit' => 'roll',    'stock' => 180, 'rx' => false, 'badge' => null,   'rating' => 4.6, 'sold' => 280],
        ['id' => 23, 'name' => 'Hydrogen Peroxide 3% 120mL',   'brand' => 'Mediline',    'category' => 'Wound Care',       'price' => 35,  'originalPrice' => null, 'unit' => 'bottle',  'stock' => 250, 'rx' => false, 'badge' => 'NEW',  'rating' => 4.5, 'sold' => 190],
        ['id' => 24, 'name' => 'Salbutamol Nebule 2.5mg',      'brand' => 'Ventolin',    'category' => 'Respiratory',      'price' => 28,  'originalPrice' => null, 'unit' => 'nebule',  'stock' => 200, 'rx' => true,  'badge' => null,   'rating' => 4.9, 'sold' => 360],
        ['id' => 25, 'name' => 'Ipratropium Nebule 0.5mg',     'brand' => 'Atrovent',    'category' => 'Respiratory',      'price' => 45,  'originalPrice' => null, 'unit' => 'nebule',  'stock' => 150, 'rx' => true,  'badge' => null,   'rating' => 4.7, 'sold' => 210],
        ['id' => 26, 'name' => 'N-Acetylcysteine 200mg',       'brand' => 'Fluimucil',   'category' => 'Respiratory',      'price' => 32,  'originalPrice' => 38,   'unit' => 'sachet',  'stock' => 280, 'rx' => false, 'badge' => 'SALE', 'rating' => 4.6, 'sold' => 295],
        ['id' => 27, 'name' => 'Disposable Syringe 5mL',       'brand' => 'Terumo',      'category' => 'Consumables',      'price' => 12,  'originalPrice' => null, 'unit' => 'piece',   'stock' => 1000,'rx' => false, 'badge' => null,   'rating' => 4.9, 'sold' => 1800],
        ['id' => 28, 'name' => 'IV Cannula G18',               'brand' => 'Terumo',      'category' => 'Consumables',      'price' => 28,  'originalPrice' => null, 'unit' => 'piece',   'stock' => 500, 'rx' => false, 'badge' => null,   'rating' => 4.8, 'sold' => 920],
        ['id' => 29, 'name' => 'Nitrile Gloves (M) Box/100',   'brand' => 'Medline',     'category' => 'Consumables',      'price' => 320, 'originalPrice' => 380,  'unit' => 'box',     'stock' => 150, 'rx' => false, 'badge' => 'SALE', 'rating' => 4.8, 'sold' => 680],
        ['id' => 30, 'name' => 'Surgical Mask Box/50',         'brand' => '3M',          'category' => 'Consumables',      'price' => 185, 'originalPrice' => null, 'unit' => 'box',     'stock' => 200, 'rx' => false, 'badge' => null,   'rating' => 4.7, 'sold' => 750],
        ['id' => 31, 'name' => 'Nasogastric Tube Fr14',        'brand' => 'Covidien',    'category' => 'Consumables',      'price' => 85,  'originalPrice' => null, 'unit' => 'piece',   'stock' => 80,  'rx' => false, 'badge' => null,   'rating' => 4.6, 'sold' => 95],
        ['id' => 32, 'name' => 'Urine Drainage Bag 2L',        'brand' => 'Covidien',    'category' => 'Consumables',      'price' => 75,  'originalPrice' => null, 'unit' => 'piece',   'stock' => 120, 'rx' => false, 'badge' => null,   'rating' => 4.7, 'sold' => 140],
        ['id' => 33, 'name' => 'Vitamin C 500mg Tab',          'brand' => 'Ascorbicap',  'category' => 'Supplements',      'price' => 5,   'originalPrice' => null, 'unit' => 'tablet',  'stock' => 800, 'rx' => false, 'badge' => null,   'rating' => 4.9, 'sold' => 1100],
        ['id' => 34, 'name' => 'Vitamin B Complex Tab',        'brand' => 'Neurobion',   'category' => 'Supplements',      'price' => 18,  'originalPrice' => null, 'unit' => 'tablet',  'stock' => 500, 'rx' => false, 'badge' => null,   'rating' => 4.8, 'sold' => 640],
        ['id' => 35, 'name' => 'Zinc Sulfate 20mg Tab',        'brand' => 'Pharex',      'category' => 'Supplements',      'price' => 7,   'originalPrice' => null, 'unit' => 'tablet',  'stock' => 400, 'rx' => false, 'badge' => 'NEW',  'rating' => 4.7, 'sold' => 380],
    ];
@endphp

<div x-data="patientPortalApp()" x-init="init()" class="min-h-screen bg-gray-50 flex flex-col">

    <!-- ── Sticky Navbar ── -->
    <header class="sticky top-0 z-40 bg-green-800 border-b-4 border-yellow-500 shadow-md">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between h-14">
            <div class="flex items-center gap-3">
                <a href="javascript:void(0)" @click="setView('dashboard')" class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center p-1 border border-white/20">
                        <svg viewBox="0 0 32 32" class="w-6 h-6 text-yellow-400" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 3L4 8v8c0 6.627 5.373 12 12 12s12-5.373 12-12V8L16 3z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M11 16l3 3 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="hidden sm:block">
                        <p class="font-bold text-yellow-400 text-sm leading-tight">USM Pharmacy</p>
                        <p class="text-[10px] text-green-300">Patient Portal</p>
                    </div>
                </a>
            </div>

            <nav class="hidden md:flex items-center gap-1">
                <template x-for="tab in navItems" :key="tab.view">
                    <button
                        @click="setView(tab.view)"
                        :class="(view === tab.view || (view === 'prescription-detail' && tab.view === 'prescriptions'))
                            ? 'bg-yellow-400 text-green-900'
                            : 'text-green-100 hover:bg-green-700'"
                        class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold transition-colors"
                    >
                        <span x-html="tab.icon"></span>
                        <span x-text="tab.label"></span>
                    </button>
                </template>
            </nav>

            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('logout') }}" class="inline" onsubmit="return confirm('Are you sure you want to log out?');">
                    @csrf
                    <button type="submit" class="flex items-center gap-1.5 text-green-300 hover:text-red-300 text-xs transition-colors font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Sign out</span>
                    </button>
                </form>

                <!-- Mobile burger toggle -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-green-200 hover:text-white p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div x-show="mobileMenuOpen" class="md:hidden bg-green-900 border-t border-green-700 px-4 py-3 space-y-1">
            <template x-for="tab in navItems" :key="tab.view">
                <button
                    @click="setView(tab.view); mobileMenuOpen = false"
                    :class="(view === tab.view || (view === 'prescription-detail' && tab.view === 'prescriptions'))
                        ? 'bg-yellow-400 text-green-900'
                        : 'text-green-100 hover:bg-green-800'"
                    class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-left"
                >
                    <span x-html="tab.icon"></span>
                    <span x-text="tab.label"></span>
                </button>
            </template>
        </div>
    </header>

    <!-- ══════════════════════════════════════════
        DASHBOARD VIEW
    ══════════════════════════════════════════ -->
    <div x-show="view === 'dashboard'" class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 py-8 space-y-6">

        <!-- Greeting card -->
        <div class="bg-green-800 rounded-2xl px-6 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b-4 border-yellow-500 shadow-sm">
            <div>
                <p class="text-green-300 text-sm mb-1">Welcome back 👋</p>
                <h1 class="text-white text-2xl font-bold" x-text="patient.name"></h1>
                <div class="flex items-center gap-3 mt-2">
                    <span class="text-xs bg-yellow-400 text-green-900 font-semibold px-2.5 py-0.5 rounded-full" x-text="patient.type"></span>
                    <span class="text-xs text-green-300 font-mono" x-text="patient.id"></span>
                </div>
            </div>
            <button
                @click="setView('prescriptions')"
                class="flex items-center gap-2 bg-yellow-400 hover:bg-yellow-300 text-green-900 font-semibold text-sm px-5 py-2.5 rounded-xl transition-colors shrink-0 self-start sm:self-auto shadow-sm"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect width="8" height="4" x="8" y="2" rx="1" ry="1" stroke-width="2"/>
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" stroke-width="2"/>
                    <path d="M12 11h4M12 16h4M8 11h.01M8 16h.01" stroke-width="2"/>
                </svg>
                View All Prescriptions
            </button>
        </div>

        <!-- Embedded AI Assistant — Heidi-style panel -->
        <div>
            <div class="relative rounded-3xl overflow-hidden bg-green-800/80 flex items-center justify-center py-12 px-4 shadow-lg" style="min-height: 220px;">
                <!-- Blurred golden orbs -->
                <div class="absolute -bottom-8 left-12 w-52 h-52 bg-yellow-400/50 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-4 right-16 w-64 h-64 bg-yellow-300/35 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute top-6 right-10 w-32 h-32 bg-green-500/30 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute top-4 left-1/3 w-20 h-20 bg-yellow-500/20 rounded-full blur-2xl pointer-events-none"></div>

                <!-- Centered content -->
                <div class="relative z-10 w-full max-w-2xl px-4 sm:px-8 space-y-3">
                    <div class="flex items-center justify-center gap-2">
                        <span class="w-2 h-2 bg-green-400 rounded-full animate-ping"></span>
                        <p class="text-green-200 text-xs font-medium">USM Health AI — Online</p>
                    </div>
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl flex items-center gap-4 px-5 py-4 border border-white/40">
                        <div class="w-6 h-6 shrink-0 flex items-center justify-center text-green-700">
                            <svg viewBox="0 0 32 32" class="w-6 h-6 text-green-800" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16 3L4 8v8c0 6.627 5.373 12 12 12s12-5.373 12-12V8L16 3z" stroke="currentColor" stroke-width="2"/>
                                <path d="M11 16l3 3 7-7" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            x-model="heroInput"
                            @keydown.enter="sendHeroMessage()"
                            placeholder="Ask USM Health anything..."
                            class="flex-1 text-sm text-gray-700 placeholder:text-gray-400 outline-none bg-transparent border-0 focus:ring-0 p-0"
                        />
                        <button
                            @click="sendHeroMessage()"
                            :disabled="!heroInput.trim()"
                            class="bg-yellow-400 hover:bg-yellow-500 active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed text-green-900 rounded-xl w-9 h-9 flex items-center justify-center transition-all shadow-sm shrink-0"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <line x1="7" y1="17" x2="17" y2="7"/>
                                <polyline points="7 7 17 7 17 17"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Suggestion chips below panel -->
            <div class="flex flex-wrap gap-2 mt-4 justify-center">
                <template x-for="chip in ['I have a headache', 'Check medicine stock', 'About my prescription', 'First aid for fever']" :key="chip">
                    <button
                        @click="heroInput = chip; sendHeroMessage()"
                        class="text-xs font-medium text-gray-600 bg-white hover:bg-yellow-50 hover:text-green-800 border border-gray-200 hover:border-yellow-400 px-4 py-2 rounded-full shadow-sm transition-all"
                        x-text="chip"
                    ></button>
                </template>
            </div>
        </div>

        <!-- Stat cards -->
        <div class="grid grid-cols-3 gap-4">
            <div class="border-green-200 bg-white border rounded-xl px-4 py-4 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Total Prescriptions</p>
                <p class="text-3xl font-bold text-green-800" x-text="prescriptions.length"></p>
            </div>
            <div class="border-yellow-200 bg-yellow-50 border rounded-xl px-4 py-4 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Pending</p>
                <p class="text-3xl font-bold text-yellow-700" x-text="countStatus('Pending')"></p>
            </div>
            <div class="border-green-200 bg-green-50 border rounded-xl px-4 py-4 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Dispensed</p>
                <p class="text-3xl font-bold text-green-700" x-text="countStatus('Dispensed')"></p>
            </div>
        </div>

        <!-- Recent prescriptions -->
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Recent Prescriptions</h2>
                <button @click="setView('prescriptions')" class="text-xs text-green-700 font-semibold hover:underline flex items-center gap-1">
                    See all
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <template x-if="prescriptions.length === 0">
                <div class="py-16 text-center text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="8" height="4" x="8" y="2" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/></svg>
                    <p class="text-sm font-semibold">No prescriptions yet</p>
                    <p class="text-xs mt-1">Prescriptions from your doctor will appear here.</p>
                </div>
            </template>

            <template x-if="prescriptions.length > 0">
                <div class="divide-y divide-gray-50">
                    <template x-for="rx in prescriptions.slice(0, 5)" :key="rx.id">
                        <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-gray-50 transition-colors">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <p class="text-sm font-semibold text-gray-800 font-mono" x-text="rx.id"></p>
                                    <span
                                        :class="statusBadgeClass(rx.status)"
                                        class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full"
                                    >
                                        <span x-html="statusIconSvg(rx.status)"></span>
                                        <span x-text="rx.status"></span>
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400">
                                    <span x-text="rx.doctor"></span> ·
                                    <span x-text="rx.items.length + (rx.items.length > 1 ? ' items' : ' item')"></span> ·
                                    <span x-text="rx.date"></span>
                                </p>
                            </div>
                            <button
                                @click="openPrescriptionDetail(rx)"
                                class="shrink-0 text-xs text-green-700 font-semibold hover:text-green-900 flex items-center gap-1"
                            >
                                View
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <!-- Quick links -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <button
                @click="setView('profile')"
                class="bg-white border border-gray-200 rounded-2xl p-5 flex items-center gap-4 hover:border-green-400 hover:shadow-sm transition-all text-left"
            >
                <div class="w-11 h-11 bg-green-100 rounded-xl flex items-center justify-center shrink-0 text-green-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4" stroke-width="2"/><path d="M18 20a6 6 0 00-12 0" stroke-width="2"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-800 text-sm">My Medical Profile</p>
                    <p class="text-xs text-gray-400 mt-0.5">Update allergies and medical conditions</p>
                </div>
                <svg class="w-4 h-4 text-gray-300 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>

            <button
                @click="setView('storefront')"
                class="bg-white border border-gray-200 rounded-2xl p-5 flex items-center gap-4 hover:border-yellow-400 hover:shadow-sm transition-all text-left"
            >
                <div class="w-11 h-11 bg-yellow-100 rounded-xl flex items-center justify-center shrink-0 text-yellow-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" stroke-width="2"/><line x1="3" y1="6" x2="21" y2="6" stroke-width="2"/><path d="M16 10a4 4 0 01-8 0" stroke-width="2"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-800 text-sm">Visit Pharmacy Store</p>
                    <p class="text-xs text-gray-400 mt-0.5">Browse available medicines and supplies</p>
                </div>
                <svg class="w-4 h-4 text-gray-300 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>

    </div>

    <!-- ══════════════════════════════════════════
        PRESCRIPTIONS VIEW
    ══════════════════════════════════════════ -->
    <div x-show="view === 'prescriptions'" class="flex-1 max-w-3xl w-full mx-auto px-4 sm:px-6 py-8">
        <h1 class="text-xl font-bold text-gray-800 mb-5">Prescription History</h1>

        <!-- Filter tabs -->
        <div class="flex gap-1.5 flex-wrap mb-6">
            <template x-for="f in ['All', 'Pending', 'At Pharmacy', 'Dispensed', 'Cancelled']" :key="f">
                <button
                    @click="rxFilter = f"
                    :class="rxFilter === f
                        ? 'bg-green-800 text-white border-green-800'
                        : 'bg-white text-gray-600 border-gray-300 hover:border-green-400'"
                    class="text-xs font-semibold px-3.5 py-1.5 rounded-full border transition-colors"
                >
                    <span x-text="f"></span>
                    <span
                        :class="rxFilter === f ? 'text-green-300' : 'text-gray-400'"
                        class="ml-1"
                        x-text="'(' + countStatus(f) + ')'"
                    ></span>
                </button>
            </template>
        </div>

        <template x-if="filteredPrescriptions().length === 0">
            <div class="bg-white border border-gray-200 rounded-2xl py-16 text-center text-gray-400 shadow-sm">
                <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="8" height="4" x="8" y="2" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/></svg>
                <p class="text-sm font-semibold">No prescriptions</p>
            </div>
        </template>

        <div class="space-y-3" x-show="filteredPrescriptions().length > 0">
            <template x-for="rx in filteredPrescriptions()" :key="rx.id">
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden hover:shadow-sm transition-shadow shadow-sm">
                    <div class="flex items-start justify-between gap-4 px-5 pt-4 pb-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <p class="text-sm font-bold text-gray-800 font-mono" x-text="rx.id"></p>
                                <span
                                    :class="statusBadgeClass(rx.status)"
                                    class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full"
                                >
                                    <span x-html="statusIconSvg(rx.status)"></span>
                                    <span x-text="rx.status"></span>
                                </span>
                            </div>
                            <p class="text-xs text-gray-500">
                                <span x-text="rx.doctor"></span> &mdash; <span x-text="rx.date"></span>
                            </p>
                            <p class="text-xs text-gray-400 mt-1 italic" x-text="rx.notes"></p>
                        </div>
                        <button
                            @click="openPrescriptionDetail(rx)"
                            class="shrink-0 text-xs font-semibold text-green-700 hover:text-green-900 flex items-center gap-1 mt-0.5"
                        >
                            Details
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                    <div class="border-t border-gray-50 px-5 py-2.5 bg-gray-50/60 flex gap-4 overflow-x-auto">
                        <template x-for="item in rx.items" :key="item.name">
                            <span class="text-[11px] text-gray-500 whitespace-nowrap flex items-center gap-1">
                                <svg class="w-3 h-3 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z" stroke-width="2"/><path d="m8.5 8.5 7 7" stroke-width="2"/></svg>
                                <span x-text="item.name"></span>
                                <span class="text-gray-400" x-text="'×' + item.qty"></span>
                            </span>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
        PRESCRIPTION DETAIL VIEW
    ══════════════════════════════════════════ -->
    <div x-show="view === 'prescription-detail' && selectedRx" class="flex-1 max-w-2xl w-full mx-auto px-4 sm:px-6 py-8 space-y-5">
        <button
            @click="setView('prescriptions')"
            class="flex items-center gap-1.5 text-sm text-green-700 hover:text-green-900 font-medium transition-colors"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Prescriptions
        </button>

        <div class="flex items-center justify-between" x-show="selectedRx">
            <div>
                <h1 class="text-xl font-bold text-gray-800 font-mono" x-text="selectedRx ? selectedRx.id : ''"></h1>
                <p class="text-xs text-gray-400 mt-0.5">Prescription Details</p>
            </div>
            <span
                :class="selectedRx ? statusBadgeClass(selectedRx.status) : ''"
                class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full"
            >
                <span x-html="selectedRx ? statusIconSvg(selectedRx.status) : ''"></span>
                <span x-text="selectedRx ? selectedRx.status : ''"></span>
            </span>
        </div>

        <!-- Status Banner -->
        <div
            x-show="selectedRx"
            :class="selectedRx ? statusBannerConfig(selectedRx.status).wrapper : ''"
            class="flex items-start gap-3 rounded-xl px-4 py-3.5 border"
        >
            <div x-html="selectedRx ? statusBannerConfig(selectedRx.status).icon : ''" class="shrink-0 mt-0.5"></div>
            <p class="text-sm text-gray-700 leading-relaxed" x-text="selectedRx ? statusBannerConfig(selectedRx.status).text : ''"></p>
        </div>

        <!-- Metadata -->
        <div class="bg-white border border-gray-200 rounded-2xl px-5 py-4 grid grid-cols-2 gap-4 shadow-sm" x-show="selectedRx">
            <div>
                <p class="text-xs text-gray-400 mb-1">Issued by</p>
                <p class="text-sm font-semibold text-gray-800" x-text="selectedRx ? selectedRx.doctor : ''"></p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Date Issued</p>
                <p class="text-sm font-semibold text-gray-800" x-text="selectedRx ? selectedRx.date : ''"></p>
            </div>
            <div class="col-span-2">
                <p class="text-xs text-gray-400 mb-1">Doctor's Notes</p>
                <p class="text-sm text-gray-700 italic" x-text="selectedRx ? selectedRx.notes : ''"></p>
            </div>
        </div>

        <!-- Medicines table -->
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm" x-show="selectedRx">
            <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50">
                <p class="text-xs font-bold text-gray-600 uppercase tracking-wide">Prescribed Medicines</p>
            </div>
            <div class="divide-y divide-gray-50">
                <template x-if="selectedRx">
                    <template x-for="(item, i) in selectedRx.items" :key="i">
                        <div class="px-5 py-4 flex items-start gap-4">
                            <div class="w-9 h-9 bg-green-50 border border-green-100 rounded-xl flex items-center justify-center shrink-0 text-green-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z" stroke-width="2"/><path d="m8.5 8.5 7 7" stroke-width="2"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800" x-text="item.name"></p>
                                <p class="text-xs text-gray-400 mt-0.5" x-text="item.generic"></p>
                                <p class="text-xs text-gray-500 mt-1 bg-gray-50 border border-gray-100 rounded-lg px-2.5 py-1 inline-block" x-text="item.dosage"></p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-xs text-gray-400">Qty</p>
                                <p class="text-lg font-bold text-green-800" x-text="item.qty"></p>
                            </div>
                        </div>
                    </template>
                </template>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
        PROFILE VIEW
    ══════════════════════════════════════════ -->
    <div x-show="view === 'profile'" class="flex-1 max-w-2xl w-full mx-auto px-4 sm:px-6 py-8 space-y-5">
        <h1 class="text-xl font-bold text-gray-800">Medical Profile</h1>

        <!-- Read-only institutional info -->
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="bg-green-800 border-b-4 border-yellow-500 px-5 py-3">
                <p class="text-white text-xs font-semibold uppercase tracking-wide">Account Information</p>
                <p class="text-green-300 text-[10px] mt-0.5">This information is managed by the system</p>
            </div>
            <div class="grid grid-cols-2 gap-4 px-5 py-5">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Full Name</p>
                    <p class="text-sm font-semibold text-gray-800" x-text="patient.name"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Patient ID</p>
                    <p class="text-sm font-semibold text-gray-800" x-text="patient.id"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Patient Type</p>
                    <p class="text-sm font-semibold text-gray-800" x-text="patient.type"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Email</p>
                    <p class="text-sm font-semibold text-gray-800" x-text="patient.email"></p>
                </div>
            </div>
        </div>

        <!-- Editable clinical info -->
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100">
                <p class="text-sm font-bold text-gray-700">Clinical Information</p>
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <form @submit.prevent="saveProfile()" class="px-5 py-5 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">
                        <svg class="w-3 h-3 inline mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" stroke-width="2"/></svg>
                        Contact Number
                    </label>
                    <input
                        type="tel"
                        x-model="profileContact"
                        @input="profileSaved = false"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                        placeholder="e.g. 09XX XXX XXXX"
                    />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">
                        <svg class="w-3 h-3 inline mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><line x1="12" y1="8" x2="12" y2="12" stroke-width="2"/><line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2"/></svg>
                        Known Allergies
                    </label>
                    <textarea
                        x-model="profileAllergies"
                        @input="profileSaved = false"
                        rows="3"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-600 resize-none"
                        placeholder="e.g. Penicillin, Aspirin, Sulfonamides"
                    ></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">
                        <svg class="w-3 h-3 inline mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12" stroke-width="2"/></svg>
                        Medical Notes / Conditions
                    </label>
                    <textarea
                        x-model="profileConditions"
                        @input="profileSaved = false"
                        rows="3"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-600 resize-none"
                        placeholder="e.g. Hypertension, Asthmatic, Diabetic Type 2"
                    ></textarea>
                </div>
                <div class="flex items-center justify-between pt-1">
                    <span x-show="profileSaved" class="text-xs text-green-600 flex items-center gap-1 font-medium">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" stroke-width="2.5"/></svg>
                        Changes saved
                    </span>
                    <button
                        type="submit"
                        :disabled="isSavingProfile"
                        class="ml-auto flex items-center gap-2 bg-green-800 hover:bg-green-700 disabled:opacity-50 text-white font-semibold text-sm px-5 py-2.5 rounded-xl transition-colors border-b-2 border-yellow-500 shadow-sm"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" stroke-width="2"/><polyline points="17 21 17 13 7 13 7 21" stroke-width="2"/><polyline points="7 3 7 8 15 8" stroke-width="2"/></svg>
                        <span x-text="isSavingProfile ? 'Saving...' : 'Save Changes'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
        STOREFRONT VIEW
    ══════════════════════════════════════════ -->
    <div x-show="view === 'storefront'" class="flex-1 flex flex-col bg-white">
        <div class="bg-green-800 border-b-4 border-yellow-500 px-6 py-4 shrink-0 shadow-sm">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="flex items-center gap-2 mr-auto">
                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" stroke-width="2"/><line x1="3" y1="6" x2="21" y2="6" stroke-width="2"/><path d="M16 10a4 4 0 01-8 0" stroke-width="2"/></svg>
                    <span class="text-white font-bold text-sm">USM Hospital Pharmacy</span>
                    <span class="text-green-300 text-xs">— Product Catalog</span>
                </div>
                <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-2 w-full sm:w-72 shadow-inner">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="2"/><line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="2"/></svg>
                    <input
                        type="text"
                        placeholder="Search products..."
                        x-model="searchQuery"
                        @input="activeCategory = 'All'"
                        class="flex-1 text-sm text-gray-700 placeholder:text-gray-400 outline-none bg-transparent border-0 p-0 focus:ring-0"
                    />
                    <button x-show="searchQuery" @click="searchQuery = ''"><svg class="w-3.5 h-3.5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18" stroke-width="2"/><line x1="6" y1="6" x2="18" y2="18" stroke-width="2"/></svg></button>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto w-full px-6 py-5 flex gap-6 flex-1">
            <!-- Sidebar -->
            <aside class="w-48 shrink-0 hidden md:block">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Categories</p>
                <ul class="space-y-0.5">
                    <template x-for="cat in categories" :key="cat.label">
                        <li>
                            <button
                                @click="activeCategory = cat.label; searchQuery = ''"
                                :class="activeCategory === cat.label ? 'bg-green-700 text-white font-semibold' : 'text-gray-600 hover:bg-gray-100'"
                                class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm transition-colors text-left"
                            >
                                <span x-html="cat.icon" class="shrink-0"></span>
                                <span x-text="cat.label"></span>
                                <span
                                    :class="activeCategory === cat.label ? 'text-green-200' : 'text-gray-400'"
                                    class="ml-auto text-xs"
                                    x-text="getCategoryCount(cat.label)"
                                ></span>
                            </button>
                        </li>
                    </template>
                </ul>
                <div class="mt-6 border-t border-gray-100 pt-5 space-y-3">
                    <div class="flex items-start gap-2 text-xs text-gray-500">
                        <svg class="w-3.5 h-3.5 text-green-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke-width="2"/><path d="m9 12 2 2 4-4" stroke-width="2"/></svg>
                        <span>All items FDA registered</span>
                    </div>
                    <div class="flex items-start gap-2 text-xs text-gray-500">
                        <svg class="w-3.5 h-3.5 text-green-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="16" height="13" x="1" y="3" rx="2" stroke-width="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8" stroke-width="2"/><circle cx="5.5" cy="18.5" r="2.5" stroke-width="2"/><circle cx="18.5" cy="18.5" r="2.5" stroke-width="2"/></svg>
                        <span>Pick up at USM Pharmacy, Bldg A</span>
                    </div>
                    <div class="flex items-start gap-2 text-xs text-gray-500">
                        <svg class="w-3.5 h-3.5 text-green-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="2"/><polyline points="14 2 14 8 20 8" stroke-width="2"/><line x1="16" y1="13" x2="8" y2="13" stroke-width="2"/><line x1="16" y1="17" x2="8" y2="17" stroke-width="2"/></svg>
                        <span>Rx items require valid prescription</span>
                    </div>
                </div>
            </aside>

            <!-- Main Catalog -->
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm text-gray-500">
                        Showing <span class="font-semibold text-gray-800" x-text="filteredProducts().length"></span>
                        <span x-text="searchQuery ? 'results for &quot;' + searchQuery + '&quot;' : activeCategory === 'All' ? 'products' : 'in ' + activeCategory"></span>
                    </p>
                    <button
                        x-show="searchQuery || activeCategory !== 'All'"
                        @click="searchQuery = ''; activeCategory = 'All'"
                        class="text-xs text-green-700 hover:underline flex items-center gap-1"
                    >
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18" stroke-width="2"/><line x1="6" y1="6" x2="18" y2="18" stroke-width="2"/></svg>
                        Clear
                    </button>
                </div>

                <!-- Mobile category chips -->
                <div class="flex gap-2 overflow-x-auto pb-2 mb-4 md:hidden">
                    <template x-for="cat in categories" :key="cat.label">
                        <button
                            @click="activeCategory = cat.label; searchQuery = ''"
                            :class="activeCategory === cat.label ? 'bg-green-700 text-white border-green-700' : 'bg-white text-gray-600 border-gray-300 hover:border-green-400'"
                            class="shrink-0 text-xs font-semibold px-3 py-1.5 rounded-full border transition-colors"
                            x-text="cat.label"
                        ></button>
                    </template>
                </div>

                <!-- Product Grid -->
                <div x-show="filteredProducts().length > 0" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    <template x-for="product in filteredProducts()" :key="product.id">
                        <div
                            :class="product.stock === 0 ? 'opacity-60' : 'hover:shadow-md'"
                            class="border border-gray-200 rounded-xl overflow-hidden bg-white flex flex-col transition-shadow"
                        >
                            <div class="bg-gray-50 flex items-center justify-center py-6 relative border-b border-gray-100">
                                <div class="w-14 h-14 bg-white border border-gray-200 rounded-xl flex items-center justify-center shadow-sm text-green-600">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z" stroke-width="2"/><path d="m8.5 8.5 7 7" stroke-width="2"/></svg>
                                </div>
                                <template x-if="product.badge === 'SALE'">
                                    <span class="absolute top-2 left-2 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded">SALE</span>
                                </template>
                                <template x-if="product.badge === 'NEW'">
                                    <span class="absolute top-2 left-2 bg-yellow-400 text-green-900 text-[10px] font-bold px-1.5 py-0.5 rounded">NEW</span>
                                </template>
                                <template x-if="product.badge === 'CRIT'">
                                    <span class="absolute top-2 left-2 bg-orange-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded">CRIT</span>
                                </template>
                                <template x-if="product.stock === 0">
                                    <div class="absolute inset-0 bg-white/60 flex items-center justify-center">
                                        <span class="text-xs font-semibold text-red-500">Out of Stock</span>
                                    </div>
                                </template>
                            </div>
                            <div class="p-3 flex flex-col flex-1">
                                <p class="text-[10px] text-gray-400 mb-0.5" x-text="product.brand + ' · ' + product.category"></p>
                                <p class="text-sm font-semibold text-gray-900 leading-snug mb-2 line-clamp-2" x-text="product.name"></p>
                                <div class="flex items-center gap-1 mb-2">
                                    <template x-for="s in [1,2,3,4,5]" :key="s">
                                        <svg
                                            :class="s <= Math.floor(product.rating) ? 'fill-yellow-400 text-yellow-400' : 'fill-gray-200 text-gray-200'"
                                            class="w-2.5 h-2.5"
                                            viewBox="0 0 24 24"
                                        ><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    </template>
                                    <span class="text-[10px] text-gray-400 ml-1" x-text="'(' + product.sold + ')'"></span>
                                </div>
                                <div class="mt-auto">
                                    <div class="flex items-baseline gap-1 mb-1">
                                        <span class="text-base font-bold text-green-800" x-text="'₱' + product.price"></span>
                                        <span class="text-xs text-gray-400" x-text="'/ ' + product.unit"></span>
                                        <template x-if="product.originalPrice">
                                            <span class="text-xs text-gray-400 line-through ml-1" x-text="'₱' + product.originalPrice"></span>
                                        </template>
                                    </div>
                                    <template x-if="product.rx">
                                        <p class="text-[10px] text-green-700 mb-2">⚕ Prescription required</p>
                                    </template>
                                    <template x-if="product.stock > 0 && product.stock < 100">
                                        <p class="text-[10px] text-orange-500 mb-2" x-text="'Only ' + product.stock + ' left'"></p>
                                    </template>
                                    <button
                                        :disabled="product.stock === 0"
                                        :class="product.stock === 0 ? 'bg-gray-100 text-gray-400 border-gray-100 cursor-not-allowed' : 'bg-white text-green-700 border-green-600 hover:bg-green-700 hover:text-white'"
                                        class="w-full text-xs font-semibold py-2 rounded-lg border transition-colors"
                                        x-text="product.stock === 0 ? 'Unavailable' : 'Inquire at Pharmacy'"
                                    ></button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="filteredProducts().length === 0" class="text-center py-20 text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16.5 9.4 7.55 4.24a1.78 1.78 0 0 0-2.5 1.55v12.42a1.78 1.78 0 0 0 2.5 1.55L16.5 14.6a1.78 1.78 0 0 0 0-3.2z"/></svg>
                    <p class="font-semibold text-gray-500">No products found</p>
                    <button @click="searchQuery = ''; activeCategory = 'All'" class="mt-3 text-sm text-green-700 hover:underline">Clear search</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
        CHAT VIEW
    ══════════════════════════════════════════ -->
    <div x-show="view === 'chat'" class="flex-1 flex flex-col max-w-3xl w-full mx-auto px-4 py-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 bg-green-700 rounded-xl flex items-center justify-center shadow text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M12 8V4m0 0a2 2 0 100-4 2 2 0 000 4z" stroke-width="2"/>
                    <rect width="16" height="12" x="4" y="8" rx="2" stroke-width="2"/>
                    <path d="M2 14h2M20 14h2M9 13v2M15 13v2" stroke-width="2"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-bold text-green-900">USM Health AI</p>
                <p class="text-xs text-green-600 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full inline-block"></span> Online
                </p>
            </div>
        </div>

        <div class="flex-1 bg-white rounded-2xl border border-gray-200 shadow-sm flex flex-col overflow-hidden" style="min-height: 480px;">
            <div class="px-4 py-3 border-b border-gray-100 bg-green-50 flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12" stroke-width="2"/></svg>
                <p class="text-xs font-semibold text-green-800">Ask about symptoms, medicines, or first aid</p>
            </div>

            <div id="chatMessagesBox" class="flex-1 overflow-y-auto p-5 space-y-4 max-h-[420px]">
                <template x-for="(msg, i) in chatMessages" :key="i">
                    <div :class="msg.role === 'user' ? 'justify-end' : ''" class="flex gap-3">
                        <template x-if="msg.role === 'assistant'">
                            <div class="w-7 h-7 bg-green-100 rounded-xl flex items-center justify-center shrink-0 mt-0.5 text-green-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8V4m0 0a2 2 0 100-4 2 2 0 000 4z" stroke-width="2"/>
                                    <rect width="16" height="12" x="4" y="8" rx="2" stroke-width="2"/>
                                    <path d="M2 14h2M20 14h2M9 13v2M15 13v2" stroke-width="2"/>
                                </svg>
                            </div>
                        </template>

                        <div
                            :class="msg.role === 'user' ? 'bg-green-700 text-white rounded-tr-sm' : 'bg-gray-100 text-gray-800 rounded-tl-sm'"
                            class="max-w-[80%] rounded-2xl px-4 py-3 text-sm leading-relaxed whitespace-pre-line"
                            x-text="msg.content"
                        ></div>

                        <template x-if="msg.role === 'user'">
                            <div class="w-7 h-7 bg-yellow-400 rounded-xl flex items-center justify-center shrink-0 mt-0.5 text-green-900">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" stroke-width="2"/><circle cx="12" cy="7" r="4" stroke-width="2"/></svg>
                            </div>
                        </template>
                    </div>
                </template>
                <div id="chatBottomAnchor"></div>
            </div>

            <div class="border-t border-gray-100 p-4 bg-white">
                <form @submit.prevent="sendChatMessage()" class="flex gap-2">
                    <input
                        type="text"
                        x-model="chatInput"
                        placeholder="Ask about symptoms, medicines, or prescriptions..."
                        class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 bg-gray-50"
                    />
                    <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2.5 rounded-xl transition-colors shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13" stroke-width="2"/><polygon points="22 2 15 22 11 13 2 9 22 2" stroke-width="2"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
function patientPortalApp() {
    return {
        view: 'dashboard',
        mobileMenuOpen: false,
        patient: @json($patientData),
        prescriptions: @json($formattedPrescriptions),
        products: @json($products),
        selectedRx: null,
        rxFilter: 'All',
        heroInput: '',
        chatMessages: [
            { role: 'assistant', content: "Hello! I'm your USM Health Assistant. Ask me about symptoms, medicines, first aid, or your prescriptions." }
        ],
        chatInput: '',
        activeCategory: 'All',
        searchQuery: '',
        profileContact: '{{ $patient->contact_number ?? "09171234567" }}',
        profileAllergies: '{{ $patient->allergies ?? "Penicillin" }}',
        profileConditions: '{{ $patient->medical_notes ?? "Asthmatic" }}',
        profileSaved: false,
        isSavingProfile: false,

        navItems: [
            {
                label: 'Dashboard',
                view: 'dashboard',
                icon: '<svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="7" height="9" x="3" y="3" rx="1" stroke-width="2"/><rect width="7" height="5" x="14" y="3" rx="1" stroke-width="2"/><rect width="7" height="9" x="14" y="12" rx="1" stroke-width="2"/><rect width="7" height="5" x="3" y="16" rx="1" stroke-width="2"/></svg>'
            },
            {
                label: 'Prescriptions',
                view: 'prescriptions',
                icon: '<svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="8" height="4" x="8" y="2" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" stroke-width="2"/><path d="M12 11h4M12 16h4M8 11h.01M8 16h.01" stroke-width="2"/></svg>'
            },
            {
                label: 'Pharmacy',
                view: 'storefront',
                icon: '<svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" stroke-width="2"/><line x1="3" y1="6" x2="21" y2="6" stroke-width="2"/><path d="M16 10a4 4 0 01-8 0" stroke-width="2"/></svg>'
            },
            {
                label: 'AI Assistant',
                view: 'chat',
                icon: '<svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8V4m0 0a2 2 0 100-4 2 2 0 000 4z" stroke-width="2"/><rect width="16" height="12" x="4" y="8" rx="2" stroke-width="2"/><path d="M2 14h2M20 14h2M9 13v2M15 13v2" stroke-width="2"/></svg>'
            },
            {
                label: 'My Profile',
                view: 'profile',
                icon: '<svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4" stroke-width="2"/><path d="M18 20a6 6 0 00-12 0" stroke-width="2"/></svg>'
            }
        ],

        categories: [
            { label: "All",              icon: '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" stroke-width="2"/><line x1="3" y1="6" x2="21" y2="6" stroke-width="2"/></svg>' },
            { label: "IV Fluids",        icon: '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z" stroke-width="2"/></svg>' },
            { label: "Injectables",      icon: '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4.5 3h15M6 3v16a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V3" stroke-width="2"/></svg>' },
            { label: "Oral Medications", icon: '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z" stroke-width="2"/><path d="m8.5 8.5 7 7" stroke-width="2"/></svg>' },
            { label: "Wound Care",       icon: '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" stroke-width="2"/></svg>' },
            { label: "Respiratory",      icon: '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12" stroke-width="2"/></svg>' },
            { label: "Consumables",      icon: '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16.5 9.4 7.55 4.24a1.78 1.78 0 0 0-2.5 1.55v12.42a1.78 1.78 0 0 0 2.5 1.55L16.5 14.6a1.78 1.78 0 0 0 0-3.2z"/></svg>' },
            { label: "Supplements",      icon: '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z" stroke-width="2"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12" stroke-width="2"/></svg>' },
        ],

        init() {
            const hash = window.location.hash.replace('#', '');
            if (['dashboard', 'prescriptions', 'storefront', 'chat', 'profile'].includes(hash)) {
                this.view = hash;
            }
            if (this.prescriptions.length > 0) {
                this.selectedRx = this.prescriptions[0];
            }
        },

        setView(v) {
            this.view = v;
            window.location.hash = v;
            window.scrollTo({ top: 0, behavior: 'smooth' });
            if (v === 'chat') {
                this.$nextTick(() => {
                    document.getElementById('chatBottomAnchor')?.scrollIntoView({ behavior: 'smooth' });
                });
            }
        },

        openPrescriptionDetail(rx) {
            this.selectedRx = rx;
            this.setView('prescription-detail');
        },

        countStatus(st) {
            if (st === 'All') return this.prescriptions.length;
            return this.prescriptions.filter(r => r.status === st).length;
        },

        filteredPrescriptions() {
            if (this.rxFilter === 'All') return this.prescriptions;
            return this.prescriptions.filter(r => r.status === this.rxFilter);
        },

        statusBadgeClass(st) {
            switch(st) {
                case 'Pending':     return 'bg-yellow-100 text-yellow-700 border border-yellow-200';
                case 'At Pharmacy': return 'bg-blue-100 text-blue-700 border border-blue-200';
                case 'Dispensed':   return 'bg-green-100 text-green-700 border border-green-200';
                case 'Cancelled':   return 'bg-red-100 text-red-600 border border-red-200';
                default:            return 'bg-gray-100 text-gray-700 border border-gray-200';
            }
        },

        statusIconSvg(st) {
            switch(st) {
                case 'Pending':
                    return '<svg class="w-3 h-3 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><polyline points="12 6 12 12 16 14" stroke-width="2"/></svg>';
                case 'At Pharmacy':
                    return '<svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12" stroke-width="2"/></svg>';
                case 'Dispensed':
                    return '<svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" stroke-width="2.5"/></svg>';
                default:
                    return '<svg class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><line x1="15" y1="9" x2="9" y2="15" stroke-width="2"/><line x1="9" y1="9" x2="15" y2="15" stroke-width="2"/></svg>';
            }
        },

        statusBannerConfig(st) {
            switch(st) {
                case 'Pending':
                    return {
                        wrapper: 'bg-yellow-50 border-yellow-300',
                        icon: '<svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><polyline points="12 6 12 12 16 14" stroke-width="2"/></svg>',
                        text: 'Your prescription is under review. You will be notified once it has been routed to the pharmacy.'
                    };
                case 'At Pharmacy':
                    return {
                        wrapper: 'bg-blue-50 border-blue-300',
                        icon: '<svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12" stroke-width="2"/></svg>',
                        text: 'Your prescription is in the pharmacy queue. Please proceed to the dispensing window when called.'
                    };
                case 'Dispensed':
                    return {
                        wrapper: 'bg-green-50 border-green-400',
                        icon: '<svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" stroke-width="2.5"/></svg>',
                        text: 'Your medicines are ready for pickup. Present this prescription ID at the pharmacy counter.'
                    };
                default:
                    return {
                        wrapper: 'bg-red-50 border-red-300',
                        icon: '<svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><line x1="15" y1="9" x2="9" y2="15" stroke-width="2"/><line x1="9" y1="9" x2="15" y2="15" stroke-width="2"/></svg>',
                        text: 'This prescription has been cancelled. Please consult your doctor for a new prescription if needed.'
                    };
            }
        },

        filteredProducts() {
            return this.products.filter(p => {
                const matchCat = this.activeCategory === 'All' || p.category === this.activeCategory;
                const matchSearch = p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    p.category.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    p.brand.toLowerCase().includes(this.searchQuery.toLowerCase());
                const matchStock = this.searchQuery.trim() ? true : p.stock > 0;
                return matchCat && matchSearch && matchStock;
            });
        },

        getCategoryCount(label) {
            if (label === 'All') return this.products.filter(p => p.stock > 0).length;
            return this.products.filter(p => p.category === label && p.stock > 0).length;
        },

        sendHeroMessage() {
            if (!this.heroInput.trim()) return;
            const msg = this.heroInput.trim();
            this.heroInput = '';
            this.chatMessages = [
                { role: 'assistant', content: "Hello! I'm your USM Health Assistant. Ask me about symptoms, medicines, first aid, or your prescriptions." },
                { role: 'user', content: msg }
            ];
            this.setView('chat');
            setTimeout(() => {
                this.chatMessages.push({ role: 'assistant', content: this.getBotResponse(msg) });
                this.$nextTick(() => {
                    document.getElementById('chatBottomAnchor')?.scrollIntoView({ behavior: 'smooth' });
                });
            }, 800);
        },

        sendChatMessage() {
            if (!this.chatInput.trim()) return;
            const msg = this.chatInput.trim();
            this.chatInput = '';
            this.chatMessages.push({ role: 'user', content: msg });
            this.$nextTick(() => {
                document.getElementById('chatBottomAnchor')?.scrollIntoView({ behavior: 'smooth' });
            });
            setTimeout(() => {
                this.chatMessages.push({ role: 'assistant', content: this.getBotResponse(msg) });
                this.$nextTick(() => {
                    document.getElementById('chatBottomAnchor')?.scrollIntoView({ behavior: 'smooth' });
                });
            }, 800);
        },

        getBotResponse(input) {
            const q = input.toLowerCase();
            if (q.includes("headache"))
                return "For a headache:\n\n1. Rest in a quiet, dark room\n2. Stay hydrated\n3. Apply a cold compress\n4. Paracetamol 500mg or Ibuprofen 400mg are available at our pharmacy\n\nIf it persists beyond 24 hours, please see a doctor.";
            if (q.includes("fever"))
                return "For fever:\n\n1. Rest and stay hydrated\n2. Use a cool compress\n3. Paracetamol 500mg every 6 hours (in stock ✓)\n\nSeek care immediately if fever exceeds 39.4°C or lasts more than 3 days.";
            if (q.includes("availab") || q.includes("stock") || q.includes("medicine"))
                return "Currently in stock at USM Pharmacy:\n\n✓ Paracetamol 500mg\n✓ Amoxicillin 500mg\n✓ Metformin 500mg\n✓ Vitamin C 500mg\n✓ Salbutamol Nebule\n✓ IV Fluids (NS, D5W, LR)\n\nBrowse the full storefront for prices and availability.";
            if (q.includes("prescription"))
                return "You can view all your prescriptions under the Prescriptions tab. For refills, please consult your assigned physician at the USM clinic.";
            return "I can help you with:\n\n• First aid advice for common conditions\n• Medicine availability checks\n• General health information\n• Understanding your prescriptions\n\nCould you give me more details?";
        },

        saveProfile() {
            this.isSavingProfile = true;
            fetch('{{ route("patient.profile.update") }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    contact_number: this.profileContact,
                    allergies: this.profileAllergies,
                    medical_notes: this.profileConditions
                })
            })
            .then(res => res.json())
            .then(data => {
                this.isSavingProfile = false;
                this.profileSaved = true;
                setTimeout(() => { this.profileSaved = false; }, 4000);
            })
            .catch(err => {
                this.isSavingProfile = false;
                alert('Could not save profile. Please try again.');
            });
        }
    };
}
</script>
</body>
</html>
