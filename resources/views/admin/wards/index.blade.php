<x-admin-layout title="Ward & Bed Management — USM Hospital Admin">

    <div class="space-y-6" x-data="{ showNewWardModal: false, selectedWardId: null, showNewBedModal: false, selectedWardName: '' }">

        <!-- Top Header & Actions -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-black text-slate-800">Hospital Ward & Bed Management</h2>
                <p class="text-xs text-slate-500">Configure clinical wards, assign nurse stations, and provision hospital beds.</p>
            </div>

            <button type="button" @click="showNewWardModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#064e2b] hover:bg-[#053e22] text-white text-xs font-bold shadow-xs transition">
                <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Provision New Ward</span>
            </button>
        </div>

        <!-- 1. Stats Bar -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Wards</div>
                <div class="text-xl font-black text-slate-800 mt-1">{{ $stats['total_wards'] }}</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Beds</div>
                <div class="text-xl font-black text-slate-800 mt-1">{{ $stats['total_beds'] }}</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Available Beds</div>
                <div class="text-xl font-black text-emerald-700 mt-1">{{ $stats['available_beds'] }}</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <div class="text-[10px] font-bold uppercase tracking-wider text-rose-600">Occupied Beds</div>
                <div class="text-xl font-black text-rose-700 mt-1">{{ $stats['occupied_beds'] }}</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs col-span-2 sm:col-span-1">
                <div class="text-[10px] font-bold uppercase tracking-wider text-amber-600">Maintenance</div>
                <div class="text-xl font-black text-amber-700 mt-1">{{ $stats['maintenance_beds'] }}</div>
            </div>
        </div>

        <!-- 2. Wards & Beds Accordion / Cards List -->
        <div class="space-y-6">
            @forelse($wards as $ward)
                <div x-data="{ open: true }" class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                    <!-- Ward Header (Click to Toggle) -->
                    <div @click="open = !open"
                         class="px-5 py-4 bg-slate-50/80 hover:bg-slate-100/90 transition-colors border-b border-slate-200 flex flex-wrap items-center justify-between gap-3 cursor-pointer select-none"
                         :class="{ 'border-b-0': !open }">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-800 text-amber-300 font-bold flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-800">{{ $ward->ward_name }}</h3>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $ward->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $ward->status }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Assigned Station: <strong class="text-slate-700">{{ $ward->nurse_station_assigned }}</strong> &bull; Type: {{ ucfirst($ward->ward_type) }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-slate-500">
                                {{ $ward->beds->count() }} beds ({{ $ward->beds->where('occupancy_status', 'available')->count() }} free)
                            </span>
                            <button type="button"
                                    @click.stop="selectedWardId = {{ $ward->id }}; selectedWardName = '{{ addslashes($ward->ward_name) }}'; showNewBedModal = true"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>Add Bed</span>
                            </button>
                            <div class="w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-500 transition-transform duration-200 ml-1"
                                 :class="open ? 'rotate-180 bg-slate-100' : ''">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Beds Table (Collapsible) -->
                    <div x-show="open" class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-600">
                            <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold tracking-wider border-b border-slate-100">
                                <tr>
                                    <th class="py-2.5 px-4">Room Number</th>
                                    <th class="py-2.5 px-4">Bed Number</th>
                                    <th class="py-2.5 px-4">Occupancy Status</th>
                                    <th class="py-2.5 px-4">Current Admitted Patient</th>
                                    <th class="py-2.5 px-4 text-right">Maintenance Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($ward->beds as $bed)
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="py-2.5 px-4 font-bold text-slate-800">{{ $bed->room_number }}</td>
                                        <td class="py-2.5 px-4 font-semibold text-slate-700">{{ $bed->bed_number }}</td>
                                        <td class="py-2.5 px-4">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $bed->occupancy_status === 'available' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($bed->occupancy_status === 'occupied' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-amber-50 text-amber-800 border border-amber-200') }}">
                                                {{ $bed->occupancy_status }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-4">
                                            @if($bed->occupancy_status === 'occupied' && $bed->currentAdmission)
                                                <span class="font-bold text-slate-800">{{ $bed->currentAdmission->patient->name ?? $bed->currentAdmission->patient->user?->name ?? 'Patient' }}</span>
                                                <span class="text-[11px] text-slate-400 font-mono">({{ $bed->currentAdmission->patient->id_number }})</span>
                                            @else
                                                <span class="text-slate-400 text-[11px]">None</span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-4 text-right">
                                            @if($bed->occupancy_status !== 'occupied')
                                                <form method="POST" action="{{ route('admin.beds.toggle-status', $bed) }}" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="px-2.5 py-1 rounded-lg text-xs font-bold transition {{ $bed->occupancy_status === 'maintenance' ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' : 'bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200' }}">
                                                        {{ $bed->occupancy_status === 'maintenance' ? 'Set Available' : 'Set Maintenance' }}
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-slate-400 text-[11px] italic">Occupied (Locked)</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 text-center text-slate-400 text-xs">
                                            No beds created in this ward yet. Click "Add Bed" to provision.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="bg-white p-12 rounded-2xl border border-slate-200 text-center text-slate-400">
                    No hospital wards found. Click "Provision New Ward" to begin setup.
                </div>
            @endforelse
        </div>

        <!-- Provision New Ward Modal -->
        <div x-show="showNewWardModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4" @click.away="showNewWardModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-800">Provision New Hospital Ward</h3>
                    <button type="button" @click="showNewWardModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('admin.wards.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Ward Name *</label>
                        <input type="text" name="ward_name" required placeholder="e.g. Surgical Ward West"
                               class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-600 focus:border-emerald-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Ward Classification / Type *</label>
                        <select name="ward_type" required class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-600 focus:border-emerald-600">
                            <option value="inpatient">Inpatient (General)</option>
                            <option value="emergency">Emergency / Triage</option>
                            <option value="pediatric">Pediatric</option>
                            <option value="maternity">Maternity & OB-GYN</option>
                            <option value="isolation">Isolation / Respiratory</option>
                            <option value="icu">Intensive Care Unit (ICU)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Assigned Nurse Station *</label>
                        <input type="text" name="nurse_station_assigned" required placeholder="e.g. Station 2 - Surgical Wing"
                               class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-600 focus:border-emerald-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Operational Status *</label>
                        <select name="status" required class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-600 focus:border-emerald-600">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="maintenance">Under Renovation / Maintenance</option>
                        </select>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="showNewWardModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs">Save Ward</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Bed to Ward Modal -->
        <div x-show="showNewBedModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4" @click.away="showNewBedModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-800">Add Bed to <span x-text="selectedWardName"></span></h3>
                    <button type="button" @click="showNewBedModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" :action="'{{ url('admin/wards') }}/' + selectedWardId + '/beds'" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Room Identifier *</label>
                        <input type="text" name="room_number" required placeholder="e.g. Room 104 or Bay 3"
                               class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-600 focus:border-emerald-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Bed Number / Label *</label>
                        <input type="text" name="bed_number" required placeholder="e.g. Bed A or Bed 01"
                               class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-600 focus:border-emerald-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Initial Status *</label>
                        <select name="occupancy_status" required class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-600 focus:border-emerald-600">
                            <option value="available">Available (Ready for patients)</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="showNewBedModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold shadow-xs">Add Bed</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-admin-layout>
