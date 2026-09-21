<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    <a href="{{ route('inventory.movements') }}" class="text-gray-400 hover:text-gray-600 transition" title="Back to Delivery Logs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <span>{{ __('Request Delivery / Restock') }}</span>
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Create an incoming restock request with multiple medicines. Units remain in <strong>Pending</strong> status and will not increase stock until confirmed.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('inventory.movements') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold transition">
                    Cancel
                </a>
                <button type="button"
                        onclick="document.getElementById('delivery-request-form').requestSubmit();"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-sm transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Submit Delivery Request</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8"
         x-data="deliveryForm(
            {{ Js::from($medicines) }},
            {{ Js::from(old('items', $preselectedMedicineId ? [['medicine_id' => (int) $preselectedMedicineId, 'quantity' => 100, 'batch_no' => '', 'expiry_date' => '']] : [['medicine_id' => '', 'quantity' => 100, 'batch_no' => '', 'expiry_date' => '']])) }}
         )">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Workflow Guidance Banner -->
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-300/80 text-amber-900 text-xs shadow-xs flex items-start gap-3">
                <div class="w-7 h-7 rounded-lg bg-amber-400 text-amber-950 flex items-center justify-center font-bold text-sm shrink-0 mt-0.5">
                    ⏳
                </div>
                <div class="space-y-1">
                    <div class="font-bold text-amber-950 text-sm">Delivery Workflow Notice</div>
                    <p class="text-amber-800 leading-relaxed">
                        Submitting this form creates a restock request with status <strong>Pending</strong>.
                        <strong>No usable inventory stock is added at this time.</strong>
                        Stock will increase only after the shipment arrives and is confirmed by an Admin or Stock Manager.
                    </p>
                </div>
            </div>

            @if (session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs shadow-xs">
                    <div class="font-bold text-rose-800 mb-1 flex items-center gap-1.5 text-sm">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Please fix the following issues:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-xs text-rose-700 pl-4 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="delivery-request-form" method="POST" action="{{ route('inventory.deliveries.store') }}" class="space-y-6">
                @csrf

                <!-- Section 1: Shipment / PO Information -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-6">
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs">1</span>
                        Delivery Information
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <!-- Reference / PO Number -->
                        <div>
                            <label for="reference_no" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Reference / PO #
                            </label>
                            <input type="text" name="reference_no" id="reference_no"
                                   value="{{ old('reference_no', 'DEL-' . date('Ymd') . '-' . rand(100, 999)) }}"
                                   placeholder="e.g., PO-2026-0812, DEL-20260920-01"
                                   class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 @error('reference_no') border-rose-500 @enderror">
                            <p class="text-[11px] text-gray-400 mt-1">Leave blank to auto-generate or use vendor PO number.</p>
                            @error('reference_no')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Supplier / Distributor -->
                        <div>
                            <label for="supplier" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Supplier / Distributor
                            </label>
                            <input type="text" name="supplier" id="supplier" value="{{ old('supplier') }}"
                                   placeholder="e.g., Metro Drug, Zuellig Pharma, Unilab"
                                   class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 @error('supplier') border-rose-500 @enderror">
                            <p class="text-[11px] text-gray-400 mt-1">Pharmaceutical distributor or supplier.</p>
                            @error('supplier')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Expected / Delivery Date -->
                        <div>
                            <label for="delivery_date" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Expected / Arrival Date
                            </label>
                            <input type="date" name="delivery_date" id="delivery_date"
                                   value="{{ old('delivery_date', now()->toDateString()) }}"
                                   class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 @error('delivery_date') border-rose-500 @enderror">
                            <p class="text-[11px] text-gray-400 mt-1">Shipment dock arrival date.</p>
                            @error('delivery_date')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Delivery Remarks -->
                    <div class="mt-4">
                        <label for="notes" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Delivery Notes / Remarks
                        </label>
                        <textarea name="notes" id="notes" rows="2"
                                  placeholder="e.g., Urgent restock for ER formulary, cold chain storage required for Amoxicillin"
                                  class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 @error('notes') border-rose-500 @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Section 2: Multi-Medicine Restocking Items -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-gray-100">
                        <div>
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs">2</span>
                                Restock Medicines &amp; Quantities
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">Add multiple medicines under this single delivery shipment.</p>
                        </div>
                        <button type="button" @click="addItem()"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-lg text-xs font-bold transition cursor-pointer self-start sm:self-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>+ Add Medicine</span>
                        </button>
                    </div>

                    <!-- Items List -->
                    <div class="space-y-3">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="p-4 rounded-xl bg-gray-50 border border-gray-200/90 transition-all hover:border-gray-300 space-y-3">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-gray-700 flex items-center gap-1.5">
                                        <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px]" x-text="index + 1"></span>
                                        Medicine Item
                                    </span>
                                    <button type="button"
                                            @click="removeItem(index)"
                                            x-show="items.length > 1"
                                            class="inline-flex items-center gap-1 text-rose-600 hover:text-rose-800 font-semibold cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        <span>Remove</span>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-start">
                                    <!-- Medicine Dropdown (6 cols) -->
                                    <div class="sm:col-span-6">
                                        <label :for="`item_med_${index}`" class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1">
                                            Medicine <span class="text-rose-500">*</span>
                                        </label>
                                        <select :name="`items[${index}][medicine_id]`"
                                                :id="`item_med_${index}`"
                                                x-model="item.medicine_id"
                                                @change="onMedicineChange(index)"
                                                required
                                                class="block w-full rounded-lg border-gray-300 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                                            <option value="">-- Select Medicine Formulation --</option>
                                            <template x-for="med in catalog" :key="med.id">
                                                <option :value="med.id"
                                                        :selected="med.id == item.medicine_id"
                                                        x-text="`${med.name} (${med.generic_name}) - Current Stock: ${med.available_stock} ${med.unit}`">
                                                </option>
                                            </template>
                                        </select>
                                    </div>

                                    <!-- Quantity Input (2 cols) -->
                                    <div class="sm:col-span-2">
                                        <label :for="`item_qty_${index}`" class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1">
                                            Quantity <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="number" min="1" step="1"
                                                   :name="`items[${index}][quantity]`"
                                                   :id="`item_qty_${index}`"
                                                   x-model.number="item.quantity"
                                                   required
                                                   placeholder="100"
                                                   class="block w-full rounded-lg border-gray-300 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 pr-10">
                                            <span class="absolute right-2.5 top-2 text-[10px] font-bold text-gray-400 uppercase pointer-events-none"
                                                  x-text="item.unit || 'pcs'"></span>
                                        </div>
                                    </div>

                                    <!-- Optional Batch / Lot # (2 cols) -->
                                    <div class="sm:col-span-2">
                                        <label :for="`item_batch_${index}`" class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1">
                                            Batch / Lot #
                                        </label>
                                        <input type="text"
                                               :name="`items[${index}][batch_no]`"
                                               :id="`item_batch_${index}`"
                                               x-model="item.batch_no"
                                               placeholder="Auto if blank"
                                               class="block w-full rounded-lg border-gray-300 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                                    </div>

                                    <!-- Optional Expiry Date (2 cols) -->
                                    <div class="sm:col-span-2">
                                        <label :for="`item_expiry_${index}`" class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1">
                                            Expiry Date
                                        </label>
                                        <input type="date"
                                               :name="`items[${index}][expiry_date]`"
                                               :id="`item_expiry_${index}`"
                                               x-model="item.expiry_date"
                                               class="block w-full rounded-lg border-gray-300 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Add Medicine Row Action Button -->
                    <div class="pt-2 flex items-center justify-between">
                        <button type="button" @click="addItem()"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 rounded-lg text-xs font-bold transition cursor-pointer">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>+ Add Another Medicine</span>
                        </button>

                        <div class="text-right text-xs text-gray-600">
                            Total: <strong class="text-gray-900" x-text="items.length"></strong> medicine line(s),
                            <strong class="text-emerald-700" x-text="totalQuantity()"></strong> total unit(s)
                        </div>
                    </div>
                </div>

                <!-- Submit Bar -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="text-xs text-gray-500 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Created request will appear in Delivery Logs under <strong>Pending</strong>.</span>
                    </div>

                    <div class="flex items-center gap-3 self-end">
                        <a href="{{ route('inventory.movements') }}" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:text-gray-800 transition">
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-lg text-xs font-bold shadow-md hover:shadow-lg transition transform active:scale-98 cursor-pointer">
                            <svg class="w-4 h-4 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Submit Delivery Request</span>
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
        function deliveryForm(catalog, initialItems) {
            return {
                catalog: catalog,
                items: initialItems && initialItems.length > 0 ? initialItems : [{
                    medicine_id: '',
                    quantity: 100,
                    batch_no: '',
                    expiry_date: '',
                    unit: 'pcs'
                }],
                init() {
                    this.items.forEach((item, index) => {
                        this.onMedicineChange(index);
                    });
                },
                addItem() {
                    this.items.push({
                        medicine_id: '',
                        quantity: 100,
                        batch_no: '',
                        expiry_date: '',
                        unit: 'pcs'
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
                        this.items[index].unit = med.unit;
                    }
                },
                totalQuantity() {
                    return this.items.reduce((sum, item) => sum + (parseInt(item.quantity) || 0), 0);
                }
            };
        }
    </script>
</x-app-layout>
