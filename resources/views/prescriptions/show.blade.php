<x-nurse-layout>
    <x-slot name="heading">
        <div class="flex items-center gap-3">
            <a href="{{ route('prescriptions.index') }}"
               class="p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition"
               title="Back to Prescriptions">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-bold text-slate-800 tracking-tight">
                    Prescription #{{ str_pad($prescription->id, 5, '0', STR_PAD_LEFT) }}
                </h1>
                @php
                    $badges = [
                        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                        'routed' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'dispensed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'cancelled' => 'bg-slate-100 text-slate-600 border-slate-200',
                    ];
                    $dotColors = [
                        'pending' => 'bg-amber-500',
                        'routed' => 'bg-blue-500',
                        'dispensed' => 'bg-emerald-500',
                        'cancelled' => 'bg-slate-400',
                    ];
                @endphp
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold capitalize border {{ $badges[$prescription->status] ?? 'bg-slate-100' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $dotColors[$prescription->status] ?? 'bg-slate-400' }}"></span>
                    <span>{{ $prescription->status }}</span>
                </span>
            </div>
        </div>
    </x-slot>

    <x-slot name="actions">
        <div class="flex items-center gap-2">
            @if ($prescription->status === 'pending')
                @can('cancel', $prescription)
                    <form method="POST" action="{{ route('prescriptions.cancel', $prescription) }}"
                          onsubmit="return confirm('Cancel this prescription?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-3 py-1.5 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-semibold transition">
                            Cancel
                        </button>
                    </form>
                @endcan

                @can('route', $prescription)
                    <form method="POST" action="{{ route('prescriptions.route', $prescription) }}"
                          onsubmit="return confirm('Route this prescription to Pharmacy?');">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#064e2b] hover:bg-[#053d22] text-white rounded-xl text-xs font-semibold shadow-xs transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                            <span>Route to Pharmacy</span>
                        </button>
                    </form>
                @endcan
            @endif
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-5">

        @if (session('status'))
            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- Summary Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Patient Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Patient</h3>
                    <span class="text-[11px] font-semibold text-slate-600 capitalize">{{ $prescription->patient->patient_type }}</span>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-slate-900">{{ $prescription->patient->name }}</h4>
                    <div class="text-xs text-slate-500 mt-0.5">
                        ID: <span class="font-medium text-slate-800">{{ $prescription->patient->id_number }}</span>
                        @if ($prescription->patient->contact_number)
                            &bull; {{ $prescription->patient->contact_number }}
                        @endif
                    </div>
                    @if ($prescription->patient->allergies)
                        <div class="mt-2 text-xs text-rose-700 font-medium">
                            ⚠ Allergies: {{ $prescription->patient->allergies }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Doctor & Timing Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Clinical Info</h3>
                    <span class="text-xs text-slate-400">{{ $prescription->created_at->format('M d, Y h:i A') }}</span>
                </div>

                <div class="space-y-1.5 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Physician:</span>
                        <span class="font-semibold text-slate-800">{{ Str::startsWith($prescription->doctor_name, 'Dr.') ? $prescription->doctor_name : 'Dr. ' . $prescription->doctor_name }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Encoded by:</span>
                        <span class="text-slate-700">{{ $prescription->encodedBy->name }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prescribed Medicines Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800">Prescribed Medicines</h3>
                <span class="text-xs text-slate-500 font-medium">
                    {{ $prescription->items->count() }} {{ Str::plural('item', $prescription->items->count()) }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/75 text-slate-500 font-semibold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th scope="col" class="px-4 py-3">Medicine</th>
                            <th scope="col" class="px-4 py-3">Qty</th>
                            <th scope="col" class="px-4 py-3">Stock</th>
                            <th scope="col" class="px-4 py-3">Instructions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($prescription->items as $item)
                            @php
                                $available = $item->medicine->available_stock;
                            @endphp
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-900">{{ $item->medicine->name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $item->medicine->generic_name }}</div>
                                </td>
                                <td class="px-4 py-3 font-bold text-slate-800 whitespace-nowrap">
                                    {{ $item->quantity }} {{ $item->medicine->unit }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium {{ $available >= $item->quantity ? 'text-emerald-700 bg-emerald-50' : 'text-rose-700 bg-rose-50' }}">
                                        {{ $available }} available
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-700 font-medium">
                                    {{ $item->dosage_instructions }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-nurse-layout>
