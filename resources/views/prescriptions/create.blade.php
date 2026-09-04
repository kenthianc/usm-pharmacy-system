<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('prescriptions.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">&larr; Back to Prescriptions</a>
                </div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mt-1">
                    {{ __('Encode New Prescription') }}
                </h2>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                Status: Pending on Save
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-md bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                    <div class="font-semibold mb-1">Please fix the following validation errors:</div>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('prescriptions.store') }}"
                  x-data="prescriptionForm({{ Js::from($medicines) }}, {{ Js::from(old('items', [['medicine_id' => '', 'quantity' => 1, 'dosage_instructions' => '']])) }})"
                  class="space-y-6">
                @csrf

                <!-- Patient Details Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-6">
                    <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">1. Patient Information</h3>
                            <p class="text-xs text-gray-500">Select an existing patient or register a new university student/resident.</p>
                        </div>

                        <!-- Toggle Registration -->
                        <div class="flex items-center space-x-2 text-xs">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="register_new_patient" value="1"
                                       x-model="registerNew"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 font-medium text-gray-700">Register New Patient</span>
                            </label>
                        </div>
                    </div>

                    <!-- Existing Patient Select -->
                    <div x-show="!registerNew" class="space-y-2">
                        <label for="patient_id" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Select Patient <span class="text-rose-500">*</span>
                        </label>
                        <select id="patient_id" name="patient_id"
                                class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Choose Patient by ID or Name --</option>
                            @foreach ($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                    [{{ $patient->id_number }}] {{ $patient->name }} &bull; {{ ucfirst($patient->patient_type) }}
                                    @if ($patient->contact_number) (Tel: {{ $patient->contact_number }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Patient Registration Fields -->
                    <div x-show="registerNew" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <div class="md:col-span-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-indigo-100 text-indigo-800">
                                Quick Patient Registration
                            </span>
                        </div>

                        <div>
                            <label for="new_patient_name" class="block text-xs font-semibold text-gray-700">Full Name <span class="text-rose-500">*</span></label>
                            <input type="text" id="new_patient_name" name="new_patient_name" value="{{ old('new_patient_name') }}"
                                   placeholder="e.g. Maria Clara Santos"
                                   class="mt-1 w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('new_patient_name') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="new_patient_type" class="block text-xs font-semibold text-gray-700">Patient Classification <span class="text-rose-500">*</span></label>
                            <select id="new_patient_type" name="new_patient_type"
                                    class="mt-1 w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="student" {{ old('new_patient_type') == 'student' ? 'selected' : '' }}>Student</option>
                                <option value="resident" {{ old('new_patient_type') == 'resident' ? 'selected' : '' }}>Resident</option>
                            </select>
                            @error('new_patient_type') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="new_id_number" class="block text-xs font-semibold text-gray-700">Institutional ID Number <span class="text-rose-500">*</span></label>
                            <input type="text" id="new_id_number" name="new_id_number" value="{{ old('new_id_number') }}"
                                   placeholder="e.g. USM-2026-0491"
                                   class="mt-1 w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('new_id_number') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="new_contact_number" class="block text-xs font-semibold text-gray-700">Contact Number</label>
                            <input type="text" id="new_contact_number" name="new_contact_number" value="{{ old('new_contact_number') }}"
                                   placeholder="e.g. 09171234567"
                                   class="mt-1 w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('new_contact_number') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Prescribing Doctor -->
                    <div class="pt-2">
                        <label for="doctor_name" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Attending Physician / Doctor Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-xs font-medium">Dr.</span>
                            <input type="text" id="doctor_name" name="doctor_name" value="{{ old('doctor_name') }}"
                                   placeholder="Firstname Lastname, MD"
                                   class="w-full text-sm rounded-md border-gray-300 pl-9 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        @error('doctor_name')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Prescription Line Items Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-6">
                    <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">2. Prescribed Medicines</h3>
                            <p class="text-xs text-gray-500">Real-time stock availability is validated against active inventory batches.</p>
                        </div>

                        <button type="button" @click="addItem()"
                                class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-md text-xs font-semibold hover:bg-indigo-100 transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Medicine
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 space-y-3 relative">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-gray-600 uppercase" x-text="'Item #' + (index + 1)"></span>
                                    <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                            class="text-xs text-rose-600 hover:text-rose-800 font-medium inline-flex items-center">
                                        <svg class="w-3.5 h-3.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Remove
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
                                    <!-- Medicine Selection -->
                                    <div class="md:col-span-6">
                                        <label class="block text-[11px] font-semibold text-gray-700 uppercase">Medicine <span class="text-rose-500">*</span></label>
                                        <select :name="'items[' + index + '][medicine_id]'"
                                                x-model="item.medicine_id"
                                                @change="onMedicineChange(index)"
                                                class="mt-1 w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                required>
                                            <option value="">-- Choose Medicine --</option>
                                            <template x-for="med in catalog" :key="med.id">
                                                <option :value="med.id"
                                                        :selected="med.id == item.medicine_id"
                                                        x-text="med.name + ' (' + med.available_stock + ' ' + med.unit + 's in stock)'">
                                                </option>
                                            </template>
                                        </select>

                                        <!-- Stock Availability Pill -->
                                        <div class="mt-1.5 flex items-center space-x-2 text-[11px]" x-show="item.medicine_id">
                                            <span class="text-gray-500">Stock Status:</span>
                                            <span :class="{
                                                'bg-emerald-100 text-emerald-800': item.available > 20,
                                                'bg-amber-100 text-amber-800': item.available > 0 && item.available <= 20,
                                                'bg-rose-100 text-rose-800': item.available === 0
                                            }" class="px-2 py-0.5 rounded-full font-medium"
                                               x-text="item.available + ' ' + (item.unit || 'unit') + 's available'">
                                            </span>
                                            <span class="text-gray-400">&bull;</span>
                                            <span class="text-gray-600 font-mono" x-text="'₱' + item.price.toFixed(2) + ' / ' + item.unit"></span>
                                        </div>
                                    </div>

                                    <!-- Quantity -->
                                    <div class="md:col-span-2">
                                        <label class="block text-[11px] font-semibold text-gray-700 uppercase">Qty <span class="text-rose-500">*</span></label>
                                        <input type="number"
                                               :name="'items[' + index + '][quantity]'"
                                               x-model.number="item.quantity"
                                               min="1"
                                               class="mt-1 w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                               required>
                                        <div x-show="item.quantity > item.available && item.medicine_id"
                                             class="text-rose-600 text-[10px] font-medium mt-1">
                                            Exceeds stock!
                                        </div>
                                    </div>

                                    <!-- Dosage Instructions -->
                                    <div class="md:col-span-4">
                                        <label class="block text-[11px] font-semibold text-gray-700 uppercase">Dosage Instructions <span class="text-rose-500">*</span></label>
                                        <input type="text"
                                               :name="'items[' + index + '][dosage_instructions]'"
                                               x-model="item.dosage_instructions"
                                               placeholder="e.g. 1 tab 3x daily after meals"
                                               class="mt-1 w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                               required>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    @error('items')
                        <p class="text-rose-600 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Bar -->
                <div class="flex items-center justify-between bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <a href="{{ route('prescriptions.index') }}" class="text-xs text-gray-600 hover:text-gray-900 font-medium">Cancel</a>
                    <button type="submit"
                            class="inline-flex items-center px-6 py-2.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        {{ __('Save as Pending Prescription') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function prescriptionForm(catalog, oldItems) {
            return {
                catalog: catalog,
                registerNew: {{ old('register_new_patient') ? 'true' : 'false' }},
                items: [],

                init() {
                    if (oldItems && oldItems.length > 0) {
                        this.items = oldItems.map(item => {
                            const med = this.catalog.find(m => m.id == item.medicine_id);
                            return {
                                medicine_id: item.medicine_id || '',
                                quantity: item.quantity || 1,
                                dosage_instructions: item.dosage_instructions || '',
                                available: med ? med.available_stock : 0,
                                price: med ? med.unit_price : 0,
                                unit: med ? med.unit : '',
                            };
                        });
                    } else {
                        this.addItem();
                    }
                },

                addItem() {
                    this.items.push({
                        medicine_id: '',
                        quantity: 1,
                        dosage_instructions: '',
                        available: 0,
                        price: 0,
                        unit: '',
                    });
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },

                onMedicineChange(index) {
                    const selectedId = this.items[index].medicine_id;
                    const med = this.catalog.find(m => m.id == selectedId);
                    if (med) {
                        this.items[index].available = med.available_stock;
                        this.items[index].price = med.unit_price;
                        this.items[index].unit = med.unit;
                    } else {
                        this.items[index].available = 0;
                        this.items[index].price = 0;
                        this.items[index].unit = '';
                    }
                }
            };
        }
    </script>
</x-app-layout>

