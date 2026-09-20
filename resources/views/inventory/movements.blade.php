<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-base shadow-sm">
                        📋
                    </span>
                    {{ __('Stock Movements & Delivery Logs') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">Multi-medicine delivery restocking, pending delivery inspections, and immutable inventory movement audit trail.</p>
            </div>
            <div class="flex items-center gap-2">
                @can('create', App\Models\Delivery::class)
                    <a href="{{ route('inventory.deliveries.create') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Request Delivery</span>
                    </a>
                @endcan
                <a href="{{ route('inventory.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold transition">
                    Back to Inventory
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

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

            <!-- 4 Delivery Status KPI Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('inventory.movements') }}"
                   class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs hover:border-gray-300 transition">
                    <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Deliveries</div>
                    <div class="text-2xl font-black text-gray-900 mt-1">{{ number_format($deliveryCounts['all']) }}</div>
                    <div class="text-[11px] text-gray-500 mt-0.5">All restock shipments</div>
                </a>

                <a href="{{ route('inventory.movements', ['delivery_status' => 'pending']) }}"
                   class="bg-white p-4 rounded-xl border {{ $deliveryStatus === 'pending' ? 'border-amber-400 ring-2 ring-amber-200' : 'border-gray-200' }} shadow-xs hover:border-amber-300 transition">
                    <div class="text-[11px] font-bold text-amber-600 uppercase tracking-wider flex items-center justify-between">
                        <span>Pending Inspection</span>
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    </div>
                    <div class="text-2xl font-black text-amber-700 mt-1">{{ number_format($deliveryCounts['pending']) }}</div>
                    <div class="text-[11px] text-amber-600 mt-0.5">Awaiting physical confirmation</div>
                </a>

                <a href="{{ route('inventory.movements', ['delivery_status' => 'delivered']) }}"
                   class="bg-white p-4 rounded-xl border {{ $deliveryStatus === 'delivered' ? 'border-emerald-400 ring-2 ring-emerald-200' : 'border-gray-200' }} shadow-xs hover:border-emerald-300 transition">
                    <div class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Delivered / Received</div>
                    <div class="text-2xl font-black text-emerald-700 mt-1">{{ number_format($deliveryCounts['delivered']) }}</div>
                    <div class="text-[11px] text-emerald-600 mt-0.5">Added to usable stock</div>
                </a>

                <a href="{{ route('inventory.movements', ['delivery_status' => 'cancelled']) }}"
                   class="bg-white p-4 rounded-xl border {{ $deliveryStatus === 'cancelled' ? 'border-rose-400 ring-2 ring-rose-200' : 'border-gray-200' }} shadow-xs hover:border-rose-300 transition">
                    <div class="text-[11px] font-bold text-rose-600 uppercase tracking-wider">Canceled</div>
                    <div class="text-2xl font-black text-rose-700 mt-1">{{ number_format($deliveryCounts['cancelled']) }}</div>
                    <div class="text-[11px] text-rose-600 mt-0.5">Zero stock added</div>
                </a>
            </div>

            <!-- SECTION 1: Delivery Shipments & Restock Requests Table -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <span>📦</span>
                            <span>{{ __('Delivery Restock Requests') }}</span>
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">Deliveries remain in <strong>Pending</strong> until an Admin or Stock Manager confirms physical receipt.</p>
                    </div>

                    <!-- Filter Pills for Delivery Status -->
                    <div class="flex flex-wrap items-center gap-1.5">
                        <a href="{{ route('inventory.movements', array_filter(['search' => $search, 'type' => $type])) }}"
                           class="px-3 py-1 rounded-lg text-xs font-semibold transition {{ empty($deliveryStatus) ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            All Statuses
                        </a>
                        <a href="{{ route('inventory.movements', array_filter(['delivery_status' => 'pending', 'search' => $search, 'type' => $type])) }}"
                           class="px-3 py-1 rounded-lg text-xs font-semibold transition {{ $deliveryStatus === 'pending' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-800 hover:bg-amber-100' }}">
                            Pending ({{ $deliveryCounts['pending'] }})
                        </a>
                        <a href="{{ route('inventory.movements', array_filter(['delivery_status' => 'delivered', 'search' => $search, 'type' => $type])) }}"
                           class="px-3 py-1 rounded-lg text-xs font-semibold transition {{ $deliveryStatus === 'delivered' ? 'bg-emerald-700 text-white' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100' }}">
                            Delivered ({{ $deliveryCounts['delivered'] }})
                        </a>
                        <a href="{{ route('inventory.movements', array_filter(['delivery_status' => 'cancelled', 'search' => $search, 'type' => $type])) }}"
                           class="px-3 py-1 rounded-lg text-xs font-semibold transition {{ $deliveryStatus === 'cancelled' ? 'bg-rose-700 text-white' : 'bg-rose-50 text-rose-800 hover:bg-rose-100' }}">
                            Canceled ({{ $deliveryCounts['cancelled'] }})
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                        <thead class="bg-gray-50 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3.5">Reference #</th>
                                <th class="px-6 py-3.5">Supplier &amp; Date</th>
                                <th class="px-6 py-3.5">Medicines &amp; Requested Quantities</th>
                                <th class="px-6 py-3.5">Status</th>
                                <th class="px-6 py-3.5">Audit Trail</th>
                                <th class="px-6 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($deliveries as $del)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <!-- Reference # -->
                                    <td class="px-6 py-4 whitespace-nowrap align-top">
                                        <div class="font-mono font-bold text-gray-900 text-xs">{{ $del->reference_no }}</div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">ID: #{{ $del->id }}</div>
                                        @if ($del->notes)
                                            <div class="text-[10px] text-gray-500 italic max-w-xs mt-1">{{ Str::limit($del->notes, 60) }}</div>
                                        @endif
                                    </td>

                                    <!-- Supplier & Date -->
                                    <td class="px-6 py-4 whitespace-nowrap align-top">
                                        <div class="font-semibold text-gray-800">{{ $del->supplier ?: '—' }}</div>
                                        <div class="text-[11px] text-gray-500 mt-0.5">
                                            Arrival: {{ $del->delivery_date?->format('M d, Y') ?? '—' }}
                                        </div>
                                    </td>

                                    <!-- Medicines & Quantities list -->
                                    <td class="px-6 py-4 align-top">
                                        <div class="space-y-1.5 max-w-md">
                                            @foreach ($del->items as $item)
                                                <div class="flex items-center justify-between text-xs py-0.5 border-b border-gray-50 last:border-0">
                                                    <span class="font-medium text-gray-800">
                                                        <a href="{{ route('inventory.medicines.show', $item->medicine) }}" class="hover:text-emerald-700 hover:underline">
                                                            {{ $item->medicine->name }}
                                                        </a>
                                                        <span class="text-[11px] text-gray-400">({{ $item->medicine->generic_name }})</span>
                                                    </span>
                                                    <span class="font-bold text-gray-900 whitespace-nowrap ml-3">
                                                        {{ number_format($item->quantity) }} <span class="text-[10px] text-gray-500 font-normal">{{ $item->medicine->unit }}</span>
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="text-[10px] text-gray-400 mt-1">
                                            Total: <strong>{{ $del->items->count() }}</strong> items &bull; <strong>{{ number_format($del->total_quantity) }}</strong> units
                                        </div>
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="px-6 py-4 whitespace-nowrap align-top">
                                        @if ($del->isPending())
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Pending
                                            </span>
                                            <div class="text-[10px] text-amber-700 mt-1 font-medium">Awaiting Inspection</div>
                                        @elseif ($del->isDelivered())
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-900 border border-emerald-300">
                                                <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Delivered / Received
                                            </span>
                                            <div class="text-[10px] text-emerald-700 mt-1 font-medium">Stock Updated</div>
                                        @elseif ($del->isCancelled())
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-300">
                                                Canceled
                                            </span>
                                            <div class="text-[10px] text-gray-400 mt-1 italic">No Stock Change</div>
                                        @endif
                                    </td>

                                    <!-- Audit Trail -->
                                    <td class="px-6 py-4 whitespace-nowrap text-[11px] align-top">
                                        <div>
                                            <span class="text-gray-400">Requested:</span>
                                            <span class="font-medium text-gray-700">{{ $del->createdBy?->name ?? 'System' }}</span>
                                        </div>
                                        <div class="text-[10px] text-gray-400">{{ $del->created_at->format('Y-m-d H:i') }}</div>

                                        @if ($del->received_by)
                                            <div class="mt-1.5 pt-1.5 border-t border-gray-100">
                                                <span class="text-gray-400">{{ $del->isDelivered() ? 'Confirmed:' : 'Handled by:' }}</span>
                                                <span class="font-medium text-gray-700">{{ $del->receivedBy?->name ?? 'Staff' }}</span>
                                                <div class="text-[10px] text-gray-400">{{ $del->confirmed_at?->format('Y-m-d H:i') }}</div>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Action Buttons -->
                                    <td class="px-6 py-4 text-right whitespace-nowrap align-top">
                                        @can('confirm', $del)
                                            @if ($del->isPending())
                                                <div class="inline-flex flex-col sm:flex-row items-end sm:items-center gap-1.5">
                                                    <!-- Confirm Delivery Button -->
                                                    <form method="POST" action="{{ route('inventory.deliveries.confirm', $del) }}">
                                                        @csrf
                                                        <button type="submit"
                                                                onclick="return confirm('Confirm physical receipt of delivery {{ $del->reference_no }}?\n\nThis will automatically add all {{ $del->items->count() }} medicine(s) ({{ $del->total_quantity }} units) into active pharmacy stock.')"
                                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition cursor-pointer">
                                                            <svg class="w-3.5 h-3.5 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                            <span>Confirm Delivery</span>
                                                        </button>
                                                    </form>

                                                    <!-- Cancel Delivery Button -->
                                                    <form method="POST" action="{{ route('inventory.deliveries.cancel', $del) }}">
                                                        @csrf
                                                        <button type="submit"
                                                                onclick="return confirm('Cancel delivery request {{ $del->reference_no }}?\n\nNo stock will be added.')"
                                                                class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold text-xs transition cursor-pointer">
                                                            Cancel
                                                        </button>
                                                    </form>
                                                </div>
                                            @elseif ($del->isDelivered())
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-emerald-50 text-emerald-800 text-[11px] font-semibold">
                                                    ✓ Stock Added
                                                </span>
                                            @else
                                                <span class="text-[11px] text-gray-400 italic">No actions</span>
                                            @endif
                                        @else
                                            <span class="text-[11px] text-gray-400 italic">Read-only</span>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                                        <div class="text-2xl mb-1">📦</div>
                                        <div class="font-semibold text-gray-700">No delivery restock requests found.</div>
                                        <p class="text-xs text-gray-400 mt-1">Click "Request Delivery" above to log a new multi-medicine shipment.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($deliveries->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $deliveries->links() }}
                    </div>
                @endif
            </div>

            <!-- SECTION 2: Stock Movement Audit Trail -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <span>📝</span>
                            <span>{{ __('Stock Movement Audit Trail') }}</span>
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">Itemized record of stock additions (deliveries), dispensations, disposals, and count corrections.</p>
                    </div>
                </div>

                <!-- Type Filter Tabs & Search Bar -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-4 sm:p-5">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <!-- Tabs -->
                        <div class="flex flex-wrap items-center gap-1.5">
                            <a href="{{ route('inventory.movements', array_filter(['search' => $search, 'delivery_status' => $deliveryStatus])) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ empty($type) ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                All Movements
                            </a>
                            <a href="{{ route('inventory.movements', array_filter(['type' => 'in', 'search' => $search, 'delivery_status' => $deliveryStatus])) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $type === 'in' ? 'bg-emerald-700 text-white' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100' }}">
                                Deliveries (In)
                            </a>
                            <a href="{{ route('inventory.movements', array_filter(['type' => 'out', 'search' => $search, 'delivery_status' => $deliveryStatus])) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $type === 'out' ? 'bg-blue-700 text-white' : 'bg-blue-50 text-blue-800 hover:bg-blue-100' }}">
                                Dispensed (Out)
                            </a>
                            <a href="{{ route('inventory.movements', array_filter(['type' => 'disposal', 'search' => $search, 'delivery_status' => $deliveryStatus])) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $type === 'disposal' ? 'bg-rose-700 text-white' : 'bg-rose-50 text-rose-800 hover:bg-rose-100' }}">
                                Disposals
                            </a>
                            <a href="{{ route('inventory.movements', array_filter(['type' => 'adjustment', 'search' => $search, 'delivery_status' => $deliveryStatus])) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $type === 'adjustment' ? 'bg-amber-700 text-white' : 'bg-amber-50 text-amber-800 hover:bg-amber-100' }}">
                                Adjustments
                            </a>
                        </div>

                        <!-- Search Form -->
                        <form method="GET" action="{{ route('inventory.movements') }}" class="flex items-center gap-2">
                            @if ($type)
                                <input type="hidden" name="type" value="{{ $type }}">
                            @endif
                            @if ($deliveryStatus)
                                <input type="hidden" name="delivery_status" value="{{ $deliveryStatus }}">
                            @endif
                            <div class="relative min-w-[220px]">
                                <input type="text" name="search" value="{{ $search }}" placeholder="Search medicine, PO, supplier..." class="w-full pl-3 pr-3 py-1.5 bg-white border border-gray-300 rounded-lg text-xs focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                            <button type="submit" class="px-3 py-1.5 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-xs font-semibold transition">
                                Search
                            </button>
                            @if ($search || $type || $deliveryStatus)
                                <a href="{{ route('inventory.movements') }}" class="text-xs text-gray-500 hover:text-gray-800 px-2 py-1.5">
                                    Reset
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                <!-- Movements Table -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                            <thead class="bg-gray-50 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-3.5">Timestamp</th>
                                    <th class="px-6 py-3.5">Medicine</th>
                                    <th class="px-6 py-3.5">Batch #</th>
                                    <th class="px-6 py-3.5">Type</th>
                                    <th class="px-6 py-3.5">Quantity</th>
                                    <th class="px-6 py-3.5">Reference / Notes</th>
                                    <th class="px-6 py-3.5">Recorded By</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($movements as $m)
                                    <tr class="hover:bg-gray-50/80 transition">
                                        <td class="px-6 py-3.5 text-gray-500 whitespace-nowrap">
                                            {{ $m->created_at->format('Y-m-d H:i:s') }}
                                            <div class="text-[10px] text-gray-400">{{ $m->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="px-6 py-3.5">
                                            <a href="{{ route('inventory.medicines.show', $m->medicine) }}" class="font-bold text-gray-900 hover:text-emerald-700">
                                                {{ $m->medicine->name }}
                                            </a>
                                            <div class="text-[11px] text-gray-400">{{ $m->medicine->generic_name }}</div>
                                        </td>
                                        <td class="px-6 py-3.5 font-semibold text-gray-800 font-mono">
                                            {{ $m->batch?->batch_no ?? '—' }}
                                        </td>
                                        <td class="px-6 py-3.5">
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
                                        <td class="px-6 py-3.5 font-extrabold {{ $m->type === 'out' || $m->type === 'disposal' ? 'text-rose-600' : 'text-emerald-700' }}">
                                            {{ $m->type === 'out' || $m->type === 'disposal' ? '-' : '+' }}{{ number_format($m->quantity) }}
                                            <span class="text-[11px] font-normal text-gray-400">{{ $m->medicine->unit }}</span>
                                        </td>
                                        <td class="px-6 py-3.5 text-gray-700 max-w-xs">
                                            <div class="font-semibold text-[11px] text-gray-600 uppercase">{{ $m->reference_type }}</div>
                                            @if ($m->notes)
                                                <div class="text-[11px] text-gray-500 italic mt-0.5">{{ $m->notes }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3.5 text-gray-600 whitespace-nowrap">
                                            {{ $m->createdBy?->name ?? 'System' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                                            No stock movements match the current filter.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($movements->hasPages())
                        <div class="p-4 border-t border-gray-100">
                            {{ $movements->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
