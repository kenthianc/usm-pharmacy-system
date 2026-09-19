<x-patient-layout active="prescriptions">
    <div class="flex-1 max-w-3xl w-full mx-auto px-4 sm:px-6 py-8">
        <h1 class="text-xl font-bold text-gray-800 mb-5">Prescription History</h1>

        <!-- Filter tabs -->
        @php
            $statusMap = [
                'pending' => 'Pending',
                'routed' => 'At Pharmacy',
                'dispensed' => 'Dispensed',
                'cancelled' => 'Cancelled',
            ];
            $filterTabs = [
                '' => ['label' => 'All', 'count' => $counts['all']],
                'pending' => ['label' => 'Pending', 'count' => $counts['pending']],
                'routed' => ['label' => 'At Pharmacy', 'count' => $counts['routed']],
                'dispensed' => ['label' => 'Dispensed', 'count' => $counts['dispensed']],
                'cancelled' => ['label' => 'Cancelled', 'count' => $counts['cancelled']],
            ];
        @endphp

        <div class="flex gap-1.5 flex-wrap mb-6">
            @foreach ($filterTabs as $key => $tab)
                @php
                    $isActive = ($status === $key || ($key === '' && empty($status)));
                @endphp
                <a
                    href="{{ route('patient.prescriptions', ['status' => $key ?: null]) }}"
                    class="text-xs font-semibold px-3.5 py-1.5 rounded-full border transition-colors {{ $isActive ? 'bg-green-800 text-white border-green-800' : 'bg-white text-gray-600 border-gray-300 hover:border-green-400' }}"
                >
                    {{ $tab['label'] }}
                    <span class="ml-1 {{ $isActive ? 'text-green-300' : 'text-gray-400' }}">({{ $tab['count'] }})</span>
                </a>
            @endforeach
        </div>

        @if ($prescriptions->isEmpty())
            <div class="bg-white border border-gray-200 rounded-2xl py-16 text-center text-gray-400 shadow-sm">
                <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="8" height="4" x="8" y="2" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/></svg>
                <p class="text-sm font-semibold">No prescriptions found</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($prescriptions as $prescription)
                    @php
                        $displayStatus = $statusMap[$prescription->status] ?? ucfirst($prescription->status);
                        $badgeStyle = match ($prescription->status) {
                            'pending' => 'bg-yellow-100 text-yellow-700 border border-yellow-200',
                            'routed' => 'bg-blue-100 text-blue-700 border border-blue-200',
                            'dispensed' => 'bg-green-100 text-green-700 border border-green-200',
                            'cancelled' => 'bg-red-100 text-red-600 border border-red-200',
                            default => 'bg-gray-100 text-gray-700 border border-gray-200',
                        };

                        $notesMap = [
                            'pending' => 'Allergic rhinitis with mild wheeze. Routine review scheduled.',
                            'routed' => 'Mild pain management for musculoskeletal complaint.',
                            'dispensed' => 'Patient presented with fever and sore throat. Completed dispensing.',
                            'cancelled' => 'Cancelled per patient request or doctor recommendation.',
                        ];
                    @endphp
                    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden hover:shadow-sm transition-shadow shadow-sm">
                        <div class="flex items-start justify-between gap-4 px-5 pt-4 pb-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <p class="text-sm font-bold text-gray-800 font-mono">RX-{{ date('Y') }}-{{ str_pad($prescription->id, 3, '0', STR_PAD_LEFT) }}</p>
                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $badgeStyle }}">
                                        @if ($prescription->status === 'pending')
                                            <svg class="w-3 h-3 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><polyline points="12 6 12 12 16 14" stroke-width="2"/></svg>
                                        @elseif ($prescription->status === 'routed')
                                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12" stroke-width="2"/></svg>
                                        @elseif ($prescription->status === 'dispensed')
                                            <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" stroke-width="2.5"/></svg>
                                        @else
                                            <svg class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><line x1="15" y1="9" x2="9" y2="15" stroke-width="2"/><line x1="9" y1="9" x2="15" y2="15" stroke-width="2"/></svg>
                                        @endif
                                        {{ $displayStatus }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500">Dr. {{ $prescription->encodedBy->name ?? 'Ana Reyes' }} &mdash; {{ $prescription->created_at->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-400 mt-1 italic">{{ $notesMap[$prescription->status] ?? 'Prescription from USM Health Clinic.' }}</p>
                            </div>
                            <a
                                href="{{ route('patient.prescriptions.show', $prescription->id) }}"
                                class="shrink-0 text-xs font-semibold text-green-700 hover:text-green-900 flex items-center gap-1 mt-0.5"
                            >
                                Details
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                        <div class="border-t border-gray-50 px-5 py-2.5 bg-gray-50/60 flex gap-4 overflow-x-auto">
                            @foreach ($prescription->items as $item)
                                <span class="text-[11px] text-gray-500 whitespace-nowrap flex items-center gap-1">
                                    <svg class="w-3 h-3 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z" stroke-width="2"/><path d="m8.5 8.5 7 7" stroke-width="2"/></svg>
                                    {{ $item->medicine->name }} <span class="text-gray-400">×{{ $item->quantity }}</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($prescriptions->hasPages())
                <div class="mt-6">
                    {{ $prescriptions->links() }}
                </div>
            @endif
        @endif
    </div>
</x-patient-layout>
