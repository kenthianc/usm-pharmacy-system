<x-nurse-layout>
    <x-slot name="heading">
        <div class="flex items-center gap-3">
            <h1 class="text-xl font-bold text-slate-800 tracking-tight">Prescription Module</h1>
            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600">
                {{ $counts['all'] }}
            </span>
        </div>
    </x-slot>

    <x-slot name="actions">
        @can('create', App\Models\Prescription::class)
            <button type="button"
                    @click="$dispatch('open-new-prescription')"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#064e2b] hover:bg-[#053d22] text-white rounded-xl text-xs font-semibold shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>New Prescription</span>
            </button>
        @endcan
    </x-slot>

    <div class="space-y-4">

        @if (session('status'))
            <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-xs">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Sleek, Single-Row Navigation & Search Bar -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-3 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
            <!-- Filter Tabs -->
            <div class="flex items-center gap-1 overflow-x-auto scrollbar-none">
                @php
                    $tabs = [
                        '' => ['label' => 'All', 'count' => $counts['all']],
                        'pending' => ['label' => 'Pending', 'count' => $counts['pending']],
                        'routed' => ['label' => 'Routed', 'count' => $counts['routed']],
                        'dispensed' => ['label' => 'Dispensed', 'count' => $counts['dispensed']],
                        'cancelled' => ['label' => 'Cancelled', 'count' => $counts['cancelled']],
                    ];
                @endphp

                @foreach ($tabs as $key => $tab)
                    @php
                        $isActive = ($status === $key || ($key === '' && empty($status)));
                    @endphp
                    <a href="{{ route('prescriptions.index', array_merge(request()->except('page'), ['status' => $key ?: null])) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-colors {{ $isActive ? 'bg-[#064e2b] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <span>{{ $tab['label'] }}</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold {{ $isActive ? 'bg-amber-400 text-[#064e2b]' : 'bg-slate-200/80 text-slate-600' }}">
                            {{ $tab['count'] }}
                        </span>
                    </a>
                @endforeach
            </div>

            <!-- Search & Scope Controls -->
            <form method="GET" action="{{ route('prescriptions.index') }}" class="flex items-center gap-2 shrink-0">
                @if ($status)
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif

                <div class="relative w-full md:w-64">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Search patient, ID, doctor..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 text-slate-800 placeholder-slate-400">
                </div>

                <select name="scope"
                        onchange="this.form.submit()"
                        class="text-xs py-1.5 pl-2.5 pr-7 rounded-xl border border-slate-200 focus:border-emerald-600 text-slate-700 bg-white">
                    <option value="all" {{ $scope === 'all' ? 'selected' : '' }}>All</option>
                    <option value="mine" {{ $scope === 'mine' ? 'selected' : '' }}>Mine</option>
                </select>

                @if ($search || $status || $scope !== 'all')
                    <a href="{{ route('prescriptions.index') }}"
                       class="text-xs font-semibold text-slate-400 hover:text-slate-700 px-1 py-1"
                       title="Reset filters">
                        ✕
                    </a>
                @endif
            </form>
        </div>

        @php
            $badgeStyles = [
                'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                'routed' => 'bg-blue-50 text-blue-700 border-blue-200',
                'dispensed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'cancelled' => 'bg-slate-100 text-slate-500 border-slate-200',
            ];
            $dotStyles = [
                'pending' => 'bg-amber-500',
                'routed' => 'bg-blue-500',
                'dispensed' => 'bg-emerald-500',
                'cancelled' => 'bg-slate-400',
            ];
        @endphp

        <!-- Clean, High-Scannability Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/75 text-slate-500 font-semibold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th scope="col" class="px-4 py-3">Rx #</th>
                            <th scope="col" class="px-4 py-3">Patient</th>
                            <th scope="col" class="px-4 py-3">Doctor</th>
                            <th scope="col" class="px-4 py-3">Medicines</th>
                            <th scope="col" class="px-4 py-3">Date</th>
                            <th scope="col" class="px-4 py-3">Status</th>
                            <th scope="col" class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($prescriptions as $prescription)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <!-- Rx # -->
                                <td class="px-4 py-3.5 font-mono font-bold text-slate-700 whitespace-nowrap">
                                    <a href="{{ route('prescriptions.show', $prescription) }}" class="hover:text-emerald-700">
                                        #{{ str_pad($prescription->id, 5, '0', STR_PAD_LEFT) }}
                                    </a>
                                </td>

                                <!-- Patient -->
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="font-bold text-slate-900">
                                        <a href="{{ route('prescriptions.show', $prescription) }}" class="hover:text-emerald-700">
                                            {{ $prescription->patient->name }}
                                        </a>
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        {{ $prescription->patient->id_number }} &bull; {{ ucfirst($prescription->patient->patient_type) }}
                                    </div>
                                </td>

                                <!-- Doctor -->
                                <td class="px-4 py-3.5 text-slate-800 font-medium whitespace-nowrap">
                                    {{ Str::startsWith($prescription->doctor_name, 'Dr.') ? $prescription->doctor_name : 'Dr. ' . $prescription->doctor_name }}
                                </td>

                                <!-- Medicines -->
                                <td class="px-4 py-3.5">
                                    @php
                                        $items = $prescription->items;
                                        $firstItem = $items->first();
                                        $remainingCount = $items->count() - 1;
                                    @endphp
                                    @if ($firstItem)
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-medium text-slate-800">
                                                {{ $firstItem->medicine->name }}
                                                <span class="text-slate-400 font-normal">({{ $firstItem->quantity }})</span>
                                            </span>
                                            @if ($remainingCount > 0)
                                                <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded-md"
                                                      title="{{ $items->skip(1)->pluck('medicine.name')->implode(', ') }}">
                                                    +{{ $remainingCount }} more
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">None</span>
                                    @endif
                                </td>

                                <!-- Date -->
                                <td class="px-4 py-3.5 text-slate-600 whitespace-nowrap">
                                    {{ $prescription->created_at->format('M d, Y') }}
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold capitalize border {{ $badgeStyles[$prescription->status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $dotStyles[$prescription->status] ?? 'bg-slate-400' }}"></span>
                                        <span>{{ $prescription->status }}</span>
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('prescriptions.show', $prescription) }}"
                                           class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold text-[11px] transition">
                                            View
                                        </a>

                                        @if ($prescription->status === 'pending')
                                            @can('route', $prescription)
                                                <form method="POST" action="{{ route('prescriptions.route', $prescription) }}"
                                                      onsubmit="return confirm('Route Rx #{{ str_pad($prescription->id, 5, '0', STR_PAD_LEFT) }} to Pharmacy?');"
                                                      class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-[11px] shadow-xs transition">
                                                        Route
                                                    </button>
                                                </form>
                                            @endcan

                                            @can('cancel', $prescription)
                                                <form method="POST" action="{{ route('prescriptions.cancel', $prescription) }}"
                                                      onsubmit="return confirm('Void this prescription?');"
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="px-2 py-1 rounded-lg text-rose-500 hover:bg-rose-50 font-semibold text-[11px] transition">
                                                        Void
                                                    </button>
                                                </form>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                                    <p class="font-bold text-slate-700 text-sm">No prescriptions found</p>
                                    <p class="text-xs text-slate-400 mt-1">Try clearing filters or search keywords.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($prescriptions->hasPages())
                <div class="p-3 border-t border-slate-100 bg-slate-50/50">
                    {{ $prescriptions->links() }}
                </div>
            @endif
        </div>

    </div>
</x-nurse-layout>
