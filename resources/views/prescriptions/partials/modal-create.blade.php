<!-- Floating Modal for New Prescription -->
<div x-show="showNewModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true"
     @keydown.escape.window="showNewModal = false">

    <!-- Backdrop -->
    <div x-show="showNewModal"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="showNewModal = false"
         class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity"></div>

    <!-- Modal Dialog Window -->
    <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
        <div x-show="showNewModal"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl border border-slate-200/90 transition-all my-6">

            <!-- Modal Header -->
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-slate-50/70">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-[#064e2b] text-amber-400 flex items-center justify-center font-bold text-sm shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 id="modal-title" class="text-base font-bold text-slate-800 tracking-tight">New Prescription</h2>
                        <p class="text-[11px] text-slate-500">Encode prescription into clinic queue</p>
                    </div>
                </div>

                <button type="button"
                        @click="showNewModal = false; if (window.location.pathname.endsWith('/create')) window.location.href = '{{ route('prescriptions.index') }}';"
                        class="p-1.5 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer"
                        title="Close modal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Error Summary in Modal -->
            @if ($errors->any())
                <div class="m-5 mb-0 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                    <div class="font-bold mb-1">Please fix the following:</div>
                    <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Modal Form -->
            <form method="POST" action="{{ route('prescriptions.store') }}"
                  x-data="prescriptionModalForm({{ Js::from($medicines) }}, {{ Js::from(old('items', [['medicine_id' => '', 'quantity' => 1, 'dosage_instructions' => '']])) }})"
                  class="p-5 space-y-4 max-h-[78vh] overflow-y-auto">
                @csrf
                <input type="hidden" name="_is_modal" value="1">

                <!-- 1. Patient Selection -->
                <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <label for="modal_patient_id" class="text-xs font-bold text-slate-800">
                            Patient <span class="text-rose-500">*</span>
                        </label>
                        <button type="button"
                                @click="registerNew = !registerNew"
                                class="text-[11px] font-semibold text-emerald-700 hover:text-emerald-800 cursor-pointer">
                            <span x-text="registerNew ? '← Select Existing' : '+ New Patient'"></span>
                        </button>
                    </div>

                    <!-- Existing Patient Select -->
                    <div x-show="!registerNew">
                        <select id="modal_patient_id" name="patient_id"
                                class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-600 text-slate-800 py-2">
                            <option value="">-- Select Registered Patient --</option>
                            @foreach ($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->name }} &bull; {{ $patient->id_number }} ({{ ucfirst($patient->patient_type) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Register New Patient (collapsible inline) -->
                    <div x-show="registerNew" x-cloak class="pt-2 border-t border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <input type="hidden" name="register_new_patient" :value="registerNew ? '1' : ''">

                        <div>
                            <label class="block text-[11px] font-medium text-slate-600 mb-1">Full Name *</label>
                            <input type="text" name="new_patient_name" value="{{ old('new_patient_name') }}"
                                   placeholder="e.g. Juan Dela Cruz"
                                   class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-600 text-slate-800 py-1.5">
                        </div>

                        <div>
                            <label class="block text-[11px] font-medium text-slate-600 mb-1">Type *</label>
                            <select name="new_patient_type" class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-600 text-slate-800 py-1.5">
                                <option value="student" {{ old('new_patient_type') == 'student' ? 'selected' : '' }}>Student</option>
                                <option value="resident" {{ old('new_patient_type') == 'resident' ? 'selected' : '' }}>Faculty / Resident</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-medium text-slate-600 mb-1">ID Number *</label>
                            <input type="text" name="new_id_number" value="{{ old('new_id_number') }}"
                                   placeholder="e.g. 2024-00123"
                                   class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-600 text-slate-800 py-1.5">
                        </div>

                        <div>
                            <label class="block text-[11px] font-medium text-slate-600 mb-1">Contact</label>
                            <input type="text" name="new_contact_number" value="{{ old('new_contact_number') }}"
                                   placeholder="e.g. 0912-345-6789"
                                   class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-600 text-slate-800 py-1.5">
                        </div>
                    </div>
                </div>

                <!-- 2. Attending Doctor -->
                <div>
                    <label for="modal_doctor_name" class="block text-xs font-bold text-slate-800 mb-1">
                        Doctor Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="modal_doctor_name" name="doctor_name"
                           value="{{ old('doctor_name') }}"
                           placeholder="e.g. Maria Santos, MD"
                           required
                           class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-600 text-slate-800 py-2">
                </div>

                <!-- 3. Prescribed Medicines -->
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-slate-800">
                            Prescribed Medicines <span class="text-rose-500">*</span>
                        </label>
                        <button type="button" @click="addItem()"
                                class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 hover:text-emerald-800 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Add Item</span>
                        </button>
                    </div>

                    <div class="space-y-2.5">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="p-3 rounded-xl bg-slate-50/80 border border-slate-200 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-semibold text-slate-500" x-text="`Medicine #${index + 1}`"></span>
                                    <button type="button" @click="removeItem(index)"
                                            x-show="items.length > 1"
                                            class="text-rose-500 hover:text-rose-700 text-[11px] font-semibold">
                                        Remove
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                    <div class="sm:col-span-2">
                                        <select :name="`items[${index}][medicine_id]`"
                                                x-model="item.medicine_id"
                                                @change="onMedicineChange(index)"
                                                required
                                                class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-600 text-slate-800 py-1.5">
                                            <option value="">-- Choose Medicine --</option>
                                            <template x-for="med in catalog" :key="med.id">
                                                <option :value="med.id"
                                                        :selected="med.id == item.medicine_id"
                                                        x-text="`${med.name} (${med.generic_name}) - ${med.available_stock} in stock`">
                                                </option>
                                            </template>
                                        </select>
                                    </div>

                                    <div>
                                        <input type="number" min="1"
                                               :name="`items[${index}][quantity]`"
                                               x-model.number="item.quantity"
                                               placeholder="Qty"
                                               required
                                               class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-600 text-slate-800 py-1.5">
                                    </div>
                                </div>

                                <!-- Live stock feedback -->
                                <div x-show="item.medicine_id" class="text-[11px]">
                                    <span x-show="item.available > 0" class="text-emerald-700 font-medium">
                                        ✓ <span x-text="`${item.available} available in stock`"></span>
                                    </span>
                                    <span x-show="item.available <= 0" class="text-rose-600 font-semibold">
                                        ⚠ Out of stock
                                    </span>
                                    <span x-show="item.quantity > item.available" class="text-rose-600 font-medium ml-2">
                                        (Exceeds stock)
                                    </span>
                                </div>

                                <div>
                                    <input type="text"
                                           :name="`items[${index}][dosage_instructions]`"
                                           x-model="item.dosage_instructions"
                                           placeholder="Dosage instructions (e.g. 1 tab 3x daily after meals)"
                                           required
                                           class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-600 text-slate-800 py-1.5">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button"
                            @click="showNewModal = false; if (window.location.pathname.endsWith('/create')) window.location.href = '{{ route('prescriptions.index') }}';"
                            class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-100 transition cursor-pointer">
                        Cancel
                    </button>

                    <button type="submit"
                            class="px-5 py-2 rounded-xl bg-[#064e2b] hover:bg-[#053d22] text-white text-xs font-bold shadow-xs transition cursor-pointer">
                        Save Prescription
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    function prescriptionModalForm(catalog, oldItems) {
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
                this.items[index].available = med ? med.available_stock : 0;
            }
        };
    }
</script>
