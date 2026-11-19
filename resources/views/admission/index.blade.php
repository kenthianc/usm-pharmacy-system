<x-admission-layout title="Admission Command Center — USM Hospital">
    <x-slot:header>
        Admission Command Center & Inpatient Telemetry
    </x-slot:header>

    <div class="space-y-6">

        <!-- 1. Top KPI Metric Strip -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Active Inpatients -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Active Inpatients</div>
                    <div class="text-2xl font-black text-slate-800">{{ $activeAdmissions->count() }}</div>
                </div>
            </div>

            <!-- Available Beds -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Available Beds</div>
                    <div class="text-2xl font-black text-blue-700">{{ $availableBeds }} <span class="text-xs font-normal text-slate-400">/ {{ $totalBeds }}</span></div>
                </div>
            </div>

            <!-- Occupancy Rate -->
            @php
                $occupancyRate = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 1) : 0;
            @endphp
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Bed Occupancy</div>
                    <div class="text-2xl font-black text-slate-800">{{ $occupancyRate }}%</div>
                </div>
            </div>

            <!-- Pending Endorsements -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pending Endorsements</div>
                    <div class="text-2xl font-black {{ $pendingEndorsementsCount > 0 ? 'text-rose-600' : 'text-slate-700' }}">{{ $pendingEndorsementsCount }}</div>
                </div>
            </div>
        </div>

        <!-- 2. Quick Action Toolbar -->
        <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Operational Actions</h3>
                <p class="text-xs text-slate-500">Intake new patients, assign available beds, and monitor clinical wards.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admission.patients') }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <span>Patient Registry & Intake</span>
                </a>
                <a href="{{ route('admission.beds') }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-[#064e2b] hover:bg-[#053e22] text-white text-xs font-bold shadow-xs transition">
                    <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <span>View Ward & Bed Board</span>
                </a>
            </div>
        </div>

        <!-- 3. Ward Capacity Cards -->
        <div>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Live Ward Capacity Breakdown</h3>
                <a href="{{ route('admission.beds') }}" class="text-xs font-bold text-emerald-700 hover:underline">View All Beds &rarr;</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($wards as $ward)
                    @php
                        $wTotal = $ward->beds->count();
                        $wOccupied = $ward->beds->where('occupancy_status', 'occupied')->count();
                        $wAvail = $ward->beds->where('occupancy_status', 'available')->count();
                        $wPct = $wTotal > 0 ? round(($wOccupied / $wTotal) * 100) : 0;
                    @endphp
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="text-xs font-bold text-slate-800 leading-tight">{{ $ward->ward_name }}</h4>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $ward->nurse_station_assigned }}</p>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $wAvail > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                {{ $wAvail }} Free
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div>
                            <div class="flex justify-between text-[11px] text-slate-500 font-medium mb-1">
                                <span>{{ $wOccupied }} Occupied</span>
                                <span>{{ $wPct }}% Full</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full {{ $wPct > 80 ? 'bg-rose-500' : ($wPct > 50 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                     style="width: {{ $wPct }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 4. Currently Admitted Inpatients Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Admitted Inpatients Roster</h3>
                    <p class="text-xs text-slate-500">Real-time listing of active inpatient admissions, bed allocations, and nurse endorsements.</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                    {{ $activeAdmissions->count() }} Admitted
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">Patient</th>
                            <th class="py-3 px-4">Ward & Bed</th>
                            <th class="py-3 px-4">Classification</th>
                            <th class="py-3 px-4">Respiratory Status</th>
                            <th class="py-3 px-4">Baseline Vitals</th>
                            <th class="py-3 px-4">Nurse Endorsement</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($activeAdmissions as $admission)
                            <tr class="hover:bg-slate-50/60 transition">
                                <!-- Patient Info -->
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-800">{{ $admission->patient->name ?? $admission->patient->user?->name ?? 'Patient Record' }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $admission->patient->id_number }} &bull; {{ ucfirst($admission->patient->patient_type) }}</div>
                                </td>

                                <!-- Ward & Bed -->
                                <td class="py-3.5 px-4 font-medium text-slate-700">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $admission->bed?->full_location ?? 'Unassigned Bed' }}
                                    </span>
                                </td>

                                <!-- Classification -->
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $admission->patient_classification === 'Emergency' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                        {{ $admission->patient_classification }}
                                    </span>
                                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $admission->admission_date->diffForHumans() }}</div>
                                </td>

                                <!-- Respiratory Status -->
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold {{ str_contains($admission->respiratory_status, 'Room Air') ? 'text-slate-700' : 'text-blue-700 font-bold' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                        {{ $admission->respiratory_status }}
                                    </span>
                                </td>

                                <!-- Baseline Vitals -->
                                <td class="py-3.5 px-4 font-mono text-[11px] text-slate-600">
                                    {{ $admission->formatted_vitals }}
                                </td>

                                <!-- Endorsement Status -->
                                <td class="py-3.5 px-4">
                                    @php $endorsement = $admission->latestEndorsement; @endphp
                                    @if($endorsement)
                                        @if($endorsement->acknowledgment_status === 'acknowledged')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Acknowledged by {{ $endorsement->receivingNurse?->name ?? 'Nurse' }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200 animate-pulse">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Pending at {{ $endorsement->nurse_station }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-slate-400 text-[11px]">No endorsement logged</span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-4 text-right">
                                    <form method="POST" action="{{ route('admission.discharge', $admission) }}" onsubmit="return confirm('Discharge patient {{ $admission->patient->id_number }} and release bed {{ $admission->bed?->full_location }}?')">
                                        @csrf
                                        <button type="submit"
                                                class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition">
                                            Discharge
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">
                                    No active inpatients currently admitted. Use the Patient Registry to admit new patients.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-admission-layout>
