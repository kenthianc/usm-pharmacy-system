@props([
    'leadTime' => 7,
])

@php
    $engineData = app(\App\Services\RiskPredictionService::class)->getDualEngineInsights($leadTime);
@endphp

<div x-data="{
        open: false,
        tab: 'all',
        loading: false
     }"
     @open-risk-engine.window="open = true"
     @keydown.escape.window="open = false"
     class="relative z-50">

    <!-- Backdrop -->
    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-950/75 backdrop-blur-xs transition-opacity"
         style="display: none;"></div>

    <!-- Slide-Over Drawer Container -->
    <div x-show="open"
         class="fixed inset-0 overflow-hidden"
         style="display: none;">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div x-show="open"
                     x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full"
                     @click.away="open = false"
                     class="pointer-events-auto w-screen max-w-2xl bg-white shadow-2xl flex flex-col">

                    <!-- Drawer Header -->
                    <div class="px-6 py-5 bg-emerald-900 text-white flex items-center justify-between shrink-0 border-b border-emerald-800">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-800 border border-emerald-700 flex items-center justify-center text-emerald-200 font-bold text-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold text-white tracking-wide">Risk &amp; Expiration Forecast</h3>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-800 text-emerald-100 border border-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        Active
                                    </span>
                                </div>
                                <p class="text-[11px] text-emerald-200 mt-0.5">Stockout warnings and batch expiration horizons</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('inventory.risk-engine') }}" class="px-2.5 py-1 rounded-lg bg-emerald-800 hover:bg-emerald-700 text-white text-[11px] font-semibold transition border border-emerald-700">
                                Full View &rarr;
                            </a>
                            <button type="button" @click="open = false" class="text-emerald-300 hover:text-white p-1 text-xl font-bold leading-none cursor-pointer">
                                &times;
                            </button>
                        </div>
                    </div>

                    <!-- Summary Strip -->
                    <div class="px-6 py-2.5 bg-emerald-950 text-emerald-200 border-b border-emerald-900 flex items-center justify-between text-xs shrink-0">
                        <div class="flex items-center gap-3">
                            <span>Stockouts: <strong class="text-rose-300 font-bold">{{ $engineData['telemetry']['high_stockout_count'] }}</strong></span>
                            <span class="text-emerald-700">·</span>
                            <span>Expiring: <strong class="text-purple-300 font-bold">{{ $engineData['telemetry']['high_expiry_count'] }}</strong></span>
                        </div>
                        <div>
                            Value at Risk: <strong class="text-amber-300 font-mono font-bold">₱{{ number_format($engineData['telemetry']['total_financial_loss_at_risk'], 2) }}</strong>
                        </div>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="px-6 py-2.5 bg-gray-50 border-b border-gray-200 flex items-center gap-2 shrink-0">
                        <button type="button" @click="tab = 'all'"
                                :class="tab === 'all' ? 'bg-gray-900 text-white font-bold' : 'bg-white text-gray-600 hover:bg-gray-100 font-medium'"
                                class="px-3 py-1.5 rounded-lg text-xs transition border border-gray-200">
                            All Insights ({{ $engineData['total_alerts_count'] }})
                        </button>
                        <button type="button" @click="tab = 'stockout'"
                                :class="tab === 'stockout' ? 'bg-rose-700 text-white font-bold' : 'bg-white text-rose-700 hover:bg-rose-50 font-medium'"
                                class="px-3 py-1.5 rounded-lg text-xs transition border border-rose-200 flex items-center gap-1.5">
                            <span>Stockout Warnings ({{ count($engineData['stockout_insights']) }})</span>
                        </button>
                        <button type="button" @click="tab = 'expiry'"
                                :class="tab === 'expiry' ? 'bg-purple-700 text-white font-bold' : 'bg-white text-purple-700 hover:bg-purple-50 font-medium'"
                                class="px-3 py-1.5 rounded-lg text-xs transition border border-purple-200 flex items-center gap-1.5">
                            <span>Expiring Batches ({{ count($engineData['expiry_insights']) }})</span>
                        </button>
                    </div>

                    <!-- Drawer Scrollable Content Feed -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-4">
                        
                        <!-- Empty State -->
                        @if ($engineData['total_alerts_count'] === 0)
                            <div class="py-16 text-center text-gray-400">
                                <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto text-xl font-bold mb-2">
                                    ✓
                                </div>
                                <div class="font-bold text-gray-700">No Critical Inventory Risks</div>
                                <p class="text-xs text-gray-500 mt-1">Formulary stock levels and batch expiration dates are within safe parameters.</p>
                            </div>
                        @endif

                        <!-- Stockout Items -->
                        @foreach ($engineData['stockout_insights'] as $item)
                            <div x-show="tab === 'all' || tab === 'stockout'"
                                 class="rounded-xl border {{ $item['risk_category'] === 'high' ? 'border-rose-300 bg-rose-50/20' : 'border-amber-300 bg-amber-50/20' }} p-4 shadow-2xs">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-rose-600 font-bold text-xs">STOCKOUT RISK</span>
                                            <span class="text-gray-300">·</span>
                                            <span class="font-bold text-sm text-gray-900">{{ $item['medicine_name'] }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="font-mono text-[11px] font-bold text-gray-700 bg-gray-100 px-1.5 py-0.2 rounded border border-gray-300">{{ $item['code'] }}</span>
                                            <span class="text-xs text-gray-500">{{ $item['generic_name'] }}</span>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-black {{ $item['badge_class'] }}">
                                        {{ number_format($item['score'], 1) }}% Risk
                                    </span>
                                </div>

                                <div class="mt-2.5 p-3 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 text-xs leading-relaxed">
                                    <div class="text-[10px] font-bold text-slate-700 uppercase mb-0.5">Clinical Note</div>
                                    {{ $item['narrative'] }}
                                </div>

                                <div class="mt-2.5 flex items-center justify-between text-xs pt-2 border-t border-gray-100">
                                    <span class="text-gray-500 font-medium">Stock: <strong>{{ number_format($item['current_stock']) }}</strong> | Burn: <strong>{{ number_format($item['daily_consumption'], 1) }}/d</strong></span>
                                    <a href="{{ route('inventory.deliveries.create', ['medicine_id' => $item['medicine_id']]) }}"
                                       class="px-2.5 py-1 rounded-md bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-2xs">
                                        + Restock PO
                                    </a>
                                </div>
                            </div>
                        @endforeach

                        <!-- Expiry Items -->
                        @foreach ($engineData['expiry_insights'] as $item)
                            <div x-show="tab === 'all' || tab === 'expiry'"
                                 class="rounded-xl border {{ $item['risk_category'] === 'high' ? 'border-purple-300 bg-purple-50/20' : 'border-amber-300 bg-amber-50/20' }} p-4 shadow-2xs">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-purple-700 font-bold text-xs">EXPIRING BATCH</span>
                                            <span class="text-gray-300">·</span>
                                            <span class="font-mono font-bold text-xs text-gray-900">#{{ $item['batch_no'] }}</span>
                                        </div>
                                        <div class="text-xs font-bold text-gray-800 flex items-center gap-1.5 mt-0.5">
                                            <span>{{ $item['medicine_name'] }}</span>
                                            @if(!empty($item['code']))
                                                <span class="font-mono text-[10px] font-bold text-gray-700 bg-gray-100 px-1.5 py-0.2 rounded border border-gray-300">{{ $item['code'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-black {{ $item['badge_class'] }}">
                                        {{ number_format($item['score'], 1) }}% Risk
                                    </span>
                                </div>

                                <div class="mt-2.5 p-3 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 text-xs leading-relaxed">
                                    <div class="text-[10px] font-bold text-slate-700 uppercase mb-0.5">Batch Assessment</div>
                                    {{ $item['narrative'] }}
                                </div>

                                <div class="mt-2.5 flex items-center justify-between text-xs pt-2 border-t border-gray-100">
                                    <span class="text-gray-500 font-medium">Expires: <strong>{{ $item['expiry_date'] }}</strong> ({{ $item['days_until_expiry'] }}d)</span>
                                    <a href="{{ route('inventory.medicines.show', $item['medicine_id']) }}#batches-table"
                                       class="px-2.5 py-1 rounded-md bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs shadow-2xs">
                                        Inspect Batch &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach

                    </div>

                    <!-- Drawer Footer -->
                    <div class="p-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between shrink-0">
                        <form method="POST" action="{{ route('inventory.risk-engine.recalculate') }}">
                            @csrf
                            <input type="hidden" name="lead_time" value="{{ $leadTime }}">
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-xs cursor-pointer">
                                <span>Recalculate Analysis</span>
                            </button>
                        </form>

                        <button type="button" @click="open = false"
                                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-xs font-semibold hover:bg-gray-100">
                            Close
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
