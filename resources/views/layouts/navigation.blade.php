<nav x-data="{ open: false }" class="bg-green-900 border-b-4 border-yellow-500 text-white shadow-md">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center space-x-6">
                <!-- Brand / Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-lg bg-yellow-500 text-green-950 flex items-center justify-center font-bold text-lg shadow-sm border border-yellow-300 group-hover:bg-yellow-400 transition-colors">
                            ℞
                        </div>
                        <div class="hidden sm:block">
                            <p class="font-bold text-yellow-400 text-sm leading-tight whitespace-nowrap">USM Pharmacy</p>
                            <p class="text-green-300 text-xs whitespace-nowrap">
                                @hasanyrole('nurse')
                                    Nurse Portal
                                @elsehasrole('pharmacist')
                                    Dispensary Portal
                                @elsehasrole('stock_manager')
                                    Inventory Portal
                                @elsehasrole('admin')
                                    Admin Control
                                @else
                                    Hospital Health System
                                @endhasanyrole
                            </p>
                        </div>
                    </a>
                </div>

                @php
                    $pendingRxCount = \App\Models\Prescription::pending()->count();
                @endphp

                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:items-center sm:space-x-1.5">
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center px-3.5 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-yellow-500 text-green-950 font-bold shadow-sm' : 'text-green-200 hover:bg-green-800 hover:text-white' }}">
                        {{ __('Dashboard') }}
                    </a>

                    @role('patient')
                        <a href="{{ route('patient.prescriptions') }}"
                           class="inline-flex items-center px-3.5 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('patient.prescriptions*') ? 'bg-yellow-500 text-green-950 font-bold shadow-sm' : 'text-green-200 hover:bg-green-800 hover:text-white' }}">
                            {{ __('My Prescriptions') }}
                        </a>
                        <a href="{{ route('patient.profile') }}"
                           class="inline-flex items-center px-3.5 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('patient.profile') ? 'bg-yellow-500 text-green-950 font-bold shadow-sm' : 'text-green-200 hover:bg-green-800 hover:text-white' }}">
                            {{ __('My Profile') }}
                        </a>
                    @endrole

                    @hasanyrole('nurse|admin')
                        <a href="{{ route('prescriptions.index') }}"
                           class="inline-flex items-center px-3.5 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('prescriptions.*') ? 'bg-yellow-500 text-green-950 font-bold shadow-sm' : 'text-green-200 hover:bg-green-800 hover:text-white' }}">
                            <span>{{ __('Prescriptions') }}</span>
                            @if ($pendingRxCount > 0)
                                <span class="ml-2 bg-yellow-400 text-green-950 text-[10px] font-extrabold px-1.5 py-0.5 rounded-full shadow-2xs">
                                    {{ $pendingRxCount }}
                                </span>
                            @endif
                        </a>
                    @endhasanyrole

                    @hasanyrole('pharmacist|admin')
                        <a href="{{ route('pos.index') }}"
                           class="inline-flex items-center px-3.5 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('pos.index') ? 'bg-yellow-500 text-green-950 font-bold shadow-sm' : 'text-green-200 hover:bg-green-800 hover:text-white' }}">
                            {{ __('Pharmacy / POS') }}
                        </a>
                        <a href="{{ route('pos.reports') }}"
                           class="inline-flex items-center px-3.5 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('pos.reports') ? 'bg-yellow-500 text-green-950 font-bold shadow-sm' : 'text-green-200 hover:bg-green-800 hover:text-white' }}">
                            {{ __('POS Reports') }}
                        </a>
                    @endhasanyrole

                    @hasanyrole('stock_manager|admin')
                        <a href="{{ route('inventory.index') }}"
                           class="inline-flex items-center px-3.5 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('inventory.index', 'inventory.medicines.*', 'inventory.batches.*') ? 'bg-yellow-500 text-green-950 font-bold shadow-sm' : 'text-green-200 hover:bg-green-800 hover:text-white' }}">
                            {{ __('Inventory') }}
                        </a>
                        <a href="{{ route('inventory.movements') }}"
                           class="inline-flex items-center px-3.5 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('inventory.movements') ? 'bg-yellow-500 text-green-950 font-bold shadow-sm' : 'text-green-200 hover:bg-green-800 hover:text-white' }}">
                            {{ __('Delivery & Logs') }}
                        </a>
                    @endhasanyrole

                    @hasanyrole('stock_manager|pharmacist|admin')
                        <a href="{{ route('inventory.risk-engine') }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('inventory.risk-engine*') ? 'bg-yellow-500 text-green-950 font-bold shadow-sm' : 'text-emerald-300 hover:bg-green-800 hover:text-white' }}">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span>{{ __('Risk Forecast') }}</span>
                        </a>
                    @endhasanyrole

                    @role('admin')
                        <div class="h-6 w-px bg-green-700/60 mx-1"></div>
                        <a href="{{ route('admin.dashboard') }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-bold transition-all {{ request()->routeIs('admin.*') ? 'bg-yellow-500 text-green-950 shadow-sm' : 'bg-yellow-500/20 text-yellow-300 hover:bg-yellow-500 hover:text-green-950 border border-yellow-500/40' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                            <span>{{ __('Admin Portal') }}</span>
                        </a>
                    @endrole
                </div>
            </div>

            <!-- Settings Dropdown & Forecast Drawer Button -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Consistent Risk Summary Pill -->
                <x-risk-summary-pill class="mr-3" />

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium text-green-100 hover:bg-green-800/80 transition-colors focus:outline-none">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-8 h-8 rounded-full bg-yellow-500 text-green-950 flex items-center justify-center font-bold text-xs shadow-sm">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div class="text-left">
                                    <span class="block text-xs font-bold text-white leading-tight">{{ Auth::user()->name }}</span>
                                    <span class="text-[10px] font-semibold uppercase text-yellow-400">
                                        {{ Auth::user()->roles->pluck('name')->first() ?? 'No Role' }}
                                    </span>
                                </div>
                            </div>

                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4 text-green-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); if (confirm('Are you sure you want to log out?')) this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger Button -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-green-200 hover:text-white hover:bg-green-800 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-green-950/90 border-t border-green-800">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-green-800">
                {{ __('Dashboard') }}
            </a>

            @hasanyrole('nurse|admin')
                <a href="{{ route('prescriptions.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-green-800 flex items-center justify-between">
                    <span>{{ __('Prescriptions') }}</span>
                    @if ($pendingRxCount > 0)
                        <span class="bg-yellow-400 text-green-950 text-xs font-bold px-2 py-0.5 rounded-full">
                            {{ $pendingRxCount }}
                        </span>
                    @endif
                </a>
            @endhasanyrole

            @hasanyrole('pharmacist|admin')
                <a href="{{ route('pos.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-green-800">
                    {{ __('Pharmacy / POS') }}
                </a>
                <a href="{{ route('pos.reports') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-green-800">
                    {{ __('POS Reports') }}
                </a>
            @endhasanyrole

            @hasanyrole('stock_manager|admin')
                <a href="{{ route('inventory.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-green-800">
                    {{ __('Inventory') }}
                </a>
                <a href="{{ route('inventory.movements') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-green-800">
                    {{ __('Delivery & Logs') }}
                </a>
            @endhasanyrole

            @hasanyrole('stock_manager|pharmacist|admin')
                <a href="{{ route('inventory.risk-engine') }}" class="block px-3 py-2 rounded-md text-base font-medium text-emerald-300 hover:bg-green-800">
                    {{ __('Risk & Forecasting') }}
                </a>
            @endhasanyrole

            @role('admin')
                <div class="border-t border-green-800/80 my-2 pt-2">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-md text-base font-bold text-yellow-300 hover:bg-green-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                        <span>{{ __('Admin Portal') }}</span>
                    </a>
                </div>
            @endrole
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-3 border-t border-green-800 px-4">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-8 h-8 rounded-full bg-yellow-500 text-green-950 flex items-center justify-center font-bold text-xs">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="font-bold text-sm text-white">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-yellow-400">{{ Auth::user()->roles->pluck('name')->first() ?? 'No Role' }}</div>
                </div>
            </div>

            <div class="space-y-1">
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md text-sm text-green-200 hover:bg-green-800 hover:text-white">
                    {{ __('Profile') }}
                </a>
                <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Are you sure you want to log out?');">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-sm text-red-300 hover:bg-red-900/60 hover:text-white">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Dual-Risk Engine Global Drawer Modal -->
    <x-dual-risk-engine-modal />
</nav>
