<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-lg bg-emerald-700 text-white flex items-center justify-center text-sm shadow-xs shrink-0">
                ⚡
            </span>
            <div class="min-w-0">
                <h1 class="text-base sm:text-lg font-bold text-slate-800 leading-tight truncate">
                    {{ __('Risk Forecasting & Demand Intelligence') }}
                </h1>
                <p class="text-[11px] text-slate-500 hidden sm:block truncate mt-0.5">Dual-engine stockout horizons and batch expiration telemetry.</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4"
         x-data="{ 
             activeTab: '{{ $activeTab ?? 'overview' }}',
             setTab(tab) {
                 this.activeTab = tab;
                 const url = new URL(window.location);
                 url.searchParams.set('tab', tab);
                 window.history.replaceState({}, '', url);
             }
         }">

        @if (session('success'))
            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(auth()->user()->hasRole('pharmacist') && !auth()->user()->hasAnyRole(['stock_manager', 'admin']))
            <div class="p-3.5 bg-sky-50 border border-sky-200 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-sky-950 shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-lg bg-sky-600 text-white flex items-center justify-center font-bold text-sm shrink-0">
                        ⚡
                    </span>
                    <div>
                        <span class="font-bold text-sky-950">Dispensary Clinical Telemetry &amp; Alert Mode:</span>
                        <span class="text-sky-800">Real-time visibility into algorithmic stockout hazards and expiring batches for dispensing safety. Reorder procurement is managed by Inventory Custodians.</span>
                    </div>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider bg-sky-100 text-sky-800 border border-sky-300/80 px-2 py-0.5 rounded-md shrink-0 self-start sm:self-auto">
                    Read-Only Telemetry
                </span>
            </div>
        @endif

        <!-- ── TAB CONTROLS & LEAD TIME SELECTOR ───────────────────────── -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 bg-white p-2 rounded-xl border border-slate-200 shadow-xs">
            <div class="flex items-center gap-1.5 flex-wrap">
                <!-- Tab 1: Overview -->
                <button type="button"
                        @click="setTab('overview')"
                        :class="activeTab === 'overview' ? 'bg-[#064e2b] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium'"
                        class="px-3.5 py-1.5 rounded-lg text-xs transition-colors cursor-pointer">
                    Overview
                </button>

                <!-- Tab 2: Stockout Risk -->
                <button type="button"
                        @click="setTab('stockout')"
                        :class="activeTab === 'stockout' ? 'bg-[#064e2b] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium'"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs transition-colors cursor-pointer">
                    <span>Stockout Risk</span>
                    @if(count($insights['stockout_insights']) > 0)
                        <span :class="activeTab === 'stockout' ? 'bg-amber-400 text-green-950' : 'bg-rose-100 text-rose-700'"
                              class="px-1.5 py-0.2 rounded-full text-[10px] font-black">
                            {{ count($insights['stockout_insights']) }}
                        </span>
                    @endif
                </button>

                <!-- Tab 3: Expiry Risk -->
                <button type="button"
                        @click="setTab('expiry')"
                        :class="activeTab === 'expiry' ? 'bg-[#064e2b] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium'"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs transition-colors cursor-pointer">
                    <span>Expiry Risk</span>
                    @if(count($insights['expiry_insights']) > 0)
                        <span :class="activeTab === 'expiry' ? 'bg-amber-400 text-green-950' : 'bg-purple-100 text-purple-700'"
                              class="px-1.5 py-0.2 rounded-full text-[10px] font-black">
                            {{ count($insights['expiry_insights']) }}
                        </span>
                    @endif
                </button>
            </div>

            <!-- Controls Right: Lead Time Selector & Recalculate -->
            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('inventory.risk-engine') }}" class="flex items-center gap-2 px-1">
                    <input type="hidden" name="tab" :value="activeTab">
                    <label for="lead_time" class="text-xs text-slate-500 font-medium">
                        Lead time:
                    </label>
                    <select name="lead_time" id="lead_time" onchange="this.form.submit()"
                            class="py-1 px-2.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-emerald-600 cursor-pointer">
                        <option value="3" {{ $leadTime === 3 ? 'selected' : '' }}>3 Days</option>
                        <option value="7" {{ $leadTime === 7 ? 'selected' : '' }}>7 Days</option>
                        <option value="14" {{ $leadTime === 14 ? 'selected' : '' }}>14 Days</option>
                        <option value="30" {{ $leadTime === 30 ? 'selected' : '' }}>30 Days</option>
                    </select>
                </form>

                @hasanyrole('stock_manager|admin')
                    <form method="POST" action="{{ route('inventory.risk-engine.recalculate') }}">
                        @csrf
                        <input type="hidden" name="lead_time" value="{{ $leadTime }}">
                        <button type="submit"
                                title="Recalculate dual-engine metrics"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#064e2b] hover:bg-[#053e22] text-white text-xs font-semibold shadow-xs transition cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span>Recalculate</span>
                        </button>
                    </form>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-medium border border-slate-200" title="Pharmacist read-only telemetry view">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span>Telemetry Mode</span>
                    </span>
                @endhasanyrole
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════════ -->
        <!-- VIEW 1: EXECUTIVE OVERVIEW TAB                                     -->
        <!-- ══════════════════════════════════════════════════════════════════ -->
        <div x-show="activeTab === 'overview'" x-transition class="space-y-4">

            <!-- ── ELEVATED PREDICTIVE INTELLIGENCE FLASH WIDGET ─────────────────────── -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                <!-- Dynamic Health Banner -->
                <div class="px-5 py-4 bg-gradient-to-r from-slate-50 via-white to-slate-50 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                            ⚡
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-sm font-bold text-slate-900 tracking-tight">Algorithmic Predictive Intelligence</h2>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $insights['global_risk']['badge_class'] ?? 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $insights['global_risk']['dot_class'] ?? 'bg-emerald-500' }}"></span>
                                    <span>{{ $insights['global_risk']['label'] ?? 'Stable' }} Status</span>
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5">Continuous evaluation across {{ $insights['telemetry']['analyzed_medicines_count'] }} medicines and {{ $insights['telemetry']['analyzed_batches_count'] }} active batches (Lead Time: {{ $leadTime }}d).</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs text-slate-400 font-medium hidden sm:inline">Quick Jump:</span>
                        <button type="button" @click="setTab('stockout')"
                                class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200 transition cursor-pointer">
                            {{ $insights['telemetry']['high_stockout_count'] }} Stockout Threats
                        </button>
                        <button type="button" @click="setTab('expiry')"
                                class="px-2.5 py-1 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs font-bold border border-purple-200 transition cursor-pointer">
                            {{ $insights['telemetry']['high_expiry_count'] }} Expiring Batches
                        </button>
                    </div>
                </div>

                <!-- 3 Elevated Metric Callouts (Glanceable HCI Telemetry) -->
                <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-100 p-2 sm:p-3">
                    <!-- Metric 1: Stockouts -->
                    <div class="p-4 rounded-xl hover:bg-slate-50/70 transition cursor-pointer" @click="setTab('stockout')">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-500">Urgent Stockout Horizon</span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold font-mono {{ $insights['telemetry']['high_stockout_count'] > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $insights['telemetry']['high_stockout_count'] > 0 ? 'bg-rose-500 animate-pulse' : 'bg-emerald-500' }}"></span>
                                {{ $insights['telemetry']['high_stockout_count'] > 0 ? 'Critical Deficit' : 'Safe Horizon' }}
                            </span>
                        </div>
                        <div class="mt-3 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold font-mono text-slate-900">{{ $insights['telemetry']['high_stockout_count'] }}</span>
                            <span class="text-xs text-slate-500 font-medium">{{ Str::plural('formulation', $insights['telemetry']['high_stockout_count']) }}</span>
                        </div>
                    </div>

                    <!-- Metric 2: Expiring Batches -->
                    <div class="p-4 rounded-xl hover:bg-slate-50/70 transition cursor-pointer" @click="setTab('expiry')">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-500">Imminent Expiry Exposure</span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold font-mono {{ $insights['telemetry']['high_expiry_count'] > 0 ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $insights['telemetry']['high_expiry_count'] > 0 ? 'bg-amber-500 animate-pulse' : 'bg-emerald-500' }}"></span>
                                {{ $insights['telemetry']['high_expiry_count'] > 0 ? 'FEFO Priority' : 'Optimal Expiry' }}
                            </span>
                        </div>
                        <div class="mt-3 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold font-mono text-slate-900">{{ $insights['telemetry']['high_expiry_count'] }}</span>
                            <span class="text-xs text-slate-500 font-medium">{{ Str::plural('batch', $insights['telemetry']['high_expiry_count']) }}</span>
                        </div>
                    </div>

                    <!-- Metric 3: Value at Risk -->
                    <div class="p-4 rounded-xl">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-500">Estimated Value at Risk</span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold font-mono bg-purple-50 text-purple-700 border border-purple-200">
                                Financial Exposure
                            </span>
                        </div>
                        <div class="mt-3 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold font-mono text-slate-900">₱{{ number_format($insights['telemetry']['total_financial_loss_at_risk'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Split: Top Items (Analytical Previews) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Preview 1: Stockout Risks -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden flex flex-col justify-between">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-xs text-slate-800">Immediate Stockout Vulnerabilities</h3>
                            <p class="text-[11px] text-slate-400">Current inventory vs. reorder safety buffer &amp; consumption run-rate</p>
                        </div>
                        <button type="button" @click="setTab('stockout')"
                                class="text-xs font-semibold text-emerald-800 hover:underline">
                            View full list &rarr;
                        </button>
                    </div>

                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-500 text-[10px] font-bold uppercase border-b border-slate-100">
                                <tr>
                                    <th class="px-4 py-2.5">Product Code</th>
                                    <th class="px-3 py-2.5 text-right">Stock / Buffer</th>
                                    <th class="px-3 py-2.5 text-right">ADC</th>
                                    <th class="px-3 py-2.5 text-center">DoSR</th>
                                    <th class="px-3 py-2.5 text-center">Stockout Risk Score</th>
                                    <th class="px-3 py-2.5 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse(array_slice($insights['stockout_insights'], 0, 5) as $item)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-4 py-3">
                                            <span class="font-mono font-bold text-slate-900 text-xs">{{ $item['code'] }}</span>
                                            <div class="text-[10px] text-slate-400">{{ $item['category'] }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-right font-mono text-xs">
                                            <span class="font-bold {{ $item['current_stock'] === 0 ? 'text-rose-600' : 'text-slate-900' }}">
                                                {{ number_format($item['current_stock']) }}
                                            </span>
                                            <span class="text-slate-400">/</span>
                                            <span class="text-slate-500 text-[11px]">{{ number_format($item['buffer_stock']) }}</span>
                                        </td>
                                        <td class="px-3 py-3 text-right font-mono text-slate-600 text-xs">
                                            {{ number_format($item['daily_consumption'], 1) }}/d
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <span class="font-mono text-xs font-semibold {{ $item['days_until_depleted'] !== null && $item['days_until_depleted'] <= $leadTime ? 'text-rose-600 font-bold' : 'text-slate-700' }}">
                                                {{ $item['dosr_label'] }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-center whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-mono {{ $item['badge_class'] }}">
                                                {{ $item['score_chip'] }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-right">
                                            <a href="{{ route('inventory.deliveries.create', ['medicine_id' => $item['medicine_id']]) }}"
                                               title="Create reorder request for {{ $item['code'] }}"
                                               aria-label="Create reorder request for {{ $item['code'] }}"
                                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 text-slate-500 hover:text-emerald-700 transition shadow-2xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-6 text-center text-slate-400 text-xs">
                                            All product codes have adequate stock.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Preview 2: Expiry Risks -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden flex flex-col justify-between">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-xs text-slate-800">Imminent Batch Expiry Vulnerabilities</h3>
                            <p class="text-[11px] text-slate-400">Shelf-life horizon vs. projected consumption &amp; potential waste units</p>
                        </div>
                        <button type="button" @click="setTab('expiry')"
                                class="text-xs font-semibold text-emerald-800 hover:underline">
                            View full list &rarr;
                        </button>
                    </div>

                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-500 text-[10px] font-bold uppercase border-b border-slate-100">
                                <tr>
                                    <th class="px-4 py-2.5">Batch / Code</th>
                                    <th class="px-3 py-2.5 text-center">Shelf Life</th>
                                    <th class="px-3 py-2.5 text-right">Proj. Cons.</th>
                                    <th class="px-3 py-2.5 text-right">At Risk</th>
                                    <th class="px-3 py-2.5 text-center">Expiry Risk Score</th>
                                    <th class="px-3 py-2.5 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse(array_slice($insights['expiry_insights'], 0, 5) as $item)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="font-mono font-bold text-slate-900 text-xs">#{{ $item['batch_no'] }}</div>
                                            <div class="font-mono text-[10px] text-emerald-800 font-semibold">{{ $item['code'] }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <div class="text-[11px] font-mono font-semibold {{ $item['days_until_expiry'] <= 0 ? 'text-rose-600 font-bold' : ($item['days_until_expiry'] <= 30 ? 'text-amber-700' : 'text-slate-700') }}">
                                                {{ $item['shelf_life_label'] }}
                                            </div>
                                            <div class="text-[9px] text-slate-400">{{ $item['expiry_date'] }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-right font-mono text-xs text-slate-600">
                                            {{ number_format($item['projected_consumption']) }}
                                        </td>
                                        <td class="px-3 py-3 text-right font-mono text-xs">
                                            <span class="font-bold text-rose-600">
                                                {{ number_format($item['projected_loss_units']) }}
                                            </span>
                                            <div class="text-[9px] text-slate-400">₱{{ number_format($item['financial_loss'], 2) }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-center whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-mono {{ $item['badge_class'] }}">
                                                {{ $item['score_chip'] }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-right">
                                            @hasanyrole('stock_manager|admin')
                                                <a href="{{ route('inventory.medicines.show', $item['medicine_id']) }}#batches-table"
                                                   title="Inspect batch #{{ $item['batch_no'] }}"
                                                   aria-label="Inspect batch #{{ $item['batch_no'] }}"
                                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 hover:border-purple-300 hover:bg-purple-50 text-slate-500 hover:text-purple-700 transition shadow-2xs">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold text-purple-700 bg-purple-50 border border-purple-200" title="Pharmacist alert: Prioritize dispensing this batch">
                                                    FEFO
                                                </span>
                                            @endhasanyrole
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-6 text-center text-slate-400 text-xs">
                                            No batch expiration risks detected.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- ══════════════════════════════════════════════════════════════════ -->
        <!-- VIEW 2: STOCKOUT RISK TABLE (BY PRODUCT CODE)                      -->
        <!-- ══════════════════════════════════════════════════════════════════ -->
        <div x-show="activeTab === 'stockout'" x-transition class="space-y-3"
             x-data="{ stockSearch: '' }">

            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                <!-- Search & Status Bar -->
                <div class="p-3.5 bg-slate-50/70 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                    <div class="relative flex-1 max-w-xs">
                        <div class="absolute left-3 top-2.5 text-slate-400 pointer-events-none">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text"
                               x-model="stockSearch"
                               placeholder="Search product code..."
                               class="w-full pl-8 pr-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-mono text-slate-800 focus:outline-none focus:ring-1 focus:ring-emerald-600">
                    </div>

                    <div class="flex items-center gap-2 text-xs text-slate-600">
                        <span>Lead time: <strong>{{ $leadTime }} days</strong></span>
                        <span>&bull;</span>
                        <span class="font-bold text-rose-700">{{ count($insights['stockout_insights']) }} items at risk</span>
                    </div>
                </div>

                <!-- Clean, Roomy Data Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs divide-y divide-slate-200">
                        <thead class="bg-slate-50 text-slate-600 font-bold uppercase text-[10px] tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5">Product Code</th>
                                <th class="px-4 py-3.5 text-right">Current Stock</th>
                                <th class="px-4 py-3.5 text-right">Buffer / Reorder Point</th>
                                <th class="px-4 py-3.5 text-right">ADC (Run Rate)</th>
                                <th class="px-4 py-3.5 text-center">DoSR (Days Left)</th>
                                <th class="px-4 py-3.5 text-center">Stockout Risk Score</th>
                                <th class="px-5 py-3.5">Analytical Status / Notes</th>
                                <th class="px-4 py-3.5 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($insights['stockout_insights'] as $item)
                                <tr class="hover:bg-slate-50/80 transition-colors"
                                    x-show="!stockSearch || '{{ strtolower($item['code'].' '.$item['category']) }}'.includes(stockSearch.toLowerCase())">
                                    
                                    <!-- Product Code (NO Product Name) -->
                                    <td class="px-5 py-4">
                                        <div class="font-mono font-bold text-slate-900 text-sm tracking-wide">
                                            {{ $item['code'] }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">
                                            {{ $item['category'] }}
                                        </div>
                                    </td>

                                    <!-- Current Stock -->
                                    <td class="px-4 py-4 text-right">
                                        <div class="font-mono font-bold text-sm {{ $item['current_stock'] === 0 ? 'text-rose-600' : 'text-slate-900' }}">
                                            {{ number_format($item['current_stock']) }}
                                        </div>
                                        <span class="text-[10px] text-slate-400">
                                            {{ $item['unit'] }}
                                        </span>
                                    </td>

                                    <!-- Safety Buffer vs Reorder Point -->
                                    <td class="px-4 py-4 text-right">
                                        <div class="font-mono text-slate-900 text-xs font-semibold">
                                            Buffer: {{ number_format($item['buffer_stock']) }}
                                        </div>
                                        <div class="text-[10px] text-slate-500 font-mono">
                                            Target: {{ number_format($item['reorder_point'], 1) }}
                                        </div>
                                    </td>

                                    <!-- ADC (Run Rate) -->
                                    <td class="px-4 py-4 text-right">
                                        <div class="font-mono text-slate-700 text-xs sm:text-sm font-medium">
                                            {{ number_format($item['daily_consumption'], 2) }}
                                        </div>
                                        <span class="text-[10px] text-slate-400">
                                            units/day
                                        </span>
                                    </td>

                                    <!-- DoSR (Days Left) -->
                                    <td class="px-4 py-4 text-center">
                                        @php
                                            $days = $item['days_until_depleted'];
                                            $badgeClass = $days !== null && $days <= $leadTime 
                                                ? 'bg-rose-50 text-rose-700 font-bold border border-rose-200' 
                                                : ($days !== null && $days <= ($leadTime * 2) 
                                                    ? 'bg-amber-50 text-amber-700 font-semibold border border-amber-200' 
                                                    : 'bg-emerald-50 text-emerald-700 font-medium border border-emerald-200');
                                        @endphp
                                        <span class="inline-block px-2.5 py-1 rounded-md text-xs font-mono {{ $badgeClass }}">
                                            {{ $item['dosr_label'] }}
                                        </span>
                                    </td>

                                    <!-- Risk Score Chip -->
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono {{ $item['badge_class'] }}">
                                            {{ $item['score_chip'] }}
                                        </span>
                                    </td>

                                    <!-- Natural Human Notes -->
                                    <td class="px-5 py-4">
                                        <span class="text-xs text-slate-700 font-medium leading-snug">
                                            {{ $item['narrative'] }}
                                        </span>
                                    </td>

                                    <!-- Compact 32x32px Action Icon -->
                                    <td class="px-4 py-4 text-right whitespace-nowrap">
                                        @can('create', App\Models\Delivery::class)
                                            <a href="{{ route('inventory.deliveries.create', ['medicine_id' => $item['medicine_id']]) }}"
                                               title="Create reorder request for {{ $item['code'] }}"
                                               aria-label="Create reorder request for {{ $item['code'] }}"
                                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 text-slate-500 hover:text-emerald-700 transition shadow-2xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            </a>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold text-rose-700 bg-rose-50 border border-rose-200" title="Dispensary stockout hazard warning">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                                                Deficit Alert
                                            </span>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-5 py-10 text-center text-slate-400 text-xs">
                                        All product codes have sufficient stock levels.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- ══════════════════════════════════════════════════════════════════ -->
        <!-- VIEW 3: BATCH EXPIRY RISK TABLE (BY PRODUCT CODE)                  -->
        <!-- ══════════════════════════════════════════════════════════════════ -->
        <div x-show="activeTab === 'expiry'" x-transition class="space-y-3"
             x-data="{ expirySearch: '' }">

            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                <!-- Search & Status Bar -->
                <div class="p-3.5 bg-slate-50/70 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                    <div class="relative flex-1 max-w-xs">
                        <div class="absolute left-3 top-2.5 text-slate-400 pointer-events-none">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text"
                               x-model="expirySearch"
                               placeholder="Search batch or product code..."
                               class="w-full pl-8 pr-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-mono text-slate-800 focus:outline-none focus:ring-1 focus:ring-emerald-600">
                    </div>

                    <div class="flex items-center gap-2 text-xs text-slate-600">
                        <span>Total at risk: <strong class="text-rose-700 font-mono">₱{{ number_format($insights['telemetry']['total_financial_loss_at_risk'], 2) }}</strong></span>
                        <span>&bull;</span>
                        <span class="font-bold text-purple-700">{{ count($insights['expiry_insights']) }} batches flagged</span>
                    </div>
                </div>

                <!-- Clean, Roomy Data Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs divide-y divide-slate-200">
                        <thead class="bg-slate-50 text-slate-600 font-bold uppercase text-[10px] tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5">Batch &amp; Product Code</th>
                                <th class="px-4 py-3.5 text-center">Shelf Life Remaining</th>
                                <th class="px-4 py-3.5 text-right">Batch Stock</th>
                                <th class="px-4 py-3.5 text-right">Proj. Consumption</th>
                                <th class="px-4 py-3.5 text-right">At-Risk Units</th>
                                <th class="px-4 py-3.5 text-right">Value at Risk</th>
                                <th class="px-4 py-3.5 text-center">Expiry Risk Score</th>
                                <th class="px-5 py-3.5">Analytical Status / Notes</th>
                                <th class="px-4 py-3.5 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($insights['expiry_insights'] as $item)
                                <tr class="hover:bg-slate-50/80 transition-colors"
                                    x-show="!expirySearch || '{{ strtolower($item['batch_no'].' '.$item['code'].' '.$item['supplier']) }}'.includes(expirySearch.toLowerCase())">
                                    
                                    <!-- Batch & Product Code (NO Product Name) -->
                                    <td class="px-5 py-4">
                                        <div class="font-mono font-bold text-slate-900 text-sm">
                                            #{{ $item['batch_no'] }}
                                        </div>
                                        <div class="font-mono text-xs text-emerald-800 font-semibold mt-0.5">
                                            {{ $item['code'] }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">
                                            {{ $item['supplier'] }}
                                        </div>
                                    </td>

                                    <!-- Expiry Date & Shelf-Life Remaining -->
                                    <td class="px-4 py-4 text-center">
                                        <div class="font-mono text-xs font-semibold {{ $item['days_until_expiry'] <= 0 ? 'text-rose-700 font-bold' : ($item['days_until_expiry'] <= 30 ? 'text-amber-700' : 'text-slate-800') }}">
                                            {{ $item['shelf_life_label'] }}
                                        </div>
                                        <div class="text-[10px] font-mono text-slate-400 mt-0.5">
                                            {{ $item['expiry_date'] }}
                                        </div>
                                    </td>

                                    <!-- Batch Remaining Units (Ic) -->
                                    <td class="px-4 py-4 text-right">
                                        <div class="font-mono font-bold text-slate-900 text-sm">
                                            {{ number_format($item['remaining_stock']) }}
                                        </div>
                                        <span class="text-[10px] text-slate-400">
                                            {{ $item['unit'] }}
                                        </span>
                                    </td>

                                    <!-- Projected Consumption (Dc * Te) -->
                                    <td class="px-4 py-4 text-right">
                                        <div class="font-mono text-slate-700 text-xs sm:text-sm font-medium">
                                            {{ number_format($item['projected_consumption']) }}
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-mono">
                                            ADC: {{ number_format($item['daily_consumption'], 1) }}/d
                                        </span>
                                    </td>

                                    <!-- At-Risk / Unconsumed Units (Ic - Projected) -->
                                    <td class="px-4 py-4 text-right">
                                        <div class="font-mono font-bold text-sm {{ $item['projected_loss_units'] > 0 ? 'text-rose-600' : 'text-slate-800' }}">
                                            {{ number_format($item['projected_loss_units']) }}
                                        </div>
                                        <span class="text-[10px] text-slate-400">
                                            {{ $item['unit'] }} unconsumed
                                        </span>
                                    </td>

                                    <!-- Financial Value at Risk -->
                                    <td class="px-4 py-4 text-right">
                                        <div class="font-mono font-bold text-rose-700 text-sm">
                                            ₱{{ number_format($item['financial_loss'], 2) }}
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-mono">
                                            @ ₱{{ number_format($item['unit_cost'], 2) }}/unit
                                        </span>
                                    </td>

                                    <!-- Risk Score Chip (Er) -->
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono {{ $item['badge_class'] }}">
                                            {{ $item['score_chip'] }}
                                        </span>
                                    </td>

                                    <!-- Analytical Status / Notes -->
                                    <td class="px-5 py-4">
                                        <span class="text-xs text-slate-700 font-medium leading-snug">
                                            {{ $item['narrative'] }}
                                        </span>
                                    </td>

                                    <!-- Compact 32x32px Action Icon -->
                                    <td class="px-4 py-4 text-right whitespace-nowrap">
                                        @hasanyrole('stock_manager|admin')
                                            <a href="{{ route('inventory.medicines.show', $item['medicine_id']) }}#batches-table"
                                               title="Inspect batch #{{ $item['batch_no'] }}"
                                               aria-label="Inspect batch #{{ $item['batch_no'] }}"
                                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 hover:border-purple-300 hover:bg-purple-50 text-slate-500 hover:text-purple-700 transition shadow-2xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold text-purple-700 bg-purple-50 border border-purple-200" title="Expiring batch alert: Triage for priority dispensing">
                                                <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span>
                                                FEFO Triage
                                            </span>
                                        @endhasanyrole
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-5 py-10 text-center text-slate-400 text-xs">
                                        No batch expiration risks detected.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
