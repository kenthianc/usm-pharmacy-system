<x-nurse-layout title="Nurse Station Command Hub — USM Hospital">

    <div class="space-y-4">

        <!-- ── TOP TELEMETRY & ACTION BAR ────────────────────────────────────── -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                
                <!-- Left: Station Identification & Live Shift Status -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#064e2b] text-white flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-sm font-extrabold uppercase tracking-wide text-slate-800">Nurse Station Command Hub</h1>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Station 1 &bull; Active Floor Shift
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Real-time clinical charting, bedside vitals monitoring, and instant pharmacy prescription dispatch.</p>
                    </div>
                </div>

                <!-- Right: Quick Action Buttons -->
                <div class="flex items-center gap-2 flex-wrap shrink-0">
                    <button type="button"
                            @click="$dispatch('open-new-prescription')"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#064e2b] hover:bg-[#053e22] text-white text-xs font-bold shadow-xs transition">
                        <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Encode Inpatient Rx</span>
                    </button>

                    <a href="{{ route('nurse.endorsements') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Endorsements ({{ $pendingEndorsements->count() }})</span>
                    </a>

                    <a href="{{ route('nurse.inpatients') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>Full Census</span>
                    </a>
                </div>
            </div>

            <!-- Glanceable 4-KPI Compact Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4 pt-3.5 border-t border-slate-100">
                <!-- Admitted Patients -->
                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs shrink-0">
                        {{ $activeInpatients->count() }}
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Admitted Patients</div>
                        <div class="text-xs font-extrabold text-slate-800">In Clinical Beds</div>
                    </div>
                </div>

                <!-- Pending Endorsements -->
                @php $pendingCount = $pendingEndorsements->count(); @endphp
                <div class="flex items-center gap-3 p-2.5 rounded-xl {{ $pendingCount > 0 ? 'bg-amber-50 border border-amber-200' : 'bg-slate-50 border border-slate-100' }}">
                    <div class="w-8 h-8 rounded-lg {{ $pendingCount > 0 ? 'bg-amber-500 text-white animate-pulse' : 'bg-slate-200 text-slate-600' }} flex items-center justify-center font-bold text-xs shrink-0">
                        {{ $pendingCount }}
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Pending Endorsements</div>
                        <div class="text-xs font-extrabold {{ $pendingCount > 0 ? 'text-amber-800' : 'text-slate-700' }}">
                            {{ $pendingCount > 0 ? 'Awaiting Nurse' : 'All Acknowledged' }}
                        </div>
                    </div>
                </div>

                <!-- Prescriptions Pipeline -->
                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-800 flex items-center justify-center font-bold text-xs shrink-0">
                        {{ $rxCounts['routed'] ?? 0 }}
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">In Pharmacy Queue</div>
                        <div class="text-xs font-extrabold text-blue-700">{{ $rxCounts['dispensed'] ?? 0 }} Dispensed Today</div>
                    </div>
                </div>

                <!-- Bed Capacity -->
                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-800 flex items-center justify-center font-bold text-xs shrink-0">
                        {{ $bedTelemetry['occupancy_rate'] ?? 0 }}%
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Hospital Bed Occupancy</div>
                        <div class="text-xs font-extrabold text-purple-800">{{ $bedTelemetry['occupied'] ?? 0 }} / {{ $bedTelemetry['total'] ?? 0 }} Beds ({{ $bedTelemetry['available'] ?? 0 }} Free)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── URGENT INCOMING ENDORSEMENTS ALERT (IF ANY) ──────────────────── -->
        @if($pendingCount > 0)
            <div class="bg-amber-500 rounded-2xl p-3.5 text-white shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white/20 backdrop-blur-md flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-white animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xs font-black uppercase tracking-wide text-amber-100">Urgent: Incoming Endorsement Awaiting Floor Reception</h2>
                        <p class="text-xs text-white font-medium">
                            {{ $pendingCount }} patient{{ $pendingCount > 1 ? 's have' : ' has' }} been admitted by Admission and assigned to your floor station. Acknowledge immediately to initiate clinical charting.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    @foreach($pendingEndorsements->take(2) as $endorsement)
                        <form method="POST" action="{{ route('nurse.endorsements.acknowledge', $endorsement) }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white text-amber-900 text-xs font-black hover:bg-amber-50 shadow-xs transition">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Acknowledge {{ Str::limit($endorsement->admission->patient->name ?? 'Patient', 12) }} ({{ $endorsement->admission->bed?->bed_number ?? 'Bed' }})</span>
                            </button>
                        </form>
                    @endforeach
                    @if($pendingCount > 2)
                        <a href="{{ route('nurse.endorsements') }}" class="text-xs font-bold text-white underline hover:text-amber-100">
                            +{{ $pendingCount - 2 }} more &rarr;
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <!-- ── 2-COLUMN ABOVE-THE-FOLD CLINICAL CONSOLE ───────────────────────── -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

            <!-- LEFT (8 COLS): Live Inpatients Under Floor Care -->
            <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden flex flex-col">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">Current Inpatients Under Floor Monitoring</h3>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">
                            {{ $activeInpatients->count() }} Active
                        </span>
                    </div>
                    <a href="{{ route('nurse.inpatients') }}" class="text-xs font-bold text-emerald-700 hover:underline">
                        View All Inpatients &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="py-2.5 px-3.5">Patient</th>
                                <th class="py-2.5 px-3">Ward &amp; Bed</th>
                                <th class="py-2.5 px-3">Vitals &amp; Respiratory</th>
                                <th class="py-2.5 px-3">Chief Complaint</th>
                                <th class="py-2.5 px-2.5 text-center">Rx</th>
                                <th class="py-2.5 px-3.5 text-right">Chart</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($activeInpatients as $admission)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <!-- Patient -->
                                    <td class="py-2.5 px-3.5">
                                        <div class="font-bold text-slate-900 leading-tight">{{ $admission->patient->name ?? $admission->patient->user?->name ?? 'Patient' }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                            {{ $admission->patient->id_number }} &bull; <span class="capitalize">{{ $admission->patient->patient_type }}</span>
                                        </div>
                                    </td>

                                    <!-- Bed Location -->
                                    <td class="py-2.5 px-3">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-[11px] font-bold">
                                            {{ $admission->bed?->full_location }}
                                        </span>
                                    </td>

                                    <!-- Vitals & Respiratory -->
                                    <td class="py-2.5 px-3">
                                        <div class="font-mono text-[11px] text-slate-800 font-medium">{{ $admission->formatted_vitals }}</div>
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold {{ str_contains($admission->respiratory_status, 'Room Air') ? 'text-slate-600' : 'text-blue-700' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ str_contains($admission->respiratory_status, 'Room Air') ? 'bg-slate-400' : 'bg-blue-600' }}"></span>
                                            {{ $admission->respiratory_status }}
                                        </span>
                                    </td>

                                    <!-- Chief Complaint -->
                                    <td class="py-2.5 px-3 max-w-[180px] truncate text-slate-600" title="{{ $admission->chief_complaint }}">
                                        {{ $admission->chief_complaint }}
                                    </td>

                                    <!-- Rx count -->
                                    <td class="py-2.5 px-2.5 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $admission->prescriptions->count() > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-500' }}">
                                            {{ $admission->prescriptions->count() }} Rx
                                        </span>
                                    </td>

                                    <!-- Action -->
                                    <td class="py-2.5 px-3.5 text-right">
                                        <a href="{{ route('nurse.chart', $admission) }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-[#064e2b] text-white text-[11px] font-bold shadow-2xs transition">
                                            <span>Chart</span>
                                            &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400">
                                        No patients currently admitted in hospital wards.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- RIGHT (4 COLS): Prescription Dispatch & Bed Availability Snapshot -->
            <div class="lg:col-span-4 space-y-4">

                <!-- 1. Prescriptions Status Card -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">Prescription Dispatch</h3>
                        </div>
                        <a href="{{ route('prescriptions.index') }}" class="text-xs font-bold text-blue-700 hover:underline">
                            Queue &rarr;
                        </a>
                    </div>

                    <!-- Pipeline Mini Badges -->
                    <div class="grid grid-cols-3 gap-2 my-3 text-center">
                        <div class="p-2 rounded-xl bg-amber-50 border border-amber-200">
                            <div class="text-base font-black text-amber-700">{{ $rxCounts['pending'] ?? 0 }}</div>
                            <div class="text-[9px] uppercase font-bold text-amber-600">Pending Review</div>
                        </div>
                        <div class="p-2 rounded-xl bg-blue-50 border border-blue-200">
                            <div class="text-base font-black text-blue-700">{{ $rxCounts['routed'] ?? 0 }}</div>
                            <div class="text-[9px] uppercase font-bold text-blue-600">In Pharmacy</div>
                        </div>
                        <div class="p-2 rounded-xl bg-emerald-50 border border-emerald-200">
                            <div class="text-base font-black text-emerald-700">{{ $rxCounts['dispensed'] ?? 0 }}</div>
                            <div class="text-[9px] uppercase font-bold text-emerald-600">Dispensed</div>
                        </div>
                    </div>

                    <!-- Recent Prescriptions Encoded by Nurse -->
                    <div class="divide-y divide-slate-100 text-xs">
                        @forelse($recentPrescriptions->take(3) as $rx)
                            <div class="py-2.5 flex items-center justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-slate-800 truncate">
                                        {{ $rx->patient->name ?? $rx->patient->user?->name ?? 'Patient' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                        <span>#{{ str_pad($rx->id, 4, '0', STR_PAD_LEFT) }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $rx->items->count() }} item(s)</span>
                                        <span>&bull;</span>
                                        <span>{{ $rx->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase shrink-0 {{ $rx->status === 'dispensed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($rx->status === 'routed' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-amber-50 text-amber-700 border border-amber-200') }}">
                                    {{ $rx->status }}
                                </span>
                            </div>
                        @empty
                            <div class="py-3 text-center text-slate-400 text-xs">
                                No recent prescriptions encoded today.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- 2. Live Ward Occupancy Mini-Radar -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">Hospital Ward Beds</h3>
                        </div>
                        <a href="{{ route('admission.beds') }}" class="text-xs font-bold text-purple-700 hover:underline">
                            Bed Board &rarr;
                        </a>
                    </div>

                    <div class="space-y-2.5 mt-3">
                        @foreach($wards as $ward)
                            @php
                                $wTotal = $ward->beds->count();
                                $wOcc = $ward->beds->where('occupancy_status', 'occupied')->count();
                                $wAvail = $ward->beds->where('occupancy_status', 'available')->count();
                                $wPct = $wTotal > 0 ? round(($wOcc / $wTotal) * 100) : 0;
                            @endphp
                            <div class="text-xs">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-bold text-slate-700 truncate max-w-[140px]">{{ $ward->ward_name }}</span>
                                    <span class="text-[11px] font-mono text-slate-500">
                                        <strong class="{{ $wAvail > 0 ? 'text-emerald-700' : 'text-rose-600' }}">{{ $wAvail }} Free</strong> / {{ $wTotal }}
                                    </span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="h-1.5 rounded-full {{ $wPct > 80 ? 'bg-rose-500' : ($wPct > 50 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                         style="width: {{ $wPct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>

    </div>

</x-nurse-layout>
