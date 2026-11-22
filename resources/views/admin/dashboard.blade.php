<x-admin-layout active="dashboard">
    <div class="space-y-6">
        
        <!-- Top Header & Banner -->
        <div class="rounded-2xl p-6 sm:p-8 shadow-xl border-b-4 border-yellow-500 relative overflow-hidden"
             style="background: linear-gradient(135deg, #064e2b 0%, #0b5c35 50%, #043c20 100%); color: #ffffff;">
            <div class="absolute right-0 top-0 translate-x-8 -translate-y-8 w-64 h-64 bg-yellow-500/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold mb-3 shadow-xs"
                         style="background: rgba(234, 179, 8, 0.2); border: 1px solid rgba(234, 179, 8, 0.4); color: #fef08a;">
                        <span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
                        Executive Governance &amp; Compliance
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white flex flex-wrap items-center gap-3">
                        <span>USM Clinic Admin Center</span>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-yellow-400 text-green-950 shadow-xs">Chief Pharmacist / Admin</span>
                    </h1>
                    <p class="mt-2 text-sm max-w-2xl" style="color: #d1fae5;">
                        Real-time oversight of university infirmary staff, medicine valuation, dispensing audit trails, and inventory spoilage logs.
                    </p>
                </div>

                <!-- Quick Navigation Badges -->
                <div class="flex flex-wrap gap-2.5">
                    <a href="{{ route('admin.users.index') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl shadow-md transition transform hover:-translate-y-0.5"
                       style="background-color: #eab308; color: #052e16;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        Manage Staff Users
                    </a>
                    <a href="{{ route('admin.audit-logs') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 text-white text-xs font-bold rounded-xl shadow-md transition"
                       style="background-color: rgba(6, 78, 43, 0.85); border: 1px solid rgba(16, 185, 129, 0.5);">
                        <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Audit Trail
                    </a>
                    <a href="{{ route('admin.reports') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 text-white text-xs font-bold rounded-xl shadow-md transition"
                       style="background-color: #047857; border: 1px solid #10b981;">
                        <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        Analytics
                    </a>
                </div>
            </div>
        </div>

        <!-- Status notification if any -->
        @if (session('status'))
            <div class="p-4 rounded-xl bg-emerald-50 border-l-4 border-emerald-600 text-emerald-800 text-sm font-medium shadow-xs flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- 4 Top KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            
            <!-- Card 1: Staff Governance -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 hover:border-green-300 transition group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Clinic Personnel</span>
                    <div class="w-10 h-10 rounded-xl bg-green-100 text-green-800 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-extrabold text-slate-900">{{ $staffCounts['active_users'] }}</div>
                    <p class="text-xs text-slate-500 mt-1">Active Staff &amp; Patient Accounts</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap gap-1.5 text-[11px]">
                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-semibold border border-emerald-100">{{ $staffCounts['nurses'] }} Nurses</span>
                    <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-semibold border border-blue-100">{{ $staffCounts['pharmacists'] }} Pharmacists</span>
                    <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 font-semibold border border-amber-100">{{ $staffCounts['stock_managers'] }} Stock</span>
                    <span class="px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 font-semibold border border-purple-100">{{ $staffCounts['admins'] }} Admins</span>
                </div>
            </div>

            <!-- Card 2: Total Inventory Valuation -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 hover:border-green-300 transition group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Pharmacy Asset Value</span>
                    <div class="w-10 h-10 rounded-xl bg-yellow-100 text-yellow-800 flex items-center justify-center font-bold">
                        ₱
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-extrabold text-slate-900">₱{{ number_format($inventoryCounts['valuation'], 2) }}</div>
                    <p class="text-xs text-slate-500 mt-1">{{ $inventoryCounts['total_medicines'] }} Products in Formulary</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-emerald-700 font-medium">In Stock: {{ $inventoryCounts['in_stock'] }}</span>
                    @if($inventoryCounts['low_stock'] > 0 || $inventoryCounts['out_of_stock'] > 0)
                        <span class="text-rose-600 font-bold">{{ $inventoryCounts['low_stock'] + $inventoryCounts['out_of_stock'] }} Alerts</span>
                    @else
                        <span class="text-slate-400">Stock Healthy</span>
                    @endif
                </div>
            </div>

            <!-- Card 3: Prescriptions & POS Sales -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 hover:border-green-300 transition group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Dispensing &amp; OTC Sales</span>
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-extrabold text-slate-900">₱{{ number_format($posStats['total_sales'], 2) }}</div>
                    <p class="text-xs text-slate-500 mt-1">{{ $posStats['transaction_count'] }} OTC Transactions Processed</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-blue-700 font-semibold">{{ $rxCounts['dispensed'] }} Prescriptions Filled</span>
                    <span class="text-amber-600 font-medium">{{ $rxCounts['pending'] + $rxCounts['routed'] }} In Progress</span>
                </div>
            </div>

            <!-- Card 4: Spoilage & Loss Control -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 hover:border-green-300 transition group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Spoilage &amp; Losses</span>
                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-extrabold text-rose-600">₱{{ number_format($totalLoss, 2) }}</div>
                    <p class="text-xs text-slate-500 mt-1">Total Disposed / Expired Value</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-600 font-medium">Expiring in 30d:</span>
                    <span class="px-2 py-0.5 rounded font-bold {{ $inventoryCounts['expiring_soon'] > 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">
                        {{ $inventoryCounts['expiring_soon'] }} Batches
                    </span>
                </div>
            </div>

        </div>

        <!-- Two Column Split: Audit Trail vs Warehouse Transparency -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            
            <!-- Left: Recent System Audit Trail (2 Cols on XL) -->
            <div class="xl:col-span-2 min-w-0 bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100 gap-4">
                    <div class="min-w-0">
                        <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2 truncate">
                            <svg class="w-5 h-5 text-green-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                            <span class="truncate">Recent Security &amp; Audit Logs</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5 truncate">Live compliance log of administrative actions, batch write-offs, and user edits.</p>
                    </div>
                    <a href="{{ route('admin.audit-logs') }}" class="text-xs font-bold text-green-800 hover:text-green-950 shrink-0 flex items-center gap-1">
                        View All &rarr;
                    </a>
                </div>

                @if($recentAuditLogs->isEmpty())
                    <div class="py-12 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        <p class="text-sm font-medium">No audit logs recorded yet.</p>
                        <p class="text-xs text-slate-400 mt-1">Actions performed by clinic admins and managers will appear here automatically.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($recentAuditLogs as $log)
                            <div class="py-3.5 flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                        {{ strtoupper(substr($log->user?->name ?? 'SYS', 0, 2)) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-sm font-semibold text-slate-900 leading-snug">
                                            {{ $log->description }}
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2 mt-1 text-xs text-slate-500">
                                            <span class="font-medium text-slate-700">{{ $log->user?->name ?? 'System' }}</span>
                                            <span>&bull;</span>
                                            <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-mono text-[10px] uppercase font-bold">{{ $log->module }}</span>
                                            @if($log->ip_address)
                                                <span>&bull;</span>
                                                <span class="font-mono text-[10px] text-slate-400">{{ $log->ip_address }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-xs text-slate-400 whitespace-nowrap">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Right: Recent Critical Stock Movements (1 Col on XL) -->
            <div class="xl:col-span-1 min-w-0 bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 gap-2">
                        <div class="min-w-0">
                            <h3 class="text-base font-extrabold text-slate-900 truncate">Stock Dispatches</h3>
                            <p class="text-xs text-slate-500 truncate">Warehouse adjustments &amp; receipts</p>
                        </div>
                        <a href="{{ route('inventory.movements') }}" class="text-xs font-bold text-green-700 hover:text-green-900 shrink-0">Movements &rarr;</a>
                    </div>

                    <div class="space-y-3">
                        @forelse($recentMovements as $mv)
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs flex items-center justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <p class="font-bold text-slate-800 truncate">{{ $mv->medicine?->name ?? 'Medicine' }}</p>
                                    <p class="text-slate-500 text-[11px] mt-0.5 truncate">
                                        Batch: <span class="font-mono">{{ $mv->batch?->batch_number ?? 'N/A' }}</span>
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-extrabold px-2 py-0.5 rounded text-[11px] {{ $mv->quantity > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $mv->quantity > 0 ? '+' : '' }}{{ $mv->quantity }}
                                    </span>
                                    <p class="text-[10px] text-slate-400 mt-1 uppercase font-semibold">{{ $mv->type }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 text-center py-6">No stock movements recorded.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Admin Help / Guidance box -->
                <div class="mt-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-950">
                    <div class="flex items-center gap-2 font-bold text-xs text-green-900">
                        <svg class="w-4 h-4 text-green-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Super-Admin Privilege</span>
                    </div>
                    <p class="text-[11px] text-green-800 mt-1 leading-relaxed">
                        As Administrator, you have full access across Prescriptions, Pharmacy POS, and Warehouse Inventory modules.
                    </p>
                </div>
            </div>

        </div>

    </div>
</x-admin-layout>
