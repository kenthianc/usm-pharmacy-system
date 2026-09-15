<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Prescriptions</h2>
                <p class="text-xs text-gray-500 mt-1">Your full prescription history from USM Health Services.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Status Filter Tabs --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center space-x-1 overflow-x-auto pb-1">
                    @php
                        $tabs = [
                            ''          => ['label' => 'All',       'count' => $counts['all']],
                            'pending'   => ['label' => 'Pending',   'count' => $counts['pending']],
                            'routed'    => ['label' => 'At Pharmacy','count' => $counts['routed']],
                            'dispensed' => ['label' => 'Dispensed', 'count' => $counts['dispensed']],
                            'cancelled' => ['label' => 'Cancelled', 'count' => $counts['cancelled']],
                        ];
                    @endphp

                    @foreach ($tabs as $key => $tab)
                        <a href="{{ route('patient.prescriptions', ['status' => $key ?: null]) }}"
                           class="px-3 py-1.5 rounded-md text-xs font-medium whitespace-nowrap transition-colors {{ ($status === $key || ($key === '' && empty($status))) ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ $tab['label'] }}
                            <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] {{ ($status === $key || ($key === '' && empty($status))) ? 'bg-indigo-800 text-white' : 'bg-gray-200 text-gray-800' }}">
                                {{ $tab['count'] }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Prescriptions List --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                @if ($prescriptions->isEmpty())
                    <div class="py-16 text-center">
                        <svg class="mx-auto w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm text-gray-500">No prescriptions found.</p>
                    </div>
                @else
                    <ul class="divide-y divide-gray-100">
                        @foreach ($prescriptions as $prescription)
                            @php
                                $statusColors = [
                                    'pending'   => 'bg-amber-100 text-amber-700',
                                    'routed'    => 'bg-blue-100 text-blue-700',
                                    'dispensed' => 'bg-emerald-100 text-emerald-700',
                                    'cancelled' => 'bg-rose-100 text-rose-700',
                                ];
                            @endphp
                            <li>
                                <a href="{{ route('patient.prescriptions.show', $prescription->id) }}"
                                   class="flex items-start justify-between px-6 py-5 hover:bg-gray-50 transition-colors">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-1">
                                            <span class="text-sm font-semibold text-gray-800">
                                                Prescription #{{ $prescription->id }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusColors[$prescription->status] ?? 'bg-gray-100 text-gray-600' }}">
                                                {{ ucfirst($prescription->status) }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 mb-2">
                                            Prescribed by Dr. {{ $prescription->encodedBy->name ?? '—' }}
                                            &bull; {{ $prescription->created_at->format('F d, Y') }}
                                        </p>
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach ($prescription->items->take(3) as $item)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs">
                                                    {{ $item->medicine->name }}
                                                </span>
                                            @endforeach
                                            @if ($prescription->items->count() > 3)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 text-xs">
                                                    +{{ $prescription->items->count() - 3 }} more
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0 ml-4 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    @if ($prescriptions->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100">
                            {{ $prescriptions->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
