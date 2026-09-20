<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Predictive Analytics
                    </span>
                    <span class="text-xs font-medium text-gray-400">·</span>
                    <span class="text-xs font-medium text-gray-500">Pharmacy Supply Chain</span>
                </div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-900 leading-tight mt-1">
                    Inventory Risk &amp; Demand Forecasting
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Continuous monitoring of stockout timelines and batch expiration exposure based on dispensing velocity.
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('inventory.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-xs font-bold transition shadow-xs">
                    &larr; Back to Inventory
                </a>

                <form method="POST" action="{{ route('inventory.risk-engine.recalculate') }}">
                    @csrf
                    <input type="hidden" name="lead_time" value="{{ $leadTime }}">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-sm transition active:scale-95 cursor-pointer">
                        <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>Recalculate Risk Scores</span>
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-bold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- ── EXECUTIVE CLINICAL KPI STRIP ─────────────────────────────────── -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- KPI 1: Critical Stockout Vulnerability -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Critical Stockouts</span>
                        <span class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </span>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl sm:text-3xl font-black text-gray-900 font-mono">
                            {{ $insights['telemetry']['high_stockout_count'] }}
                            <span class="text-xs font-semibold text-gray-500 font-sans">{{ Str::plural('medicine', $insights['telemetry']['high_stockout_count']) }}</span>
                        </div>
                        <p class="text-xs text-rose-600 font-medium mt-1">
                            Depletion projected within 7-day reorder lead time
                        </p>
                    </div>
                </div>

                <!-- KPI 2: Batches Near Expiry -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Batches Near Expiry</span>
                        <span class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl sm:text-3xl font-black text-gray-900 font-mono">
                            {{ $insights['telemetry']['high_expiry_count'] }}
                            <span class="text-xs font-semibold text-gray-500 font-sans">{{ Str::plural('batch', $insights['telemetry']['high_expiry_count']) }}</span>
                        </div>
                        <p class="text-xs text-purple-700 font-medium mt-1">
                            Projected to expire before full dispensing
                        </p>
                    </div>
                </div>

                <!-- KPI 3: Total Spoilage Exposure -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Value at Expiry Risk</span>
                        <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">
                            ₱
                        </span>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl sm:text-3xl font-black text-gray-900 font-mono">
                            ₱{{ number_format($insights['telemetry']['total_financial_loss_at_risk'], 2) }}
                        </div>
                        <p class="text-xs text-amber-700 font-medium mt-1">
                            Estimated financial loss from expiring stock
                        </p>
                    </div>
                </div>

                <!-- KPI 4: Monitored Formulary Coverage -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Active Formulary</span>
                        <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </span>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl sm:text-3xl font-black text-gray-900 font-mono">
                            {{ $insights['telemetry']['analyzed_medicines_count'] }}
                            <span class="text-xs font-semibold text-gray-500 font-sans">meds · {{ $insights['telemetry']['analyzed_batches_count'] }} batches</span>
                        </div>
                        <p class="text-xs text-emerald-700 font-medium mt-1">
                            Analyzed via {{ $insights['telemetry']['velocity_window_days'] }}-day dispensing velocity
                        </p>
                    </div>
                </div>
            </div>

            <!-- ── DUAL PERSPECTIVE ANALYSIS GRID (SIDE-BY-SIDE) ──────────────────── -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

                <!-- ══════════════════════════════════════════════════════════════════ -->
                <!-- LEFT PERSPECTIVE: STOCKOUT VULNERABILITY FORECASTING              -->
                <!-- ══════════════════════════════════════════════════════════════════ -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
                    <div class="p-5 bg-gradient-to-r from-red-50/70 to-amber-50/50 border-b border-gray-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-red-600 text-white flex items-center justify-center font-black text-base shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-gray-900 tracking-wide">
                                    Stockout Vulnerability Forecast
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Medicines projected to deplete before restock delivery arrives
                                </p>
                            </div>
                        </div>

                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                            {{ count($insights['stockout_insights']) }} Flagged
                        </span>
                    </div>

                    <!-- Evaluation Standard Bar -->
                    <div class="px-5 py-2.5 bg-gray-50 border-b border-gray-100 text-xs text-gray-600 flex items-center justify-between flex-wrap gap-2">
                        <span>Criterion: On-Hand Stock vs. 7-Day Lead Time &amp; Safety Buffer</span>
                        <span class="font-bold text-gray-700">Triage: &gt;70% Critical Runout</span>
                    </div>

                    <!-- Stockout Insights Feed -->
                    <div class="p-5 space-y-4 max-h-[750px] overflow-y-auto">
                        @forelse ($insights['stockout_insights'] as $item)
                            <div class="rounded-xl border {{ $item['risk_category'] === 'high' ? 'border-rose-300 bg-rose-50/20' : 'border-amber-300 bg-amber-50/20' }} p-4 shadow-2xs transition hover:shadow-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="font-black text-base text-gray-900">{{ trim(str_replace('(Out of Stock Demo)', '', $item['medicine_name'])) }}</h4>
                                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 border border-gray-200">
                                                {{ $item['category'] }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 mt-1 flex-wrap text-xs">
                                            <span class="font-mono font-bold text-gray-700 bg-gray-100 px-2 py-0.5 rounded border border-gray-300 text-[11px] tracking-wide">{{ $item['code'] }}</span>
                                            <span class="text-gray-300">•</span>
                                            <span class="text-gray-500 font-medium">{{ $item['generic_name'] }}</span>
                                        </div>
                                    </div>

                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black {{ $item['badge_class'] }}">
                                        <span class="font-mono">{{ number_format($item['score'], 1) }}%</span>
                                        <span>{{ $item['label'] }}</span>
                                    </span>
                                </div>

                                <!-- Clinical Finding Box -->
                                <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 text-xs leading-relaxed">
                                    <div class="text-[11px] font-bold text-slate-800 mb-1 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full {{ $item['risk_category'] === 'high' ? 'bg-rose-500' : 'bg-amber-500' }}"></span>
                                        <span>Clinical Observation</span>
                                    </div>
                                    <p class="text-slate-600">{{ $item['narrative'] }}</p>
                                </div>

                                <!-- Metrics Grid -->
                                <div class="grid grid-cols-3 gap-2 mt-3 text-[11px]">
                                    <div class="bg-white p-2 rounded-lg border border-gray-200">
                                        <div class="text-gray-400 text-[10px] font-bold uppercase">Current Stock</div>
                                        <div class="font-black text-gray-800">{{ number_format($item['current_stock']) }} {{ $item['unit'] }}</div>
                                    </div>
                                    <div class="bg-white p-2 rounded-lg border border-gray-200">
                                        <div class="text-gray-400 text-[10px] font-bold uppercase">Burn Velocity</div>
                                        <div class="font-black text-amber-700">{{ number_format($item['daily_consumption'], 1) }} /day</div>
                                    </div>
                                    <div class="bg-white p-2 rounded-lg border border-gray-200">
                                        <div class="text-gray-400 text-[10px] font-bold uppercase">Supply Horizon</div>
                                        <div class="font-black {{ $item['days_until_depleted'] !== null && $item['days_until_depleted'] < 7 ? 'text-rose-600 font-extrabold' : 'text-gray-800' }}">
                                            {{ $item['days_until_depleted'] !== null ? $item['days_until_depleted'].'d' : '0d' }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-3 pt-3 border-t border-gray-200/60 flex items-center justify-between">
                                    <span class="text-[11px] font-semibold text-gray-600">
                                        Action: <span class="font-bold text-gray-800">{{ $item['action'] }}</span>
                                    </span>

                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('inventory.deliveries.create', ['medicine_id' => $item['medicine_id']]) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-2xs transition">
                                            <span>+ Create Restock PO</span>
                                        </a>
                                        <a href="{{ route('inventory.medicines.show', $item['medicine_id']) }}"
                                           class="p-1 text-gray-400 hover:text-gray-700 transition" title="Inspect Formulary">
                                            &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center text-gray-400">
                                <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto text-xl font-bold mb-2">
                                    ✓
                                </div>
                                <div class="font-bold text-gray-700">No Stockout Vulnerabilities Detected</div>
                                <p class="text-xs text-gray-500 mt-0.5">All active formulations have adequate days of supply exceeding lead times.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- ══════════════════════════════════════════════════════════════════ -->
                <!-- RIGHT PERSPECTIVE: EXPIRY SPOILAGE FORECASTING                   -->
                <!-- ══════════════════════════════════════════════════════════════════ -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
                    <div class="p-5 bg-gradient-to-r from-purple-50/70 to-rose-50/50 border-b border-gray-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-purple-700 text-white flex items-center justify-center font-black text-base shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-gray-900 tracking-wide">
                                    Batch Expiration &amp; Spoilage Forecast
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Batches projected to reach expiration before full dispensing
                                </p>
                            </div>
                        </div>

                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                            {{ count($insights['expiry_insights']) }} Flagged
                        </span>
                    </div>

                    <!-- Evaluation Standard Bar -->
                    <div class="px-5 py-2.5 bg-gray-50 border-b border-gray-100 text-xs text-gray-600 flex items-center justify-between flex-wrap gap-2">
                        <span>Criterion: Expiration Date vs. Dispensing Velocity (FEFO Priority)</span>
                        <span class="font-bold text-gray-700">Triage: &gt;70% High Spoilage Risk</span>
                    </div>

                    <!-- Expiry Insights Feed -->
                    <div class="p-5 space-y-4 max-h-[750px] overflow-y-auto">
                        @forelse ($insights['expiry_insights'] as $item)
                            <div class="rounded-xl border {{ $item['risk_category'] === 'high' ? 'border-purple-300 bg-purple-50/20' : 'border-amber-300 bg-amber-50/20' }} p-4 shadow-2xs transition hover:shadow-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-mono font-bold text-gray-900 text-sm">Batch #{{ $item['batch_no'] }}</span>
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-purple-100 text-purple-800">
                                                Expires {{ $item['expiry_date'] }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-700 font-semibold mt-1 flex items-center gap-2 flex-wrap">
                                            <span>{{ trim(str_replace('(Out of Stock Demo)', '', $item['medicine_name'])) }}</span>
                                            @if (!empty($item['code']))
                                                <span class="font-mono text-[11px] font-bold text-gray-700 bg-gray-100 px-2 py-0.5 rounded border border-gray-300 tracking-wide">{{ $item['code'] }}</span>
                                            @endif
                                            <span class="text-gray-400 font-normal">· {{ $item['supplier'] }}</span>
                                        </div>
                                    </div>

                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black {{ $item['badge_class'] }}">
                                        <span class="font-mono">{{ number_format($item['score'], 1) }}%</span>
                                        <span>{{ $item['label'] }}</span>
                                    </span>
                                </div>

                                <!-- Clinical Finding Box -->
                                <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 text-xs leading-relaxed">
                                    <div class="text-[11px] font-bold text-slate-800 mb-1 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full {{ $item['risk_category'] === 'high' ? 'bg-purple-600' : 'bg-amber-500' }}"></span>
                                        <span>Batch Triage Assessment</span>
                                    </div>
                                    <p class="text-slate-600">{{ $item['narrative'] }}</p>
                                </div>

                                <!-- Spoilage Valuation Grid -->
                                <div class="grid grid-cols-3 gap-2 mt-3 text-[11px]">
                                    <div class="bg-white p-2 rounded-lg border border-gray-200">
                                        <div class="text-gray-400 text-[10px] font-bold uppercase">Remaining Stock</div>
                                        <div class="font-black text-gray-800">{{ number_format($item['remaining_stock']) }} {{ $item['unit'] }}</div>
                                    </div>
                                    <div class="bg-white p-2 rounded-lg border border-gray-200">
                                        <div class="text-gray-400 text-[10px] font-bold uppercase">Days to Expiry</div>
                                        <div class="font-black {{ $item['days_until_expiry'] <= 0 ? 'text-rose-600' : 'text-purple-700' }}">
                                            {{ $item['days_until_expiry'] }} days
                                        </div>
                                    </div>
                                    <div class="bg-white p-2 rounded-lg border border-gray-200">
                                        <div class="text-gray-400 text-[10px] font-bold uppercase">Projected Loss</div>
                                        <div class="font-black text-rose-600 font-mono">
                                            ₱{{ number_format($item['financial_loss'], 2) }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-3 pt-3 border-t border-gray-200/60 flex items-center justify-between">
                                    <span class="text-[11px] font-semibold text-gray-600">
                                        Action: <span class="font-bold text-gray-800">{{ $item['action'] }}</span>
                                    </span>

                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('inventory.medicines.show', $item['medicine_id']) }}#batches-table"
                                           class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs shadow-2xs transition">
                                            <span>Inspect Batch &rarr;</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center text-gray-400">
                                <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto text-xl font-bold mb-2">
                                    ✓
                                </div>
                                <div class="font-bold text-gray-700">No Imminent Expiry Spoilage Detected</div>
                                <p class="text-xs text-gray-500 mt-0.5">Active stock batches will be safely dispensed before reaching expiration dates under current velocities.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
