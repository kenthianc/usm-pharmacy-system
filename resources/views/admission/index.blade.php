<x-admission-layout title="Admission Command Center — USM Hospital">
    <x-slot:header>
        Admission Command Center &amp; Inpatient Telemetry
    </x-slot:header>

    <div class="space-y-4">

        <!-- ── TOP TELEMETRY & OPERATIONAL ACTION BAR ────────────────────────── -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                
                <!-- Left: Facility Summary & Live Status -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#064e2b] text-white flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-sm font-extrabold uppercase tracking-wide text-slate-800">Admission Command Center</h1>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Clinical Intake Live
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Real-time bed telemetry, emergency triage allotment, and floor nurse endorsements.</p>
                    </div>
                </div>

                <!-- Right: Quick Operational Actions -->
                <div class="flex items-center gap-2 flex-wrap shrink-0">
                    <a href="{{ route('admission.patients') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#064e2b] hover:bg-[#053e22] text-white text-xs font-bold shadow-xs transition">
                        <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        <span>Patient Registry &amp; Intake</span>
                    </a>

                    <a href="{{ route('admission.beds') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        <span>Ward &amp; Bed Board ({{ $availableBeds }} Free)</span>
                    </a>
                </div>
            </div>

            <!-- Glanceable 4-KPI Compact Strip -->
            @php
                $occupancyRate = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 1) : 0;
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4 pt-3.5 border-t border-slate-100">
                <!-- Active Inpatients -->
                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs shrink-0">
                        {{ $activeAdmissions->count() }}
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Active Inpatients</div>
                        <div class="text-xs font-extrabold text-slate-800">Currently Admitted</div>
                    </div>
                </div>

                <!-- Available Beds -->
                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-800 flex items-center justify-center font-bold text-xs shrink-0">
                        {{ $availableBeds }}
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Available Beds</div>
                        <div class="text-xs font-extrabold text-blue-700">Ready for Allotment ({{ $totalBeds }} total)</div>
                    </div>
                </div>

                <!-- Bed Occupancy Rate -->
                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-xs shrink-0">
                        {{ $occupancyRate }}%
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Bed Occupancy</div>
                        <div class="text-xs font-extrabold text-amber-800">{{ $occupiedBeds }} Occupied &bull; {{ $maintenanceBeds }} Maint.</div>
                    </div>
                </div>

                <!-- Pending Nurse Endorsements -->
                <div class="flex items-center gap-3 p-2.5 rounded-xl {{ $pendingEndorsementsCount > 0 ? 'bg-rose-50 border border-rose-200' : 'bg-slate-50 border border-slate-100' }}">
                    <div class="w-8 h-8 rounded-lg {{ $pendingEndorsementsCount > 0 ? 'bg-rose-500 text-white animate-pulse' : 'bg-slate-200 text-slate-600' }} flex items-center justify-center font-bold text-xs shrink-0">
                        {{ $pendingEndorsementsCount }}
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Pending Endorsements</div>
                        <div class="text-xs font-extrabold {{ $pendingEndorsementsCount > 0 ? 'text-rose-700' : 'text-slate-700' }}">
                            {{ $pendingEndorsementsCount > 0 ? 'Awaiting Nurse Reception' : 'All Acknowledged' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── 2-COLUMN SPLIT CONSOLE (Live Wards + Admitted Roster) ────────── -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

            <!-- LEFT (4 COLS): Live Ward Capacity Breakdown -->
            <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/90 shadow-xs p-4 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">Live Ward Capacity</h3>
                        </div>
                        <a href="{{ route('admission.beds') }}" class="text-xs font-bold text-emerald-700 hover:underline">
                            Bed Board &rarr;
                        </a>
                    </div>

                    <div class="space-y-3 mt-3">
                        @foreach($wards as $ward)
                            @php
                                $wTotal = $ward->beds->count();
                                $wOccupied = $ward->beds->where('occupancy_status', 'occupied')->count();
                                $wAvail = $ward->beds->where('occupancy_status', 'available')->count();
                                $wPct = $wTotal > 0 ? round(($wOccupied / $wTotal) * 100) : 0;
                            @endphp
                            <div class="p-2.5 rounded-xl border border-slate-100 bg-slate-50/60 hover:bg-slate-50 transition">
                                <div class="flex items-center justify-between">
                                    <div class="min-w-0 pr-2">
                                        <h4 class="text-xs font-bold text-slate-800 truncate">{{ $ward->ward_name }}</h4>
                                        <p class="text-[10px] text-slate-400 truncate">{{ $ward->nurse_station_assigned }}</p>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase shrink-0 {{ $wAvail > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                        {{ $wAvail }} Free
                                    </span>
                                </div>

                                <div class="mt-2">
                                    <div class="flex justify-between text-[10px] text-slate-500 font-medium mb-1">
                                        <span>{{ $wOccupied }}/{{ $wTotal }} Beds</span>
                                        <span>{{ $wPct }}% Full</span>
                                    </div>
                                    <div class="w-full bg-slate-200/80 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-1.5 rounded-full {{ $wPct > 80 ? 'bg-rose-500' : ($wPct > 50 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                             style="width: {{ $wPct }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-3 mt-3 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                    <span>Total Clinical Beds: <strong class="text-slate-800 font-mono">{{ $totalBeds }}</strong></span>
                    <span class="text-emerald-700 font-bold font-mono">{{ $availableBeds }} Available</span>
                </div>
            </div>

            <!-- RIGHT (8 COLS): Admitted Inpatients Roster -->
            <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden flex flex-col">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">Current Inpatients Roster &amp; Endorsement Status</h3>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700">
                            {{ $activeAdmissions->count() }} Admitted
                        </span>
                    </div>
                    <a href="{{ route('admission.patients') }}" class="text-xs font-bold text-[#064e2b] hover:underline">
                        + New Admission &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="py-2.5 px-3.5">Patient</th>
                                <th class="py-2.5 px-3">Ward &amp; Bed</th>
                                <th class="py-2.5 px-3">Classification</th>
                                <th class="py-2.5 px-3">Respiratory &amp; Vitals</th>
                                <th class="py-2.5 px-3">Floor Endorsement</th>
                                <th class="py-2.5 px-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($activeAdmissions as $admission)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <!-- Patient -->
                                    <td class="py-2.5 px-3.5">
                                        <div class="font-bold text-slate-900 leading-tight">{{ $admission->patient->name ?? $admission->patient->user?->name ?? 'Patient Record' }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                            {{ $admission->patient->id_number }} &bull; <span class="capitalize">{{ $admission->patient->patient_type }}</span>
                                        </div>
                                    </td>

                                    <!-- Ward & Bed -->
                                    <td class="py-2.5 px-3">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-[11px] font-bold">
                                            {{ $admission->bed?->full_location ?? 'Unassigned Bed' }}
                                        </span>
                                    </td>

                                    <!-- Classification -->
                                    <td class="py-2.5 px-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $admission->patient_classification === 'Emergency' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                            {{ $admission->patient_classification }}
                                        </span>
                                        <div class="text-[10px] text-slate-400 mt-0.5">{{ $admission->admission_date->diffForHumans() }}</div>
                                    </td>

                                    <!-- Respiratory & Vitals -->
                                    <td class="py-2.5 px-3">
                                        <div class="font-mono text-[11px] text-slate-800">{{ $admission->formatted_vitals }}</div>
                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold {{ str_contains($admission->respiratory_status, 'Room Air') ? 'text-slate-600' : 'text-blue-700 font-bold' }}">
                                            {{ $admission->respiratory_status }}
                                        </span>
                                    </td>

                                    <!-- Endorsement Status -->
                                    <td class="py-2.5 px-3">
                                        @php $endorsement = $admission->latestEndorsement; @endphp
                                        @if($endorsement)
                                            @if($endorsement->acknowledgment_status === 'acknowledged')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    Received: {{ $endorsement->receivingNurse?->name ?? 'Floor Nurse' }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200 animate-pulse">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    Pending: {{ $endorsement->nurse_station }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-slate-400 text-[10px]">No endorsement</span>
                                        @endif
                                    </td>

                                    <!-- Action -->
                                    <td class="py-2.5 px-3 text-right">
                                        <form method="POST" action="{{ route('admission.discharge', $admission) }}" onsubmit="return confirm('Discharge patient {{ $admission->patient->id_number }} and release bed {{ $admission->bed?->full_location }}?')">
                                            @csrf
                                            <button type="submit"
                                                    class="px-2 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-[11px] font-bold transition">
                                                Discharge
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400">
                                        No active inpatients currently admitted. Use Patient Registry to admit new patients.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</x-admission-layout>
