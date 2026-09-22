<x-admin-layout active="reports">
    <div class="space-y-6">

            <!-- Breadcrumbs / Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-green-800 font-medium">Admin Hub</a>
                        <span>/</span>
                        <span class="text-slate-800 font-bold">Executive Analytics</span>
                    </div>
                    <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2.5">
                        <svg class="w-6 h-6 text-green-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        University Pharmacy &amp; Clinic Analytics
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">Financial performance, inventory valuation breakdown, medicine velocity, and loss analysis.</p>
                </div>

                <div class="flex items-center gap-3">
                    <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl border border-slate-300 shadow-xs transition cursor-pointer">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        Print / Save PDF
                    </button>
                    <a href="{{ route('inventory.export-pdf') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-900 hover:bg-green-800 text-yellow-400 text-xs font-bold rounded-xl shadow-md transition border border-green-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Export Stock Report
                    </a>
                </div>
            </div>

            <!-- Financial & Loss KPI Highlights -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Asset Valuation</span>
                    <div class="text-2xl font-extrabold text-slate-900 mt-2">₱{{ number_format($financials['total_valuation'], 2) }}</div>
                    <div class="text-xs text-slate-500 mt-1">{{ $financials['total_medicines'] }} Active Products in Stock</div>
                </div>

                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Dispensary OTC Sales</span>
                    <div class="text-2xl font-extrabold text-emerald-700 mt-2">₱{{ number_format($financials['otc_revenue'], 2) }}</div>
                    <div class="text-xs text-slate-500 mt-1">{{ $financials['otc_count'] }} Paid Transactions</div>
                </div>

                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Dispensed Prescriptions</span>
                    <div class="text-2xl font-extrabold text-blue-700 mt-2">{{ number_format($financials['rx_dispensed_count']) }}</div>
                    <div class="text-xs text-slate-500 mt-1">Student &amp; Faculty subsidized care</div>
                </div>

                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Spoilage &amp; Expiry Loss</span>
                    <div class="text-2xl font-extrabold text-rose-600 mt-2">₱{{ number_format($financials['total_loss_value'], 2) }}</div>
                    <div class="text-xs text-slate-500 mt-1">{{ number_format($financials['total_disposed_units']) }} Units Written-Off</div>
                </div>
            </div>

            <!-- Valuation by Dosage Unit & Demographics Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Category Breakdown Table (7 Cols) -->
                <div class="lg:col-span-7 bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-base font-extrabold text-slate-900 mb-1">Inventory Valuation by Dosage Form</h2>
                    <p class="text-xs text-slate-500 mb-4">Breakdown of capital tied up across pharmaceutical formulations.</p>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase text-[10px]">
                                <tr>
                                    <th class="py-2.5 px-3">Form / Unit</th>
                                    <th class="py-2.5 px-3">Medicines</th>
                                    <th class="py-2.5 px-3">Units in Stock</th>
                                    <th class="py-2.5 px-3 text-right">Total Valuation</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($categoryBreakdown as $unit => $data)
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="py-2.5 px-3 font-bold text-slate-900 uppercase font-mono">{{ $unit }}</td>
                                        <td class="py-2.5 px-3">{{ $data['count'] }}</td>
                                        <td class="py-2.5 px-3 font-semibold">{{ number_format($data['stock']) }}</td>
                                        <td class="py-2.5 px-3 text-right font-extrabold text-slate-900">₱{{ number_format($data['value'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-6 text-center text-slate-400">No inventory categories available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Patient Demographics & Utilization (5 Cols) -->
                <div class="lg:col-span-5 bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col justify-between">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900 mb-1">Patient Demographics</h2>
                        <p class="text-xs text-slate-500 mb-4">Breakdown of registered campus health service beneficiaries.</p>

                        <div class="space-y-4">
                            @php
                                $totalPatients = array_sum($patientTypes) ?: 1;
                            @endphp
                            @foreach(['student' => 'Students', 'faculty' => 'Faculty & Staff', 'dependent' => 'Family Dependents'] as $ptKey => $ptLabel)
                                @php
                                    $ptCount = $patientTypes[$ptKey] ?? 0;
                                    $ptPct = round(($ptCount / $totalPatients) * 100);
                                @endphp
                                <div>
                                    <div class="flex items-center justify-between text-xs font-bold mb-1">
                                        <span class="text-slate-800">{{ $ptLabel }}</span>
                                        <span class="text-slate-500">{{ $ptCount }} ({{ $ptPct }}%)</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                        <div class="h-2.5 rounded-full {{ $ptKey === 'student' ? 'bg-green-700' : ($ptKey === 'faculty' ? 'bg-yellow-500' : 'bg-blue-600') }}" style="width: {{ $ptPct }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-6 p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600">
                        <span class="font-bold text-slate-800">Campus Note:</span> Medicine distribution priority is maintained for full-time USM undergraduate students under the university medical fund.
                    </div>
                </div>

            </div>

            <!-- Top Velocity Medicines: RX vs OTC -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Top Prescribed -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-base font-extrabold text-slate-900 mb-1 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        Top Prescribed Medicines (Infirmary Clinic)
                    </h2>
                    <p class="text-xs text-slate-500 mb-4">Highest volume items dispensed via doctor/nurse prescriptions.</p>

                    <div class="divide-y divide-slate-100">
                        @forelse($topRxMedicines as $rxItem)
                            <div class="py-2.5 flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-slate-900 text-xs">{{ $rxItem->medicine?->name ?? 'Medicine' }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $rxItem->medicine?->generic_name }}</div>
                                </div>
                                <div class="text-right font-extrabold text-xs text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100">
                                    {{ number_format($rxItem->total_qty) }} {{ $rxItem->medicine?->unit }}
                                </div>
                            </div>
                        @empty
                            <p class="py-6 text-center text-xs text-slate-400">No prescription dispensing records yet.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Top OTC Sold -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-base font-extrabold text-slate-900 mb-1 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                        Top Over-The-Counter Sales (Pharmacy POS)
                    </h2>
                    <p class="text-xs text-slate-500 mb-4">Highest retail volume items purchased through OTC dispensary.</p>

                    <div class="divide-y divide-slate-100">
                        @forelse($topOtcMedicines as $otcItem)
                            <div class="py-2.5 flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-slate-900 text-xs">{{ $otcItem->medicine?->name ?? 'Medicine' }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $otcItem->medicine?->generic_name }}</div>
                                </div>
                                <div class="text-right font-extrabold text-xs text-blue-800 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-100">
                                    {{ number_format($otcItem->total_qty) }} {{ $otcItem->medicine?->unit }}
                                </div>
                            </div>
                        @empty
                            <p class="py-6 text-center text-xs text-slate-400">No OTC transactions recorded yet.</p>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- Spoilage & Batch Write-Offs Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2 text-rose-700">
                            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            Spoilage, Expiry, and Disposal Audit Trail
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Itemized record of all stock disposals for university accountability.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase text-[10px]">
                            <tr>
                                <th class="py-3 px-4">Date Disposed</th>
                                <th class="py-3 px-4">Medicine Name</th>
                                <th class="py-3 px-4">Batch Code</th>
                                <th class="py-3 px-4">Quantity Disposed</th>
                                <th class="py-3 px-4 text-right">Est. Loss Value</th>
                                <th class="py-3 px-4">Disposed By</th>
                                <th class="py-3 px-4">Notes / Disposal Justification</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($disposedMovements as $mv)
                                @php
                                    $price = (float) ($mv->medicine?->purchase_price ?? ($mv->medicine?->unit_price * 0.7));
                                    $loss = abs($mv->quantity) * $price;
                                @endphp
                                <tr class="hover:bg-slate-50/70">
                                    <td class="py-3 px-4 whitespace-nowrap font-medium text-slate-800">
                                        {{ $mv->created_at ? $mv->created_at->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="py-3 px-4 font-bold text-slate-900">
                                        {{ $mv->medicine?->name ?? 'N/A' }}
                                    </td>
                                    <td class="py-3 px-4 font-mono text-slate-600">
                                        {{ $mv->batch?->batch_number ?? 'N/A' }}
                                    </td>
                                    <td class="py-3 px-4 font-bold text-rose-700">
                                        {{ abs($mv->quantity) }} {{ $mv->medicine?->unit }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-extrabold text-slate-900">
                                        ₱{{ number_format($loss, 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-700">
                                        {{ $mv->createdBy?->name ?? 'System' }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-500 italic max-w-xs truncate">
                                        {{ $mv->notes ?: 'No justification noted' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-10 text-center text-slate-400">
                                        No batches have been disposed. Spoilage write-offs are at 0.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
</x-admin-layout>
