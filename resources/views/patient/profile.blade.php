<x-patient-layout active="profile">
    <div class="flex-1 max-w-2xl w-full mx-auto px-4 sm:px-6 py-8 space-y-5">
        <h1 class="text-xl font-bold text-gray-800">Medical Profile</h1>

        @if (session('status'))
            <div class="flex items-center gap-2 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" stroke-width="2.5"/></svg>
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Read-only institutional info -->
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="bg-green-800 border-b-4 border-yellow-500 px-5 py-3">
                <p class="text-white text-xs font-semibold uppercase tracking-wide">Account Information</p>
                <p class="text-green-300 text-[10px] mt-0.5">This information is managed by the system</p>
            </div>
            <div class="grid grid-cols-2 gap-4 px-5 py-5">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Full Name</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $patient->user->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Patient ID</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $patient->id_number }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Patient Type</p>
                    <p class="text-sm font-semibold text-gray-800">{{ ucfirst($patient->patient_type) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Email</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $patient->user->email }}</p>
                </div>
            </div>
        </div>

        <!-- Editable clinical info -->
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100">
                <p class="text-sm font-bold text-gray-700">Clinical Information</p>
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <form method="POST" action="{{ route('patient.profile.update') }}" class="px-5 py-5 space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label for="contact_number" class="block text-xs font-medium text-gray-600 mb-1.5">
                        <svg class="w-3 h-3 inline mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" stroke-width="2"/></svg>
                        Contact Number
                    </label>
                    <input
                        type="tel"
                        id="contact_number"
                        name="contact_number"
                        value="{{ old('contact_number', $patient->contact_number) }}"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                        placeholder="e.g. 09XX XXX XXXX"
                    />
                </div>

                <div>
                    <label for="allergies" class="block text-xs font-medium text-gray-600 mb-1.5">
                        <svg class="w-3 h-3 inline mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><line x1="12" y1="8" x2="12" y2="12" stroke-width="2"/><line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2"/></svg>
                        Known Allergies
                    </label>
                    <textarea
                        id="allergies"
                        name="allergies"
                        rows="3"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-600 resize-none"
                        placeholder="e.g. Penicillin, Aspirin, Sulfonamides"
                    >{{ old('allergies', $patient->allergies) }}</textarea>
                </div>

                <div>
                    <label for="medical_notes" class="block text-xs font-medium text-gray-600 mb-1.5">
                        <svg class="w-3 h-3 inline mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12" stroke-width="2"/></svg>
                        Medical Notes / Conditions
                    </label>
                    <textarea
                        id="medical_notes"
                        name="medical_notes"
                        rows="3"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-600 resize-none"
                        placeholder="e.g. Hypertension, Asthmatic, Diabetic Type 2"
                    >{{ old('medical_notes', $patient->medical_notes) }}</textarea>
                </div>

                <div class="flex items-center justify-end pt-1">
                    <button
                        type="submit"
                        class="flex items-center gap-2 bg-green-800 hover:bg-green-700 text-white font-semibold text-sm px-5 py-2.5 rounded-xl transition-colors border-b-2 border-yellow-500 shadow-sm"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" stroke-width="2"/><polyline points="17 21 17 13 7 13 7 21" stroke-width="2"/><polyline points="7 3 7 8 15 8" stroke-width="2"/></svg>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-patient-layout>
