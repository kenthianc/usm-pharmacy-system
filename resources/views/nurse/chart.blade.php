<x-nurse-layout title="Clinical Chart — USM Hospital">

    <div class="max-w-5xl mx-auto space-y-6">

        <!-- Back Button -->
        <div>
            <a href="{{ route('nurse.inpatients') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-emerald-700 transition">
                &larr; Back to Inpatient Roster
            </a>
        </div>

        <!-- Patient Demographics & Bed Strip -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-[#064e2b] text-amber-400 font-black text-xl flex items-center justify-center shrink-0">
                    {{ strtoupper(substr($admission->patient->name ?? $admission->patient->user?->name ?? 'P', 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-black text-slate-800">{{ $admission->patient->name ?? $admission->patient->user?->name ?? 'Patient Record' }}</h2>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $admission->patient_classification === 'Emergency' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-700' }}">
                            {{ $admission->patient_classification }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 font-mono mt-0.5">
                        ID: {{ $admission->patient->id_number }} &bull; Admitted {{ $admission->admission_date->format('M d, Y h:i A') }} ({{ $admission->admission_date->diffForHumans() }})
                    </p>
                </div>
            </div>

            <!-- Bed & Ward Badge -->
            <div class="text-right">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 font-black text-xs">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    {{ $admission->bed?->full_location }}
                </span>
                <p class="text-[11px] text-slate-400 mt-1">Assigned Station: <strong>{{ $admission->bed?->ward?->nurse_station_assigned }}</strong></p>
                <div class="mt-2">
                    <a href="{{ route('nurse.chart.summary', $admission) }}"
                       target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs shadow-xs transition">
                        <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Print Medication Summary</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Allergy Alert Banner -->
        @if($admission->patient->allergies && $admission->patient->allergies !== 'None recorded' && $admission->patient->allergies !== 'None known')
            <div class="p-4 bg-rose-50 border-2 border-rose-300 rounded-2xl flex items-center gap-3 text-rose-800">
                <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center shrink-0 text-rose-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs font-black uppercase tracking-wider text-rose-900">Critical Clinical Alert: Known Allergies</h4>
                    <p class="text-xs font-bold mt-0.5">{{ $admission->patient->allergies }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left Col (2 cols): Chief Complaint, Vitals Update & Prescriptions -->
            <div class="lg:col-span-2 space-y-6">

                <!-- 1. Chief Complaint & Clinical Intake -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Chief Complaint & History of Present Illness</h3>
                    <p class="text-sm text-slate-800 font-medium leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100">
                        {{ $admission->chief_complaint }}
                    </p>
                </div>

                <!-- 2. Vitals Update & Nursing Progress Note Form -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Update Clinical Vitals & Nursing Chart</h3>
                            <p class="text-xs text-slate-500">Record current shift observations and vital signs.</p>
                        </div>
                        <span class="text-xs font-mono text-emerald-700 font-bold bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                            Current SpO2: {{ $admission->baseline_vitals['spo2'] ?? '98' }}%
                        </span>
                    </div>

                    <form method="POST" action="{{ route('nurse.chart.vitals', $admission) }}" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Blood Pressure</label>
                                <input type="text" name="bp" value="{{ $admission->baseline_vitals['bp'] ?? '120/80' }}"
                                       class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-600 focus:border-emerald-600">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Temp (°C)</label>
                                <input type="text" name="temp" value="{{ $admission->baseline_vitals['temp'] ?? '36.8' }}"
                                       class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-600 focus:border-emerald-600">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Pulse (bpm)</label>
                                <input type="text" name="pulse" value="{{ $admission->baseline_vitals['pulse'] ?? '75' }}"
                                       class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-600 focus:border-emerald-600">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Resp Rate (/min)</label>
                                <input type="text" name="rr" value="{{ $admission->baseline_vitals['rr'] ?? '18' }}"
                                       class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-600 focus:border-emerald-600">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">SpO2 (%)</label>
                                <input type="text" name="spo2" value="{{ $admission->baseline_vitals['spo2'] ?? '98' }}"
                                       class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-600 focus:border-emerald-600">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Respiratory Mode</label>
                                <select name="respiratory_status" class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-600 focus:border-emerald-600">
                                    <option value="Room Air" {{ $admission->respiratory_status === 'Room Air' ? 'selected' : '' }}>Room Air</option>
                                    <option value="Nasal Cannula (2-4 LPM)" {{ str_contains($admission->respiratory_status, 'Nasal') ? 'selected' : '' }}>Nasal Cannula (2-4 LPM)</option>
                                    <option value="Face Mask (6-10 LPM)" {{ str_contains($admission->respiratory_status, 'Face Mask') ? 'selected' : '' }}>Face Mask (6-10 LPM)</option>
                                    <option value="Non-Rebreather Mask (10-15 LPM)" {{ str_contains($admission->respiratory_status, 'Non-Rebreather') ? 'selected' : '' }}>Non-Rebreather</option>
                                    <option value="High Flow / BiPAP" {{ str_contains($admission->respiratory_status, 'BiPAP') ? 'selected' : '' }}>High Flow / BiPAP</option>
                                    <option value="Intubated / Mechanical" {{ str_contains($admission->respiratory_status, 'Intubated') ? 'selected' : '' }}>Intubated</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Nursing Shift Progress Note</label>
                            <textarea name="clinical_note" rows="2" placeholder="Record patient status, symptoms, responses to medication..."
                                      class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-600 focus:border-emerald-600"></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-xs transition">
                                Save Vitals & Nursing Chart
                            </button>
                        </div>
                    </form>
                </div>

                <!-- 3. Inpatient Medication Schedule & Prescriptions -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Inpatient Medication Schedule & Pharmacy Routing</h3>
                            <p class="text-xs text-slate-500">Scheduled prescriptions and pharmacy dispensing fulfillment.</p>
                        </div>

                        <a href="{{ route('prescriptions.create') }}?patient_id={{ $admission->patient_id }}&admission_id={{ $admission->id }}&room_bed_number={{ urlencode($admission->bed?->full_location) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#064e2b] hover:bg-[#053e22] text-white text-xs font-bold shadow-xs transition">
                            <svg class="w-3.5 h-3.5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Add Prescription</span>
                        </a>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse($admission->prescriptions as $prescription)
                            <div class="p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-800">Rx #{{ $prescription->id }}</span>
                                        <span class="text-xs text-slate-500">Attending: Dr. {{ $prescription->doctor_name }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $prescription->order_type === 'stat' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700' }}">
                                            {{ $prescription->order_type }}
                                        </span>
                                    </div>

                                    <!-- Status Pill -->
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $prescription->status === 'dispensed' ? 'bg-emerald-100 text-emerald-800' : ($prescription->status === 'routed' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $prescription->status }}
                                    </span>
                                </div>

                                <div class="space-y-1 pl-3 border-l-2 border-emerald-500">
                                    @foreach($prescription->items as $item)
                                        <div class="text-xs text-slate-700 flex items-center justify-between">
                                            <span><strong>{{ $item->medicine?->name ?? 'Medicine' }}</strong> ({{ $item->quantity }} {{ $item->medicine?->unit ?? 'units' }})</span>
                                            <span class="text-slate-500 font-mono text-[11px]">{{ $item->dosage_instructions }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                @if($prescription->status === 'dispensed')
                                    <div class="mt-2 pt-2 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-[11px] text-slate-500">
                                        <div class="flex items-center gap-2">
                                            <span class="text-emerald-700 font-bold flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Dispensed (For Billing Reference)
                                            </span>
                                            @if($prescription->collected_by)
                                                <span>&bull; Collected by: <strong class="text-slate-800">{{ $prescription->collected_by }}</strong></span>
                                            @endif
                                            @if($prescription->posTransaction)
                                                <span>&bull; {{ $prescription->posTransaction->created_at->format('M d, h:i A') }}</span>
                                            @endif
                                        </div>

                                        @if($prescription->posTransaction && $prescription->posTransaction->items->isNotEmpty())
                                            <div class="text-[10px] font-mono text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                                Batches: {{ $prescription->posTransaction->items->map(fn($it) => ($it->batch?->batch_no ?? 'Batch').' ('.$it->quantity.')')->implode(', ') }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="p-8 text-center text-xs text-slate-400">
                                No active inpatient prescriptions encoded yet. Click "Add Prescription" to order medication from the pharmacy.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- Right Col (1 col): Ward Management, Endorsement Log & Clinical Notes -->
            <div class="space-y-6">

                <!-- Ward & Bed Status Actions (Transfer / Discharge) -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Ward & Bed Management</h3>

                    @if($admission->status === 'admitted')
                        <!-- Bed Transfer Form -->
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-3" x-data="{ openTransfer: false }">
                            <div class="flex items-center justify-between">
                                <div class="text-xs font-bold text-slate-800">Bed Transfer</div>
                                <button type="button" @click="openTransfer = !openTransfer" class="text-xs text-emerald-700 font-semibold hover:underline">
                                    <span x-text="openTransfer ? 'Cancel' : 'Change Bed'"></span>
                                </button>
                            </div>

                            <form x-show="openTransfer" x-transition method="POST" action="{{ route('nurse.admissions.transfer', $admission) }}" class="space-y-3 pt-2 border-t border-slate-200">
                                @csrf
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Target Available Bed *</label>
                                    <select name="new_bed_id" required class="w-full text-xs rounded-xl border-slate-300 py-1.5 px-2">
                                        <option value="">-- Select Destination Bed --</option>
                                        @foreach($availableBeds as $b)
                                            <option value="{{ $b->id }}">{{ $b->ward->ward_name }} &bull; {{ $b->room_number }} - {{ $b->bed_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Transfer Reason *</label>
                                    <input type="text" name="transfer_reason" required placeholder="e.g. Clinical isolation required, ICU transfer..." class="w-full text-xs rounded-xl border-slate-300 py-1.5 px-2">
                                </div>
                                <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition">
                                    Process Bed Transfer
                                </button>
                            </form>
                        </div>

                        <!-- Discharge Form -->
                        <div class="p-4 rounded-xl bg-rose-50/50 border border-rose-100 space-y-3" x-data="{ openDischarge: false }">
                            <div class="flex items-center justify-between">
                                <div class="text-xs font-bold text-rose-900">Patient Discharge</div>
                                <button type="button" @click="openDischarge = !openDischarge" class="text-xs text-rose-700 font-semibold hover:underline">
                                    <span x-text="openDischarge ? 'Cancel' : 'Discharge'"></span>
                                </button>
                            </div>

                            <form x-show="openDischarge" x-transition method="POST" action="{{ route('nurse.admissions.discharge', $admission) }}" class="space-y-3 pt-2 border-t border-rose-200" onsubmit="return confirm('Confirm patient clinical discharge and release assigned bed?')">
                                @csrf
                                <div>
                                    <label class="block text-[11px] font-bold text-rose-800 mb-1">Discharge Summary / Notes</label>
                                    <textarea name="discharge_notes" rows="2" placeholder="Clinical condition upon discharge..." class="w-full text-xs rounded-xl border-rose-200 py-1.5 px-2"></textarea>
                                </div>
                                <button type="submit" class="w-full py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition">
                                    Confirm Patient Discharge &amp; Release Bed
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="p-3 bg-slate-100 rounded-xl text-center text-xs text-slate-500 font-semibold">
                            Patient Discharged on {{ $admission->discharge_date?->format('M d, Y') }}
                        </div>
                    @endif
                </div>

                <!-- Clinical History Card -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Past Medical History</h3>
                    <p class="text-xs text-slate-700 whitespace-pre-line leading-relaxed">
                        {{ $admission->patient->medical_notes ?: 'No past medical history on file.' }}
                    </p>
                </div>

                <!-- Digital Endorsement Log -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Clinical Handoff & Endorsements</h3>

                    <div class="space-y-4">
                        @foreach($admission->endorsements as $endorsement)
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-800">{{ $endorsement->nurse_station }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase {{ $endorsement->acknowledgment_status === 'acknowledged' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $endorsement->acknowledgment_status }}
                                    </span>
                                </div>

                                <blockquote class="italic text-slate-600 text-[11px]">
                                    "{{ $endorsement->endorsement_notes }}"
                                </blockquote>

                                <div class="text-[10px] text-slate-400 pt-2 border-t border-slate-200/60 space-y-0.5">
                                    <p>Dispatched by: {{ $endorsement->endorsedBy?->name ?? 'Admission' }} ({{ $endorsement->created_at->format('M d, h:i A') }})</p>
                                    @if($endorsement->acknowledgment_status === 'acknowledged')
                                        <p class="text-emerald-700 font-semibold">Acknowledged by: {{ $endorsement->receivingNurse?->name ?? 'Nurse' }} ({{ $endorsement->acknowledged_at?->format('M d, h:i A') }})</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>

    </div>
</x-nurse-layout>
