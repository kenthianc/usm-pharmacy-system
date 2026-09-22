<x-admin-layout active="audit-logs">
    <div class="space-y-6">

            <!-- Breadcrumbs / Header -->
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-green-800 font-medium">Admin Hub</a>
                        <span>/</span>
                        <span class="text-slate-800 font-bold">Audit Logs</span>
                    </div>
                    <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2.5">
                        <svg class="w-6 h-6 text-green-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Compliance Audit Trail &amp; Activity Log
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">Immutable security log tracking user management, inventory disposals, adjustments, and clinical operations.</p>
                </div>
            </div>

            <!-- Filters Bar -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
                <form method="GET" action="{{ route('admin.audit-logs') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    <div class="sm:col-span-4">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search by description or user..." class="w-full py-2 px-3 text-xs rounded-xl border-slate-300 focus:border-green-600 focus:ring-green-600 placeholder:text-slate-400">
                    </div>

                    <div class="sm:col-span-2">
                        <select name="module" class="w-full py-2 text-xs rounded-xl border-slate-300 focus:border-green-600 focus:ring-green-600">
                            <option value="">All Modules</option>
                            @foreach($modules as $m)
                                <option value="{{ $m }}" {{ $module === $m ? 'selected' : '' }}>{{ ucfirst($m) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <select name="action" class="w-full py-2 text-xs rounded-xl border-slate-300 focus:border-green-600 focus:ring-green-600">
                            <option value="">All Actions</option>
                            @foreach($actions as $a)
                                <option value="{{ $a }}" {{ $action === $a ? 'selected' : '' }}>{{ str_replace('_', ' ', $a) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <input type="date" name="start_date" value="{{ $startDate }}" placeholder="Start Date" class="w-full py-2 text-xs rounded-xl border-slate-300 focus:border-green-600 focus:ring-green-600">
                    </div>

                    <div class="sm:col-span-2 flex gap-2">
                        <button type="submit" class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition">
                            Filter
                        </button>
                        @if($search || $module || $action || $startDate || $endDate)
                            <a href="{{ route('admin.audit-logs') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition text-center">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider text-[11px] font-bold">
                            <tr>
                                <th scope="col" class="py-3.5 px-6">Timestamp</th>
                                <th scope="col" class="py-3.5 px-4">Actor</th>
                                <th scope="col" class="py-3.5 px-4">Module</th>
                                <th scope="col" class="py-3.5 px-4">Action</th>
                                <th scope="col" class="py-3.5 px-6">Description / Details</th>
                                <th scope="col" class="py-3.5 px-4 text-right">IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($logs as $log)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-6 whitespace-nowrap">
                                        <div class="font-bold text-slate-900">{{ $log->created_at->format('M d, Y H:i:s') }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $log->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-bold text-slate-800">{{ $log->user?->name ?? 'System' }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $log->user?->email ?? 'Automated Service' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($log->module === 'users')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-100 text-purple-800 uppercase font-mono">USERS</span>
                                        @elseif($log->module === 'inventory')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 uppercase font-mono">INVENTORY</span>
                                        @elseif($log->module === 'prescriptions')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 uppercase font-mono">RX</span>
                                        @elseif($log->module === 'pos')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800 uppercase font-mono">POS</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 uppercase font-mono">{{ $log->module }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="font-mono text-[11px] font-bold text-slate-700">{{ $log->action }}</span>
                                    </td>
                                    <td class="py-3.5 px-6">
                                        <div class="font-medium text-slate-800 leading-snug">{{ $log->description }}</div>
                                        @if($log->metadata)
                                            <details class="mt-1">
                                                <summary class="text-[10px] font-bold text-green-700 hover:text-green-900 cursor-pointer">
                                                    View Metadata Payload
                                                </summary>
                                                <pre class="mt-1 p-2 bg-slate-900 text-emerald-400 rounded-lg text-[10px] font-mono overflow-x-auto max-w-lg">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                            </details>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-[11px] text-slate-400 whitespace-nowrap">
                                        {{ $log->ip_address ?? '127.0.0.1' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400">
                                        No audit records found matching your filters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="p-4 border-t border-slate-200 bg-slate-50">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>

        </div>
</x-admin-layout>
