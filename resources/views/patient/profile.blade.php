<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Medical Profile</h2>
            <p class="text-xs text-gray-500 mt-1">Keep your information up to date to help us serve you better.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-md bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Read-only Patient Info --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Patient Information</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs text-gray-500">Full Name</dt>
                        <dd class="text-sm font-medium text-gray-800">{{ $patient->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Email Address</dt>
                        <dd class="text-sm font-medium text-gray-800">{{ $patient->user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Patient ID</dt>
                        <dd class="text-sm font-medium text-gray-800">{{ $patient->id_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Patient Type</dt>
                        <dd class="text-sm font-medium text-gray-800">{{ ucfirst($patient->patient_type) }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Editable Medical Details --}}
            <form method="POST" action="{{ route('patient.profile.update') }}">
                @csrf
                @method('PATCH')

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-5">
                    <h3 class="text-sm font-semibold text-gray-700">Medical Details</h3>

                    {{-- Contact Number --}}
                    <div>
                        <label for="contact_number" class="block text-xs font-medium text-gray-600 mb-1">
                            Contact Number
                        </label>
                        <input
                            type="text"
                            id="contact_number"
                            name="contact_number"
                            value="{{ old('contact_number', $patient->contact_number) }}"
                            placeholder="e.g. 09XX-XXX-XXXX"
                            class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('contact_number') border-rose-400 @enderror"
                        />
                        @error('contact_number')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Allergies --}}
                    <div>
                        <label for="allergies" class="block text-xs font-medium text-gray-600 mb-1">
                            Known Allergies
                        </label>
                        <textarea
                            id="allergies"
                            name="allergies"
                            rows="3"
                            placeholder="e.g. Penicillin, Aspirin, Sulfonamides..."
                            class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('allergies') border-rose-400 @enderror"
                        >{{ old('allergies', $patient->allergies) }}</textarea>
                        @error('allergies')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-400">List any medicines or substances you are allergic to, separated by commas.</p>
                    </div>

                    {{-- Medical Notes --}}
                    <div>
                        <label for="medical_notes" class="block text-xs font-medium text-gray-600 mb-1">
                            Medical Notes / Conditions
                        </label>
                        <textarea
                            id="medical_notes"
                            name="medical_notes"
                            rows="4"
                            placeholder="e.g. Hypertensive, Diabetic (Type 2), asthmatic..."
                            class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('medical_notes') border-rose-400 @enderror"
                        >{{ old('medical_notes', $patient->medical_notes) }}</textarea>
                        @error('medical_notes')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-400">Include any chronic conditions or relevant medical history.</p>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center px-5 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
