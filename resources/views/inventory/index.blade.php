<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-base shadow-sm">
                        📦
                    </span>
                    {{ __('Hospital Inventory & Stock Management') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">Real-time stock monitoring, batch allocation, deliveries, and formulary control.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('inventory.export-pdf') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50 shadow-xs transition">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Export Report</span>
                </a>
                @can('create', App\Models\Delivery::class)
                    <a href="{{ route('inventory.deliveries.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Request Delivery</span>
                    </a>
                @endcan
                <a href="{{ route('inventory.movements') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50 shadow-xs transition">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span>Delivery Logs</span>
                </a>
                @can('create', App\Models\Medicine::class)
                    <a href="{{ route('inventory.medicines.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-800 hover:bg-emerald-900 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Add New Medicine</span>
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
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

            <!-- 6 Overview KPI Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                <!-- Total Formulations -->
                <a href="{{ route('inventory.index') }}" class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs hover:border-gray-300 transition">
                    <div class="flex items-center justify-between text-gray-500">
                        <span class="text-xs font-semibold uppercase tracking-wider">Total Formulary</span>
                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600">
                            💊
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-extrabold text-gray-900">{{ $totalItems }}</div>
                        <p class="text-[11px] text-gray-400 mt-0.5">Catalogued medicines</p>
                    </div>
                </a>

                <!-- Healthy Stock -->
                <a href="{{ route('inventory.index', ['stock_status' => 'in_stock']) }}" class="bg-white p-4 rounded-xl border {{ $stockStatus === 'in_stock' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-emerald-200/80' }} shadow-xs hover:border-emerald-400 transition">
                    <div class="flex items-center justify-between text-emerald-700">
                        <span class="text-xs font-semibold uppercase tracking-wider">Healthy Stock</span>
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                            ✓
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-extrabold text-emerald-700">{{ $inStockCount }}</div>
                        <p class="text-[11px] text-emerald-600 mt-0.5">Above reorder level</p>
                    </div>
                </a>

                <!-- Low Stock Alert -->
                <a href="{{ route('inventory.index', ['stock_status' => 'low_stock']) }}" class="bg-white p-4 rounded-xl border {{ $stockStatus === 'low_stock' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-amber-200/80' }} shadow-xs hover:border-amber-400 transition">
                    <div class="flex items-center justify-between text-amber-700">
                        <span class="text-xs font-semibold uppercase tracking-wider">Low Stock</span>
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                            ⚠️
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-extrabold text-amber-700">{{ $lowStockCount }}</div>
                        <p class="text-[11px] text-amber-600 mt-0.5">At/below reorder limit</p>
                    </div>
                </a>

                <!-- Out of Stock -->
                <a href="{{ route('inventory.index', ['stock_status' => 'out_of_stock']) }}" class="bg-white p-4 rounded-xl border {{ $stockStatus === 'out_of_stock' ? 'border-rose-500 ring-2 ring-rose-500/20' : 'border-rose-200/80' }} shadow-xs hover:border-rose-400 transition">
                    <div class="flex items-center justify-between text-rose-700">
                        <span class="text-xs font-semibold uppercase tracking-wider">Out of Stock</span>
                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                            ✕
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-extrabold text-rose-700">{{ $outOfStockCount }}</div>
                        <p class="text-[11px] text-rose-600 mt-0.5">0 units available</p>
                    </div>
                </a>

                <!-- Expiring Soon (30 days) -->
                <div class="bg-white p-4 rounded-xl border border-purple-200/80 shadow-xs">
                    <div class="flex items-center justify-between text-purple-700">
                        <span class="text-xs font-semibold uppercase tracking-wider">Expiring (30d)</span>
                        <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                            ⏳
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-extrabold text-purple-700">{{ $expiringSoonCount }}</div>
                        <p class="text-[11px] text-purple-600 mt-0.5">Active batches expiring</p>
                    </div>
                </div>

                <!-- High Stockout Risk (Dual Risk Engine) -->
                <a href="{{ route('inventory.index', ['stock_status' => 'high_risk']) }}" class="bg-white p-4 rounded-xl border {{ $stockStatus === 'high_risk' ? 'border-red-500 ring-2 ring-red-500/20' : 'border-red-200/80' }} shadow-xs hover:border-red-400 transition">
                    <div class="flex items-center justify-between text-red-700">
                        <span class="text-xs font-semibold uppercase tracking-wider">High Risk ($S_r$)</span>
                        <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center font-bold">
                            ⚡
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-extrabold text-red-700">{{ $highRiskCount }}</div>
                        <p class="text-[11px] text-red-600 mt-0.5">Predicted stockouts</p>
                    </div>
                </a>
            </div>

            <!-- 3 Valuation & Profit Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Total Stock Value -->
                <div class="bg-white p-4 rounded-xl border border-blue-200/80 shadow-xs hover:border-blue-300 transition">
                    <div class="flex items-center justify-between text-blue-700">
                        <span class="text-xs font-semibold uppercase tracking-wider">Total Stock Value</span>
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                            ₱
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-extrabold text-blue-900">₱{{ number_format($totalStockValue, 2) }}</div>
                        <p class="text-[11px] text-blue-600 mt-0.5">Sum of (Stock Qty &times; Cost Price)</p>
                    </div>
                </div>

                <!-- Total Sale Value -->
                <div class="bg-white p-4 rounded-xl border border-indigo-200/80 shadow-xs hover:border-indigo-300 transition">
                    <div class="flex items-center justify-between text-indigo-700">
                        <span class="text-xs font-semibold uppercase tracking-wider">Total Sale Value</span>
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                            🏷️
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-extrabold text-indigo-900">₱{{ number_format($totalSaleValue, 2) }}</div>
                        <p class="text-[11px] text-indigo-600 mt-0.5">Sum of (Stock Qty &times; Selling Price)</p>
                    </div>
                </div>

                <!-- Expected Profit -->
                <div class="bg-white p-4 rounded-xl border border-emerald-200/80 shadow-xs hover:border-emerald-300 transition">
                    <div class="flex items-center justify-between text-emerald-700">
                        <span class="text-xs font-semibold uppercase tracking-wider">Expected Profit</span>
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                            📈
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-extrabold {{ $expectedProfit >= 0 ? 'text-emerald-800' : 'text-rose-700' }}">₱{{ number_format($expectedProfit, 2) }}</div>
                        <p class="text-[11px] text-emerald-600 mt-0.5">Total Sale Value &minus; Total Stock Value</p>
                    </div>
                </div>
            </div>

            <!-- Search, Filter & Medicine Catalog Table -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
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
                                <th class="px-6 py-3.5">Category & Unit</th>
                                <th class="px-6 py-3.5">Unit Price</th>
                                <th class="px-6 py-3.5">Reorder Limit</th>
                                <th class="px-6 py-3.5">Current Stock</th>
                                <th class="px-6 py-3.5">Status</th>
                                <th class="px-6 py-3.5">Stockout Risk (S<sub>r</sub>)</th>
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
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $med->stockout_risk_badge_class }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $med->stockout_risk_category === 'high' ? 'bg-rose-500 animate-pulse' : ($med->stockout_risk_category === 'moderate' ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                                                <span class="font-mono">{{ number_format($med->stockout_risk_score, 1) }}%</span>
                                                <span>{{ $med->stockout_risk_label }}</span>
                                            </span>
                                            <div class="text-[10px] text-gray-400 mt-0.5">
                                                Burn: {{ number_format($med->daily_consumption_rate, 1) }} /day
                                            </div>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-gray-100 text-gray-500 italic">
                                                Calc pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="{{ route('inventory.deliveries.create', ['medicine_id' => $med->id]) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-semibold text-xs transition" title="Request restock delivery">
                                                <span>+ Restock</span>
                                            </a>
                                            <a href="{{ route('inventory.medicines.show', $med) }}" class="p-1.5 rounded-lg text-gray-500 hover:text-emerald-700 hover:bg-gray-100 transition" title="View details & batches">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            @can('update', $med)
                                                <a href="{{ route('inventory.medicines.edit', $med) }}" class="p-1.5 rounded-lg text-gray-500 hover:text-amber-700 hover:bg-gray-100 transition" title="Edit formulary item">
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
    </div>
</x-app-layout>
