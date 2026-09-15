<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('patient.prescriptions') }}"
               class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Prescriptions
            </a>
            <span class="text-gray-300">|</span>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Prescription #{{ $prescription->id }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Status Banner --}}
            @php
                $bannerColors = [
                    'pending'   => 'bg-amber-50 border-amber-200 text-amber-800',
                    'routed'    => 'bg-blue-50 border-blue-200 text-blue-800',
                    'dispensed' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
                    'cancelled' => 'bg-rose-50 border-rose-200 text-rose-800',
                ];
                $statusLabels = [
                    'pending'   => 'Your prescription is pending review.',
                    'routed'    => 'Your prescription has been sent to the pharmacy queue.',
                    'dispensed' => 'Your medicines have been dispensed. Please collect at the pharmacy.',
                    'cancelled' => 'This prescription has been cancelled.',
                ];
            @endphp
            <div class="p-4 rounded-md border {{ $bannerColors[$prescription->status] ?? 'bg-gray-50 border-gray-200 text-gray-700' }} text-sm font-medium">
                {{ $statusLabels[$prescription->status] ?? 'Status unknown.' }}
            </div>

            {{-- Prescription Meta --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Prescription Details</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs text-gray-500">Prescription ID</dt>
                        <dd class="text-sm font-medium text-gray-800">#{{ $prescription->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Status</dt>
                        <dd>
                            @php
                                $badgeColors = [
                                    'pending'   => 'bg-amber-100 text-amber-700',
                                    'routed'    => 'bg-blue-100 text-blue-700',
                                    'dispensed' => 'bg-emerald-100 text-emerald-700',
                                    'cancelled' => 'bg-rose-100 text-rose-700',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium {{ $badgeColors[$prescription->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($prescription->status) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Prescribing Doctor</dt>
                        <dd class="text-sm font-medium text-gray-800">Dr. {{ $prescription->encodedBy->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Date Issued</dt>
                        <dd class="text-sm font-medium text-gray-800">{{ $prescription->created_at->format('F d, Y \a\t h:i A') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Prescribed Medicines --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700">Prescribed Medicines</h3>
                </div>
                <ul class="divide-y divide-gray-100">
                    @foreach ($prescription->items as $item)
                        <li class="px-6 py-4">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800">{{ $item->medicine->name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $item->medicine->generic_name }}</p>
                                    @if ($item->dosage_instructions)
                                        <p class="text-xs text-gray-600 mt-1 italic">"{{ $item->dosage_instructions }}"</p>
                                    @endif
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <span class="text-sm font-semibold text-gray-800">{{ $item->quantity }}</span>
                                    <span class="text-xs text-gray-500 ml-0.5">{{ $item->medicine->unit }}(s)</span>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

        </div>
    </div>
</x-app-layout>
