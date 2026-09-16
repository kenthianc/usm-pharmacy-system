<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Welcome back, {{ $patient->user->name }} 👋
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Patient ID: {{ $patient->id_number }} &bull; {{ ucfirst($patient->patient_type) }}
                </p>
            </div>
            <a href="{{ route('patient.prescriptions') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition ease-in-out duration-150 shadow-sm">
                View All Prescriptions
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 flex items-center gap-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ $counts['total'] }}</p>
                        <p class="text-xs text-gray-500">Total Prescriptions</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 flex items-center gap-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ $counts['pending'] }}</p>
                        <p class="text-xs text-gray-500">Pending</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 flex items-center gap-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ $counts['dispensed'] }}</p>
                        <p class="text-xs text-gray-500">Dispensed</p>
                    </div>
                </div>
            </div>

            {{-- Recent Prescriptions --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700">Recent Prescriptions</h3>
                    <a href="{{ route('patient.prescriptions') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                        View all →
                    </a>
                </div>

                @if ($recentPrescriptions->isEmpty())
                    <div class="px-6 py-10 text-center">
                        <svg class="mx-auto w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm text-gray-500">No prescriptions yet.</p>
                    </div>
                @else
                    <ul class="divide-y divide-gray-100">
                        @foreach ($recentPrescriptions as $prescription)
                            <li>
                                <a href="{{ route('patient.prescriptions.show', $prescription->id) }}"
                                   class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium text-gray-800">
                                                Prescription #{{ $prescription->id }}
                                            </span>
                                            @php
                                                $statusColors = [
                                                    'pending'   => 'bg-amber-100 text-amber-700',
                                                    'routed'    => 'bg-blue-100 text-blue-700',
                                                    'dispensed' => 'bg-emerald-100 text-emerald-700',
                                                    'cancelled' => 'bg-rose-100 text-rose-700',
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusColors[$prescription->status] ?? 'bg-gray-100 text-gray-600' }}">
                                                {{ ucfirst($prescription->status) }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            Dr. {{ $prescription->encodedBy->name ?? '—' }}
                                            &bull; {{ $prescription->items->count() }} item(s)
                                            &bull; {{ $prescription->created_at->format('M d, Y') }}
                                        </p>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0 ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Quick Links --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('patient.profile') }}"
                   class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 flex items-center gap-4 hover:bg-gray-50 transition-colors">
                    <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">My Medical Profile</p>
                        <p class="text-xs text-gray-500">View allergies, medical notes, and contact info</p>
                    </div>
                </a>

                <a href="{{ route('home') }}"
                   class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 flex items-center gap-4 hover:bg-gray-50 transition-colors">
                    <div class="flex-shrink-0 w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Visit Store</p>
                        <p class="text-xs text-gray-500">Browse available medicines and use the chatbot</p>
                    </div>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
