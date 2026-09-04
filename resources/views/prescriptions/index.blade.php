<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Prescription Module') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">Encode patient prescriptions, review statuses, and route orders to the pharmacy queue.</p>
            </div>

            @can('create', App\Models\Prescription::class)
                <a href="{{ route('prescriptions.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('Encode Prescription') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-md bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Filter & Search Controls -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <form method="GET" action="{{ route('prescriptions.index') }}" class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <!-- Status Filter Tabs -->
                    <div class="flex items-center space-x-1 overflow-x-auto pb-2 md:pb-0">
                        @php
                            $tabs = [
                                '' => ['label' => 'All', 'count' => $counts['all']],
                                'pending' => ['label' => 'Pending', 'count' => $counts['pending']],
                                'routed' => ['label' => 'Routed to Pharmacy', 'count' => $counts['routed']],
                                'dispensed' => ['label' => 'Dispensed', 'count' => $counts['dispensed']],
                                'cancelled' => ['label' => 'Cancelled', 'count' => $counts['cancelled']],
                            ];
                        @endphp

                        @foreach ($tabs as $key => $tab)
                            <a href="{{ route('prescriptions.index', array_merge(request()->except('page'), ['status' => $key ?: null])) }}"
                               class="px-3 py-1.5 rounded-md text-xs font-medium whitespace-nowrap transition-colors {{ ($status === $key || ($key === '' && empty($status))) ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                {{ $tab['label'] }}
                                <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] {{ ($status === $key || ($key === '' && empty($status))) ? 'bg-indigo-800 text-white' : 'bg-gray-200 text-gray-800' }}">
                                    {{ $tab['count'] }}
                                </span>
                            </a>
                        @endforeach
                    </div>

                    <!-- Search Input -->
                    <div class="flex items-center space-x-2">
                        <div class="relative w-full md:w-64">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Search patient or doctor..."
                                   class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pl-8 pr-3 py-2">
                            <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        @if ($status)
                            <input type="hidden" name="status" value="{{ $status }}">
                        @endif
                        <button type="submit" class="px-3 py-2 text-xs bg-gray-800 text-white rounded-md hover:bg-gray-700">Filter</button>
                        @if ($search || $status)
                            <a href="{{ route('prescriptions.index') }}" class="px-2.5 py-2 text-xs text-gray-600 hover:text-gray-900">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Prescriptions Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                @if ($prescriptions->isEmpty())
                    <div class="p-12 text-center">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-sm font-medium text-gray-900">No prescriptions found</h3>
                        <p class="text-xs text-gray-500 mt-1">Get started by encoding a prescription for a student or resident patient.</p>
                        @can('create', App\Models\Prescription::class)
                            <div class="mt-4">
                                <a href="{{ route('prescriptions.create') }}" class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                    + Encode New Prescription
                                </a>
                            </div>
                        @endcan
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                            <thead class="bg-gray-50 text-gray-600 uppercase tracking-wider font-semibold">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Prescription #</th>
                                    <th scope="col" class="px-6 py-3">Patient</th>
                                    <th scope="col" class="px-6 py-3">Doctor</th>
                                    <th scope="col" class="px-6 py-3">Items</th>
                                    <th scope="col" class="px-6 py-3">Encoded By</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Date</th>
                                    <th scope="col" class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach ($prescriptions as $prescription)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 font-mono font-medium text-gray-900 whitespace-nowrap">
                                            #{{ str_pad($prescription->id, 5, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900">{{ $prescription->patient->name }}</div>
                                            <div class="flex items-center space-x-1.5 mt-0.5">
                                                <span class="text-gray-500 text-[11px]">{{ $prescription->patient->id_number }}</span>
                                                <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold uppercase {{ $prescription->patient->patient_type === 'student' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                                    {{ $prescription->patient->patient_type }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-800">
                                            Dr. {{ $prescription->doctor_name }}
                                        </td>
                                        <td class="px-6 py-4 text-gray-700">
                                            <span class="font-semibold">{{ $prescription->items->count() }}</span>
                                            {{ Str::plural('item', $prescription->items->count()) }}
                                        </td>
                                        <td class="px-6 py-4 text-gray-500 text-[11px]">
                                            {{ $prescription->encodedBy->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $badges = [
                                                    'pending' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                                    'routed' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                                    'dispensed' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                                    'cancelled' => 'bg-gray-100 text-gray-700 border border-gray-200',
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium uppercase {{ $badges[$prescription->status] ?? 'bg-gray-100' }}">
                                                {{ $prescription->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-[11px]">
                                            {{ $prescription->created_at->format('M d, Y h:i A') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                            <a href="{{ route('prescriptions.show', $prescription) }}"
                                               class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-900">
                                                View
                                            </a>

                                            @if ($prescription->status === 'pending')
                                                @can('route', $prescription)
                                                    <form method="POST" action="{{ route('prescriptions.route', $prescription) }}" class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                                onclick="return confirm('Route this prescription to the Pharmacy queue?')"
                                                                class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-900">
                                                            Route
                                                        </button>
                                                    </form>
                                                @endcan
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($prescriptions->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $prescriptions->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

