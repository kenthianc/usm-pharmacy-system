<x-admission-layout title="Patient Registry & Intake — USM Hospital">
    <x-slot:header>
        Patient Registry & Clinical Intake Directory
    </x-slot:header>

    <div class="space-y-6" x-data="{ showRegisterModal: false }">

        <!-- Top Search & Actions Toolbar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('admission.patients') }}" class="flex-1 min-w-[260px] max-w-md flex items-center gap-2">
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by patient name, ID number, or phone..."
                           class="w-full text-xs rounded-xl border-slate-300 pl-9 pr-3 py-2 focus:ring-emerald-600 focus:border-emerald-600">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-800 text-white text-xs font-bold hover:bg-slate-900 transition">
                    Search
                </button>
                @if($search)
                    <a href="{{ route('admission.patients') }}" class="text-xs text-rose-600 hover:underline font-bold">Reset</a>
                @endif
            </form>

            <button type="button" @click="showRegisterModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#064e2b] hover:bg-[#053e22] text-white text-xs font-bold shadow-xs transition">
                <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Register New Patient</span>
            </button>
        </div>

        <!-- Patients Roster Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Master Patient Roster</h3>
                    <p class="text-xs text-slate-500">Official catalog of registered university students and resident patients.</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                    {{ $patients->total() }} Total Patients
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">Patient ID & Name</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4">Contact & Location</th>
                            <th class="py-3 px-4">Known Allergies</th>
                            <th class="py-3 px-4">Admission Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($patients as $patient)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-800">{{ $patient->name ?? $patient->user?->name ?? 'Patient' }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $patient->id_number }}</div>
                                </td>

                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $patient->patient_type === 'student' ? 'bg-blue-50 text-blue-700 border border-blue-200' : ($patient->patient_type === 'employee' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200') }}">
                                        {{ $patient->patient_type === 'student' ? 'USM Student' : ($patient->patient_type === 'employee' ? 'USM Employee' : 'Regular Resident') }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4">
                                    <div class="text-slate-700 font-medium">{{ $patient->contact_number ?: 'No phone recorded' }}</div>
                                    <div class="text-[11px] text-slate-400 truncate max-w-xs">{{ $patient->address ?: 'Cotabato Province' }}</div>
                                </td>

                                <td class="py-3.5 px-4">
                                    @if($patient->allergies && $patient->allergies !== 'None recorded' && $patient->allergies !== 'None known')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <svg class="w-3 h-3 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ Str::limit($patient->allergies, 30) }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">No alerts</span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4">
                                    @if($patient->currentAdmission)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold text-xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Admitted: {{ $patient->currentAdmission->bed?->room_number }} - {{ $patient->currentAdmission->bed?->bed_number }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                            Outpatient / Not Admitted
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-right">
                                    @if($patient->currentAdmission)
                                        <a href="{{ route('nurse.chart', $patient->currentAdmission) }}"
                                           class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                                            View Chart
                                        </a>
                                    @else
                                        <a href="{{ route('admission.create', $patient) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition">
                                            <span>Admit Patient</span>
                                            &rarr;
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    No patient records found matching your search.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($patients->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $patients->links() }}
                </div>
            @endif
        </div>

        <!-- Register Patient Modal -->
        <div x-show="showRegisterModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-black/60 p-3 sm:p-4 md:p-6 flex min-h-full items-center justify-center"
             style="display: none;"
             @keydown.escape.window="showRegisterModal = false">

            <div class="relative w-full max-w-xl max-h-[calc(100vh-2rem)] sm:max-h-[calc(100vh-3rem)] bg-white rounded-2xl shadow-2xl border border-slate-200 flex flex-col my-auto overflow-hidden"
                 @click.away="showRegisterModal = false">

                <!-- Pinned Header -->
                <div class="px-5 py-3.5 sm:px-6 sm:py-4 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-[#064e2b] border border-emerald-200 flex items-center justify-center font-bold text-sm shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-extrabold text-slate-800 leading-tight">Register New Patient</h3>
                            <p class="text-[11px] text-slate-400">Create an institutional outpatient or clinical profile</p>
                        </div>
                    </div>
                    <button type="button" @click="showRegisterModal = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-lg font-bold leading-none transition cursor-pointer">&times;</button>
                </div>

                <!-- Form with Scrollable Body & Pinned Footer -->
                <form method="POST" action="{{ route('admission.patients.store') }}" class="flex flex-col flex-1 min-h-0">
                    @csrf

                    <!-- Scrollable Form Fields -->
                    <div class="p-5 sm:p-6 overflow-y-auto flex-1 space-y-3.5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-3.5">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Full Name *</label>
                                <input type="text" name="name" required placeholder="e.g. Juan Dela Cruz"
                                       class="w-full text-xs rounded-xl border-slate-300 focus:ring-[#064e2b] focus:border-[#064e2b]">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Patient Classification *</label>
                                <select name="patient_type" required class="w-full text-xs rounded-xl border-slate-300 focus:ring-[#064e2b] focus:border-[#064e2b]">
                                    <option value="student">USM Student</option>
                                    <option value="employee">USM Employee / Faculty</option>
                                    <option value="resident">Regular Resident</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Institutional / Resident ID *</label>
                                <input type="text" name="id_number" required placeholder="e.g. USM-2026-0045"
                                       class="w-full text-xs rounded-xl border-slate-300 focus:ring-[#064e2b] focus:border-[#064e2b]">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Contact Number</label>
                                <input type="text" name="contact_number" placeholder="09xxxxxxxxx"
                                       class="w-full text-xs rounded-xl border-slate-300 focus:ring-[#064e2b] focus:border-[#064e2b]">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Emergency Contact (Name &amp; Phone)</label>
                                <input type="text" name="emergency_contact" placeholder="e.g. Maria Dela Cruz (09123456789)"
                                       class="w-full text-xs rounded-xl border-slate-300 focus:ring-[#064e2b] focus:border-[#064e2b]">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Date of Birth</label>
                                <input type="date" name="date_of_birth"
                                       class="w-full text-xs rounded-xl border-slate-300 focus:ring-[#064e2b] focus:border-[#064e2b]">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Sex</label>
                                <select name="sex" class="w-full text-xs rounded-xl border-slate-300 focus:ring-[#064e2b] focus:border-[#064e2b]">
                                    <option value="">Select Sex</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Address</label>
                                <input type="text" name="address" placeholder="e.g. Kabacan, Cotabato"
                                       class="w-full text-xs rounded-xl border-slate-300 focus:ring-[#064e2b] focus:border-[#064e2b]">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-rose-700 mb-1">Known Allergies (Food / Drugs)</label>
                                <input type="text" name="allergies" placeholder="e.g. Penicillin, Aspirin, Seafood (or None known)"
                                       class="w-full text-xs rounded-xl border-rose-300 focus:ring-rose-600 focus:border-rose-600 bg-rose-50/30">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Past Medical History / Clinical Notes</label>
                                <textarea name="medical_notes" rows="2" placeholder="e.g. Asthmatic since 2018, hypertensive"
                                          class="w-full text-xs rounded-xl border-slate-300 focus:ring-[#064e2b] focus:border-[#064e2b]"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Pinned Footer -->
                    <div class="px-5 py-3 sm:px-6 sm:py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5 shrink-0">
                        <button type="button" @click="showRegisterModal = false"
                                class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-800 hover:bg-slate-200/70 transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#064e2b] hover:bg-[#053e22] text-white text-xs font-bold shadow-xs transition transform active:scale-98 cursor-pointer">
                            <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Save Patient Record</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-admission-layout>
