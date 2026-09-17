<x-patient-layout active="prescriptions">
    @php
        $statusMap = [
            'pending' => 'Pending',
            'routed' => 'At Pharmacy',
            'dispensed' => 'Dispensed',
            'cancelled' => 'Cancelled',
        ];
        $displayStatus = $statusMap[$prescription->status] ?? ucfirst($prescription->status);

        $badgeStyle = match ($prescription->status) {
            'pending' => 'bg-yellow-100 text-yellow-700 border border-yellow-200',
            'routed' => 'bg-blue-100 text-blue-700 border border-blue-200',
            'dispensed' => 'bg-green-100 text-green-700 border border-green-200',
            'cancelled' => 'bg-red-100 text-red-600 border border-red-200',
            default => 'bg-gray-100 text-gray-700 border border-gray-200',
        };

        $bannerConfig = match ($prescription->status) {
            'pending' => [
                'wrapper' => 'bg-yellow-50 border-yellow-300',
                'text' => 'Your prescription is under review. You will be notified once it has been routed to the pharmacy.',
            ],
            'routed' => [
                'wrapper' => 'bg-blue-50 border-blue-300',
                'text' => 'Your prescription is in the pharmacy queue. Please proceed to the dispensing window when called.',
            ],
            'dispensed' => [
                'wrapper' => 'bg-green-50 border-green-400',
                'text' => 'Your medicines are ready for pickup. Present this prescription ID at the pharmacy counter.',
            ],
            default => [
                'wrapper' => 'bg-red-50 border-red-300',
                'text' => 'This prescription has been cancelled. Please consult your doctor for a new prescription if needed.',
            ],
        };

        $notesMap = [
            'pending' => 'Allergic rhinitis with mild wheeze. Routine review scheduled.',
            'routed' => 'Mild pain management for musculoskeletal complaint.',
            'dispensed' => 'Patient presented with fever and sore throat. Completed dispensing.',
            'cancelled' => 'Cancelled per patient request or doctor recommendation.',
        ];
    @endphp

    <div class="flex-1 max-w-2xl w-full mx-auto px-4 sm:px-6 py-8 space-y-5">
        <a
            href="{{ route('patient.prescriptions') }}"
            class="inline-flex items-center gap-1.5 text-sm text-green-700 hover:text-green-900 font-medium transition-colors"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Prescriptions
        </a>

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-800 font-mono">RX-{{ date('Y') }}-{{ str_pad($prescription->id, 3, '0', STR_PAD_LEFT) }}</h1>
                <p class="text-xs text-gray-400 mt-0.5">Prescription Details</p>
            </div>
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full {{ $badgeStyle }}">
                @if ($prescription->status === 'pending')
                    <svg class="w-3.5 h-3.5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><polyline points="12 6 12 12 16 14" stroke-width="2"/></svg>
                @elseif ($prescription->status === 'routed')
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12" stroke-width="2"/></svg>
                @elseif ($prescription->status === 'dispensed')
                    <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" stroke-width="2.5"/></svg>
                @else
                    <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><line x1="15" y1="9" x2="9" y2="15" stroke-width="2"/><line x1="9" y1="9" x2="15" y2="15" stroke-width="2"/></svg>
                @endif
                {{ $displayStatus }}
            </span>
        </div>

        <!-- Status Banner -->
        <div class="flex items-start gap-3 rounded-xl px-4 py-3.5 border {{ $bannerConfig['wrapper'] }}">
            @if ($prescription->status === 'pending')
                <svg class="w-4 h-4 text-yellow-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><polyline points="12 6 12 12 16 14" stroke-width="2"/></svg>
            @elseif ($prescription->status === 'routed')
                <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12" stroke-width="2"/></svg>
            @elseif ($prescription->status === 'dispensed')
                <svg class="w-4 h-4 text-green-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" stroke-width="2.5"/></svg>
            @else
                <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><line x1="15" y1="9" x2="9" y2="15" stroke-width="2"/><line x1="9" y1="9" x2="15" y2="15" stroke-width="2"/></svg>
            @endif
            <p class="text-sm text-gray-700 leading-relaxed">{{ $bannerConfig['text'] }}</p>
        </div>

        <!-- Metadata -->
        <div class="bg-white border border-gray-200 rounded-2xl px-5 py-4 grid grid-cols-2 gap-4 shadow-sm">
            <div>
                <p class="text-xs text-gray-400 mb-1">Issued by</p>
                <p class="text-sm font-semibold text-gray-800">Dr. {{ $prescription->encodedBy->name ?? 'Ana Reyes' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Date Issued</p>
                <p class="text-sm font-semibold text-gray-800">{{ $prescription->created_at->format('M d, Y') }}</p>
            </div>
            <div class="col-span-2">
                <p class="text-xs text-gray-400 mb-1">Doctor's Notes</p>
                <p class="text-sm text-gray-700 italic">{{ $notesMap[$prescription->status] ?? 'General clinical consultation prescription.' }}</p>
            </div>
        </div>

        <!-- Medicines table -->
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50">
                <p class="text-xs font-bold text-gray-600 uppercase tracking-wide">Prescribed Medicines</p>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach ($prescription->items as $item)
                    <div class="px-5 py-4 flex items-start gap-4">
                        <div class="w-9 h-9 bg-green-50 border border-green-100 rounded-xl flex items-center justify-center shrink-0 text-green-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z" stroke-width="2"/><path d="m8.5 8.5 7 7" stroke-width="2"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800">{{ $item->medicine->name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $item->medicine->generic_name }}</p>
                            <p class="text-xs text-gray-500 mt-1 bg-gray-50 border border-gray-100 rounded-lg px-2.5 py-1 inline-block">
                                {{ $item->dosage_instructions ?: '1 tab OD as directed' }}
                            </p>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="text-xs text-gray-400">Qty</p>
                            <p class="text-lg font-bold text-green-800">{{ $item->quantity }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-patient-layout>
