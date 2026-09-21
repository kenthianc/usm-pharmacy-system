<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2.5 flex-wrap">
                    <a href="{{ route('inventory.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <span>{{ trim(str_replace('(Out of Stock Demo)', '', $medicine->name)) }}</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                        {{ $medicine->category }}
                    </span>
                </h2>
                <div class="flex items-center gap-2.5 mt-1.5 flex-wrap">
                    <span class="font-mono text-xs font-semibold text-gray-600 tracking-wider bg-gray-100 px-2 py-0.5 rounded border border-gray-200">
                        {{ $medicine->item_code }}
                    </span>
                    <span class="text-gray-300">•</span>
                    <span class="text-xs text-gray-500 font-medium">{{ $medicine->generic_name }}</span>
                    @if ($medicine->barcode)
                        <span class="text-gray-300">•</span>
                        <span class="text-xs font-mono text-gray-500 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                            {{ $medicine->barcode }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2">
                @can('receiveBatch', $medicine)
                    <a href="{{ route('inventory.deliveries.create', ['medicine_id' => $medicine->id]) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>+ Restock</span>
                    </a>
                @endcan
                @can('update', $medicine)
                    <a href="{{ route('inventory.medicines.edit', $medicine) }}" class="inline-flex items-center gap-1 px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50 shadow-xs transition">
                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        <span>Edit</span>
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ receiveModal: false, disposeModal: false, adjustModal: false, activeBatch: null, activeBatchNo: '', activeRemaining: 0 }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            @php
                $pendingCount = $medicine->stockBatches->where('status', 'pending')->count();
            @endphp

            @if ($pendingCount > 0)
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 text-sm flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-400 text-amber-950 flex items-center justify-center font-bold text-sm shrink-0">
                            ⏳
                        </div>
                        <div>
                            <div class="font-bold text-amber-950">{{ $pendingCount }} Pending Delivery Shipment(s) Awaiting Physical Inspection</div>
                            <p class="text-xs text-amber-800 mt-0.5">
                                These units are quarantined and <strong>not counted in active stock</strong> until confirmed by a Stock Manager or Admin.
                            </p>
                        </div>
                    </div>
                    <a href="#batches-table" class="px-3 py-1.5 rounded-lg bg-amber-200 hover:bg-amber-300 text-amber-900 text-xs font-bold transition">
                        Review Batches &darr;
                    </a>
                </div>
            @endif

            @if ($expiringSoonBatches->isNotEmpty())
                <div class="p-4 rounded-xl bg-purple-50 border border-purple-200 text-purple-900 text-sm flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center font-bold text-purple-700 shrink-0">
                            ⏳
                        </div>
                        <div>
                            <div class="font-bold text-purple-950">Expiry Warning</div>
                            <p class="text-xs text-purple-800 mt-0.5">
                                {{ $expiringSoonBatches->count() }} batch(es) will expire within 30 days. Prioritize FEFO dispensing or review for disposal.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Medicine Details & Enterprise Key Metrics Card -->
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-sm p-6 sm:p-8">
                <!-- Card Header with Medicine Identity, Code, and Action -->
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 pb-6 border-b border-gray-100">
                    <div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <h3 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                                {{ trim(str_replace('(Out of Stock Demo)', '', $medicine->name)) }}
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                {{ $medicine->category }}
                            </span>
                            @if ($medicine->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active Formulary
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Inactive
                                </span>
                            @endif
                        </div>

                        <!-- Prominent Medicine Code & Clinical Identifiers -->
                        <div class="flex items-center gap-3 mt-2 flex-wrap text-xs">
                            <span class="font-mono font-semibold text-gray-700 tracking-wider bg-gray-100/90 px-2.5 py-1 rounded-md border border-gray-200">
                                {{ $medicine->item_code }}
                            </span>
                            <span class="text-gray-300">•</span>
                            <span class="text-gray-600 font-medium">{{ $medicine->generic_name }}</span>
                            @if ($medicine->barcode)
                                <span class="text-gray-300">•</span>
                                <span class="font-mono text-gray-500 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                    </svg>
                                    {{ $medicine->barcode }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Action Area -->
                    <div class="flex items-center gap-4 shrink-0">
                        <div class="text-right hidden sm:block">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Unit Price</div>
                            <div class="text-lg font-extrabold text-gray-900">
                                ₱{{ number_format($medicine->unit_price, 2) }}
                                <span class="text-xs font-normal text-gray-500">/ {{ $medicine->unit }}</span>
                            </div>
                        </div>

                        @can('receiveBatch', $medicine)
                            <a href="{{ route('inventory.deliveries.create', ['medicine_id' => $medicine->id]) }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>+ Restock</span>
                            </a>
                        @endcan
                    </div>
                </div>

                <!-- 4-Column Distinct Key Metric Cards Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mt-6">
                    <!-- 1. On-Hand Stock -->
                    <div class="bg-gray-50/90 rounded-xl border border-gray-200/80 p-4 sm:p-5 flex flex-col justify-between hover:border-gray-300 transition shadow-2xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">On-Hand Stock</span>
                            <span class="w-2.5 h-2.5 rounded-full {{ $medicine->available_stock > $medicine->reorder_level ? 'bg-emerald-500' : ($medicine->available_stock > 0 ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                        </div>
                        <div class="mt-4">
                            <div class="text-3xl sm:text-4xl font-extrabold tracking-tight {{ $medicine->available_stock > $medicine->reorder_level ? 'text-emerald-700' : ($medicine->available_stock > 0 ? 'text-amber-600' : 'text-rose-600') }}">
                                {{ number_format($medicine->available_stock) }}
                            </div>
                            <div class="text-xs font-medium text-gray-500 mt-1.5">
                                {{ Str::plural($medicine->unit, $medicine->available_stock) }}
                                @if ($medicine->pending_delivery_stock > 0)
                                    <span class="block text-[11px] font-semibold text-amber-700 mt-1">
                                        +{{ number_format($medicine->pending_delivery_stock) }} pending inspection
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 2. Avg. Burn Rate (per day) -->
                    <div class="bg-gray-50/90 rounded-xl border border-gray-200/80 p-4 sm:p-5 flex flex-col justify-between hover:border-gray-300 transition shadow-2xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Avg. Burn Rate (per day)</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">Velocity</span>
                        </div>
                        <div class="mt-4">
                            <div class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">
                                {{ number_format($stockoutRisk['daily_consumption'], 2) }}
                            </div>
                            <div class="text-xs font-medium text-gray-500 mt-1.5">
                                {{ Str::plural($medicine->unit, 2) }} / day
                            </div>
                        </div>
                    </div>

                    <!-- 3. Reorder Level (B_s) -->
                    <div class="bg-gray-50/90 rounded-xl border border-gray-200/80 p-4 sm:p-5 flex flex-col justify-between hover:border-gray-300 transition shadow-2xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Reorder Level (B<sub>s</sub>)</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200">Buffer</span>
                        </div>
                        <div class="mt-4">
                            <div class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">
                                {{ number_format($medicine->reorder_level) }}
                            </div>
                            <div class="text-xs font-medium text-gray-500 mt-1.5">
                                {{ Str::plural($medicine->unit, $medicine->reorder_level) }}
                            </div>
                        </div>
                    </div>

                    <!-- 4. Lead Time (L_t) -->
                    <div class="bg-gray-50/90 rounded-xl border border-gray-200/80 p-4 sm:p-5 flex flex-col justify-between hover:border-gray-300 transition shadow-2xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Lead Time (L<sub>t</sub>)</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-purple-50 text-purple-700 border border-purple-200">Supplier</span>
                        </div>
                        <div class="mt-4">
                            <div class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">
                                {{ $stockoutRisk['lead_time_days'] }}
                            </div>
                            <div class="text-xs font-medium text-gray-500 mt-1.5">
                                days
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dual Risk Prediction Engine Card -->
            <div class="bg-gradient-to-r from-gray-900 via-emerald-950 to-gray-900 rounded-xl border border-emerald-800/60 shadow-md p-6 text-white">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-emerald-800/40 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-400 font-extrabold text-lg">
                            ⚡
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-black text-sm uppercase tracking-wider text-emerald-300">Dual-Risk Prediction Engine</h3>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-400/20 text-emerald-300 border border-emerald-400/30">
                                    AI Health Core
                                </span>
                            </div>
                            <p class="text-xs text-gray-300 mt-0.5">Real-time predictive forecasting for stockout burn rate and batch expiry vulnerabilities.</p>
                        </div>
                    </div>

                    <!-- Stockout Score Pill -->
                    <div class="flex items-center gap-3 bg-white/5 border border-white/10 rounded-xl px-4 py-2.5">
                        <div>
                            <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Stockout Risk (S<sub>r</sub>)</div>
                            <div class="text-xl font-black text-white flex items-center gap-2">
                                <span>{{ number_format($stockoutRisk['score'], 1) }}%</span>
                                <span class="text-xs font-bold px-2 py-0.5 rounded-md {{ $stockoutRisk['badge_class'] }}">
                                    {{ $stockoutRisk['label'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mathematical Breakdown Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4 text-xs">
                    <div class="bg-white/5 border border-white/10 rounded-lg p-3">
                        <div class="text-[10px] text-gray-400 uppercase font-semibold">Available Stock (I<sub>c</sub>)</div>
                        <div class="text-base font-bold text-emerald-300 mt-1">{{ number_format($stockoutRisk['current_stock']) }} {{ $medicine->unit }}</div>
                        <div class="text-[10px] text-gray-400 mt-0.5">Unexpired active stock</div>
                    </div>

                    <div class="bg-white/5 border border-white/10 rounded-lg p-3">
                        <div class="text-[10px] text-gray-400 uppercase font-semibold">Daily Consumption (D<sub>c</sub>)</div>
                        <div class="text-base font-bold text-amber-300 mt-1">{{ number_format($stockoutRisk['daily_consumption'], 2) }} /day</div>
                        <div class="text-[10px] text-gray-400 mt-0.5">30-day velocity window</div>
                    </div>

                    <div class="bg-white/5 border border-white/10 rounded-lg p-3">
                        <div class="text-[10px] text-gray-400 uppercase font-semibold">Lead Time (L<sub>t</sub>) & Buffer (B<sub>s</sub>)</div>
                        <div class="text-base font-bold text-white mt-1">{{ $stockoutRisk['lead_time_days'] }}d lead · {{ $stockoutRisk['buffer_stock'] }} min</div>
                        <div class="text-[10px] text-gray-400 mt-0.5">Safety threshold: {{ number_format($stockoutRisk['reorder_point'], 1) }}</div>
                    </div>

                    <div class="bg-white/5 border border-white/10 rounded-lg p-3">
                        <div class="text-[10px] text-gray-400 uppercase font-semibold">Triage Action</div>
                        <div class="text-xs font-bold text-emerald-200 mt-1 leading-snug">{{ $stockoutRisk['action'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Batches Section -->
            <div id="batches-table" class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h3 class="font-bold text-sm text-gray-800 uppercase tracking-wider">Stock Batches &amp; Deliveries</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Active batches are sorted in strict FEFO order (earliest expiry first).</p>
                    </div>
                    <span class="text-xs text-gray-500 font-medium">
                        {{ $medicine->stockBatches->count() }} Total {{ Str::plural('Batch', $medicine->stockBatches->count()) }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left">
                        <thead class="bg-gray-50 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3.5">Batch #</th>
                                <th class="px-6 py-3.5">Supplier</th>
                                <th class="px-6 py-3.5">Received Date</th>
                                <th class="px-6 py-3.5">Expiration Date</th>
                                <th class="px-6 py-3.5">Delivered Qty</th>
                                <th class="px-6 py-3.5">Usable Qty</th>
                                <th class="px-6 py-3.5">Status</th>
                                <th class="px-6 py-3.5">Expiry Risk (E<sub>r</sub>)</th>
                                <th class="px-6 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs">
                            @forelse ($medicine->stockBatches as $batch)
                                @php
                                    $isExpired = $batch->expiry_date->isPast();
                                    $isExpiringSoon = !$isExpired && $batch->expiry_date->diffInDays(now()) <= 30;
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition {{ $batch->isPending() ? 'bg-amber-50/30' : ($batch->quantity_remaining === 0 ? 'opacity-60 bg-gray-50/40' : '') }}">
                                    <td class="px-6 py-4 font-bold text-gray-900 font-mono">
                                        {{ $batch->batch_no }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $batch->supplier ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $batch->received_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold {{ $isExpired ? 'text-rose-600' : ($isExpiringSoon ? 'text-purple-600' : 'text-gray-900') }}">
                                            {{ $batch->expiry_date->format('M d, Y') }}
                                        </div>
                                        <div class="text-[10px] text-gray-400">
                                            @if ($isExpired)
                                                Expired {{ $batch->expiry_date->diffForHumans() }}
                                            @else
                                                Expires {{ $batch->expiry_date->diffForHumans() }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-700">
                                        {{ number_format($batch->quantity_received) }}
                                    </td>
                                    <td class="px-6 py-4 font-extrabold {{ $batch->isPending() ? 'text-amber-800' : ($batch->quantity_remaining > 0 ? 'text-gray-900' : 'text-gray-400') }}">
                                        @if ($batch->isPending())
                                            0 <span class="text-[10px] font-normal text-amber-700">({{ number_format($batch->quantity_received) }} pending)</span>
                                        @else
                                            {{ number_format($batch->quantity_remaining) }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($batch->isPending())
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                                <span>⏳ Pending Inspection</span>
                                            </span>
                                        @elseif ($batch->status === 'cancelled')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-rose-100 text-rose-700">
                                                Rejected
                                            </span>
                                        @elseif ($batch->quantity_remaining === 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-gray-100 text-gray-600">
                                                Depleted
                                            </span>
                                        @elseif ($isExpired)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-800">
                                                Expired
                                            </span>
                                        @elseif ($isExpiringSoon)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-100 text-purple-800">
                                                Expiring Soon
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                Active on Shelves
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $batchRisk = app(\App\Services\RiskPredictionService::class)->calculateExpiryRisk($batch, (float) $stockoutRisk['daily_consumption']);
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $batchRisk['badge_class'] }}">
                                            <span class="font-mono">{{ number_format($batchRisk['score'], 1) }}%</span>
                                            <span>{{ $batchRisk['label'] }}</span>
                                        </span>
                                        <div class="text-[10px] text-gray-400 mt-0.5" title="{{ $batchRisk['action'] }}">
                                            @if ($batch->quantity_remaining <= 0)
                                                Depleted
                                            @elseif ($batchRisk['days_until_expiry'] <= 0)
                                                Expired stock
                                            @elseif ($batchRisk['projected_loss_units'] > 0)
                                                Loss: ~{{ number_format($batchRisk['projected_loss_units']) }} {{ $medicine->unit }}
                                            @else
                                                Safe FEFO
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        @if ($batch->isPending())
                                            <!-- Pending Confirmation Actions -->
                                            <div class="inline-flex items-center gap-1.5">
                                                <form method="POST" action="{{ route('inventory.batches.confirm', $batch) }}">
                                                    @csrf
                                                    <button type="submit"
                                                            onclick="return confirm('Confirm inspection and add {{ $batch->quantity_received }} {{ $medicine->unit }} of batch {{ $batch->batch_no }} into usable pharmacy stock?')"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-xs transition cursor-pointer">
                                                        <svg class="w-3.5 h-3.5 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        <span>Confirm Receipt</span>
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('inventory.batches.reject', $batch) }}">
                                                    @csrf
                                                    <button type="submit"
                                                            onclick="return confirm('Reject this delivery?')"
                                                            class="px-2 py-1 rounded-md bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold text-[11px] transition cursor-pointer">
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                        @elseif ($batch->quantity_remaining > 0)
                                            <div class="inline-flex items-center gap-1.5">
                                                <button type="button"
                                                        @click="activeBatch = {{ $batch->id }}; activeBatchNo = '{{ $batch->batch_no }}'; activeRemaining = {{ $batch->quantity_remaining }}; adjustModal = true"
                                                        class="px-2.5 py-1 rounded bg-amber-50 hover:bg-amber-100 text-amber-800 font-semibold text-[11px] transition">
                                                    Adjust
                                                </button>
                                                <button type="button"
                                                        @click="activeBatch = {{ $batch->id }}; activeBatchNo = '{{ $batch->batch_no }}'; disposeModal = true"
                                                        class="px-2.5 py-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-800 font-semibold text-[11px] transition">
                                                    Dispose
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-[11px] text-gray-400 italic">No actions</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                                        No batches recorded for this medicine yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Movement Audit History -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-sm text-gray-800 uppercase tracking-wider">Recent Stock Movements</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Audit log of deliveries, dispensing deductions, disposals, and adjustments.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                        <thead class="bg-gray-50 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3">Timestamp</th>
                                <th class="px-6 py-3">Batch</th>
                                <th class="px-6 py-3">Type</th>
                                <th class="px-6 py-3">Quantity</th>
                                <th class="px-6 py-3">Reference / Reason</th>
                                <th class="px-6 py-3">Recorded By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($movements as $m)
                                <tr>
                                    <td class="px-6 py-3 text-gray-500 whitespace-nowrap">
                                        {{ $m->created_at->format('Y-m-d H:i') }}
                                    </td>
                                    <td class="px-6 py-3 font-semibold text-gray-800 font-mono">
                                        {{ $m->batch?->batch_no ?? '—' }}
                                    </td>
                                    <td class="px-6 py-3">
                                        @if ($m->type === 'in')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase">Received (In)</span>
                                        @elseif ($m->type === 'out')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 uppercase">Dispensed (Out)</span>
                                        @elseif ($m->type === 'disposal')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 uppercase">Disposal</span>
                                        @elseif ($m->type === 'adjustment')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 uppercase">Adjustment</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 font-extrabold text-gray-900">
                                        {{ $m->type === 'out' || $m->type === 'disposal' ? '-' : '+' }}{{ number_format($m->quantity) }}
                                    </td>
                                    <td class="px-6 py-3 text-gray-600">
                                        <div class="font-medium">{{ $m->reference_type }}</div>
                                        @if ($m->notes)
                                            <div class="text-[11px] text-gray-500 italic">{{ $m->notes }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-gray-600">
                                        {{ $m->createdBy?->name ?? 'System' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-6 text-center text-gray-400">
                                        No movements recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Dispose Batch Modal -->
        <div x-show="disposeModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="disposeModal" @click="disposeModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="disposeModal" class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-6">
                    <form :action="`/inventory/batches/${activeBatch}/dispose`" method="POST">
                        @csrf
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                ⚠️
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900">Dispose Batch <span x-text="activeBatchNo" class="text-rose-600 font-mono"></span></h3>
                                <p class="text-xs text-gray-500 mt-0.5">This will zero out remaining stock and permanently log a disposal record.</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="dispose_reason" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Reason for Disposal <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="reason" id="dispose_reason" rows="3" required placeholder="e.g., Expired, broken ampoule, compromised seal, recalled by FDA" class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-rose-500 focus:ring-rose-500"></textarea>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="disposeModal = false" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:text-gray-800 transition">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold shadow-sm transition">
                                Confirm Disposal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Adjust Stock Modal -->
        <div x-show="adjustModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="adjustModal" @click="adjustModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="adjustModal" class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-6">
                    <form :action="`/inventory/batches/${activeBatch}/adjust`" method="POST">
                        @csrf
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                                ⚖️
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900">Adjust Stock for <span x-text="activeBatchNo" class="text-amber-700 font-mono"></span></h3>
                                <p class="text-xs text-gray-500 mt-0.5">Correct quantity discrepancy from physical count audit.</p>
                            </div>
                        </div>

                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="adjust_quantity" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    New Count Quantity <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" min="0" name="new_quantity" id="adjust_quantity" :value="activeRemaining" required class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                            </div>

                            <div>
                                <label for="adjust_reason" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Adjustment Reason <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="reason" id="adjust_reason" rows="2" required placeholder="e.g., Monthly inventory physical count reconciliation" class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-amber-500 focus:ring-amber-500"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="adjustModal = false" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:text-gray-800 transition">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold shadow-sm transition">
                                Save Adjustment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Receive Batch Modal -->
        <div x-show="receiveModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="receiveModal" @click="receiveModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="receiveModal" class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-6">
                    <form action="{{ route('inventory.medicines.receive.store', $medicine) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-lg shrink-0">
                                    📦
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900">Log Delivery Batch</h3>
                                    <p class="text-xs text-gray-500">{{ trim(str_replace('(Out of Stock Demo)', '', $medicine->name)) }} ({{ $medicine->generic_name }})</p>
                                </div>
                            </div>
                            <button type="button" @click="receiveModal = false" class="text-gray-400 hover:text-gray-600 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Batch / Lot Number <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="batch_no" required placeholder="e.g. BATCH-2026-001" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <p class="text-[10px] text-gray-400 mt-0.5">Must be a unique batch number.</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Quantity ({{ $medicine->unit }}) <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" min="1" name="quantity_received" required placeholder="e.g. 100" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Supplier / Distributor
                                </label>
                                <input type="text" name="supplier" placeholder="e.g. Unilab Corp." class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Expiration Date <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" name="expiry_date" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Date Received <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" name="received_date" value="{{ now()->toDateString() }}" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Delivery Remarks / PO Number
                            </label>
                            <input type="text" name="notes" placeholder="e.g. PO #2026-0812, verified sealed packaging" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>

                        <div class="p-3 rounded-lg bg-gray-50 border border-gray-200 text-xs space-y-1">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="auto_confirm" value="1" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="font-semibold text-gray-800">Physical inspection verified (Add directly to active stock)</span>
                            </label>
                            <p class="text-[10px] text-gray-400 pl-5">Leave unchecked to save as pending delivery awaiting inspection.</p>
                        </div>

                        <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                            <a href="{{ route('inventory.medicines.receive', $medicine) }}" class="text-xs text-emerald-700 hover:underline font-semibold">
                                Full Page Form &rarr;
                            </a>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="receiveModal = false" class="px-3.5 py-2 text-xs font-semibold text-gray-600 hover:text-gray-800 transition">
                                    Cancel
                                </button>
                                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-sm transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>Save Delivery</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
