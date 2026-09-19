<x-nurse-layout>
    <x-slot name="heading">
        <div class="flex items-center gap-3">
            <h1 class="text-xl font-bold text-slate-800 tracking-tight">Patient Directory</h1>
            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600">
                {{ $totalPatients }} registered
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

        <!-- Search & Classification Filter Bar -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-3 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
            <!-- Filter Tabs -->
            <div class="flex items-center gap-1 overflow-x-auto scrollbar-none">
                @php
                    $tabs = [
                        '' => ['label' => 'All Patients', 'count' => $totalPatients],
                        'student' => ['label' => 'Students', 'count' => $studentCount],
                        'resident' => ['label' => 'Faculty / Residents', 'count' => $residentCount],
                    ];
                @endphp

                @foreach ($tabs as $key => $tab)
                    @php
                        $isActive = ($type === $key || ($key === '' && empty($type)));
                    @endphp
                    <a href="{{ route('prescriptions.patients', array_merge(request()->except('page'), ['type' => $key ?: null])) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-colors {{ $isActive ? 'bg-[#064e2b] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <span>{{ $tab['label'] }}</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold {{ $isActive ? 'bg-amber-400 text-[#064e2b]' : 'bg-slate-200/80 text-slate-600' }}">
                            {{ $tab['count'] }}
                        </span>
                    </a>
                @endforeach
            </div>

            <!-- Search Controls -->
            <form method="GET" action="{{ route('prescriptions.patients') }}" class="flex items-center gap-2 shrink-0">
                @if ($type)
                    <input type="hidden" name="type" value="{{ $type }}">
                @endif

                <div class="relative w-full md:w-64">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Search name, ID number..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 text-slate-800 placeholder-slate-400">
                </div>

                @if ($search || $type)
                    <a href="{{ route('prescriptions.patients') }}"
                       class="text-xs font-semibold text-slate-400 hover:text-slate-700 px-1 py-1"
                       title="Reset search">
                        ✕
                    </a>
                @endif
            </form>
        </div>

        <!-- Patients Directory Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/75 text-slate-500 font-semibold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th scope="col" class="px-4 py-3">Patient ID</th>
                            <th scope="col" class="px-4 py-3">Full Name</th>
                            <th scope="col" class="px-4 py-3">Classification</th>
                            <th scope="col" class="px-4 py-3">Contact</th>
                            <th scope="col" class="px-4 py-3">Total Prescriptions</th>
                            <th scope="col" class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($patients as $patient)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <!-- ID Number -->
                                <td class="px-4 py-3.5 font-mono font-bold text-slate-700 whitespace-nowrap">
                                    {{ $patient->id_number }}
                                </td>

                                <!-- Name & Email -->
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="font-bold text-slate-900">
                                        {{ $patient->name }}
                                    </div>
                                    @if ($patient->user?->email)
                                        <div class="text-[11px] text-slate-400">
                                            {{ $patient->user->email }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Classification -->
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold capitalize {{ $patient->patient_type === 'student' ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-purple-50 text-purple-700 border border-purple-200' }}">
                                        {{ $patient->patient_type }}
                                    </span>
                                </td>

                                <!-- Contact -->
                                <td class="px-4 py-3.5 text-slate-600 whitespace-nowrap">
                                    {{ $patient->contact_number ?: '—' }}
                                </td>

                                <!-- Prescriptions Count -->
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    @php $pCount = $patient->prescriptions->count(); @endphp
                                    @if ($pCount > 0)
                                        <a href="{{ route('prescriptions.index', ['search' => $patient->id_number]) }}"
                                           class="font-semibold text-emerald-700 hover:text-emerald-800">
                                            {{ $pCount }} {{ Str::plural('Rx', $pCount) }} &rarr;
                                        </a>
                                    @else
                                        <span class="text-slate-400">0 Rx</span>
                                    @endif
                                </td>

                                <!-- Action -->
                                <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                    <a href="{{ route('prescriptions.index', ['search' => $patient->id_number]) }}"
                                       class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold text-[11px] transition">
                                        View History
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-slate-500">
                                    <p class="font-bold text-slate-700 text-sm">No registered patients found</p>
                                    <p class="text-xs text-slate-400 mt-1">Try clearing search filters or encode a new prescription.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($patients->hasPages())
                <div class="p-3 border-t border-slate-100 bg-slate-50/50">
                    {{ $patients->links() }}
                </div>
            @endif
        </div>

    </div>
</x-nurse-layout>
