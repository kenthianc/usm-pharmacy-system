@hasrole('nurse')
<x-nurse-layout>
    <x-slot name="heading">
        <div>
            <h1 class="text-xl font-bold text-slate-800 tracking-tight">Clinic Dashboard</h1>
            <p class="text-xs text-slate-500 mt-0.5">Welcome back, Nurse {{ Auth::user()->name }}. Here is your clinical queue overview.</p>
        </div>
    </x-slot>

    <div class="space-y-6">

        @if (session('status'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium">{{ session('status') }}</span>
                </div>
            </div>
        @endif

        <!-- Quick Action Banner -->
        <div class="bg-gradient-to-r from-emerald-900 via-emerald-800 to-teal-800 rounded-2xl p-6 text-white shadow-lg shadow-emerald-950/20 relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 relative z-10">
                <div class="space-y-1.5 max-w-xl">
                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-emerald-500/20 border border-emerald-400/30 text-emerald-200 text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        <span>USM University Hospital &amp; Health Services</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Prescription &amp; Clinic Operations</h2>
                    <p class="text-xs sm:text-sm text-emerald-100/80 leading-relaxed">
                        Encode outpatient and student prescriptions, monitor pharmacy queue fulfillment, and coordinate with dispensary pharmacists in real time.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <button type="button"
                       @click="$dispatch('open-new-prescription')"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-emerald-900 hover:bg-emerald-50 text-xs font-bold shadow-md transition transform active:scale-98 cursor-pointer">
                        <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>New Prescription</span>
                    </button>

                    <a href="{{ route('prescriptions.index', ['status' => 'pending']) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-700/80 hover:bg-emerald-700 text-white border border-emerald-500/40 text-xs font-semibold transition">
                        <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Review Pending ({{ $counts['pending'] }})</span>
                    </a>

                    <a href="{{ route('prescriptions.inventory') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-700/80 hover:bg-emerald-700 text-white border border-emerald-500/40 text-xs font-semibold transition">
                        <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span>Inventory Check</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 5 Status KPI Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5 sm:gap-4">
            <!-- All Prescriptions -->
            <a href="{{ route('prescriptions.index') }}"
               class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 hover:shadow-md transition-all group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 group-hover:text-slate-700">Total Encoded</span>
                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center group-hover:bg-slate-200 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $counts['all'] }}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">All prescription records</div>
                </div>
            </a>

            <!-- Pending Review -->
            <a href="{{ route('prescriptions.index', ['status' => 'pending']) }}"
               class="bg-white p-4 rounded-2xl border border-amber-200/80 shadow-xs hover:border-amber-400 hover:shadow-md transition-all group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-amber-700">Pending</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-extrabold text-amber-600 tracking-tight">{{ $counts['pending'] }}</div>
                    <div class="text-[11px] text-amber-600/80 mt-0.5 font-medium">Awaiting nurse routing</div>
                </div>
            </a>

            <!-- Routed to Pharmacy -->
            <a href="{{ route('prescriptions.index', ['status' => 'routed']) }}"
               class="bg-white p-4 rounded-2xl border border-blue-200/80 shadow-xs hover:border-blue-400 hover:shadow-md transition-all group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-blue-700">In Pharmacy</span>
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-extrabold text-blue-600 tracking-tight">{{ $counts['routed'] }}</div>
                    <div class="text-[11px] text-blue-600/80 mt-0.5 font-medium">Queued for dispensing</div>
                </div>
            </a>

            <!-- Dispensed -->
            <a href="{{ route('prescriptions.index', ['status' => 'dispensed']) }}"
               class="bg-white p-4 rounded-2xl border border-emerald-200/80 shadow-xs hover:border-emerald-400 hover:shadow-md transition-all group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-emerald-700">Dispensed</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-extrabold text-emerald-600 tracking-tight">{{ $counts['dispensed'] }}</div>
                    <div class="text-[11px] text-emerald-600/80 mt-0.5 font-medium">Fulfilled &amp; logged</div>
                </div>
            </a>

            <!-- Cancelled -->
            <a href="{{ route('prescriptions.index', ['status' => 'cancelled']) }}"
               class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs hover:border-rose-300 hover:shadow-md transition-all group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 group-hover:text-rose-600">Cancelled</span>
                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center group-hover:bg-rose-50 group-hover:text-rose-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-extrabold text-slate-700 group-hover:text-rose-600 tracking-tight">{{ $counts['cancelled'] }}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Voided prescriptions</div>
                </div>
            </a>
        </div>

        <!-- 2 Column Section: Recent Prescriptions & Stock Alerts -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left: Recent Prescriptions List (2 Columns wide) -->
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                            <h3 class="text-sm font-bold text-slate-800">Recent Prescriptions</h3>
                        </div>
                        <a href="{{ route('prescriptions.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 inline-flex items-center gap-1">
                            <span>View All Records</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse ($recentPrescriptions as $prescription)
                            @php
                                $badgeStyles = [
                                    'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'routed' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'dispensed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'cancelled' => 'bg-slate-100 text-slate-600 border-slate-200',
                                ];
                            @endphp

                            <div class="p-5 hover:bg-slate-50/70 transition-colors">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700 font-bold text-xs shrink-0 mt-0.5">
                                            #{{ str_pad($prescription->id, 4, '0', STR_PAD_LEFT) }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h4 class="text-sm font-bold text-slate-900">{{ $prescription->patient->name }}</h4>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold uppercase tracking-wider {{ $prescription->patient->patient_type === 'student' ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-purple-50 text-purple-700 border border-purple-200' }}">
                                                    {{ $prescription->patient->patient_type }}
                                                </span>
                                            </div>
                                            <div class="text-xs text-slate-500 mt-1 flex items-center gap-3 flex-wrap">
                                                <span>Dr. {{ $prescription->doctor_name }}</span>
                                                <span class="text-slate-300">&bull;</span>
                                                <span>Encoded by {{ $prescription->encodedBy->name }}</span>
                                                <span class="text-slate-300">&bull;</span>
                                                <span>{{ $prescription->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 self-start sm:self-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold uppercase tracking-wider border {{ $badgeStyles[$prescription->status] ?? 'bg-slate-100 text-slate-700' }}">
                                            {{ $prescription->status }}
                                        </span>
                                        <a href="{{ route('prescriptions.show', $prescription) }}"
                                           class="p-2 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 transition"
                                           title="View Details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>

                                <!-- Medicine tags -->
                                @if ($prescription->items->isNotEmpty())
                                    <div class="mt-3 flex flex-wrap gap-1.5 pl-13">
                                        @foreach ($prescription->items as $item)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200/60">
                                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                                </svg>
                                                <span>{{ $item->medicine->name }}</span>
                                                <span class="text-slate-400">×{{ $item->quantity }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-sm font-semibold text-slate-800">No prescriptions found</h4>
                                <p class="text-xs text-slate-500 mt-1">Start by encoding a new prescription for an outpatient or student.</p>
                                <a href="{{ route('prescriptions.create') }}" class="inline-flex items-center gap-1.5 mt-4 px-3.5 py-2 rounded-xl bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 shadow-sm transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    <span>Encode Prescription</span>
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right: Dispensary Stock Overview & Clinic Guide (1 Column wide) -->
            <div class="space-y-6">
                <!-- Stock Status Card -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-teal-500"></div>
                            <h3 class="text-sm font-bold text-slate-800">Dispensary Stock</h3>
                        </div>
                        <a href="{{ route('prescriptions.inventory') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">Full Inventory</a>
                    </div>

                    <div class="mt-4 divide-y divide-slate-100">
                        @foreach ($inventory as $med)
                            @php
                                $stock = $med->available_stock;
                            @endphp
                            <div class="py-2.5 flex items-center justify-between text-xs">
                                <div class="min-w-0 pr-2">
                                    <div class="font-semibold text-slate-800 truncate">{{ $med->name }}</div>
                                    <div class="text-[11px] text-slate-400 truncate">{{ $med->category }}</div>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold shrink-0 {{ $stock > 20 ? 'bg-emerald-50 text-emerald-700' : ($stock > 0 ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                                    {{ $stock }} {{ $med->unit }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Clinic Workflow Guide -->
                <div class="bg-slate-50 rounded-2xl border border-slate-200/80 p-5 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Prescription Workflow</h3>
                    <ol class="text-xs text-slate-600 space-y-2.5 list-decimal list-inside">
                        <li><strong class="text-slate-800">Encode:</strong> Enter patient info, doctor, and medicine quantities.</li>
                        <li><strong class="text-slate-800">Pending:</strong> Review prescription items and verify dosage.</li>
                        <li><strong class="text-slate-800">Route to Pharmacy:</strong> Send to the dispensary POS queue.</li>
                        <li><strong class="text-slate-800">Dispense:</strong> Pharmacist fulfills the items with FEFO batches.</li>
                    </ol>
                </div>
            </div>

        </div>

    </div>
</x-nurse-layout>
@elseif(auth()->user()->hasAnyRole(['stock_manager', 'admin', 'pharmacist']))
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-base sm:text-lg text-slate-800 leading-tight flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-sm shadow-xs shrink-0">
                📦
            </span>
            <span class="truncate">{{ auth()->user()->hasRole('admin') ? __('Hospital Administrator Dashboard') : (auth()->user()->hasRole('pharmacist') ? __('Dispensary & Pharmacy Operations Dashboard') : __('Inventory & Stock Management')) }}</span>
        </h2>
    </x-slot>

    <div class="space-y-4">
            @php
                $canManageInventory = auth()->user()->hasAnyRole(['stock_manager', 'admin']);
            @endphp

            <!-- 5 KPI Overview Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Total Formulations -->
                <a href="{{ $canManageInventory ? route('inventory.index') : route('inventory.risk-engine') }}" class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs hover:border-gray-300 transition">
                    <div class="flex items-center justify-between text-gray-500">
                        <span class="text-xs font-semibold uppercase tracking-wider">Total Formulary</span>
                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600">
                            💊
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-extrabold text-gray-900">{{ $inventoryCounts['total'] ?? 0 }}</div>
                        <p class="text-[11px] text-gray-400 mt-0.5">Catalogued medicines</p>
                    </div>
                </a>

                <!-- Healthy Stock -->
                <a href="{{ $canManageInventory ? route('inventory.index', ['stock_status' => 'in_stock']) : route('inventory.risk-engine') }}" class="bg-white p-4 rounded-xl border border-emerald-200/80 shadow-xs hover:border-emerald-400 transition">
                    <div class="flex items-center justify-between text-emerald-700">
                        <span class="text-xs font-semibold uppercase tracking-wider">Healthy Stock</span>
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                            ✓
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-extrabold text-emerald-700">{{ $inventoryCounts['in_stock'] ?? 0 }}</div>
                        <p class="text-[11px] text-emerald-600 mt-0.5">Above reorder limits</p>
                    </div>
                </a>

                <!-- Low Stock Alert -->
                <a href="{{ $canManageInventory ? route('inventory.index', ['stock_status' => 'low_stock']) : route('inventory.risk-engine', ['tab' => 'stockout']) }}" class="bg-white p-4 rounded-xl border border-amber-200/80 shadow-xs hover:border-amber-400 transition">
                    <div class="flex items-center justify-between text-amber-700">
                        <span class="text-xs font-semibold uppercase tracking-wider">Low Stock</span>
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                            ⚠️
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-extrabold text-amber-700">{{ $inventoryCounts['low_stock'] ?? 0 }}</div>
                        <p class="text-[11px] text-amber-600 mt-0.5">Needs reordering</p>
                    </div>
                </a>

                <!-- Out of Stock -->
                <a href="{{ $canManageInventory ? route('inventory.index', ['stock_status' => 'out_of_stock']) : route('inventory.risk-engine', ['tab' => 'stockout']) }}" class="bg-white p-4 rounded-xl border border-rose-200/80 shadow-xs hover:border-rose-400 transition">
                    <div class="flex items-center justify-between text-rose-700">
                        <span class="text-xs font-semibold uppercase tracking-wider">Out of Stock</span>
                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                            ✕
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-extrabold text-rose-700">{{ $inventoryCounts['out_of_stock'] ?? 0 }}</div>
                        <p class="text-[11px] text-rose-600 mt-0.5">Critical stockout</p>
                    </div>
                </a>

                <!-- Expiring Soon -->
                <div class="bg-white p-4 rounded-xl border border-purple-200/80 shadow-xs">
                    <div class="flex items-center justify-between text-purple-700">
                        <span class="text-xs font-semibold uppercase tracking-wider">Expiring (30d)</span>
                        <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                            ⏳
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-extrabold text-purple-700">{{ $inventoryCounts['expiring_soon'] ?? 0 }}</div>
                        <p class="text-[11px] text-purple-600 mt-0.5">Active batches</p>
                    </div>
                </div>
            </div>

            <!-- ── DUAL-RISK PREDICTION ENGINE LIVE DASHBOARD SECTION ────────────── -->
            <div class="rounded-2xl border border-gray-200 shadow-sm overflow-hidden bg-white">
                <!-- Section Header Banner -->
                <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 bg-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Inventory Risk &amp; Demand Forecasting</h3>
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Active Monitoring
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Stockout warning horizons and batch-level expiration exposure
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 flex-wrap">
                        <div class="text-left sm:text-right">
                            <span class="text-[10px] uppercase font-bold text-gray-400 block">Value at Expiry Risk</span>
                            <span class="text-base font-bold text-gray-900 font-mono">
                                ₱{{ number_format($riskEngine['telemetry']['total_financial_loss_at_risk'], 2) }}
                            </span>
                        </div>

                        <a href="{{ route('inventory.risk-engine') }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition shadow-xs">
                            <span>Detailed Risk Analysis &rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- 4 High-Level Risk Stats Strip -->
                <div class="grid grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-gray-100 bg-gray-50/70 border-b border-gray-200 text-xs">
                    <div class="p-3.5 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-sm shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase font-bold text-gray-400">Stockout Threats</div>
                            <div class="text-sm font-bold text-rose-600">{{ $riskEngine['telemetry']['high_stockout_count'] }} at risk</div>
                        </div>
                    </div>

                    <div class="p-3.5 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-sm shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase font-bold text-gray-400">Batches Near Expiry</div>
                            <div class="text-sm font-bold text-purple-700">{{ $riskEngine['telemetry']['high_expiry_count'] }} batches</div>
                        </div>
                    </div>

                    <div class="p-3.5 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase font-bold text-gray-400">Monitored Formulary</div>
                            <div class="text-xs font-bold text-gray-900">{{ $riskEngine['telemetry']['analyzed_medicines_count'] }} meds ({{ $riskEngine['telemetry']['analyzed_batches_count'] }} batches)</div>
                        </div>
                    </div>

                    <div class="p-3.5 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase font-bold text-gray-400">Forecast Horizon</div>
                            <div class="text-xs font-bold text-gray-800">{{ $riskEngine['telemetry']['lead_time_days'] }}d Lead Time</div>
                        </div>
                    </div>
                </div>

                <!-- Side-by-Side Live Feeds -->
                <div class="p-5 grid grid-cols-1 lg:grid-cols-2 gap-5 bg-white">
                    <!-- Left: Stockout Vulnerabilities (Sr) -->
                    <div class="rounded-xl border border-rose-200 bg-rose-50/20 p-4 flex flex-col">
                        <div class="flex items-center justify-between pb-3 border-b border-rose-100">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-600 animate-pulse"></span>
                                <h4 class="text-xs font-black text-rose-950 uppercase tracking-wider">
                                    Stockout Vulnerabilities
                                </h4>
                            </div>
                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md bg-rose-100 text-rose-800 border border-rose-200">
                                {{ count($riskEngine['stockout_insights']) }} Flagged
                            </span>
                        </div>

                        <div class="mt-3 space-y-3">
                            @forelse (array_slice($riskEngine['stockout_insights'], 0, 4) as $item)
                                <div class="p-4 rounded-xl bg-white border border-rose-200/90 hover:border-rose-300 transition shadow-xs">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h5 class="font-mono font-bold text-sm text-gray-900 tracking-wide">
                                                    {{ $item['code'] }}
                                                </h5>
                                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 border border-gray-200/80">
                                                    {{ $item['category'] }}
                                                </span>
                                            </div>
                                            <div class="text-[11px] text-gray-500 flex items-center gap-2.5 mt-2 flex-wrap">
                                                <span>On-Hand: <strong class="text-rose-600 font-bold">{{ $item['current_stock'] }} {{ $item['unit'] }}</strong></span>
                                                <span class="text-gray-300">•</span>
                                                <span>Daily use: {{ number_format($item['daily_consumption'], 1) }}/day</span>
                                                <span class="text-gray-300">•</span>
                                                <span class="font-extrabold {{ $item['days_until_depleted'] !== null && $item['days_until_depleted'] <= 3 ? 'text-rose-600' : 'text-amber-700' }}">
                                                    {{ $item['days_until_depleted'] !== null ? ($item['days_until_depleted'] == 0 ? 'Depleted' : $item['days_until_depleted'].'d left') : 'Depleted' }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2 shrink-0">
                                            @if($item['is_dual_risk'])
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold font-mono bg-rose-50 text-rose-700 border border-rose-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                                                    <span>{{ $item['score_chip'] ?? ('Dual · L'.$item['level'].' - '.round($item['risk_score']).'%') }}</span>
                                                </span>
                                            @elseif($item['level'] === 3)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold font-mono bg-rose-50 text-rose-700 border border-rose-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                    <span>{{ $item['score_chip'] ?? ('L3 - '.round($item['risk_score']).'%') }}</span>
                                                </span>
                                            @elseif($item['level'] === 2)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold font-mono bg-amber-50 text-amber-800 border border-amber-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    <span>{{ $item['score_chip'] ?? ('L2 - '.round($item['risk_score']).'%') }}</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold font-mono bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    <span>{{ $item['score_chip'] ?? ('L1 - '.round($item['risk_score']).'%') }}</span>
                                                </span>
                                            @endif
                                            @can('create', App\Models\Delivery::class)
                                                <a href="{{ route('inventory.deliveries.create', ['medicine_id' => $item['medicine_id']]) }}"
                                                    title="Create restock order for {{ $item['code'] }}"
                                                    class="w-8 h-8 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center transition shadow-xs">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </a>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold text-rose-700 bg-rose-50 border border-rose-200">
                                                    Alert
                                                </span>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="py-6 text-center text-gray-500 text-xs">
                                    <span class="text-emerald-600 font-bold text-base block mb-1">✓ Safe Inventory</span>
                                    Zero stockout threats detected.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Right: Expiry Vulnerabilities (Er) -->
                    <div class="rounded-xl border border-amber-200 bg-amber-50/20 p-4 flex flex-col">
                        <div class="flex items-center justify-between pb-3 border-b border-amber-100">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                                <h4 class="text-xs font-black text-amber-950 uppercase tracking-wider">
                                    Expiry Risks
                                </h4>
                            </div>
                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 border border-amber-200">
                                {{ count($riskEngine['expiry_insights']) }} Flagged
                            </span>
                        </div>

                        <div class="mt-3 space-y-3">
                            @forelse (array_slice($riskEngine['expiry_insights'], 0, 4) as $item)
                                <div class="p-4 rounded-xl bg-white border border-amber-200 hover:border-amber-300 transition shadow-xs">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h5 class="font-mono font-bold text-sm text-gray-900 tracking-wide">
                                                    #{{ $item['batch_no'] }}
                                                </h5>
                                                <span class="text-[11px] font-mono font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                                    Code: {{ $item['code'] ?? '' }}
                                                </span>
                                            </div>
                                            <div class="text-[11px] text-gray-500 flex items-center gap-2.5 mt-2 flex-wrap">
                                                <span><strong class="text-amber-900 font-bold">{{ $item['remaining_stock'] }} {{ $item['unit'] }}</strong> at risk</span>
                                                <span class="text-gray-300">•</span>
                                                <span class="font-mono font-bold text-rose-600">₱{{ number_format($item['financial_loss'], 2) }}</span>
                                                <span class="text-gray-300">•</span>
                                                <span class="font-semibold {{ $item['days_until_expiry'] <= 30 ? 'text-rose-600 font-bold' : 'text-amber-800' }}">
                                                    {{ $item['days_until_expiry'] <= 0 ? 'Expired' : $item['days_until_expiry'].'d left' }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2 shrink-0">
                                            @if($item['is_dual_risk'])
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold font-mono bg-rose-50 text-rose-700 border border-rose-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                                                    <span>{{ $item['score_chip'] ?? ('Dual · L'.$item['level'].' - '.round($item['risk_score']).'%') }}</span>
                                                </span>
                                            @elseif($item['level'] === 3)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold font-mono bg-rose-50 text-rose-700 border border-rose-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                    <span>{{ $item['score_chip'] ?? ('L3 - '.round($item['risk_score']).'%') }}</span>
                                                </span>
                                            @elseif($item['level'] === 2)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold font-mono bg-amber-50 text-amber-800 border border-amber-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    <span>{{ $item['score_chip'] ?? ('L2 - '.round($item['risk_score']).'%') }}</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold font-mono bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    <span>{{ $item['score_chip'] ?? ('L1 - '.round($item['risk_score']).'%') }}</span>
                                                </span>
                                            @endif
                                            <a href="{{ route('inventory.risk-engine', ['tab' => 'expiry']) }}"
                                                title="Inspect batch in Expiry Risk Table"
                                                class="w-8 h-8 rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-900 border border-amber-300 flex items-center justify-center transition shadow-xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="py-6 text-center text-gray-500 text-xs">
                                    <span class="text-emerald-600 font-bold text-base block mb-1">✓ Safe Horizons</span>
                                    No batches projected to spoil before consumption.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Two Columns: Stock Status & Recent Movements -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Stock Overview Panel -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-5">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Formulary Stock Snapshot
                        </h3>
                        <a href="{{ $canManageInventory ? route('inventory.index') : route('inventory.risk-engine') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                            View All &rarr;
                        </a>
                    </div>

                    <div class="mt-4 divide-y divide-gray-100">
                        @foreach ($inventory as $med)
                            @php
                                $stock = $med->available_stock;
                            @endphp
                            <div class="py-3 flex items-center justify-between text-xs">
                                <div class="min-w-0 pr-3">
                                    @if($canManageInventory)
                                        <a href="{{ route('inventory.medicines.show', $med) }}" class="font-mono font-bold text-xs text-gray-900 hover:text-emerald-700 truncate block">
                                            {{ $med->item_code }}
                                        </a>
                                    @else
                                        <span class="font-mono font-bold text-xs text-gray-900 truncate block">
                                            {{ $med->item_code }}
                                        </span>
                                    @endif
                                    <div class="text-[11px] text-gray-400 truncate">{{ $med->category }}</div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold {{ $stock > $med->reorder_level ? 'bg-emerald-50 text-emerald-700' : ($stock > 0 ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                                        {{ number_format($stock) }} {{ $med->unit }}
                                    </span>
                                    @if($canManageInventory)
                                        <a href="{{ route('inventory.medicines.receive', $med) }}" class="p-1 rounded text-emerald-600 hover:bg-emerald-50 text-[11px] font-semibold" title="Receive shipment">
                                            + Receive
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Movement Audit -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-5">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            Recent Stock Activities
                        </h3>
                        <a href="{{ route('inventory.movements') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                            Full Log &rarr;
                        </a>
                    </div>

                    <div class="mt-4 divide-y divide-gray-100 text-xs">
                        @forelse ($recentMovements as $m)
                            <div class="py-3 flex items-center justify-between">
                                <div class="min-w-0 pr-3">
                                    <div class="font-bold text-gray-900 truncate">{{ $m->medicine->name }}</div>
                                    <div class="text-[11px] text-gray-400">
                                        Batch {{ $m->batch?->batch_no ?? 'N/A' }} • {{ $m->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    @if ($m->type === 'in')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            +{{ $m->quantity }} IN
                                        </span>
                                    @elseif ($m->type === 'out')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">
                                            -{{ $m->quantity }} OUT
                                        </span>
                                    @elseif ($m->type === 'disposal')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">
                                            -{{ $m->quantity }} DISPOSAL
                                        </span>
                                    @elseif ($m->type === 'adjustment')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                            ADJUST {{ $m->quantity }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="py-6 text-center text-gray-400">
                                No recent movements logged.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
</x-app-layout>
@else
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
@endhasrole

