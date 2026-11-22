<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-lg bg-[#064e2b] text-white flex items-center justify-center text-sm shadow-xs shrink-0">
                ⚡
            </span>
            <div class="min-w-0">
                <h1 class="text-base sm:text-lg font-bold text-slate-800 leading-tight truncate">
                    {{ __('Risk Forecasting & Demand Intelligence') }}
                </h1>
                <p class="text-[11px] text-slate-500 hidden sm:block truncate mt-0.5">Dual-engine algorithmic stockout horizons and clinical batch expiration telemetry.</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4"
         x-data="{ 
             activeTab: '{{ $activeTab ?? 'overview' }}',
             leadTime: {{ $leadTime ?? 7 }},
             insights: @js($insights),
             loading: false,
             stockSearch: '',
             expirySearch: '',
             restockModalOpen: false,
             modalData: {
                 medicine_id: '',
                 medicine_name: '',
                 code: '',
                 category: '',
                 unit: 'units',
                 unit_short: 'units',
                 current_stock: 0,
                 reorder_level: 0,
                 daily_consumption: 0,
                 recommended_qty: 0,
                 supplier: 'Unilab Corp.',
                 delivery_date: '{{ now()->addDays($leadTime ?? 7)->toDateString() }}',
                 notes: ''
             },
             openRestockModal(item) {
                 const lead = this.leadTime || 7;
                 const deliveryDate = new Date();
                 deliveryDate.setDate(deliveryDate.getDate() + lead);
                 const dateStr = deliveryDate.toISOString().split('T')[0];

                 this.modalData = {
                     medicine_id: item.medicine_id,
                     medicine_name: item.medicine_name,
                     code: item.code,
                     category: item.category || '',
                     unit: item.unit || 'units',
                     unit_short: item.unit_short || item.unit || 'units',
                     current_stock: item.current_stock || 0,
                     reorder_level: item.reorder_level || item.buffer_stock || 0,
                     daily_consumption: item.daily_consumption || 0,
                     recommended_qty: item.recommended_reorder_qty || 10,
                     supplier: item.supplier || 'Unilab Corp.',
                     delivery_date: dateStr,
                     notes: `Algorithmic reorder recommendation (${lead}d What-If Lead Time) for ${item.medicine_name} [${item.code}]. Recommended: ${item.recommended_reorder_qty || 10} ${item.unit_short || item.unit || 'units'}.`
                 };
                 this.restockModalOpen = true;
             },
             closeRestockModal() {
                 this.restockModalOpen = false;
             },
             setTab(tab) {
                 this.activeTab = tab;
                 const url = new URL(window.location);
                 url.searchParams.set('tab', tab);
                 window.history.replaceState({}, '', url);
             },
             async changeLeadTime(days) {
                 this.leadTime = parseInt(days);
                 this.loading = true;
                 const url = new URL(window.location.origin + '{{ route('inventory.risk-engine') }}');
                 url.searchParams.set('lead_time', this.leadTime);
                 url.searchParams.set('tab', this.activeTab);
                 window.history.replaceState({}, '', url);

                 try {
                     const response = await fetch(url.toString(), {
                         headers: {
                             'Accept': 'application/json',
                             'X-Requested-With': 'XMLHttpRequest'
                         }
                     });
                     if (response.ok) {
                         const data = await response.json();
                         this.insights = data;
                     }
                 } catch (e) {
                     console.error('Failed to update what-if simulation data:', e);
                 } finally {
                     this.loading = false;
                 }
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
                    Telemetry Mode
                </span>
            </div>
        @endif

        <!-- ── TAB CONTROLS, WHAT-IF LEAD TIME SIMULATOR & HYBRID STATUS ── -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 bg-white p-2.5 rounded-2xl border border-slate-200/90 shadow-xs">
            <div class="flex items-center gap-1.5 flex-wrap">
                <!-- Tab 1: Overview -->
                <button type="button"
                        @click="setTab('overview')"
                        :class="activeTab === 'overview' ? 'bg-[#064e2b] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium'"
                        class="px-3.5 py-1.5 rounded-xl text-xs transition-colors cursor-pointer">
                    Overview
                </button>

                <!-- Tab 2: Stockout Risk -->
                <button type="button"
                        @click="setTab('stockout')"
                        :class="activeTab === 'stockout' ? 'bg-[#064e2b] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium'"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs transition-colors cursor-pointer">
                    <span>Stockout Risk</span>
                    <span :class="activeTab === 'stockout' ? 'bg-amber-400 text-green-950' : 'bg-rose-100 text-rose-700'"
                          class="px-1.5 py-0.2 rounded-full text-[10px] font-black"
                          x-text="(insights.stockout_insights || []).length">
                        {{ count($insights['stockout_insights']) }}
                    </span>
                </button>

                <!-- Tab 3: Expiry Risk -->
                <button type="button"
                        @click="setTab('expiry')"
                        :class="activeTab === 'expiry' ? 'bg-[#064e2b] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium'"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs transition-colors cursor-pointer">
                    <span>Expiry Risk</span>
                    <span :class="activeTab === 'expiry' ? 'bg-amber-400 text-green-950' : 'bg-purple-100 text-purple-700'"
                          class="px-1.5 py-0.2 rounded-full text-[10px] font-black"
                          x-text="(insights.expiry_insights || []).length">
                        {{ count($insights['expiry_insights']) }}
                    </span>
                </button>
            </div>

            <!-- Controls Right: What-If Lead Time Simulator, Hybrid Status & Recalculate -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <!-- What-If Lead Time Simulator -->
                <div class="flex items-center gap-1.5 px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-xl">
                    <label for="lead_time" class="text-xs text-slate-700 font-semibold flex items-center gap-1 whitespace-nowrap">
                        <span class="text-amber-600">⚡</span>
                        <span>Lead Time (What-If):</span>
                    </label>
                    <select id="lead_time"
                            name="lead_time"
                            x-model="leadTime"
                            @change="changeLeadTime($event.target.value)"
                            class="py-1 pl-2 pr-6 bg-white border border-slate-300 rounded-lg text-xs font-bold text-[#064e2b] focus:outline-none focus:ring-1 focus:ring-emerald-600 cursor-pointer">
                        <option value="3" {{ $leadTime === 3 ? 'selected' : '' }}>3 Days</option>
                        <option value="7" {{ $leadTime === 7 ? 'selected' : '' }}>7 Days</option>
                        <option value="10" {{ $leadTime === 10 ? 'selected' : '' }}>10 Days</option>
                        <option value="14" {{ $leadTime === 14 ? 'selected' : '' }}>14 Days</option>
                        <option value="30" {{ $leadTime === 30 ? 'selected' : '' }}>30 Days</option>
                    </select>
                </div>

                <!-- Subtle Status Pill showing hybrid recalculation -->
                <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-800 text-[11px] font-medium border border-emerald-200/80 shadow-2xs"
                      title="Real-time stock movement listeners with automated daily End-of-Day audit sync">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Sync: Real-time (Stock Movement) • Daily EOD</span>
                </span>

                @hasanyrole('stock_manager|admin')
                    <form method="POST" action="{{ route('inventory.risk-engine.recalculate') }}">
                        @csrf
                        <input type="hidden" name="lead_time" :value="leadTime">
                        <button type="submit"
                                title="Recalculate dual-engine metrics"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#064e2b] hover:bg-[#053e22] text-white text-xs font-semibold shadow-xs transition cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span>Recalculate</span>
                        </button>
                    </form>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-medium border border-slate-200" title="Pharmacist read-only telemetry view">
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

            <!-- ── ELEVATED PREDICTIVE INTELLIGENCE FLASH WIDGET & DECISION BANNER ── -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                <!-- Dynamic Health Banner -->
                <div class="px-5 py-4 bg-gradient-to-r from-slate-50 via-white to-slate-50 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-[#064e2b] text-amber-300 flex items-center justify-center font-bold text-sm shadow-xs">
                            ⚡
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-sm font-bold text-slate-900 tracking-tight">Algorithmic Predictive Intelligence</h2>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold"
                                      :class="insights.global_risk ? insights.global_risk.badge_class : '{{ $insights['global_risk']['badge_class'] ?? 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}'">
                                    <span class="w-1.5 h-1.5 rounded-full"
                                          :class="insights.global_risk ? insights.global_risk.dot_class : '{{ $insights['global_risk']['dot_class'] ?? 'bg-emerald-500' }}'"></span>
                                    <span x-text="insights.global_risk ? insights.global_risk.label : '{{ $insights['global_risk']['label'] ?? 'Stable' }}'">
                                        {{ $insights['global_risk']['label'] ?? 'Stable' }} Status
                                    </span>
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Continuous evaluation across <span class="font-bold text-slate-700" x-text="insights.telemetry.analyzed_medicines_count">{{ $insights['telemetry']['analyzed_medicines_count'] }}</span> medicines and <span class="font-bold text-slate-700" x-text="insights.telemetry.analyzed_batches_count">{{ $insights['telemetry']['analyzed_batches_count'] }}</span> batches (What-If Lead Time: <span class="font-bold text-[#064e2b]" x-text="leadTime + 'd'">{{ $leadTime }}d</span>).
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs text-slate-400 font-medium hidden sm:inline">Quick Jump:</span>
                        <button type="button" @click="setTab('stockout')"
                                class="px-2.5 py-1 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200 transition cursor-pointer">
                            <span x-text="insights.telemetry.high_stockout_count">{{ $insights['telemetry']['high_stockout_count'] }}</span> Stockout Threats
                        </button>
                        <button type="button" @click="setTab('expiry')"
                                class="px-2.5 py-1 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs font-bold border border-purple-200 transition cursor-pointer">
                            <span x-text="insights.telemetry.high_expiry_count">{{ $insights['telemetry']['high_expiry_count'] }}</span> Expiring Batches
                        </button>
                    </div>
                </div>

                <!-- 1. Decision Support Action Banner (Top Summary) -->
                <div class="px-5 py-3.5 bg-gradient-to-r from-amber-50/90 via-amber-50/50 to-emerald-50/40 border-b border-amber-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-6 h-6 rounded-lg bg-amber-400 text-green-950 flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                            💡
                        </span>
                        <div class="min-w-0">
                            <span class="font-extrabold text-[#064e2b] tracking-wide uppercase text-[10px] mr-1">Decision Support:</span>
                            <span class="text-slate-800 font-semibold"
                                  x-text="insights.decision_summary || '{{ $insights['decision_summary'] }}'">
                                {{ $insights['decision_summary'] }}
                            </span>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/80 border border-amber-300 text-[10px] font-bold text-amber-900 shrink-0 self-start sm:self-auto shadow-2xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                        Actionable Guidance
                    </span>
                </div>

                <!-- 3 Glanceable Telemetry Callouts -->
                <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-100 p-2 sm:p-3">
                    <!-- Metric 1: Stockouts -->
                    <div class="p-4 rounded-xl hover:bg-slate-50/70 transition cursor-pointer" @click="setTab('stockout')">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-500">Urgent Stockout Horizon</span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold font-mono"
                                  :class="insights.telemetry.high_stockout_count > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'">
                                <span class="w-1.5 h-1.5 rounded-full"
                                      :class="insights.telemetry.high_stockout_count > 0 ? 'bg-rose-500 animate-pulse' : 'bg-emerald-500'"></span>
                                <span x-text="insights.telemetry.high_stockout_count > 0 ? 'Critical Deficit' : 'Safe Horizon'">
                                    {{ $insights['telemetry']['high_stockout_count'] > 0 ? 'Critical Deficit' : 'Safe Horizon' }}
                                </span>
                            </span>
                        </div>
                        <div class="mt-3 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold font-mono text-slate-900" x-text="insights.telemetry.high_stockout_count">
                                {{ $insights['telemetry']['high_stockout_count'] }}
                            </span>
                            <span class="text-xs text-slate-500 font-medium">critical formulation(s)</span>
                        </div>
                    </div>

                    <!-- Metric 2: Expiring Batches -->
                    <div class="p-4 rounded-xl hover:bg-slate-50/70 transition cursor-pointer" @click="setTab('expiry')">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-500">Imminent Expiry Exposure</span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold font-mono"
                                  :class="insights.telemetry.high_expiry_count > 0 ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'">
                                <span class="w-1.5 h-1.5 rounded-full"
                                      :class="insights.telemetry.high_expiry_count > 0 ? 'bg-amber-500 animate-pulse' : 'bg-emerald-500'"></span>
                                <span x-text="insights.telemetry.high_expiry_count > 0 ? 'FEFO Priority' : 'Optimal Expiry'">
                                    {{ $insights['telemetry']['high_expiry_count'] > 0 ? 'FEFO Priority' : 'Optimal Expiry' }}
                                </span>
                            </span>
                        </div>
                        <div class="mt-3 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold font-mono text-slate-900" x-text="insights.telemetry.high_expiry_count">
                                {{ $insights['telemetry']['high_expiry_count'] }}
                            </span>
                            <span class="text-xs text-slate-500 font-medium">batch(es) flagged</span>
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
                            <span class="text-3xl font-extrabold font-mono text-slate-900"
                                  x-text="'₱' + Number(insights.telemetry.total_financial_loss_at_risk).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})">
                                ₱{{ number_format($insights['telemetry']['total_financial_loss_at_risk'], 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Split: Top Items (Decision Support Tables) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- 3. Immediate Stockout Vulnerabilities Table (Left Card) -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden flex flex-col justify-between">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-xs text-slate-800">Immediate Stockout Vulnerabilities</h3>
                            <p class="text-[11px] text-slate-400">Inventory vs. safety buffer &amp; dynamic consumption run-rate</p>
                        </div>
                        <button type="button" @click="setTab('stockout')"
                                class="text-xs font-semibold text-[#064e2b] hover:underline cursor-pointer">
                            View full list &rarr;
                        </button>
                    </div>

                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-600 text-[10px] font-bold uppercase tracking-wider border-b border-slate-100">
                                <tr>
                                    <th class="px-4 py-2.5">MEDICINE</th>
                                    <th class="px-3 py-2.5 text-right">STOCK / RL</th>
                                    <th class="px-3 py-2.5 text-right">CR</th>
                                    <th class="px-3 py-2.5 text-center">DOH</th>
                                    <th class="px-3 py-2.5 text-center">STOCKOUT RISK (SR)</th>
                                    <th class="px-3 py-2.5">RECOMMENDED ACTION</th>
                                    <th class="px-3 py-2.5 text-right">ACTION</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="(item, idx) in (insights.stockout_insights || []).slice(0, 5)" :key="item.medicine_id">
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <!-- MEDICINE -->
                                        <td class="px-4 py-3">
                                            <div class="font-bold text-slate-900 text-xs" x-text="item.medicine_name"></div>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <span class="font-mono text-[10px] text-emerald-800 font-semibold" x-text="item.code"></span>
                                                <span class="text-[10px] text-slate-400" x-text="'· ' + item.category"></span>
                                            </div>
                                        </td>
                                        <!-- STOCK / RL -->
                                        <td class="px-3 py-3 text-right font-mono text-xs">
                                            <span class="font-bold" :class="item.current_stock === 0 ? 'text-rose-600' : 'text-slate-900'" x-text="item.current_stock"></span>
                                            <span class="text-slate-400">/</span>
                                            <span class="text-slate-500 text-[11px]" x-text="item.reorder_level"></span>
                                            <span class="text-[9px] text-slate-400 block" x-text="item.unit_short || item.unit"></span>
                                        </td>
                                        <!-- CR (Dynamic Consumption Rate) -->
                                        <td class="px-3 py-3 text-right font-mono text-slate-700 text-xs font-medium">
                                            <span x-text="(item.daily_consumption || 0).toFixed(1) + '/d'"></span>
                                        </td>
                                        <!-- DOH (Days on Hand) -->
                                        <td class="px-3 py-3 text-center">
                                            <span class="font-mono text-xs font-semibold inline-block px-1.5 py-0.5 rounded"
                                                  :class="item.days_until_depleted !== null && item.days_until_depleted <= leadTime ? 'text-rose-700 bg-rose-50 font-bold' : (item.days_until_depleted !== null && item.days_until_depleted <= (leadTime * 2) ? 'text-amber-800 bg-amber-50' : 'text-slate-700')"
                                                  x-text="item.doh_label || item.dosr_label">
                                            </span>
                                        </td>
                                        <!-- STOCKOUT RISK (SR) Urgency Badges -->
                                        <td class="px-3 py-3 text-center whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-mono"
                                                  :class="item.score >= 70 ? 'bg-rose-50 text-rose-700 border border-rose-200 font-bold' : (item.score >= 40 ? 'bg-amber-50 text-amber-800 border border-amber-200 font-semibold' : 'bg-emerald-50 text-emerald-700 border border-emerald-200 font-medium')">
                                                <span x-text="(item.score >= 70 ? 'High Risk' : (item.score >= 40 ? 'Moderate' : 'Low Risk')) + ' · ' + Math.round(item.score) + '%'"></span>
                                            </span>
                                        </td>
                                        <!-- RECOMMENDED ACTION -->
                                        <td class="px-3 py-3">
                                            <div class="text-xs font-semibold"
                                                 :class="item.score >= 70 ? 'text-rose-700' : (item.score >= 40 ? 'text-amber-800' : 'text-slate-600')"
                                                 x-text="item.recommended_action || item.action">
                                            </div>
                                        </td>
                                        <!-- ACTION (Compact 32x32px + Icon opens Restock Modal) -->
                                        <td class="px-3 py-3 text-right">
                                            @hasanyrole('stock_manager|admin')
                                                <button type="button"
                                                        @click="openRestockModal(item)"
                                                        :title="'Create restock order for ' + item.code"
                                                        :aria-label="'Create restock order for ' + item.code"
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 transition shadow-2xs cursor-pointer">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                    </svg>
                                                </button>
                                            @else
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-600 border border-rose-200 text-xs font-bold" title="Deficit Alert">
                                                    !
                                                </span>
                                            @endhasanyrole
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="!insights.stockout_insights || insights.stockout_insights.length === 0">
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-slate-400 text-xs">
                                            All formulations have adequate stock reserves for {{ $leadTime }}d lead time.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4. Imminent Batch Expiry Vulnerabilities Table (Right Card) -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden flex flex-col justify-between">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-xs text-slate-800">Imminent Batch Expiry Vulnerabilities</h3>
                            <p class="text-[11px] text-slate-400">Shelf-life horizon vs. projected consumption &amp; potential waste units</p>
                        </div>
                        <button type="button" @click="setTab('expiry')"
                                class="text-xs font-semibold text-[#064e2b] hover:underline cursor-pointer">
                            View full list &rarr;
                        </button>
                    </div>

                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-600 text-[10px] font-bold uppercase tracking-wider border-b border-slate-100">
                                <tr>
                                    <th class="px-4 py-2.5">BATCH / MEDICINE</th>
                                    <th class="px-3 py-2.5 text-center">SHELF LIFE (DTE)</th>
                                    <th class="px-3 py-2.5 text-right">AT RISK (QTY &amp; ₱)</th>
                                    <th class="px-3 py-2.5 text-center">EXPIRY RISK (ER)</th>
                                    <th class="px-3 py-2.5">RECOMMENDED ACTION</th>
                                    <th class="px-3 py-2.5 text-right">ACTION</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="(item, idx) in (insights.expiry_insights || []).slice(0, 5)" :key="item.batch_id">
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <!-- BATCH / MEDICINE -->
                                        <td class="px-4 py-3">
                                            <div class="font-mono font-bold text-slate-900 text-xs" x-text="'#' + item.batch_no"></div>
                                            <div class="text-[11px] font-medium text-slate-700" x-text="item.medicine_name"></div>
                                            <div class="font-mono text-[10px] text-emerald-800 font-semibold" x-text="item.code"></div>
                                        </td>
                                        <!-- SHELF LIFE (DTE) -->
                                        <td class="px-3 py-3 text-center">
                                            <div class="text-[11px] font-mono font-semibold"
                                                 :class="item.days_until_expiry <= 0 ? 'text-rose-700 font-bold' : (item.days_until_expiry <= 15 ? 'text-rose-600 font-bold' : (item.days_until_expiry <= 30 ? 'text-amber-700' : 'text-slate-700'))"
                                                 x-text="item.shelf_life_label">
                                            </div>
                                            <div class="text-[9px] text-slate-400" x-text="item.expiry_date"></div>
                                        </td>
                                        <!-- AT RISK (QTY & ₱) -->
                                        <td class="px-3 py-3 text-right font-mono text-xs">
                                            <span class="font-bold text-rose-600" x-text="item.projected_loss_units + ' ' + (item.unit_short || item.unit)"></span>
                                            <div class="text-[10px] font-bold text-rose-700"
                                                 x-text="'₱' + Number(item.financial_loss).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})">
                                            </div>
                                        </td>
                                        <!-- EXPIRY RISK (ER) Urgency Badges -->
                                        <td class="px-3 py-3 text-center whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-mono"
                                                  :class="item.score >= 70 ? 'bg-rose-50 text-rose-700 border border-rose-200 font-bold' : (item.score >= 40 ? 'bg-amber-50 text-amber-800 border border-amber-200 font-semibold' : 'bg-emerald-50 text-emerald-700 border border-emerald-200 font-medium')">
                                                <span x-text="(item.score >= 70 ? 'High Risk' : (item.score >= 40 ? 'Moderate' : 'Low Risk')) + ' · ' + Math.round(item.score) + '%'"></span>
                                            </span>
                                        </td>
                                        <!-- RECOMMENDED ACTION -->
                                        <td class="px-3 py-3">
                                            <div class="text-xs font-semibold"
                                                 :class="item.score >= 70 ? 'text-purple-900 font-bold' : (item.score >= 40 ? 'text-amber-800' : 'text-slate-600')"
                                                 x-text="item.recommended_action || item.action">
                                            </div>
                                        </td>
                                        <!-- ACTION (Compact Icon Buttons: Inspect & FEFO Flag) -->
                                        <td class="px-3 py-3 text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <a :href="'{{ url('inventory/medicines') }}/' + item.medicine_id + '#batches-table'"
                                                   :title="'Inspect batch #' + item.batch_no"
                                                   :aria-label="'Inspect batch #' + item.batch_no"
                                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 hover:border-purple-300 hover:bg-purple-50 text-slate-500 hover:text-purple-700 transition shadow-2xs">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                                <button type="button"
                                                        @click="alert('FEFO Priority Notification: Batch #' + item.batch_no + ' has been flagged for priority dispensing or inter-ward rotation.')"
                                                        :title="'Flag FEFO priority for #' + item.batch_no"
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 hover:border-amber-300 hover:bg-amber-50 text-slate-500 hover:text-amber-700 transition shadow-2xs cursor-pointer">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="!insights.expiry_insights || insights.expiry_insights.length === 0">
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-slate-400 text-xs">
                                            No batch expiration risks detected. All batches are within safe dispensing horizons.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- ══════════════════════════════════════════════════════════════════ -->
        <!-- VIEW 2: STOCKOUT RISK DEDICATED TABLE VIEW                         -->
        <!-- ══════════════════════════════════════════════════════════════════ -->
        <div x-show="activeTab === 'stockout'" x-transition class="space-y-3">
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
                               placeholder="Search formulation or code..."
                               class="w-full pl-8 pr-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-mono text-slate-800 focus:outline-none focus:ring-1 focus:ring-emerald-600">
                    </div>

                    <div class="flex items-center gap-2 text-xs text-slate-600">
                        <span>Lead time: <strong class="text-[#064e2b]" x-text="leadTime + ' days'">{{ $leadTime }} days</strong></span>
                        <span>&bull;</span>
                        <span class="font-bold text-rose-700" x-text="(insights.stockout_insights || []).length + ' items at risk'">
                            {{ count($insights['stockout_insights']) }} items at risk
                        </span>
                    </div>
                </div>

                <!-- Clean, Roomy Data Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs divide-y divide-slate-200">
                        <thead class="bg-slate-50 text-slate-600 font-bold uppercase text-[10px] tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5">MEDICINE</th>
                                <th class="px-4 py-3.5 text-right">STOCK / RL</th>
                                <th class="px-4 py-3.5 text-right">CR</th>
                                <th class="px-4 py-3.5 text-center">DOH</th>
                                <th class="px-4 py-3.5 text-center">STOCKOUT RISK (SR)</th>
                                <th class="px-5 py-3.5">RECOMMENDED ACTION</th>
                                <th class="px-4 py-3.5 text-right">ACTION</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <template x-for="item in (insights.stockout_insights || [])" :key="item.medicine_id">
                                <tr class="hover:bg-slate-50/80 transition-colors"
                                    x-show="!stockSearch || (item.code + ' ' + item.medicine_name + ' ' + item.category).toLowerCase().includes(stockSearch.toLowerCase())">
                                    <!-- MEDICINE -->
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-slate-900 text-sm tracking-wide" x-text="item.medicine_name"></div>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="font-mono text-xs text-emerald-800 font-semibold" x-text="item.code"></span>
                                            <span class="text-[11px] text-slate-400" x-text="'· ' + item.category"></span>
                                        </div>
                                    </td>
                                    <!-- STOCK / RL -->
                                    <td class="px-4 py-4 text-right">
                                        <div class="font-mono font-bold text-sm"
                                             :class="item.current_stock === 0 ? 'text-rose-600' : 'text-slate-900'"
                                             x-text="item.current_stock">
                                        </div>
                                        <span class="text-[10px] text-slate-400" x-text="'RL: ' + item.reorder_level + ' ' + (item.unit_short || item.unit)"></span>
                                    </td>
                                    <!-- CR -->
                                    <td class="px-4 py-4 text-right">
                                        <div class="font-mono text-slate-700 text-sm font-medium" x-text="(item.daily_consumption || 0).toFixed(1) + '/d'"></div>
                                        <span class="text-[10px] text-slate-400">units/day</span>
                                    </td>
                                    <!-- DOH -->
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-block px-2.5 py-1 rounded-md text-xs font-mono"
                                              :class="item.days_until_depleted !== null && item.days_until_depleted <= leadTime ? 'bg-rose-50 text-rose-700 font-bold border border-rose-200' : (item.days_until_depleted !== null && item.days_until_depleted <= leadTime * 2 ? 'bg-amber-50 text-amber-700 font-semibold border border-amber-200' : 'bg-emerald-50 text-emerald-700 font-medium border border-emerald-200')"
                                              x-text="item.doh_label || item.dosr_label">
                                        </span>
                                    </td>
                                    <!-- STOCKOUT RISK (SR) -->
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono"
                                              :class="item.score >= 70 ? 'bg-rose-50 text-rose-700 border border-rose-200 font-bold' : (item.score >= 40 ? 'bg-amber-50 text-amber-800 border border-amber-200 font-semibold' : 'bg-emerald-50 text-emerald-700 border border-emerald-200 font-medium')">
                                            <span x-text="(item.score >= 70 ? 'High Risk' : (item.score >= 40 ? 'Moderate' : 'Low Risk')) + ' · ' + Math.round(item.score) + '%'"></span>
                                        </span>
                                    </td>
                                    <!-- RECOMMENDED ACTION -->
                                    <td class="px-5 py-4">
                                        <div class="text-xs font-bold"
                                             :class="item.score >= 70 ? 'text-rose-700' : (item.score >= 40 ? 'text-amber-800' : 'text-slate-600')"
                                             x-text="item.recommended_action || item.action">
                                        </div>
                                        <div class="text-[11px] text-slate-500 mt-0.5" x-text="item.narrative"></div>
                                    </td>
                                    <!-- ACTION -->
                                    <td class="px-4 py-4 text-right whitespace-nowrap">
                                        @hasanyrole('stock_manager|admin')
                                            <button type="button"
                                                    @click="openRestockModal(item)"
                                                    :title="'Create restock order for ' + item.code"
                                                    :aria-label="'Create restock order for ' + item.code"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 transition shadow-2xs cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            </button>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold text-rose-700 bg-rose-50 border border-rose-200">
                                                Deficit Alert
                                            </span>
                                        @endhasanyrole
                                    </td>
                                </tr>
                            </template>
                            <template x-if="!insights.stockout_insights || insights.stockout_insights.length === 0">
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-slate-400 text-xs">
                                        All formulations have sufficient stock levels for {{ $leadTime }}d lead time.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════════ -->
        <!-- VIEW 3: BATCH EXPIRY RISK DEDICATED TABLE VIEW                     -->
        <!-- ══════════════════════════════════════════════════════════════════ -->
        <div x-show="activeTab === 'expiry'" x-transition class="space-y-3">
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
                               placeholder="Search batch or formulation..."
                               class="w-full pl-8 pr-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-mono text-slate-800 focus:outline-none focus:ring-1 focus:ring-emerald-600">
                    </div>

                    <div class="flex items-center gap-2 text-xs text-slate-600">
                        <span>Total at risk: <strong class="text-rose-700 font-mono" x-text="'₱' + Number(insights.telemetry.total_financial_loss_at_risk).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})">₱{{ number_format($insights['telemetry']['total_financial_loss_at_risk'], 2) }}</strong></span>
                        <span>&bull;</span>
                        <span class="font-bold text-purple-700" x-text="(insights.expiry_insights || []).length + ' batches flagged'">
                            {{ count($insights['expiry_insights']) }} batches flagged
                        </span>
                    </div>
                </div>

                <!-- Clean, Roomy Data Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs divide-y divide-slate-200">
                        <thead class="bg-slate-50 text-slate-600 font-bold uppercase text-[10px] tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5">BATCH / MEDICINE</th>
                                <th class="px-4 py-3.5 text-center">SHELF LIFE (DTE)</th>
                                <th class="px-4 py-3.5 text-right">AT RISK (QTY &amp; ₱)</th>
                                <th class="px-4 py-3.5 text-center">EXPIRY RISK (ER)</th>
                                <th class="px-5 py-3.5">RECOMMENDED ACTION</th>
                                <th class="px-4 py-3.5 text-right">ACTION</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <template x-for="item in (insights.expiry_insights || [])" :key="item.batch_id">
                                <tr class="hover:bg-slate-50/80 transition-colors"
                                    x-show="!expirySearch || (item.batch_no + ' ' + item.code + ' ' + item.medicine_name + ' ' + item.supplier).toLowerCase().includes(expirySearch.toLowerCase())">
                                    <!-- BATCH / MEDICINE -->
                                    <td class="px-5 py-4">
                                        <div class="font-mono font-bold text-slate-900 text-sm" x-text="'#' + item.batch_no"></div>
                                        <div class="text-xs font-semibold text-slate-800 mt-0.5" x-text="item.medicine_name"></div>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="font-mono text-[11px] text-emerald-800 font-semibold" x-text="item.code"></span>
                                            <span class="text-[10px] text-slate-400" x-text="'· ' + item.supplier"></span>
                                        </div>
                                    </td>
                                    <!-- SHELF LIFE (DTE) -->
                                    <td class="px-4 py-4 text-center">
                                        <div class="font-mono text-xs font-semibold"
                                             :class="item.days_until_expiry <= 0 ? 'text-rose-700 font-bold' : (item.days_until_expiry <= 15 ? 'text-rose-600 font-bold' : (item.days_until_expiry <= 30 ? 'text-amber-700' : 'text-slate-800'))"
                                             x-text="item.shelf_life_label">
                                        </div>
                                        <div class="text-[10px] font-mono text-slate-400 mt-0.5" x-text="item.expiry_date"></div>
                                    </td>
                                    <!-- AT RISK (QTY & ₱) -->
                                    <td class="px-4 py-4 text-right">
                                        <div class="font-mono font-bold text-sm text-rose-600" x-text="item.projected_loss_units + ' ' + (item.unit_short || item.unit)"></div>
                                        <div class="font-mono text-xs font-bold text-rose-700 mt-0.5"
                                             x-text="'₱' + Number(item.financial_loss).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})">
                                        </div>
                                    </td>
                                    <!-- EXPIRY RISK (ER) -->
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono"
                                              :class="item.score >= 70 ? 'bg-rose-50 text-rose-700 border border-rose-200 font-bold' : (item.score >= 40 ? 'bg-amber-50 text-amber-800 border border-amber-200 font-semibold' : 'bg-emerald-50 text-emerald-700 border border-emerald-200 font-medium')">
                                            <span x-text="(item.score >= 70 ? 'High Risk' : (item.score >= 40 ? 'Moderate' : 'Low Risk')) + ' · ' + Math.round(item.score) + '%'"></span>
                                        </span>
                                    </td>
                                    <!-- RECOMMENDED ACTION -->
                                    <td class="px-5 py-4">
                                        <div class="text-xs font-bold"
                                             :class="item.score >= 70 ? 'text-purple-900 font-bold' : (item.score >= 40 ? 'text-amber-800' : 'text-slate-600')"
                                             x-text="item.recommended_action || item.action">
                                        </div>
                                        <div class="text-[11px] text-slate-500 mt-0.5" x-text="item.narrative"></div>
                                    </td>
                                    <!-- ACTION (Compact Icon Buttons) -->
                                    <td class="px-4 py-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a :href="'{{ url('inventory/medicines') }}/' + item.medicine_id + '#batches-table'"
                                               :title="'Inspect batch #' + item.batch_no"
                                               :aria-label="'Inspect batch #' + item.batch_no"
                                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 hover:border-purple-300 hover:bg-purple-50 text-slate-500 hover:text-purple-700 transition shadow-2xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            <button type="button"
                                                    @click="alert('FEFO Priority Notification: Batch #' + item.batch_no + ' has been flagged for priority dispensing or inter-ward rotation.')"
                                                    :title="'Flag FEFO priority for #' + item.batch_no"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 hover:border-amber-300 hover:bg-amber-50 text-slate-500 hover:text-amber-700 transition shadow-2xs cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="!insights.expiry_insights || insights.expiry_insights.length === 0">
                                <tr>
                                    <td colspan="6" class="px-5 py-10 text-center text-slate-400 text-xs">
                                        No batch expiration risks detected. All batches are within safe dispensing horizons.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════════ -->
        <!-- RESTOCK / DELIVERY REQUEST MODAL (PRE-FILLED DECISION SUPPORT)     -->
        <!-- ══════════════════════════════════════════════════════════════════ -->
        <div x-show="restockModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
             style="display: none;"
             @keydown.escape.window="closeRestockModal()">

            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full overflow-hidden"
                 @click.outside="closeRestockModal()">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-[#064e2b] text-white flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-amber-400 text-green-950 flex items-center justify-center font-bold text-sm shadow-xs">
                            📦
                        </span>
                        <div>
                            <h3 class="text-sm font-bold tracking-tight text-white">Create Restock / Delivery Request</h3>
                            <p class="text-[11px] text-emerald-200/90">Pre-filled with Algorithmic Reorder Recommendation</p>
                        </div>
                    </div>
                    <button type="button" @click="closeRestockModal()" class="text-emerald-300 hover:text-white transition p-1 rounded-lg cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form method="POST" action="{{ route('inventory.deliveries.store') }}" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="items[0][medicine_id]" :value="modalData.medicine_id">

                    <!-- Target Medicine Summary Card -->
                    <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between text-xs">
                        <div>
                            <span class="font-extrabold text-slate-900 text-sm block" x-text="modalData.medicine_name"></span>
                            <span class="font-mono text-[11px] text-emerald-800 font-semibold" x-text="modalData.code"></span>
                            <span class="text-slate-400 text-[11px]" x-text="'· ' + modalData.category"></span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-semibold">Stock / Buffer</span>
                            <span class="font-mono font-bold text-slate-900" x-text="modalData.current_stock + ' / ' + modalData.reorder_level"></span>
                        </div>
                    </div>

                    <!-- Recommended Order Quantity (Editable) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="modal_quantity" class="text-xs font-bold text-slate-700">
                                Recommended Reorder Quantity:
                            </label>
                            <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                Decision Engine Output
                            </span>
                        </div>
                        <div class="relative">
                            <input type="number"
                                   id="modal_quantity"
                                   name="items[0][quantity]"
                                   x-model="modalData.recommended_qty"
                                   min="1"
                                   required
                                   class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#064e2b] shadow-2xs">
                            <span class="absolute right-3.5 top-2.5 text-xs text-slate-400 uppercase font-mono font-bold" x-text="modalData.unit_short || modalData.unit"></span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Computed from <span class="font-mono font-semibold" x-text="modalData.daily_consumption + ' units/day'"></span> consumption velocity and <span class="font-semibold" x-text="leadTime + ' days'"></span> lead time plus buffer.</p>
                    </div>

                    <!-- Supplier & Expected Delivery Date -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="modal_supplier" class="text-xs font-semibold text-slate-700 block mb-1">
                                Supplier:
                            </label>
                            <input type="text"
                                   id="modal_supplier"
                                   name="supplier"
                                   x-model="modalData.supplier"
                                   placeholder="e.g. Unilab Corp."
                                   class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-emerald-600">
                        </div>
                        <div>
                            <label for="modal_delivery_date" class="text-xs font-semibold text-slate-700 block mb-1">
                                Expected Delivery Date:
                            </label>
                            <input type="date"
                                   id="modal_delivery_date"
                                   name="delivery_date"
                                   x-model="modalData.delivery_date"
                                   class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-emerald-600">
                        </div>
                    </div>

                    <!-- Reference Number & Notes -->
                    <div>
                        <label for="modal_notes" class="text-xs font-semibold text-slate-700 block mb-1">
                            Procurement Notes / Justification:
                        </label>
                        <textarea id="modal_notes"
                                  name="notes"
                                  x-model="modalData.notes"
                                  rows="2"
                                  class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-emerald-600"></textarea>
                    </div>

                    <!-- Modal Actions -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button type="button"
                                @click="closeRestockModal()"
                                class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#064e2b] hover:bg-[#053e22] text-white text-xs font-bold shadow-xs transition cursor-pointer">
                            <span>Create Restock Request</span>
                            <span>&rarr;</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</x-app-layout>
