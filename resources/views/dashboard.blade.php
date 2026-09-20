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
@elseif(auth()->user()->hasAnyRole(['stock_manager', 'admin']))
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-base shadow-sm">
                        📦
                    </span>
                    {{ auth()->user()->hasRole('admin') ? __('Hospital Administrator Dashboard') : __('Inventory & Stock Management Dashboard') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">Welcome back, {{ Auth::user()->name }} ({{ strtoupper(Auth::user()->roles->pluck('name')->first() ?? 'Staff') }}). Real-time stock status, batch allocations, and supply logs.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('inventory.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    <span>Full Inventory Catalog</span>
                </a>
                <a href="{{ route('inventory.movements') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50 shadow-xs transition">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span>Delivery Logs</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- 5 KPI Overview Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Total Formulations -->
                <a href="{{ route('inventory.index') }}" class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs hover:border-gray-300 transition">
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
                <a href="{{ route('inventory.index', ['stock_status' => 'in_stock']) }}" class="bg-white p-4 rounded-xl border border-emerald-200/80 shadow-xs hover:border-emerald-400 transition">
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
                <a href="{{ route('inventory.index', ['stock_status' => 'low_stock']) }}" class="bg-white p-4 rounded-xl border border-amber-200/80 shadow-xs hover:border-amber-400 transition">
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
                <a href="{{ route('inventory.index', ['stock_status' => 'out_of_stock']) }}" class="bg-white p-4 rounded-xl border border-rose-200/80 shadow-xs hover:border-rose-400 transition">
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

            <!-- Two Columns: Stock Status & Recent Movements -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Stock Overview Panel -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-5">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Formulary Stock Snapshot
                        </h3>
                        <a href="{{ route('inventory.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
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
                                    <a href="{{ route('inventory.medicines.show', $med) }}" class="font-bold text-gray-900 hover:text-emerald-700 truncate block">
                                        {{ $med->name }}
                                    </a>
                                    <div class="text-[11px] text-gray-400 truncate">{{ $med->generic_name }} • {{ $med->category }}</div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold {{ $stock > $med->reorder_level ? 'bg-emerald-50 text-emerald-700' : ($stock > 0 ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                                        {{ number_format($stock) }} {{ $med->unit }}
                                    </span>
                                    <a href="{{ route('inventory.medicines.receive', $med) }}" class="p-1 rounded text-emerald-600 hover:bg-emerald-50 text-[11px] font-semibold" title="Receive shipment">
                                        + Receive
                                    </a>
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

