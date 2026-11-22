<x-nurse-layout title="Admitted Inpatient Roster — Nurse Station">

    <div class="space-y-6">

        <!-- Top Header & Filter -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-sm font-bold text-slate-800">Inpatient Monitoring & Bed Roster</h2>
                <p class="text-xs text-slate-500">Live clinical surveillance of admitted patients across hospital wards.</p>
            </div>

            <!-- Ward Filter Dropdown -->
            <form method="GET" action="{{ route('nurse.inpatients') }}" class="flex items-center gap-2">
                <label for="ward_id" class="text-xs font-bold text-slate-600">Ward:</label>
                <select name="ward_id" id="ward_id" onchange="this.form.submit()"
                        class="text-xs rounded-xl border-slate-300 py-1.5 px-3 focus:ring-emerald-600 focus:border-emerald-600">
                    <option value="">All Hospital Wards</option>
                    @foreach($wards as $w)
                        <option value="{{ $w->id }}" {{ (string)$wardFilter === (string)$w->id ? 'selected' : '' }}>
                            {{ $w->ward_name }}
                        </option>
                    @endforeach
                </select>
                @if($wardFilter)
                    <a href="{{ route('nurse.inpatients') }}" class="text-xs text-rose-600 hover:underline font-bold">Clear</a>
                @endif
            </form>
        </div>

        <!-- Inpatient Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($inpatients as $admission)
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between space-y-4 hover:border-slate-300 transition">
                    <div class="space-y-3">
                        <!-- Card Header -->
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $admission->patient_classification === 'Emergency' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $admission->patient_classification }}
                                </span>
                                <h3 class="text-sm font-bold text-slate-800 mt-1">{{ $admission->patient->name ?? $admission->patient->user?->name ?? 'Patient' }}</h3>
                                <p class="text-[11px] text-slate-400 font-mono">{{ $admission->patient->id_number }} &bull; {{ ucfirst($admission->patient->patient_type) }}</p>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 font-black text-xs">
                                {{ $admission->bed?->room_number }} &bull; {{ $admission->bed?->bed_number }}
                            </span>
                        </div>

                        <!-- Endorsement Status -->
                        <div>
                            @if($admission->latestEndorsement && $admission->latestEndorsement->acknowledgment_status === 'pending')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Pending Nurse Acknowledgment
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Received into Ward Care
                                </span>
                            @endif
                        </div>

                        <!-- Ward info -->
                        <div class="text-[11px] text-slate-500">
                            <strong>Ward:</strong> {{ $admission->bed?->ward?->ward_name ?? 'Inpatient Ward' }}
                            <div class="text-[10px] text-slate-400">Admitted {{ $admission->admission_date->format('M d, Y h:i A') }} ({{ $admission->admission_date->diffForHumans() }})</div>
                        </div>

                        <!-- Vitals -->
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs space-y-1">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Current Vitals:</div>
                            <div class="font-mono text-slate-700 text-[11px]">{{ $admission->formatted_vitals }}</div>
                            <div class="text-[10px] text-blue-700 font-bold flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span>{{ $admission->respiratory_status }}</span>
                            </div>
                        </div>

                        <!-- Complaint -->
                        <div class="text-xs text-slate-600 line-clamp-2">
                            <strong class="text-slate-700">Complaint:</strong> {{ $admission->chief_complaint }}
                        </div>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        <span class="text-[11px] font-bold text-slate-500">
                            {{ $admission->prescriptions->count() }} Prescriptions
                        </span>

                        <div class="flex items-center gap-1.5">
                            @if($admission->latestEndorsement && $admission->latestEndorsement->acknowledgment_status === 'pending')
                                <form method="POST" action="{{ route('nurse.endorsements.acknowledge', $admission->latestEndorsement) }}">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-xs transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Acknowledge</span>
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('nurse.chart', $admission) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#064e2b] hover:bg-[#053e22] text-white text-xs font-bold shadow-xs transition">
                                <span>Clinical Chart</span>
                                &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white p-12 rounded-2xl border border-slate-200 text-center text-slate-400">
                    No active inpatients found for the selected ward filter.
                </div>
            @endforelse
        </div>

        @if($inpatients->hasPages())
            <div class="p-4 bg-white rounded-2xl border border-slate-200">
                {{ $inpatients->links() }}
            </div>
        @endif

    </div>
</x-nurse-layout>
