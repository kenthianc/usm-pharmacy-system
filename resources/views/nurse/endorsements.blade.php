<x-nurse-layout title="Patient Endorsement Queue — Nurse Station">

    <div class="space-y-6">

        <!-- Top Header & Tabs -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-sm font-bold text-slate-800">Incoming Patient Endorsement Queue</h2>
                <p class="text-xs text-slate-500">Review clinical transfers and acknowledge incoming patients dispatched from Admission.</p>
            </div>

            <!-- Tab Pills -->
            <div class="flex items-center gap-2">
                <a href="{{ route('nurse.endorsements', ['status' => 'pending']) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold transition {{ $statusFilter === 'pending' ? 'bg-amber-500 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <span class="w-2 h-2 rounded-full {{ $pendingCount > 0 ? 'bg-white animate-pulse' : 'bg-slate-400' }}"></span>
                    <span>Pending Acknowledgment ({{ $pendingCount }})</span>
                </a>
                <a href="{{ route('nurse.endorsements', ['status' => 'acknowledged']) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold transition {{ $statusFilter === 'acknowledged' ? 'bg-[#064e2b] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Acknowledged History</span>
                </a>
            </div>
        </div>

        <!-- Endorsement Cards List -->
        <div class="space-y-4">
            @forelse($endorsements as $endorsement)
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4 transition hover:border-slate-300">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl {{ $endorsement->acknowledgment_status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }} font-black text-sm flex items-center justify-center shrink-0">
                                ℞
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-800">{{ $endorsement->admission->patient->name ?? $endorsement->admission->patient->user?->name ?? 'Patient Record' }}</h3>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $endorsement->admission->patient->patient_type === 'student' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $endorsement->admission->patient->patient_type }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 font-mono mt-0.5">
                                    ID: {{ $endorsement->admission->patient->id_number }} &bull; Bed: <span class="font-bold text-emerald-800">{{ $endorsement->admission->bed?->full_location }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Status Badge / Action -->
                        <div>
                            @if($endorsement->acknowledgment_status === 'pending')
                                <form method="POST" action="{{ route('nurse.endorsements.acknowledge', $endorsement) }}">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition cursor-pointer">
                                        <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span>1-Click Acknowledge</span>
                                    </button>
                                </form>
                            @else
                                <div class="text-right">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Acknowledged by {{ $endorsement->receivingNurse?->name ?? 'Floor Nurse' }}
                                    </span>
                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $endorsement->acknowledged_at?->format('M d, Y h:i A') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Clinical Intake Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Chief Complaint:</span>
                            <p class="text-slate-700 font-medium mt-0.5">{{ $endorsement->admission->chief_complaint }}</p>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Baseline Vitals & Respiratory:</span>
                            <p class="text-slate-700 font-mono mt-0.5">{{ $endorsement->admission->formatted_vitals }}</p>
                            <p class="text-[11px] text-blue-700 font-bold mt-0.5">{{ $endorsement->admission->respiratory_status }}</p>
                        </div>
                    </div>

                    <!-- Endorsement Clinical Note -->
                    <div class="text-xs">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Admission Staff Endorsement Note:</span>
                        <blockquote class="mt-1 pl-3 border-l-2 border-emerald-600 text-slate-700 italic bg-emerald-50/20 py-1.5 rounded-r-lg">
                            "{{ $endorsement->endorsement_notes }}"
                        </blockquote>
                        <div class="text-[10px] text-slate-400 mt-1">
                            Dispatched by {{ $endorsement->endorsedBy?->name ?? 'Admission Officer' }} &bull; {{ $endorsement->created_at->format('M d, Y h:i A') }} ({{ $endorsement->created_at->diffForHumans() }})
                        </div>
                    </div>

                    <!-- Footer Link to Chart -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-400">Target Station: <strong class="text-slate-700">{{ $endorsement->nurse_station }}</strong></span>
                        <a href="{{ route('nurse.chart', $endorsement->admission) }}" class="font-bold text-emerald-700 hover:underline">
                            Open Patient Clinical Chart &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white p-12 rounded-2xl border border-slate-200 text-center text-slate-400">
                    No endorsements found for this status.
                </div>
            @endforelse
        </div>

        @if($endorsements->hasPages())
            <div class="p-4 bg-white rounded-2xl border border-slate-200">
                {{ $endorsements->links() }}
            </div>
        @endif

    </div>
</x-nurse-layout>
