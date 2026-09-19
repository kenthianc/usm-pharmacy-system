<x-nurse-layout>
    <x-slot name="heading">
        <div class="flex items-center gap-3">
            <a href="{{ route('prescriptions.index') }}"
               class="p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition"
               title="Back to Prescriptions">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-xl font-bold text-slate-800 tracking-tight">Prescription Module</h1>
        </div>
    </x-slot>
</x-nurse-layout>
