<!-- Sign Out Confirmation Modal Component -->
<div x-data="{ open: false }"
     @open-signout-modal.window="open = true"
     @keydown.escape.window="open = false"
     x-cloak
     x-show="open"
     class="relative z-50"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true"
     style="display: none;">

    <!-- Backdrop Blur & Fade -->
    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"
         @click="open = false">
    </div>

    <!-- Modal Dialog Window -->
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.outside="open = false"
                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200">

                <!-- Top Accent Gradient Line -->
                <div class="h-1.5 bg-gradient-to-r from-amber-500 via-emerald-600 to-teal-700"></div>

                <div class="p-6">
                    <!-- Icon + Title Header -->
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 shrink-0 shadow-xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight leading-snug">
                                Confirm Sign Out
                            </h3>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                Are you sure you want to end your active session? You will need to enter your credentials to log back in.
                            </p>
                        </div>
                    </div>

                    @auth
                    <!-- Active Account Snapshot -->
                    <div class="mt-5 p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#064e2b] text-amber-400 font-bold text-xs flex items-center justify-center border border-[#043c20] shrink-0 shadow-xs">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->name }}</div>
                            <div class="text-[11px] text-slate-500 truncate">{{ Auth::user()->email }}</div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 shrink-0">
                            {{ Auth::user()->hasRole('admin') ? 'Chief Admin' : (Auth::user()->hasRole('nurse') ? 'Nurse' : (Auth::user()->hasRole('pharmacist') ? 'Pharmacist' : (Auth::user()->hasRole('stock_manager') ? 'Inventory Mgr' : 'Patient'))) }}
                        </span>
                    </div>
                    @endauth

                    <!-- Action Buttons -->
                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button"
                                @click="open = false"
                                class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 active:bg-slate-200 border border-slate-300 transition-colors shadow-2xs cursor-pointer">
                            Stay Signed In
                        </button>

                        <form method="POST" action="{{ route('logout') }}" class="inline" data-test="confirm('Are you sure you want to log out?')">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 active:bg-rose-800 shadow-sm shadow-rose-600/25 transition-all transform active:scale-98 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span>Yes, Sign Out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
