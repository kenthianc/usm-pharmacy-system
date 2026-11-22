<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>POS & Sales Reports - USM Pharmacy</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
            .print-card {
                border: 1px solid #e2e8f0 !important;
                box-shadow: none !important;
                break-inside: avoid;
            }
        }
    </style>
</head>
<body class="font-sans antialiased min-h-screen bg-slate-100 text-slate-900">

<div class="min-h-screen flex flex-col">

    <!-- ── Top Header ───────────────────────────────────────────────────── -->
    <header class="h-14 bg-green-950 text-white flex items-center px-4 sm:px-6 gap-3 shrink-0 shadow-md border-b border-green-900 sticky top-0 z-30">
        <a href="{{ route('pos.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-green-900 hover:bg-green-800 text-green-200 hover:text-white text-xs font-semibold transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Back to POS Terminal</span>
        </a>

        <div class="h-5 w-px bg-green-800"></div>

        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-yellow-500 text-green-950 flex items-center justify-center font-bold text-sm shadow-sm border border-yellow-300">
                ℞
            </div>
            <div class="flex items-center gap-1.5">
                <span class="text-sm font-extrabold text-yellow-400 tracking-tight">USM</span>
                <span class="text-green-500 text-sm font-semibold">/</span>
                <span class="text-sm font-semibold text-white">Dispensary &amp; POS Sales Reports</span>
            </div>
        </div>

        <div class="flex-1"></div>

        <div class="flex items-center gap-2.5 sm:gap-3 no-print">
            <!-- Consistent Risk Summary Pill -->
            <x-risk-summary-pill />

            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-yellow-500 hover:bg-yellow-400 text-green-950 text-xs font-bold transition-colors cursor-pointer shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Print Report</span>
            </button>
        </div>
    </header>

    <!-- ── Main Content Container ───────────────────────────────────────── -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 space-y-6">

        <!-- Header Controls & Period Filter -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">Sales &amp; Dispensing Analytics</h1>
                <p class="text-xs text-slate-500 mt-0.5">Comprehensive audit and financial report generated from dispensary POS activity.</p>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-center gap-1.5 overflow-x-auto no-print">
                @php
                    $periods = [
                        'today' => 'Today',
                        'yesterday' => 'Yesterday',
                        '7days' => 'Last 7 Days',
                        'month' => 'This Month',
                        'all' => 'All Time',
                    ];
                @endphp
                @foreach ($periods as $key => $label)
                    <a href="{{ route('pos.reports', ['period' => $key]) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ $period === $key ? 'bg-[#064e2b] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- KPI Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Revenue -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs print-card">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Gross Sales Revenue</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                        ₱
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-3 tracking-tight">₱{{ number_format($totalRevenue, 2) }}</p>
                <p class="text-xs text-emerald-600 font-semibold mt-1">Total revenue collected</p>
            </div>

            <!-- Total Transactions -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs print-card">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Orders</span>
                    <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-3 tracking-tight">{{ number_format($totalCount) }}</p>
                <p class="text-xs text-slate-500 font-medium mt-1">Processed transactions</p>
            </div>

            <!-- Prescriptions Dispensed -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs print-card">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Prescriptions</span>
                    <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs">
                        ℞
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-3 tracking-tight">{{ number_format($prescriptionCount) }}</p>
                <p class="text-xs text-purple-700 font-semibold mt-1">₱{{ number_format($prescriptionRevenue, 2) }} revenue</p>
            </div>

            <!-- OTC Sales -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs print-card">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">OTC Direct Sales</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-3 tracking-tight">{{ number_format($otcCount) }}</p>
                <p class="text-xs text-amber-700 font-semibold mt-1">₱{{ number_format($otcRevenue, 2) }} revenue</p>
            </div>
        </div>

        <!-- Breakdown Section: Payment Methods & Top Moving Medicines -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Payment Methods Breakdown (4 cols) -->
            <div class="lg:col-span-5 bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs print-card">
                <h2 class="text-base font-bold text-slate-900 mb-1">Payment Tender Distribution</h2>
                <p class="text-xs text-slate-500 mb-4">Breakdown of revenue across accepted payment modes.</p>

                <div class="space-y-4">
                    @foreach ($paymentMethods as $method => $amount)
                        @php
                            $percentage = $totalRevenue > 0 ? round(($amount / $totalRevenue) * 100, 1) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-semibold text-slate-700">{{ $method }}</span>
                                <div class="text-right">
                                    <span class="font-bold text-slate-900">₱{{ number_format($amount, 2) }}</span>
                                    <span class="text-slate-400 text-[10px]">({{ $percentage }}%)</span>
                                </div>
                            </div>
                            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-600 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top Dispensed Medicines (7 cols) -->
            <div class="lg:col-span-7 bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs print-card overflow-hidden">
                <h2 class="text-base font-bold text-slate-900 mb-1">Top Dispensed Medicines</h2>
                <p class="text-xs text-slate-500 mb-4">Highest-moving inventory items during this selected period.</p>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-left">
                                <th class="pb-2.5">Medicine</th>
                                <th class="pb-2.5">Category</th>
                                <th class="pb-2.5 text-center">Units Dispensed</th>
                                <th class="pb-2.5 text-right">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($topMedicines as $med)
                                <tr class="hover:bg-slate-50/80">
                                    <td class="py-2.5 font-semibold text-slate-800">
                                        {{ $med->generic_name }}
                                        <span class="block text-[10px] text-slate-400 font-normal">({{ $med->name }})</span>
                                    </td>
                                    <td class="py-2.5 text-slate-500">{{ $med->category ?? 'General' }}</td>
                                    <td class="py-2.5 text-center font-bold text-slate-800">{{ number_format($med->total_qty) }}</td>
                                    <td class="py-2.5 text-right font-bold text-emerald-700">₱{{ number_format($med->total_revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-400">No items dispensed during this period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Detailed Transaction Ledger Table -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs print-card">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Transaction Audit Log</h2>
                    <p class="text-xs text-slate-500">Every individual receipt record recorded by the POS system.</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">
                    {{ $transactions->count() }} records
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 font-bold uppercase tracking-wider text-left border-y border-slate-100">
                            <th class="px-4 py-3">Receipt #</th>
                            <th class="px-4 py-3">Timestamp</th>
                            <th class="px-4 py-3">Patient / Customer</th>
                            <th class="px-4 py-3">Sale Type</th>
                            <th class="px-4 py-3">Cashier</th>
                            <th class="px-4 py-3">Payment</th>
                            <th class="px-4 py-3 text-right">Amount</th>
                            <th class="px-4 py-3 text-right no-print">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($transactions as $tx)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 font-mono font-bold text-slate-700">
                                    #{{ str_pad($tx->id, 8, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-3 font-mono text-slate-500">
                                    {{ $tx->created_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-900">
                                    {{ $tx->prescription?->patient?->name ?? 'Walk-in Customer' }}
                                </td>
                                <td class="px-4 py-3">
                                    @if ($tx->isOtc())
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            OTC
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            Rx #{{ $tx->prescription_id }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-500">{{ $tx->cashier?->name ?? 'Staff' }}</td>
                                <td class="px-4 py-3 font-medium text-slate-600 capitalize">{{ $tx->payment_method }}</td>
                                <td class="px-4 py-3 text-right font-bold text-emerald-700 text-sm">₱{{ number_format($tx->total_amount, 2) }}</td>
                                <td class="px-4 py-3 text-right no-print">
                                    <a href="{{ route('pos.receipt', $tx) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 hover:text-emerald-900 hover:underline">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-slate-400">
                                    No transactions recorded for the selected period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</div>

<!-- Dual-Risk Engine Global Drawer Modal -->
<x-dual-risk-engine-modal />

<!-- Sign Out Confirmation Modal UI -->
<x-signout-modal />

</body>
</html>
