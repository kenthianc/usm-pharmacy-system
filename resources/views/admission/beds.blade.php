<x-admission-layout title="Ward & Bed Availability Board — USM Hospital">
    <x-slot:header>
        Real-Time Ward & Bed Availability Board
    </x-slot:header>

    <div class="space-y-6">

        <!-- Top Summary Cards & Filters -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Bed Occupancy Telemetry</h2>
                    <p class="text-xs text-slate-500">Live operational status of clinical beds across all USM Hospital wards.</p>
                </div>

                <!-- Status Filter Pills -->
                <div class="flex flex-wrap items-center gap-1.5">
                    <a href="{{ route('admission.beds', array_filter(['ward_type' => $typeFilter ?? null, 'ward_id' => $wardFilter])) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ empty($statusFilter) ? 'bg-[#064e2b] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        All ({{ $summary['total'] }})
                    </a>
                    <a href="{{ route('admission.beds', array_filter(['status' => 'available', 'ward_type' => $typeFilter ?? null, 'ward_id' => $wardFilter])) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'available' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' }}">
                        🟢 Available ({{ $summary['available'] }})
                    </a>
                    <a href="{{ route('admission.beds', array_filter(['status' => 'occupied', 'ward_type' => $typeFilter ?? null, 'ward_id' => $wardFilter])) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'occupied' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100' }}">
                        🔴 Occupied ({{ $summary['occupied'] }})
                    </a>
                    <a href="{{ route('admission.beds', array_filter(['status' => 'maintenance', 'ward_type' => $typeFilter ?? null, 'ward_id' => $wardFilter])) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'maintenance' ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100' }}">
                        🟡 Maintenance ({{ $summary['maintenance'] }})
                    </a>
                </div>
            </div>

            <!-- Ward Type / Classification Filter Bar -->
            @if(isset($wardTypes) && $wardTypes->count() > 0)
                <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-slate-100">
                    <span class="text-xs font-bold text-slate-600">Ward Classification:</span>
                    <a href="{{ route('admission.beds', array_filter(['status' => $statusFilter, 'ward_id' => $wardFilter])) }}"
                       class="px-2.5 py-1 rounded-lg text-xs font-bold transition {{ empty($typeFilter) ? 'bg-slate-800 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        All Types ({{ $allWards->count() }})
                    </a>
                    @foreach($wardTypes as $type)
                        @php
                            $typeCount = $allWards->where('ward_type', $type)->count();
                        @endphp
                        <a href="{{ route('admission.beds', array_filter(['status' => $statusFilter, 'ward_type' => $type])) }}"
                           class="px-2.5 py-1 rounded-lg text-xs font-bold capitalize transition {{ $typeFilter === $type ? 'bg-emerald-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            {{ $type }} ({{ $typeCount }})
                        </a>
                    @endforeach
                </div>
            @endif

            <!-- Specific Ward Dropdown Filter -->
            <form method="GET" action="{{ route('admission.beds') }}" class="flex flex-wrap items-center gap-3 pt-3 border-t border-slate-100">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
                <input type="hidden" name="ward_type" value="{{ $typeFilter ?? '' }}">
                <label for="ward_id" class="text-xs font-bold text-slate-600">Filter by Specific Ward:</label>
                <select name="ward_id" id="ward_id" onchange="this.form.submit()"
                        class="text-xs rounded-xl border-slate-300 py-1.5 px-3 focus:ring-emerald-600 focus:border-emerald-600">
                    <option value="">All Hospital Wards</option>
                    @foreach($allWards as $w)
                        <option value="{{ $w->id }}" {{ (string)$wardFilter === (string)$w->id ? 'selected' : '' }}>
                            {{ $w->ward_name }} ({{ ucfirst($w->ward_type) }} &bull; {{ $w->nurse_station_assigned }})
                        </option>
                    @endforeach
                </select>
                @if($wardFilter || $statusFilter || !empty($typeFilter))
                    <a href="{{ route('admission.beds') }}" class="text-xs text-rose-600 hover:underline font-bold">Clear All Filters</a>
                @endif
            </form>
        </div>

        <!-- Wards Accordion Container -->
        <div x-data="{
            openWards: {
                @foreach($wards as $w)
                    {{ $w->id }}: {{ ($wardFilter || !empty($typeFilter) || $wards->count() <= 2 || $loop->first) ? 'true' : 'false' }},
                @endforeach
            },
            toggle(id) {
                this.openWards[id] = !this.openWards[id];
            },
            isOpen(id) {
                return !!this.openWards[id];
            },
            expandAll() {
                Object.keys(this.openWards).forEach(k => this.openWards[k] = true);
            },
            collapseAll() {
                Object.keys(this.openWards).forEach(k => this.openWards[k] = false);
            }
        }" class="space-y-4">

            <!-- Toolbar Header: Count + Expand/Collapse All Buttons -->
            <div class="flex flex-wrap items-center justify-between gap-3 px-1">
                <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                    <span class="font-bold text-slate-800">{{ $wards->count() }} {{ Str::plural('Ward', $wards->count()) }}</span>
                    <span>&bull;</span>
                    <span>Click any ward header to collapse or expand</span>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button"
                            @click="expandAll()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold shadow-2xs transition active:scale-98 cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                        <span>Expand All</span>
                    </button>
                    <button type="button"
                            @click="collapseAll()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold shadow-2xs transition active:scale-98 cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
                        </svg>
                        <span>Collapse All</span>
                    </button>
                </div>
            </div>

            <!-- Collapsible Wards List -->
            @forelse($wards as $ward)
                @php
                    $availBeds = $ward->beds->where('occupancy_status', 'available')->count();
                    $occBeds = $ward->beds->where('occupancy_status', 'occupied')->count();
                    $maintBeds = $ward->beds->where('occupancy_status', 'maintenance')->count();
                    $totalBeds = $ward->beds->count();
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden transition-all">
                    <!-- Ward Header Strip (Click to Collapse / Expand) -->
                    <div @click="toggle({{ $ward->id }})"
                         class="px-5 py-3.5 bg-slate-50/80 hover:bg-slate-100/90 transition-colors flex flex-wrap items-center justify-between gap-3 cursor-pointer select-none border-b border-slate-200"
                         :class="{ 'border-b-0': !isOpen({{ $ward->id }}) }">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full {{ $availBeds > 0 ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-800 leading-tight">{{ $ward->ward_name }}</h3>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider {{ $ward->ward_type === 'emergency' ? 'bg-rose-100 text-rose-800 border border-rose-200' : ($ward->ward_type === 'isolation' ? 'bg-purple-100 text-purple-800 border border-purple-200' : ($ward->ward_type === 'pediatric' ? 'bg-sky-100 text-sky-800 border border-sky-200' : ($ward->ward_type === 'maternity' ? 'bg-pink-100 text-pink-800 border border-pink-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200'))) }}">
                                        {{ ucfirst($ward->ward_type) }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5">Nurse Station: <span class="font-bold text-emerald-800">{{ $ward->nurse_station_assigned }}</span></p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5">
                            <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">
                                {{ $availBeds }} Available
                            </span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800">
                                {{ $occBeds }} Occupied
                            </span>
                            @if($maintBeds > 0)
                                <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800">
                                    {{ $maintBeds }} Maintenance
                                </span>
                            @endif
                            <span class="text-xs font-semibold text-slate-400">
                                ({{ $totalBeds }} {{ Str::plural('bed', $totalBeds) }})
                            </span>

                            <!-- Rotating Arrow Toggle Indicator -->
                            <div class="w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-500 transition-transform duration-200 ml-1"
                                 :class="isOpen({{ $ward->id }}) ? 'rotate-180 bg-slate-100 text-emerald-800' : ''">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Beds Grid (Collapsible) -->
                    <div x-show="isOpen({{ $ward->id }})"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="p-5 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 bg-white">
                        @forelse($ward->beds as $bed)
                            <div class="rounded-xl border p-4 transition-all {{ $bed->occupancy_status === 'available' ? 'bg-emerald-50/40 border-emerald-200 hover:border-emerald-400' : ($bed->occupancy_status === 'occupied' ? 'bg-rose-50/40 border-rose-200' : 'bg-amber-50/50 border-amber-200') }}">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $bed->room_number }}</span>
                                        <h4 class="text-base font-black text-slate-800">{{ $bed->bed_number }}</h4>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase {{ $bed->occupancy_status === 'available' ? 'bg-emerald-100 text-emerald-800' : ($bed->occupancy_status === 'occupied' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $bed->occupancy_status }}
                                    </span>
                                </div>

                                <div class="mt-3 pt-3 border-t border-slate-200/60 text-xs">
                                    @if($bed->occupancy_status === 'available')
                                        <p class="text-[11px] text-emerald-700 font-semibold mb-2.5 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Ready for patient admission
                                        </p>
                                        <a href="{{ route('admission.patients') }}"
                                           class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition">
                                            <span>Assign Patient</span>
                                            &rarr;
                                        </a>
                                    @elseif($bed->occupancy_status === 'occupied' && $bed->currentAdmission)
                                        @php $ad = $bed->currentAdmission; @endphp
                                        <div class="space-y-1">
                                            <p class="font-bold text-slate-800 truncate">{{ $ad->patient->name ?? $ad->patient->user?->name ?? 'Admitted Patient' }}</p>
                                            <p class="text-[11px] text-slate-500 font-mono">{{ $ad->patient->id_number }}</p>
                                            <p class="text-[10px] text-rose-600 font-bold uppercase">{{ $ad->patient_classification }} &bull; {{ $ad->admission_date->diffForHumans() }}</p>
                                        </div>
                                    @else
                                        <p class="text-[11px] text-amber-800 font-medium">Bed under routine clinical sanitization or maintenance.</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-4 text-center text-xs text-slate-400">
                                No beds matching the current filter in this ward.
                            </div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="bg-white p-12 rounded-2xl border border-slate-200 text-center text-slate-400">
                    No active hospital wards found. Please configure wards in the administration panel.
                </div>
            @endforelse
        </div>

    </div>
</x-admission-layout>
