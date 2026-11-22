<x-admission-layout title="Admit Patient to Bed — USM Hospital">
    <x-slot:header>
        Patient Inpatient Admission & Clinical Endorsement
    </x-slot:header>

    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Back Link -->
        <div>
            <a href="{{ route('admission.patients') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-emerald-700 transition">
                &larr; Back to Patient Registry
            </a>
        </div>

        <!-- Patient Demographics Banner -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-800 font-black text-lg flex items-center justify-center shrink-0">
                    {{ strtoupper(substr($patient->name ?? $patient->user?->name ?? 'P', 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-black text-slate-800">{{ $patient->name ?? $patient->user?->name ?? 'Patient' }}</h2>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $patient->patient_type === 'student' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                            {{ $patient->patient_type }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 font-mono mt-0.5">ID: {{ $patient->id_number }} &bull; Contact: {{ $patient->contact_number ?: 'None' }}</p>
                </div>
            </div>

            <!-- Allergy Banner -->
            <div>
                @if($patient->allergies && $patient->allergies !== 'None recorded' && $patient->allergies !== 'None known')
                    <div class="px-3.5 py-1.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span>Allergy Alert: {{ $patient->allergies }}</span>
                    </div>
                @else
                    <span class="text-xs text-slate-400">No drug allergies recorded</span>
                @endif
            </div>
        </div>

        <!-- Admission Form -->
        <form method="POST" action="{{ route('admission.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-6">
            @csrf
            <input type="hidden" name="patient_id" value="{{ $patient->id }}">

            <div>
                <h3 class="text-sm font-bold text-slate-800">1. Bed Allocation & Ward Assignment</h3>
                <p class="text-xs text-slate-500 mb-3">Select an active available bed in the target hospital ward.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="bed_id" class="block text-xs font-bold text-slate-700 mb-1">Target Bed *</label>
                        <select name="bed_id" id="bed_id" required
                                class="w-full text-xs rounded-xl border-slate-300 py-2.5 px-3 focus:ring-emerald-600 focus:border-emerald-600">
                            <option value="">-- Select Available Hospital Bed --</option>
                            @foreach($wards as $ward)
                                @php $wBeds = $availableBeds->where('ward_id', $ward->id); @endphp
                                @if($wBeds->isNotEmpty())
                                    <optgroup label="{{ $ward->ward_name }} ({{ $ward->nurse_station_assigned }})">
                                        @foreach($wBeds as $bed)
                                            <option value="{{ $bed->id }}" {{ old('bed_id') == $bed->id ? 'selected' : '' }}>
                                                {{ $bed->room_number }} &bull; {{ $bed->bed_number }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                        </select>
                        @error('bed_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-sm font-bold text-slate-800">2. Clinical Triage & Intake Classification</h3>
                <p class="text-xs text-slate-500 mb-3">Specify patient admission level and initial respiratory requirement.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="patient_classification" class="block text-xs font-bold text-slate-700 mb-1">Patient Classification *</label>
                        <select name="patient_classification" id="patient_classification" required
                                class="w-full text-xs rounded-xl border-slate-300 py-2 px-3 focus:ring-emerald-600 focus:border-emerald-600">
                            <option value="USM Student" {{ old('patient_classification', $patient->patient_type === 'student' ? 'USM Student' : '') === 'USM Student' ? 'selected' : '' }}>USM Student</option>
                            <option value="USM Employee/Faculty" {{ old('patient_classification') === 'USM Employee/Faculty' ? 'selected' : '' }}>USM Employee / Faculty</option>
                            <option value="Regular Resident" {{ old('patient_classification', $patient->patient_type === 'resident' ? 'Regular Resident' : '') === 'Regular Resident' ? 'selected' : '' }}>Regular Resident (Non-USM / Campus Resident)</option>
                            <option value="Inpatient (General)" {{ old('patient_classification') === 'Inpatient (General)' ? 'selected' : '' }}>Inpatient (General Hospitalization)</option>
                            <option value="Emergency" {{ old('patient_classification') === 'Emergency' ? 'selected' : '' }}>Emergency (Acute / Critical)</option>
                        </select>
                    </div>

                    <div>
                        <label for="respiratory_status" class="block text-xs font-bold text-slate-700 mb-1">Case Classification (Respiratory Isolation) *</label>
                        <select name="respiratory_status" id="respiratory_status" required
                                class="w-full text-xs rounded-xl border-slate-300 py-2 px-3 focus:ring-emerald-600 focus:border-emerald-600">
                            <option value="Non-Respiratory Case (General Ward)" {{ old('respiratory_status') === 'Non-Respiratory Case (General Ward)' ? 'selected' : '' }}>Non-Respiratory Case (General Ward)</option>
                            <option value="Infectious Respiratory Case (Isolation Required)" {{ old('respiratory_status') === 'Infectious Respiratory Case (Isolation Required)' ? 'selected' : '' }}>Infectious Respiratory Case (Isolation Ward Required)</option>
                            <option value="Room Air" {{ old('respiratory_status') === 'Room Air' ? 'selected' : '' }}>Room Air (SpO2 &ge; 95%)</option>
                            <option value="Nasal Cannula (2-4 LPM)" {{ old('respiratory_status') === 'Nasal Cannula (2-4 LPM)' ? 'selected' : '' }}>Nasal Cannula (2-4 LPM)</option>
                            <option value="Face Mask / High Flow" {{ old('respiratory_status') === 'Face Mask / High Flow' ? 'selected' : '' }}>Face Mask / High Flow Support</option>
                            <option value="Intubated / Mechanical" {{ old('respiratory_status') === 'Intubated / Mechanical' ? 'selected' : '' }}>Intubated / Mechanical Ventilation</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="chief_complaint" class="block text-xs font-bold text-slate-700 mb-1">Chief Complaint & History of Present Illness *</label>
                        <textarea name="chief_complaint" id="chief_complaint" rows="3" required
                                  placeholder="Describe the primary reason for admission, symptoms, onset, and clinical assessment..."
                                  class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-600 focus:border-emerald-600">{{ old('chief_complaint') }}</textarea>
                        @error('chief_complaint') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-sm font-bold text-slate-800">3. Baseline Clinical Vitals</h3>
                <p class="text-xs text-slate-500 mb-3">Record intake physiological vitals at point of admission.</p>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Blood Pressure <span class="text-rose-500">*</span></label>
                        <input type="text" name="bp" value="{{ old('bp', '120/80') }}" placeholder="120/80" required
                               class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-600 focus:border-emerald-600">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Temp (°C) <span class="text-rose-500">*</span></label>
                        <input type="text" name="temp" value="{{ old('temp', '36.8') }}" placeholder="36.8" required
                               class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-600 focus:border-emerald-600">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Pulse (bpm) <span class="text-rose-500">*</span></label>
                        <input type="text" name="pulse" value="{{ old('pulse', '76') }}" placeholder="76" required
                               class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-600 focus:border-emerald-600">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Resp Rate (/min) <span class="text-rose-500">*</span></label>
                        <input type="text" name="rr" value="{{ old('rr', '18') }}" placeholder="18" required
                               class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-600 focus:border-emerald-600">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">SpO2 (%) <span class="text-rose-500">*</span></label>
                        <input type="text" name="spo2" value="{{ old('spo2', '98') }}" placeholder="98" required
                               class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-600 focus:border-emerald-600">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Weight (kg)</label>
                        <input type="text" name="weight" value="{{ old('weight') }}" placeholder="60"
                               class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-600 focus:border-emerald-600">
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-sm font-bold text-slate-800">4. Nurse Station Endorsement Notes</h3>
                <p class="text-xs text-slate-500 mb-3">Provide instructions for the receiving floor nurse upon patient transfer.</p>

                <div>
                    <textarea name="endorsement_notes" id="endorsement_notes" rows="3" required
                              placeholder="e.g., Patient transferred from Admission. IV line initiated in ER. Strict I&O monitoring required. Please administer maintenance nebulization as prescribed."
                              class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-600 focus:border-emerald-600">{{ old('endorsement_notes') }}</textarea>
                    @error('endorsement_notes') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admission.patients') }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-[#064e2b] hover:bg-[#053e22] text-white text-xs font-bold shadow-xs transition flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Confirm Admission & Endorse to Nurse Station</span>
                </button>
            </div>
        </form>

    </div>
</x-admission-layout>
