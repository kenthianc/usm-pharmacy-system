<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-lg bg-emerald-700 text-white flex items-center justify-center text-sm shadow-xs shrink-0">
                📦
            </span>
            <div class="min-w-0">
                <h1 class="text-base sm:text-lg font-bold text-slate-800 leading-tight truncate">
                    {{ __('Hospital Inventory & Stock Management') }}
                </h1>
                <p class="text-[11px] text-slate-500 hidden sm:block truncate mt-0.5">Real-time stock monitoring, batch allocation, deliveries, and formulary control.</p>
            </div>
        </div>
    </x-slot>

    <x-slot name="actions">
        <div class="flex items-center gap-2">
            <!-- Secondary Utility Actions Dropdown ("Actions ▾") -->
            <div class="relative" x-data="{ open: false }">
                <button type="button"
                        @click="open = !open"
                        @click.outside="open = false"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                    </svg>
                    <span>Actions</span>
                    <svg class="w-3 h-3 text-slate-400 transition-transform duration-150" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-1.5 w-52 bg-white rounded-xl shadow-lg border border-slate-200 py-1.5 z-50 text-xs font-medium text-slate-700"
                     style="display: none;">
                    <a href="{{ route('inventory.export-pdf') }}"
                       class="flex items-center gap-2.5 px-3.5 py-2 hover:bg-slate-50 text-slate-700 hover:text-emerald-700 transition">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <div>
                            <div class="font-semibold">Export Report</div>
                            <div class="text-[10px] text-slate-400">Download inventory PDF</div>
                        </div>
                    </a>
                    <a href="{{ route('inventory.movements') }}"
                       class="flex items-center gap-2.5 px-3.5 py-2 hover:bg-slate-50 text-slate-700 hover:text-emerald-700 transition">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <div>
                            <div class="font-semibold">Delivery Logs</div>
                            <div class="text-[10px] text-slate-400">Stock movements &amp; history</div>
                        </div>
                    </a>
                    @can('create', App\Models\Delivery::class)
                        <a href="{{ route('inventory.deliveries.create') }}"
                           class="flex items-center gap-2.5 px-3.5 py-2 hover:bg-emerald-50 text-emerald-800 border-t border-slate-100 transition">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <div>
                                <div class="font-semibold">Request Delivery</div>
                                <div class="text-[10px] text-emerald-600">Restock procurement order</div>
                            </div>
                        </a>
                    @endcan
                </div>
            </div>

            <!-- Primary Action: ONE prominent primary button -->
            @can('create', App\Models\Medicine::class)
                <a href="{{ route('inventory.medicines.create') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-bold shadow-xs transition shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Add Medicine</span>
                </a>
            @elsecan('create', App\Models\Delivery::class)
                <a href="{{ route('inventory.deliveries.create') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-bold shadow-xs transition shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Request Delivery</span>
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="space-y-4">

            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- ── 1. PREDICTIVE RISK INTELLIGENCE TACTICAL ALERT WIDGET ─────────────── -->
            @php
                $topStockout = $riskInsights['stockout_insights'][0] ?? null;
                $highStockoutCount = (int) ($riskInsights['telemetry']['high_stockout_count'] ?? $highRiskCount);
                $stockoutBuffer = $topStockout['days_until_depleted'] ?? null;
                $stockoutBufferFormatted = ($stockoutBuffer === 0 || $stockoutBuffer === 0.0) ? '0d Buffer' : ($stockoutBuffer !== null ? "{$stockoutBuffer}d Buffer" : '0d Buffer');
                $stockoutItemCode = $topStockout['medicine']->item_code ?? null;

                $topExpiry = $riskInsights['expiry_insights'][0] ?? null;
                $highExpiryCount = (int) ($riskInsights['telemetry']['high_expiry_count'] ?? 0);
                $expiryShelfLife = $topExpiry['days_until_expiry'] ?? null;
                $expiryShelfLifeFormatted = $expiryShelfLife !== null ? "<{$expiryShelfLife}d Shelf Life" : '<180d Shelf Life';
                $expiryBatchNumber = $topExpiry['batch']->batch_no ?? null;

                $lossAtRisk = (float) ($riskInsights['telemetry']['total_financial_loss_at_risk'] ?? 0.0);
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                <!-- Header with Dynamic Status Badge & Subtle Sleek Forecast Dashboard Link -->
                <div class="px-5 py-3 bg-gradient-to-r from-slate-50 via-white to-slate-50 border-b border-slate-100 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-7 h-7 rounded-lg bg-emerald-700 text-white flex items-center justify-center font-bold text-xs shadow-xs shrink-0">
                            ⚡
                        </div>
                        <div class="flex items-center gap-2 min-w-0 flex-wrap">
                            <h3 class="text-xs sm:text-sm font-bold text-slate-900 tracking-tight whitespace-nowrap">Dual Risk Prediction Intelligence</h3>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $globalRisk['badge_class'] ?? 'bg-emerald-50 text-emerald-700 border border-emerald-200' }} shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full {{ $globalRisk['dot_class'] ?? 'bg-emerald-500' }}"></span>
                                <span>{{ $globalRisk['label'] ?? 'Stable' }} Status</span>
                            </span>
                        </div>
                    </div>

                    <!-- Subtle, sleek CTA aligned to top-right -->
                    <a href="{{ route('inventory.risk-engine') }}"
                       class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 hover:text-emerald-800 hover:underline transition shrink-0 group">
                        <span>Forecast Dashboard</span>
                        <span class="transition-transform group-hover:translate-x-0.5">&rarr;</span>
                    </a>
                </div>

                <!-- 3 Glanceable Tactical Metric Panels (No Prose / Pure Visual Telemetry) -->
                <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-100 p-2 sm:p-3">
                    <!-- Metric 1: Urgent Stockout Risk -->
                    <a href="{{ route('inventory.risk-engine', ['tab' => 'stockout']) }}"
                       class="p-4 rounded-xl hover:bg-slate-50/80 transition group block">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-500">Urgent Stockout Risk</span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold font-mono {{ $highStockoutCount > 0 ? 'bg-rose-100 text-rose-800 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $highStockoutCount > 0 ? 'bg-rose-600 animate-pulse' : 'bg-emerald-500' }}"></span>
                                {{ $highStockoutCount > 0 ? 'Critical Deficit' : 'Safe Horizon' }}
                            </span>
                        </div>
                        <div class="mt-2.5 flex items-baseline justify-between">
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-black font-mono {{ $highStockoutCount > 0 ? 'text-rose-600' : 'text-slate-900' }} group-hover:scale-105 transition-transform inline-block">
                                    {{ $highStockoutCount }}
                                </span>
                                <span class="text-xs text-slate-500 font-medium">{{ Str::plural('formulation', $highStockoutCount) }}</span>
                            </div>
                            @if ($stockoutItemCode)
                                <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200" title="Most Critical Item">
                                    {{ $stockoutItemCode }}
                                </span>
                            @endif
                        </div>
                        <div class="mt-2 flex items-center gap-1.5 text-[11px] font-semibold {{ $highStockoutCount > 0 ? 'text-rose-700' : 'text-emerald-700' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $highStockoutCount > 0 ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                            <span>{{ $highStockoutCount > 0 ? 'Immediate Replenishment' : 'Inventory Levels Sufficient' }}</span>
                        </div>
                    </a>

                    <!-- Metric 2: Batch Expiration Risk -->
                    <a href="{{ route('inventory.risk-engine', ['tab' => 'expiry']) }}"
                       class="p-4 rounded-xl hover:bg-slate-50/80 transition group block">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-500">Batch Expiration Risk</span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold font-mono {{ $highExpiryCount > 0 ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $highExpiryCount > 0 ? 'bg-amber-600' : 'bg-emerald-500' }}"></span>
                                {{ $highExpiryCount > 0 ? 'FEFO Priority' : 'Optimal Shelf-Life' }}
                            </span>
                        </div>
                        <div class="mt-2.5 flex items-baseline justify-between">
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-black font-mono {{ $highExpiryCount > 0 ? 'text-amber-600' : 'text-slate-900' }} group-hover:scale-105 transition-transform inline-block">
                                    {{ $highExpiryCount }}
                                </span>
                                <span class="text-xs text-slate-500 font-medium">{{ Str::plural('batch', $highExpiryCount) }}</span>
                            </div>
                            @if ($expiryBatchNumber)
                                <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-amber-50 text-amber-800 border border-amber-200" title="FEFO Priority Batch">
                                    {{ $expiryBatchNumber }}
                                </span>
                            @endif
                        </div>
                        <div class="mt-2 flex items-center gap-1.5 text-[11px] font-semibold {{ $highExpiryCount > 0 ? 'text-amber-700' : 'text-emerald-700' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $highExpiryCount > 0 ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                            <span>{{ $highExpiryCount > 0 ? 'FEFO Dispensing Priority' : 'Batches Within Healthy Shelf Life' }}</span>
                        </div>
                    </a>

                    <!-- Metric 3: Value at Risk -->
                    <div class="p-4 rounded-xl block">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-500">Value at Risk</span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold font-mono {{ $lossAtRisk > 0 ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                Financial Exposure
                            </span>
                        </div>
                        <div class="mt-2.5 flex items-baseline gap-2">
                            <span class="text-3xl font-black font-mono {{ $lossAtRisk > 0 ? 'text-purple-700' : 'text-slate-900' }}">
                                ₱{{ number_format($lossAtRisk, 2) }}
                            </span>
                        </div>
                        <div class="mt-2 flex items-center gap-1.5 text-[11px] font-semibold {{ $lossAtRisk > 0 ? 'text-purple-700' : 'text-emerald-700' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $lossAtRisk > 0 ? 'bg-purple-500' : 'bg-emerald-500' }}"></span>
                            <span>{{ $lossAtRisk > 0 ? 'Projected Inventory Loss' : 'Zero Loss Exposure' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── 2. FORMULARY STATUS & ASSET VALUATION SUMMARY STRIP ─────────────────── -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                <!-- Formulary Stock Health (7 cols) -->
                <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Formulary Stock Health</h4>
                                <div class="text-xl font-extrabold text-slate-900 mt-1">
                                    {{ $totalItems }} <span class="text-xs font-normal text-slate-500">Catalogued Medicines</span>
                                </div>
                            </div>
                            @if ($stockStatus)
                                <a href="{{ route('inventory.index') }}" class="text-xs font-semibold text-emerald-700 hover:underline">
                                    Clear filter &times;
                                </a>
                            @endif
                        </div>

                        <!-- Segmented Visual Bar -->
                        @php
                            $inStockPct = $totalItems > 0 ? round(($inStockCount / $totalItems) * 100) : 0;
                            $lowStockPct = $totalItems > 0 ? round(($lowStockCount / $totalItems) * 100) : 0;
                            $outStockPct = $totalItems > 0 ? max(0, 100 - $inStockPct - $lowStockPct) : 0;
                        @endphp
                        <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden flex gap-0.5 mt-4">
                            <div style="width: {{ $inStockPct }}%" class="bg-emerald-500 rounded-l-full transition-all" title="Healthy Stock: {{ $inStockCount }} ({{ $inStockPct }}%)"></div>
                            <div style="width: {{ $lowStockPct }}%" class="bg-amber-400 transition-all" title="Low Stock: {{ $lowStockCount }} ({{ $lowStockPct }}%)"></div>
                            <div style="width: {{ $outStockPct }}%" class="bg-rose-500 rounded-r-full transition-all" title="Out of Stock: {{ $outOfStockCount }} ({{ $outStockPct }}%)"></div>
                        </div>
                    </div>

                    <!-- 3 Interactive Filter Pills -->
                    <div class="grid grid-cols-3 gap-2.5 mt-5 pt-4 border-t border-slate-100">
                        <!-- Healthy -->
                        <a href="{{ route('inventory.index', ['stock_status' => 'in_stock']) }}"
                           class="p-2.5 rounded-xl border {{ $stockStatus === 'in_stock' ? 'border-emerald-500 bg-emerald-50/50 ring-2 ring-emerald-500/20' : 'border-slate-200 hover:border-emerald-300 hover:bg-emerald-50/20' }} transition">
                            <div class="flex items-center gap-1.5 text-emerald-700 text-[11px] font-bold">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Healthy Stock</span>
                            </div>
                            <div class="text-xl font-bold font-mono text-slate-900 mt-1.5">{{ $inStockCount }}</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">Above reorder point</div>
                        </a>

                        <!-- Low -->
                        <a href="{{ route('inventory.index', ['stock_status' => 'low_stock']) }}"
                           class="p-2.5 rounded-xl border {{ $stockStatus === 'low_stock' ? 'border-amber-500 bg-amber-50/50 ring-2 ring-amber-500/20' : 'border-slate-200 hover:border-amber-300 hover:bg-amber-50/20' }} transition">
                            <div class="flex items-center gap-1.5 text-amber-700 text-[11px] font-bold">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span>Low Stock</span>
                            </div>
                            <div class="text-xl font-bold font-mono text-slate-900 mt-1.5">{{ $lowStockCount }}</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">At or below reorder</div>
                        </a>

                        <!-- Out -->
                        <a href="{{ route('inventory.index', ['stock_status' => 'out_of_stock']) }}"
                           class="p-2.5 rounded-xl border {{ $stockStatus === 'out_of_stock' ? 'border-rose-500 bg-rose-50/50 ring-2 ring-rose-500/20' : 'border-slate-200 hover:border-rose-300 hover:bg-rose-50/20' }} transition">
                            <div class="flex items-center gap-1.5 text-rose-700 text-[11px] font-bold">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                <span>Out of Stock</span>
                            </div>
                            <div class="text-xl font-bold font-mono text-slate-900 mt-1.5">{{ $outOfStockCount }}</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">0 units available</div>
                        </a>
                    </div>
                </div>

                <!-- Asset Valuation & Margin Summary (5 cols) -->
                <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Inventory Asset Valuation</h4>
                            <span class="text-[11px] text-slate-400">Total Stock Value</span>
                        </div>
                        <div class="text-xl font-extrabold text-slate-900 mt-1">
                            ₱{{ number_format($totalStockValue, 2) }}
                            <span class="text-xs font-normal text-slate-500">Total Stock Value</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-0.5">Capital invested in active physical hospital inventory</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-5 pt-4 border-t border-slate-100">
                        <div class="p-3 rounded-xl bg-slate-50/80 border border-slate-100">
                            <div class="text-[11px] font-semibold text-slate-500">Total Sale Value</div>
                            <div class="text-base font-extrabold font-mono text-slate-900 mt-1">₱{{ number_format($totalSaleValue, 2) }}</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">At selling prices</div>
                        </div>
                        <div class="p-3 rounded-xl bg-emerald-50/50 border border-emerald-100">
                            <div class="text-[11px] font-semibold text-emerald-800">Expected Profit</div>
                            <div class="text-base font-extrabold font-mono {{ $expectedProfit >= 0 ? 'text-emerald-800' : 'text-rose-700' }} mt-1">
                                ₱{{ number_format($expectedProfit, 2) }}
                            </div>
                            <div class="text-[10px] text-emerald-600 mt-0.5">Expected return</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search, Filter & Medicine Catalog Table -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
                <!-- Filter Bar -->
                <div class="p-4 sm:p-5 border-b border-gray-100 bg-gray-50/50 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <form method="GET" action="{{ route('inventory.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
                        @if ($stockStatus)
                            <input type="hidden" name="stock_status" value="{{ $stockStatus }}">
                        @endif

                        <div class="relative flex-1 min-w-[200px] max-w-md">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </span>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Search item code (e.g. PAR-500-TAB), generic, brand..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-lg text-xs focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <select name="category" onchange="this.form.submit()" class="py-2 pl-3 pr-8 bg-white border border-gray-300 rounded-lg text-xs focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All Categories</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-xs font-semibold transition">
                            Filter
                        </button>

                        @if ($search || $category || $stockStatus)
                            <a href="{{ route('inventory.index') }}" class="px-3 py-2 text-xs font-medium text-gray-500 hover:text-gray-800 transition">
                                Clear
                            </a>
                        @endif
                    </form>

                    <div class="text-xs text-gray-500 font-medium">
                        Showing <span class="font-bold text-gray-800">{{ $medicines->count() }}</span> items
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left">
                        <thead class="bg-gray-50 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3.5">Item Code</th>
                                <th class="px-6 py-3.5">Medicine</th>
                                <th class="px-6 py-3.5">Category &amp; Unit</th>
                                <th class="px-6 py-3.5">Unit Price</th>
                                <th class="px-6 py-3.5">Reorder Limit</th>
                                <th class="px-6 py-3.5">Current Stock</th>
                                <th class="px-6 py-3.5">Status</th>
                                <th class="px-6 py-3.5">Stockout Risk</th>
                                <th class="px-6 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs">
                            @forelse ($medicines as $med)
                                @php
                                    $available = $med->available_stock;
                                    $batchesCount = $med->stockBatches->where('expiry_date', '>=', now()->toDateString())->where('quantity_remaining', '>', 0)->count();
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('inventory.medicines.show', $med) }}" class="font-mono text-xs font-bold text-gray-800 hover:text-emerald-700 bg-gray-100/90 hover:bg-gray-200 px-2.5 py-1 rounded-md border border-gray-200 shadow-2xs transition inline-block">
                                            {{ $med->item_code }}
                                        </a>
                                        @if ($med->barcode)
                                            <div class="font-mono text-[10px] text-gray-400 mt-1 flex items-center gap-1">
                                                <svg class="w-3 h-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                                </svg>
                                                <span>{{ $med->barcode }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('inventory.medicines.show', $med) }}" class="font-bold text-gray-900 hover:text-emerald-700 transition">
                                            {{ trim(str_replace('(Out of Stock Demo)', '', $med->name)) }}
                                        </a>
                                        <div class="text-[11px] text-gray-500 font-medium mt-0.5">{{ $med->generic_name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 text-gray-700">
                                            {{ $med->category }}
                                        </span>
                                        <div class="text-[11px] text-gray-400 mt-1">per {{ $med->unit }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-800">
                                        ₱{{ number_format($med->unit_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $med->reorder_level }} {{ $med->unit }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-extrabold text-sm {{ $available > $med->reorder_level ? 'text-emerald-700' : ($available > 0 ? 'text-amber-600' : 'text-rose-600') }}">
                                            {{ number_format($available) }} {{ $med->unit }}
                                        </div>
                                        <div class="text-[10px] text-gray-400">
                                            {{ $batchesCount }} active {{ Str::plural('batch', $batchesCount) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($available === 0)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                                Out of Stock
                                            </span>
                                        @elseif ($available <= $med->reorder_level)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                                Low Stock
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                In Stock
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($med->stockout_risk_score !== null)
                                            @php
                                                $scoreFormatted = number_format($med->stockout_risk_score, 1);
                                                $isHigh = $med->stockout_risk_category === 'high';
                                                $isMod = $med->stockout_risk_category === 'moderate';
                                                $chipClass = $isHigh
                                                    ? 'bg-rose-50 text-rose-700 border-rose-200'
                                                    : ($isMod ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200');
                                                $levelTag = $isHigh ? 'L3' : ($isMod ? 'L2' : 'L1');
                                                $levelName = $isHigh ? 'High Risk' : ($isMod ? 'Moderate Risk' : 'Low Risk');
                                            @endphp
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-bold font-mono border {{ $chipClass }}" title="{{ $levelTag }}: {{ $levelName }} ({{ $scoreFormatted }}%)">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $isHigh ? 'bg-rose-500 animate-pulse' : ($isMod ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                                                <span>{{ $levelTag }} &bull; {{ $levelName }} &bull; {{ $scoreFormatted }}%</span>
                                            </span>
                                            <div class="text-[10px] text-gray-400 font-mono mt-0.5">
                                                Burn: {{ number_format($med->daily_consumption_rate, 1) }}/d
                                            </div>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-gray-100 text-gray-500 italic">
                                                Calc pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <!-- Request Delivery Action -->
                                            <a href="{{ route('inventory.deliveries.create', ['medicine_id' => $med->id]) }}"
                                               title="Request restock delivery for {{ $med->item_code }}"
                                               aria-label="Request restock delivery for {{ $med->item_code }}"
                                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 hover:border-emerald-300 hover:bg-emerald-50 text-gray-500 hover:text-emerald-700 transition shadow-2xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            </a>

                                            <!-- View Details Action -->
                                            <a href="{{ route('inventory.medicines.show', $med) }}"
                                               title="View details &amp; batches for {{ $med->item_code }}"
                                               aria-label="View details &amp; batches for {{ $med->item_code }}"
                                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-500 hover:text-gray-800 transition shadow-2xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>

                                            <!-- Edit Action -->
                                            @can('update', $med)
                                                <a href="{{ route('inventory.medicines.edit', $med) }}"
                                                   title="Edit formulary item {{ $med->item_code }}"
                                                   aria-label="Edit formulary item {{ $med->item_code }}"
                                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 hover:border-amber-300 hover:bg-amber-50 text-gray-500 hover:text-amber-700 transition shadow-2xs">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                    </svg>
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <div class="text-3xl mb-2">📦</div>
                                        <div class="font-semibold text-gray-800">No medicines found</div>
                                        <p class="text-xs text-gray-400 mt-1">Try adjusting your search or category filter.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
</x-app-layout>
