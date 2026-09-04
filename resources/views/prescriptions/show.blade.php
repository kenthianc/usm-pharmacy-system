<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('prescriptions.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">&larr; Back to Prescriptions</a>
                </div>
                <div class="flex items-center space-x-3 mt-1">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Prescription #{{ str_pad($prescription->id, 5, '0', STR_PAD_LEFT) }}
                    </h2>
                    @php
                        $badges = [
                            'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                            'routed' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'dispensed' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'cancelled' => 'bg-gray-100 text-gray-700 border-gray-200',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase border {{ $badges[$prescription->status] ?? 'bg-gray-100' }}">
                        {{ $prescription->status }}
                    </span>
                </div>
            </div>

            <!-- Workflow Action Buttons -->
            <div class="flex items-center space-x-3">
                @if ($prescription->status === 'pending')
                    @can('cancel', $prescription)
                        <form method="POST" action="{{ route('prescriptions.cancel', $prescription) }}"
                              onsubmit="return confirm('Are you sure you want to cancel this prescription?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center px-3.5 py-2 bg-white border border-gray-300 rounded-md text-xs font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-sm">
                                Cancel
                            </button>
                        </form>
                    @endcan

                    @can('route', $prescription)
                        <form method="POST" action="{{ route('prescriptions.route', $prescription) }}"
                              onsubmit="return confirm('Route this prescription to the Pharmacy Queue?');">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-xs font-semibold text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 shadow-sm transition">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                                Route to Pharmacy
                            </button>
                        </form>
                    @endcan
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-md bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Patient Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Patient Details</h3>
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-base font-semibold text-gray-900">{{ $prescription->patient->name }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">ID: {{ $prescription->patient->id_number }}</div>
                            @if ($prescription->patient->contact_number)
                                <div class="text-xs text-gray-500 mt-0.5">Contact: {{ $prescription->patient->contact_number }}</div>
                            @endif
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold uppercase {{ $prescription->patient->patient_type === 'student' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-purple-50 text-purple-700 border border-purple-200' }}">
                            {{ $prescription->patient->patient_type }}
                        </span>
                    </div>
                </div>

                <!-- Prescription Details Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Prescription Details</h3>
                    <div class="space-y-1.5 text-xs">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Attending Physician:</span>
                            <span class="font-medium text-gray-900">Dr. {{ $prescription->doctor_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Encoded By:</span>
                            <span class="font-medium text-gray-900">{{ $prescription->encodedBy->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Encoded At:</span>
                            <span class="font-medium text-gray-900">{{ $prescription->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Current Status:</span>
                            <span class="font-medium uppercase text-gray-900">{{ $prescription->status }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prescribed Line Items -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Prescribed Medicine Items</h3>
                        <p class="text-xs text-gray-500">Items and dosage instructions prescribed for the patient.</p>
                    </div>
                    <span class="text-xs font-semibold text-gray-600">
                        Total Items: {{ $prescription->items->count() }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                        <thead class="bg-gray-50 text-gray-600 font-semibold uppercase">
                            <tr>
                                <th scope="col" class="px-6 py-3">Medicine</th>
                                <th scope="col" class="px-6 py-3">Category</th>
                                <th scope="col" class="px-6 py-3">Prescribed Qty</th>
                                <th scope="col" class="px-6 py-3">Current Stock</th>
                                <th scope="col" class="px-6 py-3">Dosage Instructions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach ($prescription->items as $item)
                                @php
                                    $available = $item->medicine->available_stock;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900">{{ $item->medicine->name }}</div>
                                        <div class="text-[11px] text-gray-500">{{ $item->medicine->generic_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $item->medicine->category }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-900">
                                        {{ $item->quantity }} {{ $item->medicine->unit }}{{ $item->quantity > 1 ? 's' : '' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium {{ $available >= $item->quantity ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                            {{ $available }} in stock
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 font-medium">
                                        {{ $item->dosage_instructions }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Workflow Status Banner -->
            @if ($prescription->status === 'routed')
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-5 flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold text-blue-900">Queued in Dispensary / POS</h4>
                        <p class="text-xs text-blue-700 mt-0.5">This prescription has been routed to the pharmacy. Pharmacists will fulfill the order via First-Expired-First-Out (FEFO) batch allocation.</p>
                    </div>
                </div>
            @elseif ($prescription->status === 'dispensed')
                <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-5 flex items-start space-x-3">
                    <svg class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold text-emerald-900">Prescription Dispensed</h4>
                        <p class="text-xs text-emerald-700 mt-0.5">Medicine inventory has been deducted and transaction logged in the Pharmacy POS system.</p>
                    </div>
                </div>
            @elseif ($prescription->status === 'cancelled')
                <div class="bg-gray-100 border border-gray-300 rounded-lg p-5 flex items-start space-x-3">
                    <svg class="w-5 h-5 text-gray-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-800">Prescription Cancelled</h4>
                        <p class="text-xs text-gray-600 mt-0.5">This prescription was cancelled and is no longer active.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

