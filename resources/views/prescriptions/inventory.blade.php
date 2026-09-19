<x-nurse-layout>
    <x-slot name="heading">
        <div>
            <h1 class="text-xl font-bold text-slate-800 tracking-tight">Inventory Check</h1>
            <p class="text-xs text-slate-500 mt-0.5">Real-time dispensary stock levels, active batch numbers, and medicine availability.</p>
        </div>
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('prescriptions.create') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-sm shadow-emerald-700/20 transition transform active:scale-98">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Encode Prescription</span>
        </a>
    </x-slot>

    <div class="space-y-6">

        <!-- 4 KPI Overview Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Catalog Items -->
            <a href="{{ route('prescriptions.inventory') }}"
               class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500">Total Medicines</span>
                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-extrabold text-slate-800">{{ $totalItems }}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Cataloged formulations</div>
                </div>
            </a>

            <!-- In Stock -->
            <a href="{{ route('prescriptions.inventory', ['stock_status' => 'in_stock']) }}"
               class="bg-white p-4 rounded-2xl border border-emerald-200/80 shadow-xs hover:border-emerald-400 transition group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-700">Healthy Stock</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-extrabold text-emerald-600">{{ $inStockCount }}</div>
                    <div class="text-[11px] text-emerald-600/80 mt-0.5 font-medium">&gt; 20 units available</div>
                </div>
            </a>

            <!-- Low Stock -->
            <a href="{{ route('prescriptions.inventory', ['stock_status' => 'low_stock']) }}"
               class="bg-white p-4 rounded-2xl border border-amber-200/80 shadow-xs hover:border-amber-400 transition group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-amber-700">Low Stock</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-extrabold text-amber-600">{{ $lowStockCount }}</div>
                    <div class="text-[11px] text-amber-600/80 mt-0.5 font-medium">1 – 20 units remaining</div>
                </div>
            </a>

            <!-- Out of Stock -->
            <a href="{{ route('prescriptions.inventory', ['stock_status' => 'out_of_stock']) }}"
               class="bg-white p-4 rounded-2xl border border-rose-200/80 shadow-xs hover:border-rose-400 transition group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-rose-700">Out of Stock</span>
                    <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-extrabold text-rose-600">{{ $outOfStockCount }}</div>
                    <div class="text-[11px] text-rose-600/80 mt-0.5 font-medium">0 active stock</div>
                </div>
            </a>
        </div>

        <!-- Filter & Search Controls Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5 space-y-4">
            <!-- Stock Status Tabs -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none border-b border-slate-100">
                @php
                    $tabs = [
                        'all' => ['label' => 'All Medicines', 'count' => $totalItems],
                        'in_stock' => ['label' => 'In Stock', 'count' => $inStockCount],
                        'low_stock' => ['label' => 'Low Stock Warning', 'count' => $lowStockCount],
                        'out_of_stock' => ['label' => 'Out of Stock', 'count' => $outOfStockCount],
                    ];
                @endphp

                @foreach ($tabs as $key => $tab)
                    @php
                        $isActive = ($stockStatus === $key || ($key === 'all' && (empty($stockStatus) || $stockStatus === 'all')));
                    @endphp
                    <a href="{{ route('prescriptions.inventory', array_merge(request()->except('page'), ['stock_status' => $key])) }}"
                       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all {{ $isActive ? 'bg-[#064e2b] text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <span>{{ $tab['label'] }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $isActive ? 'bg-amber-400 text-[#064e2b]' : 'bg-slate-200 text-slate-700' }}">
                            {{ $tab['count'] }}
                        </span>
                    </a>
                @endforeach
            </div>

            <!-- Search and Category Filter Form -->
            <form method="GET" action="{{ route('prescriptions.inventory') }}" class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                @if ($stockStatus && $stockStatus !== 'all')
                    <input type="hidden" name="stock_status" value="{{ $stockStatus }}">
                @endif

                <div class="relative flex-1">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Search by brand name, generic name..."
                           class="w-full pl-10 pr-4 py-2 text-xs rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-slate-800 placeholder-slate-400">
                </div>

                <div class="flex items-center gap-2">
                    <select name="category"
                            onchange="this.form.submit()"
                            class="text-xs py-2 pl-3 pr-8 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-slate-700 bg-white">
                        <option value="">All Categories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold transition">
                        Filter
                    </button>

                    @if ($search || $category || ($stockStatus && $stockStatus !== 'all'))
                        <a href="{{ route('prescriptions.inventory') }}" class="px-3 py-2 text-xs font-medium text-slate-500 hover:text-slate-800 transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Clean Straightforward Medicine Stock Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Dispensary Medicine Catalog</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Showing {{ $medicines->count() }} formulation{{ $medicines->count() === 1 ? '' : 's' }}</p>
                </div>
                <span class="text-xs font-bold text-slate-600 bg-slate-100 px-3 py-1 rounded-xl">
                    FEFO Managed Batches
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th scope="col" class="px-6 py-3.5">#</th>
                            <th scope="col" class="px-6 py-3.5">Medicine Formulation</th>
                            <th scope="col" class="px-6 py-3.5">Category</th>
                            <th scope="col" class="px-6 py-3.5">Available Stock</th>
                            <th scope="col" class="px-6 py-3.5">Status</th>
                            <th scope="col" class="px-6 py-3.5">Active Batches &amp; Expiry</th>
                            <th scope="col" class="px-6 py-3.5">Unit Price</th>
                            <th scope="col" class="px-6 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($medicines as $index => $medicine)
                            @php
                                $stock = $medicine->available_stock;
                                $batches = $medicine->stockBatches;
                                $nextExpiry = $batches->first()?->expiry_date?->format('M d, Y');
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-6 py-4 text-slate-400 font-semibold">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 text-sm">{{ $medicine->name }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">{{ $medicine->generic_name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                                        {{ $medicine->category }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 text-sm">
                                        {{ $stock }} <span class="text-xs font-normal text-slate-500">{{ $medicine->unit }}{{ $stock > 1 ? 's' : '' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($stock > 20)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>In Stock</span>
                                        </span>
                                    @elseif ($stock > 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            <span>Low Stock</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            <span>Out of Stock</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    @if ($batches->isNotEmpty())
                                        <div class="space-y-0.5">
                                            <div class="font-medium text-slate-800">{{ $batches->count() }} active {{ Str::plural('batch', $batches->count()) }}</div>
                                            @if ($nextExpiry)
                                                <div class="text-[11px] text-slate-500">Next expiry: <strong class="text-slate-700">{{ $nextExpiry }}</strong></div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">No active batches</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    &#8369;{{ number_format($medicine->unit_price, 2) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('prescriptions.create') }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200 transition">
                                        <span>Prescribe</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                    <p class="font-bold text-slate-800 text-sm">No medicines found</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Try clearing the search or category filter.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-nurse-layout>
