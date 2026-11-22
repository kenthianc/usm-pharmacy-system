<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medication Summary &bull; {{ $admission->patient->name }} &bull; USM Hospital</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 11pt; color: #000; background: #fff !important; }
            .print-page { padding: 0 !important; box-shadow: none !important; border: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans min-h-screen py-6 px-4">

    <!-- Top Action Bar (hidden on print) -->
    <div class="max-w-4xl mx-auto mb-4 flex items-center justify-between no-print">
        <a href="{{ route('nurse.chart', $admission) }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-emerald-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Clinical Chart</span>
        </a>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-sm transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print Medication Summary</span>
            </button>
        </div>
    </div>

    <!-- Official Printable Document Container -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-10 rounded-2xl border border-slate-200 shadow-md print-page space-y-6">

        <!-- Header -->
        <div class="border-b-2 border-emerald-800 pb-4 flex items-center justify-between">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-emerald-900 text-amber-400 font-bold flex items-center justify-center text-xl shrink-0 font-serif border border-amber-400/40">
                    USM
                </div>
                <div>
                    <h1 class="text-lg font-black text-emerald-950 uppercase tracking-tight">University of Southern Mindanao Hospital</h1>
                    <p class="text-xs text-slate-600 font-medium">Clinical Services &bull; Dispensary Division &bull; Kabacan, North Cotabato 9407</p>
                    <p class="text-[11px] font-bold text-emerald-700 mt-0.5 uppercase tracking-wider">Patient Medication Summary (For Billing Reference)</p>
                </div>
            </div>

            <div class="text-right text-xs">
                <div class="text-[10px] font-mono uppercase text-slate-400 font-bold">Document Reference</div>
                <div class="font-mono font-bold text-slate-800">MED-SUM-{{ str_pad($admission->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div class="text-[10px] text-slate-500 mt-1">Generated: {{ now()->format('M d, Y h:i A') }}</div>
            </div>
        </div>

        <!-- Patient Demographics & Admission Record -->
        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/80 text-xs grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 block">Patient Name</span>
                <span class="font-extrabold text-slate-900 text-sm">{{ $admission->patient->name }}</span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 block">Patient ID / Classification</span>
                <span class="font-mono font-bold text-slate-800">{{ $admission->patient->id_number }}</span>
                <span class="block text-[10px] text-emerald-700 font-semibold">{{ $admission->patient_classification }}</span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 block">Ward &amp; Bed Number</span>
                <span class="font-bold text-slate-800">{{ $admission->bed?->ward?->ward_name ?? 'Admitted' }}</span>
                <span class="block text-[10px] text-slate-500 font-mono">{{ $admission->bed?->full_location ?? 'Bed Unassigned' }}</span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 block">Nurse Station</span>
                <span class="font-bold text-slate-800">{{ $admission->bed?->ward?->nurse_station_assigned ?? 'Station 1' }}</span>
                <span class="block text-[10px] text-slate-500">{{ $admission->status === 'admitted' ? 'Currently Admitted' : 'Discharged on '.$admission->discharge_date?->format('M d, Y') }}</span>
            </div>

            <div class="pt-2 border-t border-slate-200/60 col-span-2">
                <span class="text-[10px] font-bold uppercase text-slate-400 block">Admission Date / Attending Clinician</span>
                <span class="text-slate-700 font-medium">{{ $admission->admission_date->format('M d, Y h:i A') }} &bull; Admitted by: {{ $admission->admittedBy?->name ?? 'Admission Officer' }}</span>
            </div>
            <div class="pt-2 border-t border-slate-200/60 col-span-2">
                <span class="text-[10px] font-bold uppercase text-slate-400 block">Clinical Case Classification</span>
                <span class="font-semibold {{ str_contains($admission->respiratory_status, 'Isolation') || str_contains($admission->respiratory_status, 'Infectious') ? 'text-rose-700' : 'text-slate-700' }}">
                    {{ $admission->respiratory_status }}
                </span>
            </div>
        </div>

        <!-- Inpatient Dispensed Medication Ledger -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-black uppercase tracking-wider text-slate-700">Fulfilled Inpatient Dispensary Records (FEFO Batches)</h2>
                <span class="text-[10px] text-slate-500 font-semibold">Synchronized directly from Hospital Pharmacy POS</span>
            </div>

            <table class="w-full text-left text-xs border border-slate-200 rounded-xl overflow-hidden">
                <thead class="bg-slate-100 text-slate-600 font-bold uppercase text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-3">Rx # / Date</th>
                        <th class="py-2.5 px-3">Item Name &amp; Generic</th>
                        <th class="py-2.5 px-3">Batch # / Exp</th>
                        <th class="py-2.5 px-3 text-center">Qty</th>
                        <th class="py-2.5 px-3 text-right">Unit Price</th>
                        <th class="py-2.5 px-3 text-right">Reference Total</th>
                        <th class="py-2.5 px-3">Collector Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @php $grandTotal = 0; $itemCount = 0; @endphp
                    @forelse($admission->prescriptions as $rx)
                        @if($rx->posTransaction)
                            @foreach($rx->posTransaction->items as $txItem)
                                @php
                                    $grandTotal += $txItem->subtotal;
                                    $itemCount++;
                                @endphp
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-2.5 px-3 font-mono text-[11px]">
                                        <div class="font-bold text-slate-900">#{{ $rx->id }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $rx->posTransaction->created_at->format('M d, h:i A') }}</div>
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <div class="font-bold text-slate-800">{{ $txItem->medicine?->name ?? 'Medicine' }}</div>
                                        <div class="text-[10px] text-slate-500 font-mono">{{ $txItem->medicine?->generic_name }}</div>
                                    </td>
                                    <td class="py-2.5 px-3 font-mono text-[11px]">
                                        <div class="font-bold text-emerald-800">{{ $txItem->batch?->batch_no ?? 'FEFO Batch' }}</div>
                                        <div class="text-[10px] text-slate-400">Exp: {{ $txItem->batch?->expiry_date?->format('m/Y') ?? 'N/A' }}</div>
                                    </td>
                                    <td class="py-2.5 px-3 text-center font-bold">
                                        {{ $txItem->quantity }} {{ $txItem->medicine?->unit ?? 'units' }}
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-mono">
                                        ₱{{ number_format($txItem->unit_price, 2) }}
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900">
                                        ₱{{ number_format($txItem->subtotal, 2) }}
                                    </td>
                                    <td class="py-2.5 px-3 text-[11px] text-slate-600">
                                        <span class="inline-block px-1.5 py-0.5 rounded bg-slate-100 font-medium">
                                            {{ $rx->collected_by ?: $rx->posTransaction->collected_by ?: 'Nurse Station' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            {{-- In case dispensed without transaction model item --}}
                            @foreach($rx->items as $item)
                                @php
                                    $subtotal = ($item->medicine?->unit_price ?? 0) * $item->quantity;
                                    $grandTotal += $subtotal;
                                    $itemCount++;
                                @endphp
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-2.5 px-3 font-mono text-[11px]">
                                        <div class="font-bold text-slate-900">#{{ $rx->id }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $rx->updated_at->format('M d, h:i A') }}</div>
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <div class="font-bold text-slate-800">{{ $item->medicine?->name ?? 'Medicine' }}</div>
                                        <div class="text-[10px] text-slate-500 font-mono">{{ $item->medicine?->generic_name }}</div>
                                    </td>
                                    <td class="py-2.5 px-3 font-mono text-[11px] text-slate-400">
                                        FEFO Active
                                    </td>
                                    <td class="py-2.5 px-3 text-center font-bold">
                                        {{ $item->quantity }} {{ $item->medicine?->unit ?? 'units' }}
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-mono">
                                        ₱{{ number_format($item->medicine?->unit_price ?? 0, 2) }}
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900">
                                        ₱{{ number_format($subtotal, 2) }}
                                    </td>
                                    <td class="py-2.5 px-3 text-[11px] text-slate-600">
                                        {{ $rx->collected_by ?: 'Ward Staff' }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 italic">
                                No pharmacy-dispensed medications recorded for this clinical admission yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($itemCount > 0)
                    <tfoot class="bg-slate-50 border-t-2 border-slate-300 font-bold text-xs">
                        <tr>
                            <td colspan="5" class="py-3 px-3 text-right uppercase tracking-wider text-slate-600">
                                Total Reference Medication Charges:
                            </td>
                            <td class="py-3 px-3 text-right font-mono text-sm text-emerald-800">
                                ₱{{ number_format($grandTotal, 2) }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <!-- Institutional Note & Policy -->
        <div class="p-3.5 bg-amber-50/70 border border-amber-200/80 rounded-xl text-xs text-amber-900 space-y-1">
            <p class="font-bold uppercase tracking-wider text-[10px] text-amber-800">Notice for Hospital Billing Office &amp; Discharge Processing</p>
            <p class="text-[11px] leading-relaxed">
                This document serves as the official dispensary reference for medications released to the ward during the patient's stay. Statutory discounts (Senior Citizen RA 9994, PWD RA 10754) or institutional privileges (USM Student 10%) will be applied upon final billing computation at the hospital cashiering office.
            </p>
        </div>

        <!-- Formal Sign-Off Blocks -->
        <div class="pt-8 border-t border-slate-200 grid grid-cols-2 gap-8 text-xs">
            <div class="space-y-12">
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Prepared &amp; Endorsed By:</span>
                    <div class="border-b border-slate-400 pb-1 font-bold text-slate-800">
                        {{ auth()->user()->name }}
                    </div>
                    <div class="text-[10px] text-slate-500 mt-0.5">Nurse-on-Duty / Nurse Station Staff</div>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Date &amp; Time Released:</span>
                    <span class="font-mono text-slate-700">{{ now()->format('F d, Y h:i A') }}</span>
                </div>
            </div>

            <div class="space-y-12 text-right">
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Received &amp; Acknowledged By:</span>
                    <div class="border-b border-slate-400 pb-1 font-bold text-slate-800 text-left">
                        &nbsp;
                    </div>
                    <div class="text-[10px] text-slate-500 mt-0.5">USM Hospital Billing &amp; Cashiering Officer</div>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Date &amp; Time Forwarded:</span>
                    <span class="font-mono text-slate-700">________________________</span>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
