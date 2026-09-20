<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    <a href="{{ route('inventory.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <span>{{ __('Add New Medicine to Formulary') }}</span>
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Register a new pharmaceutical or medical supply in the catalog.</p>
            </div>
            <a href="{{ route('inventory.index') }}" class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold transition">
                Back to Inventory
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-6 sm:p-8">

                <form method="POST" action="{{ route('inventory.medicines.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Brand / Trade Name -->
                        <div class="sm:col-span-2">
                            <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Brand / Trade Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   placeholder="e.g., Biogesic 500mg, Amoxicillin Trihydrate"
                                   class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 @error('name') border-rose-500 @enderror">
                            @error('name')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Generic Name -->
                        <div class="sm:col-span-2">
                            <label for="generic_name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Generic Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="generic_name" id="generic_name" value="{{ old('generic_name') }}" required
                                   placeholder="e.g., Paracetamol, Amoxicillin"
                                   class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 @error('generic_name') border-rose-500 @enderror">
                            @error('generic_name')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Category <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="category" id="category" value="{{ old('category') }}" required
                                   placeholder="e.g., Antibiotic, Analgesic, Vitamin"
                                   class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 @error('category') border-rose-500 @enderror">
                            @error('category')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Unit Form -->
                        <div>
                            <label for="unit" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Unit / Dosage Form <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="unit" id="unit" value="{{ old('unit') }}" required
                                   placeholder="e.g., tablet, capsule, bottle, vial"
                                   class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 @error('unit') border-rose-500 @enderror">
                            @error('unit')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Selling Price -->
                        <div>
                            <label for="unit_price" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Selling Price (₱) <span class="text-rose-500">*</span>
                            </label>
                            <div class="mt-1.5 relative rounded-lg shadow-xs">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 font-semibold">
                                    ₱
                                </div>
                                <input type="number" step="0.01" min="0" name="unit_price" id="unit_price" value="{{ old('unit_price') }}" required
                                       placeholder="0.00"
                                       class="pl-7 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 @error('unit_price') border-rose-500 @enderror">
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1">Dispensing / patient sale price</p>
                            @error('unit_price')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Purchase / Cost Price -->
                        <div>
                            <label for="purchase_price" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Cost / Purchase Price (₱)
                            </label>
                            <div class="mt-1.5 relative rounded-lg shadow-xs">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 font-semibold">
                                    ₱
                                </div>
                                <input type="number" step="0.01" min="0" name="purchase_price" id="purchase_price" value="{{ old('purchase_price') }}"
                                       placeholder="Auto-calculated if blank"
                                       class="pl-7 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 @error('purchase_price') border-rose-500 @enderror">
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1">Wholesale cost per unit used for inventory valuation</p>
                            @error('purchase_price')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Reorder Level -->
                        <div>
                            <label for="reorder_level" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Reorder Level (Low Stock Threshold) <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" min="0" name="reorder_level" id="reorder_level" value="{{ old('reorder_level', 10) }}" required
                                   class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 @error('reorder_level') border-rose-500 @enderror">
                            <p class="text-[11px] text-gray-400 mt-1">Alerts triggered when total stock drops to or below this count.</p>
                            @error('reorder_level')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Active Status -->
                        <div class="sm:col-span-2 pt-2 border-t border-gray-100">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-emerald-600 shadow-xs focus:ring-emerald-500">
                                <span class="text-xs font-semibold text-gray-800">Active Formulation</span>
                            </label>
                            <p class="text-[11px] text-gray-400 pl-6">Active medicines appear in clinical prescribing forms and dispensary queues.</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('inventory.index') }}" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:text-gray-800 transition">
                            Cancel
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-bold shadow-sm transition">
                            Save Medicine
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
