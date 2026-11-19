<x-nurse-layout title="Nurse Station Command Hub — USM Hospital">

    <div class="space-y-6">

        <!-- Top Header & Notification Banner -->
        @php
            $pendingCount = $pendingEndorsements->count();
        @endphp

        @if($pendingCount > 0)
            <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-2xl p-4 sm:p-5 text-white shadow-md flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-white animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black uppercase tracking-wide text-amber-100">Incoming Patient Endorsement Alert</h2>
                        <p class="text-xs text-white/90 font-medium">You have <strong class="underline font-bold">{{ $pendingCount }} patient endorsement{{ $pendingCount > 1 ? 's' : '' }}</strong> waiting for floor acknowledgment from Admission.</p>
                    </div>
                </div>

                <a href="{{ route('nurse.endorsements') }}"
                   class="px-4 py-2 rounded-xl bg-white text-amber-900 text-xs font-black hover:bg-amber-50 shadow-xs transition">
                    Review & Acknowledge Now &rarr;
                </a>
            </div>
        @endif

        <!-- 1. Nurse KPIs -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Admitted Patients</div>
                    <div class="text-2xl font-black text-slate-800">{{ $activeInpatients->count() }}</div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pending Endorsements</div>
                    <div class="text-2xl font-black text-amber-700">{{ $pendingCount }}</div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Prescriptions Encoded</div>
                    <div class="text-2xl font-black text-slate-800">{{ $recentPrescriptions->count() }}</div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-purple-50 border border-purple-200 text-purple-700 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Wards</div>
                    <div class="text-2xl font-black text-slate-800">{{ $wards->count() }}</div>
                </div>
            </div>
        </div>

        <!-- 2. Endorsements Acknowledgment Quick Strip -->
        @if($pendingCount > 0)
            <div class="bg-white rounded-2xl border border-amber-200 shadow-xs overflow-hidden">
                <div class="px-5 py-3.5 bg-amber-50/70 border-b border-amber-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                        <h3 class="text-xs font-black uppercase tracking-wider text-amber-900">Urgent: Incoming Endorsements Awaiting Floor Nurse</h3>
                    </div>
                    <a href="{{ route('nurse.endorsements') }}" class="text-xs font-bold text-amber-800 hover:underline">View Full Queue &rarr;</a>
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach($pendingEndorsements as $endorsement)
                        <div class="p-4 flex flex-wrap items-center justify-between gap-4 hover:bg-slate-50/60 transition">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-sm font-bold text-slate-800">{{ $endorsement->admission->patient->name ?? $endorsement->admission->patient->user?->name ?? 'Patient' }}</h4>
                                    <span class="text-xs font-mono text-slate-400">({{ $endorsement->admission->patient->id_number }})</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        {{ $endorsement->admission->bed?->full_location }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 italic">"{{ Str::limit($endorsement->endorsement_notes, 100) }}"</p>
                                <p class="text-[10px] text-slate-400">Endorsed by {{ $endorsement->endorsedBy?->name ?? 'Admission' }} &bull; {{ $endorsement->created_at->diffForHumans() }}</p>
                            </div>

                            <form method="POST" action="{{ route('nurse.endorsements.acknowledge', $endorsement) }}">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>Acknowledge Patient</span>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 3. Active Inpatients Under Floor Care -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Current Inpatients Roster</h3>
                    <p class="text-xs text-slate-500">Admitted patients currently under clinical monitoring in hospital wards.</p>
                </div>
                <a href="{{ route('nurse.inpatients') }}" class="text-xs font-bold text-emerald-700 hover:underline">View All Inpatients &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">Patient</th>
                            <th class="py-3 px-4">Bed Location</th>
                            <th class="py-3 px-4">Vitals & Respiratory</th>
                            <th class="py-3 px-4">Chief Complaint</th>
                            <th class="py-3 px-4">Prescriptions</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($activeInpatients as $admission)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-800">{{ $admission->patient->name ?? $admission->patient->user?->name ?? 'Patient' }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $admission->patient->id_number }}</div>
                                </td>

                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold">
                                        {{ $admission->bed?->full_location }}
                                    </span>
                                </td>

                                <td class="py-3 px-4">
                                    <div class="font-mono text-[11px] text-slate-700">{{ $admission->formatted_vitals }}</div>
                                    <div class="text-[10px] text-blue-600 font-bold mt-0.5">{{ $admission->respiratory_status }}</div>
                                </td>

                                <td class="py-3 px-4 text-slate-600 max-w-xs truncate">
                                    {{ $admission->chief_complaint }}
                                </td>

                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700">
                                        {{ $admission->prescriptions->count() }} active Rx
                                    </span>
                                </td>

                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('nurse.chart', $admission) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold shadow-xs transition">
                                        <span>Clinical Chart</span>
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

    </div>
</x-nurse-layout>
