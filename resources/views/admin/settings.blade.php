<x-admin-layout active="settings">
    <div class="max-w-4xl space-y-6">

            <!-- Breadcrumbs / Header -->
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-green-800 font-medium">Admin Hub</a>
                    <span>/</span>
                    <span class="text-slate-800 font-bold">Settings</span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2.5">
                    <svg class="w-6 h-6 text-green-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Clinic Operational Settings &amp; Thresholds
                </h1>
                <p class="text-xs text-slate-500 mt-1">Configure automated stock warning thresholds, clinic identity, and receipt details.</p>
            </div>

            <!-- Flash alerts -->
            @if (session('status'))
                <div class="p-4 rounded-xl bg-emerald-50 border-l-4 border-emerald-600 text-emerald-800 text-sm font-medium shadow-xs flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border-l-4 border-rose-600 text-rose-800 text-sm shadow-xs">
                    <div class="font-bold mb-1">Please correct the following errors:</div>
                    <ul class="list-disc list-inside text-xs space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
                <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                    @csrf

                    <!-- Section 1: Clinic Identity -->
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-green-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                            University Clinic Identity &amp; Contact
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Infirmary / Pharmacy Name</label>
                                <input type="text" name="clinic_name" value="{{ old('clinic_name', $settings['clinic_name']) }}" required class="w-full text-xs rounded-xl border-slate-300 focus:border-green-600 focus:ring-green-600">
                                <p class="text-[11px] text-slate-400 mt-1">Appears on official POS receipts and printable inventory reports.</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Contact Numbers</label>
                                    <input type="text" name="contact_number" value="{{ old('contact_number', $settings['contact_number']) }}" class="w-full text-xs rounded-xl border-slate-300 focus:border-green-600 focus:ring-green-600">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Campus Location</label>
                                    <input type="text" name="campus_location" value="{{ old('campus_location', $settings['campus_location']) }}" class="w-full text-xs rounded-xl border-slate-300 focus:border-green-600 focus:ring-green-600">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Automated Alert Thresholds -->
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-green-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                            Inventory Safety Thresholds
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Default Low Stock Alert Level</label>
                                <div class="relative">
                                    <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', $settings['low_stock_threshold']) }}" min="1" max="1000" required class="w-full text-xs rounded-xl border-slate-300 focus:border-green-600 focus:ring-green-600 pr-16">
                                    <span class="absolute right-3 top-2 text-xs text-slate-400 font-semibold">units</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">Items below this threshold trigger alerts on the staff dashboard.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Near-Expiry Warning Window</label>
                                <div class="relative">
                                    <input type="number" name="expiry_warning_days" value="{{ old('expiry_warning_days', $settings['expiry_warning_days']) }}" min="7" max="365" required class="w-full text-xs rounded-xl border-slate-300 focus:border-green-600 focus:ring-green-600 pr-16">
                                    <span class="absolute right-3 top-2 text-xs text-slate-400 font-semibold">days</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">Batches expiring within these days flag as "Expiring Soon".</p>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Receipt Footer Note -->
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-green-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                            POS Receipt Footer Note
                        </h2>

                        <div>
                            <textarea name="receipt_footer_note" rows="3" class="w-full text-xs rounded-xl border-slate-300 focus:border-green-600 focus:ring-green-600">{{ old('receipt_footer_note', $settings['receipt_footer_note']) }}</textarea>
                            <p class="text-[11px] text-slate-400 mt-1">Printed at the bottom of customer receipts issued during OTC sales.</p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-green-900 hover:bg-green-800 text-yellow-400 text-xs font-bold rounded-xl shadow-md transition border border-green-700">
                            Save Operational Settings
                        </button>
                    </div>

                </form>
            </div>

        </div>
</x-admin-layout>
