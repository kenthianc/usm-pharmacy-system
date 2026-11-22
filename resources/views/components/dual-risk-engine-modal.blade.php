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
     @open-dual-risk-modal.window="open = true"
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
                    <div class="px-6 py-5 bg-[#064e2b] text-white flex items-center justify-between shrink-0 border-b border-[#043c20]">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-yellow-500 text-green-950 flex items-center justify-center font-bold text-lg shadow-sm border border-yellow-300">
                                📊
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold text-white tracking-wide">Risk Summary</h3>
                                    @php
                                        $gRisk = $engineData['global_risk'] ?? app(\App\Services\RiskPredictionService::class)->getGlobalRiskStatus($leadTime);
                                    @endphp
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $gRisk['status'] === 'danger' ? 'bg-rose-900/90 text-rose-200 border border-rose-600' : ($gRisk['status'] === 'warning' ? 'bg-amber-900/90 text-amber-200 border border-amber-600' : 'bg-emerald-900/90 text-emerald-200 border border-emerald-700') }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $gRisk['status'] === 'danger' ? 'bg-rose-400 animate-pulse' : ($gRisk['status'] === 'warning' ? 'bg-amber-400' : 'bg-emerald-400') }}"></span>
                                        {{ ucfirst($gRisk['status']) }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-emerald-200 mt-0.5">Stockout warnings and batch expiration horizons</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('inventory.risk-engine') }}" class="px-3 py-1.5 rounded-lg bg-yellow-500 hover:bg-yellow-400 text-green-950 text-xs font-bold transition shadow-xs">
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
                            <span class="text-emerald-700">&bull;</span>
                            <span>Expiring: <strong class="text-purple-300 font-bold">{{ $engineData['telemetry']['high_expiry_count'] }}</strong></span>
                        </div>
                        <div>
                            Value at Risk: <strong class="text-amber-300 font-mono font-bold">₱{{ number_format($engineData['telemetry']['total_financial_loss_at_risk'], 2) }}</strong>
                        </div>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="px-6 py-2.5 bg-gray-50 border-b border-gray-200 flex items-center gap-2 shrink-0">
                        <button type="button" @click="tab = 'all'"
                                :class="tab === 'all' ? 'bg-[#064e2b] text-white font-bold' : 'bg-white text-gray-600 hover:bg-gray-100 font-medium'"
                                class="px-3 py-1.5 rounded-lg text-xs transition border border-gray-200 cursor-pointer">
                            All Insights ({{ $engineData['total_alerts_count'] }})
                        </button>
                        <button type="button" @click="tab = 'stockout'"
                                :class="tab === 'stockout' ? 'bg-rose-700 text-white font-bold' : 'bg-white text-rose-700 hover:bg-rose-50 font-medium'"
                                class="px-3 py-1.5 rounded-lg text-xs transition border border-rose-200 flex items-center gap-1.5 cursor-pointer">
                            <span>Stockouts ({{ count($engineData['stockout_insights']) }})</span>
                        </button>
                        <button type="button" @click="tab = 'expiry'"
                                :class="tab === 'expiry' ? 'bg-purple-700 text-white font-bold' : 'bg-white text-purple-700 hover:bg-purple-50 font-medium'"
                                class="px-3 py-1.5 rounded-lg text-xs transition border border-purple-200 flex items-center gap-1.5 cursor-pointer">
                            <span>Expiring Batches ({{ count($engineData['expiry_insights']) }})</span>
                        </button>
                    </div>

                    <!-- Drawer Scrollable Content Feed -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-4">
                        
                        <!-- Empty State -->
                        @if ($engineData['total_alerts_count'] === 0)
                            <div class="py-16 text-center text-gray-400">
                                <div class="text-4xl mb-2">🎉</div>
                                <div class="text-sm font-semibold text-gray-700">All Systems Nominal</div>
                                <div class="text-xs text-gray-500 mt-1">No critical stockout or near-expiry thresholds triggered.</div>
                            </div>
                        @endif

                        <!-- Stockout Items -->
                        @foreach ($engineData['stockout_insights'] as $item)
                            <div x-show="tab === 'all' || tab === 'stockout'"
                                 class="rounded-xl border {{ $item['is_dual_risk'] ? 'border-rose-300 bg-rose-50/25' : ($item['level'] === 3 ? 'border-orange-300 bg-orange-50/25' : 'border-amber-300 bg-amber-50/25') }} p-4 shadow-2xs">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono font-bold text-sm text-gray-900">{{ $item['code'] }}</span>
                                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-white border border-gray-200 text-gray-600">
                                                {{ $item['category'] }}
                                            </span>
                                        </div>
                                    </div>

                                    <div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-mono {{ $item['badge_class'] }}">
                                            {{ $item['score_chip'] }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-2 text-xs text-slate-700">
                                    {{ $item['narrative'] }}
                                </div>

                                <div class="mt-3 flex items-center justify-between text-xs pt-2.5 border-t border-gray-200/70">
                                    <span class="text-gray-500 font-medium font-mono text-[11px]">
                                        Stock: <strong class="text-slate-800">{{ number_format($item['current_stock']) }}</strong> / {{ number_format($item['buffer_stock']) }} &bull; ADC: <strong class="text-slate-800">{{ number_format($item['daily_consumption'], 1) }}/d</strong> &bull; DoSR: <strong class="{{ $item['days_until_depleted'] !== null && $item['days_until_depleted'] <= $leadTime ? 'text-rose-600' : 'text-slate-800' }}">{{ $item['dosr_label'] }}</strong>
                                    </span>
                                    @can('create', App\Models\Delivery::class)
                                        <a href="{{ route('inventory.deliveries.create', ['medicine_id' => $item['medicine_id']]) }}"
                                           title="Create Reorder Purchase Order for {{ $item['code'] }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white shadow-2xs transition cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </a>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold text-rose-700 bg-rose-50 border border-rose-200" title="Low stock alert for dispensary">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                                            Alert
                                        </span>
                                    @endcan
                                </div>
                            </div>
                        @endforeach

                        <!-- Expiry Items -->
                        @foreach ($engineData['expiry_insights'] as $item)
                            <div x-show="tab === 'all' || tab === 'expiry'"
                                 class="rounded-xl border {{ $item['is_dual_risk'] ? 'border-rose-300 bg-rose-50/25' : ($item['level'] === 3 ? 'border-orange-300 bg-orange-50/25' : 'border-purple-300 bg-purple-50/25') }} p-4 shadow-2xs">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono font-bold text-xs text-gray-900">#{{ $item['batch_no'] }}</span>
                                            <span class="font-mono text-xs font-semibold text-emerald-800">Code: {{ $item['code'] }}</span>
                                        </div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">{{ $item['supplier'] }}</div>
                                    </div>

                                    <div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-mono {{ $item['badge_class'] }}">
                                            {{ $item['score_chip'] }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-2 text-xs text-slate-700">
                                    {{ $item['narrative'] }}
                                </div>

                                <div class="mt-3 flex items-center justify-between text-xs pt-2.5 border-t border-gray-200/70">
                                    <span class="text-gray-500 font-medium text-[11px]">
                                        Expires: <strong class="text-slate-800">{{ $item['expiry_date'] }}</strong> ({{ $item['shelf_life_label'] }}) &bull; Loss: <strong class="text-rose-700 font-mono">₱{{ number_format($item['financial_loss'], 2) }}</strong>
                                    </span>
                                    @hasanyrole('stock_manager|admin')
                                        <a href="{{ route('inventory.medicines.show', $item['medicine_id']) }}#batches-table"
                                           title="Inspect batch #{{ $item['batch_no'] }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-purple-700 hover:bg-purple-800 text-white shadow-2xs transition cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold text-purple-700 bg-purple-50 border border-purple-200" title="Expiring batch alert: Triage for priority dispensing">
                                            FEFO Triage
                                        </span>
                                    @endhasanyrole
                                </div>
                            </div>
                        @endforeach

                    </div>

                    <!-- Drawer Footer -->
                    <div class="p-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between shrink-0">
                        @hasanyrole('stock_manager|admin')
                            <form method="POST" action="{{ route('inventory.risk-engine.recalculate') }}">
                                @csrf
                                <input type="hidden" name="lead_time" value="{{ $leadTime }}">
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-[#064e2b] hover:bg-[#053e22] text-white text-xs font-bold shadow-xs cursor-pointer">
                                    <span>Recalculate Analysis</span>
                                </button>
                            </form>
                        @else
                            <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Dispensary Live Alert Telemetry</span>
                            </div>
                        @endhasanyrole

                        <button type="button" @click="open = false"
                                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-xs font-semibold hover:bg-gray-100 cursor-pointer">
                            Close
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
