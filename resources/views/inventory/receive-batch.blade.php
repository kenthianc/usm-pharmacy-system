<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    <a href="{{ route('inventory.medicines.show', $medicine) }}" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <span>{{ __('Log Delivery Shipment') }}</span>
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Record incoming delivery for <strong class="text-gray-700">{{ $medicine->name }}</strong> ({{ $medicine->generic_name }}).</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('inventory.medicines.show', $medicine) }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold transition">
                    Cancel
                </a>
                <button type="button"
                        onclick="document.getElementById('receive-batch-form').requestSubmit();"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-sm transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Save Delivery Log</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            <div class="bg-white rounded-xl border border-gray-200 shadow-xs p-6 sm:p-8">

                <!-- Medicine Summary Card -->
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-between">
                    <div>
                        <div class="font-bold text-emerald-950 text-sm">{{ $medicine->name }}</div>
                        <div class="text-xs text-emerald-700">{{ $medicine->generic_name }} • {{ $medicine->category }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-emerald-800 font-medium">Current Stock</div>
                        <div class="text-lg font-extrabold text-emerald-900">{{ number_format($medicine->available_stock) }} {{ $medicine->unit }}</div>
                    </div>
                </div>

                <form id="receive-batch-form" method="POST" action="{{ route('inventory.medicines.receive.store', $medicine) }}" class="space-y-5">
                    @csrf

                    <!-- Batch Number -->
                    <div>
                        <label for="batch_no" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Batch / Lot Number <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="batch_no" id="batch_no" value="{{ old('batch_no') }}" required
                               placeholder="e.g., BATCH-2026-001, LOT-9482"
                               class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 @error('batch_no') border-rose-500 @enderror">
                        <p class="text-[11px] text-gray-400 mt-1">Unique batch or lot code printed on shipment packaging.</p>
                        @error('batch_no')
                            <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Quantity Received -->
                        <div>
                            <label for="quantity_received" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Quantity Received ({{ $medicine->unit }}) <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" min="1" name="quantity_received" id="quantity_received" value="{{ old('quantity_received') }}" required
                                   placeholder="e.g., 500"
                                   class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 @error('quantity_received') border-rose-500 @enderror">
                            @error('quantity_received')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Supplier -->
                        <div>
                            <label for="supplier" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Supplier / Distributor
                            </label>
                            <input type="text" name="supplier" id="supplier" value="{{ old('supplier') }}"
                                   placeholder="e.g., Unilab, Zuellig Pharma"
                                   class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 @error('supplier') border-rose-500 @enderror">
                            @error('supplier')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Expiry Date -->
                        <div>
                            <label for="expiry_date" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Expiration Date <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date') }}" required
                                   class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 @error('expiry_date') border-rose-500 @enderror">
                            <p class="text-[11px] text-gray-400 mt-1">Required for FEFO dispensing sequence.</p>
                            @error('expiry_date')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Received Date -->
                        <div>
                            <label for="received_date" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Date Received <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="received_date" id="received_date" value="{{ old('received_date', now()->toDateString()) }}" required
                                   class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 @error('received_date') border-rose-500 @enderror">
                            <p class="text-[11px] text-gray-400 mt-1">Date shipment arrived at hospital dock.</p>
                            @error('received_date')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Notes / Remarks -->
                    <div>
                        <label for="notes" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Delivery Remarks / PO Number
                        </label>
                        <textarea name="notes" id="notes" rows="2" placeholder="e.g., PO #2026-0812, inspected sealed, cold chain intact"
                                  class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 @error('notes') border-rose-500 @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Immediate Acceptance vs Pending Inspection Option -->
                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-200/80 space-y-2">
                        <label class="inline-flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="auto_confirm" value="1" {{ old('auto_confirm') ? 'checked' : '' }}
                                   class="mt-0.5 rounded border-gray-300 text-emerald-600 shadow-xs focus:ring-emerald-500">
                            <div>
                                <span class="text-xs font-bold text-gray-800">Physical Inspection Verified (Confirm Immediately)</span>
                                <p class="text-[11px] text-gray-500 mt-0.5">
                                    Check this if you have already physically inspected the boxes, counted the units, and want to add them directly to active usable stock.
                                    Leave unchecked to save as <strong>Pending Inspection</strong> in delivery logs.
                                </p>
                            </div>
                        </label>
                    </div>

                    <!-- Submit Action Bar -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 border-t border-gray-100">
                        <div class="text-xs text-gray-500">
                            Logged deliveries appear in <a href="{{ route('inventory.movements') }}" class="text-emerald-700 underline font-semibold" target="_blank">Delivery Logs</a>.
                        </div>
                        <div class="flex items-center gap-3 self-end">
                            <a href="{{ route('inventory.medicines.show', $medicine) }}" class="px-4 py-2.5 text-xs font-semibold text-gray-600 hover:text-gray-800 transition">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-lg text-xs font-bold shadow-md hover:shadow-lg transition transform active:scale-98 cursor-pointer">
                                <svg class="w-4 h-4 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Save Delivery Log</span>
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
