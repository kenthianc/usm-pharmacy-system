@props(['active' => 'dashboard'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'USM Pharmacy System') }} — Patient Portal</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900 min-h-screen flex flex-col">
    <!-- ── Sticky Navbar ── -->
    <header x-data="{ mobileMenuOpen: false }" class="sticky top-0 z-40 bg-green-800 border-b-4 border-yellow-500 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between h-14">
            <!-- Brand Logo -->
            <a href="{{ route('patient.dashboard') }}" class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center p-1 border border-white/20">
                    <svg viewBox="0 0 32 32" class="w-6 h-6 text-yellow-400" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 3L4 8v8c0 6.627 5.373 12 12 12s12-5.373 12-12V8L16 3z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        <path d="M11 16l3 3 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="hidden sm:block">
                    <p class="font-bold text-yellow-400 text-sm leading-tight">USM Pharmacy</p>
                    <p class="text-[10px] text-green-300">Patient Portal</p>
                </div>
            </a>

            <!-- Desktop Nav Items -->
            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ route('patient.dashboard') }}"
                   class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold transition-colors {{ $active === 'dashboard' ? 'bg-yellow-400 text-green-900' : 'text-green-100 hover:bg-green-700' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect width="7" height="9" x="3" y="3" rx="1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <rect width="7" height="5" x="14" y="3" rx="1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <rect width="7" height="9" x="14" y="12" rx="1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <rect width="7" height="5" x="3" y="16" rx="1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('patient.prescriptions') }}"
                   class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold transition-colors {{ in_array($active, ['prescriptions', 'prescription-detail']) ? 'bg-yellow-400 text-green-900' : 'text-green-100 hover:bg-green-700' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect width="8" height="4" x="8" y="2" rx="1" ry="1" stroke-width="2"/>
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 11h4M12 16h4M8 11h.01M8 16h.01" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Prescriptions
                </a>

                <a href="{{ route('medicines') }}"
                   class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold transition-colors {{ $active === 'storefront' ? 'bg-yellow-400 text-green-900' : 'text-green-100 hover:bg-green-700' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="3" y1="6" x2="21" y2="6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 10a4 4 0 01-8 0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Pharmacy
                </a>

                <a href="{{ route('patient.dashboard') }}#chat"
                   class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold transition-colors {{ $active === 'chat' ? 'bg-yellow-400 text-green-900' : 'text-green-100 hover:bg-green-700' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8V4m0 0a2 2 0 100-4 2 2 0 000 4z" stroke-width="2" stroke-linecap="round"/>
                        <rect width="16" height="12" x="4" y="8" rx="2" stroke-width="2"/>
                        <path d="M2 14h2M20 14h2M9 13v2M15 13v2" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    AI Assistant
                </a>

                <a href="{{ route('patient.profile') }}"
                   class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold transition-colors {{ $active === 'profile' ? 'bg-yellow-400 text-green-900' : 'text-green-100 hover:bg-green-700' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="8" r="4" stroke-width="2"/>
                        <path d="M18 20a6 6 0 00-12 0" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    My Profile
                </a>
            </nav>

            <!-- Sign Out Button & Mobile Toggle -->
            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('logout') }}" class="inline" onsubmit="return confirm('Are you sure you want to log out?');">
                    @csrf
                    <button type="submit" class="flex items-center gap-1.5 text-green-300 hover:text-red-300 text-xs transition-colors font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="hidden sm:inline">Sign out</span>
                    </button>
                </form>

                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-green-200 hover:text-white p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Drawer -->
        <div x-show="mobileMenuOpen" class="md:hidden bg-green-900 border-t border-green-700 px-4 py-3 space-y-1.5">
            <a href="{{ route('patient.dashboard') }}" class="block px-3 py-2 rounded-lg text-xs font-semibold {{ $active === 'dashboard' ? 'bg-yellow-400 text-green-900' : 'text-green-100 hover:bg-green-800' }}">Dashboard</a>
            <a href="{{ route('patient.prescriptions') }}" class="block px-3 py-2 rounded-lg text-xs font-semibold {{ in_array($active, ['prescriptions', 'prescription-detail']) ? 'bg-yellow-400 text-green-900' : 'text-green-100 hover:bg-green-800' }}">Prescriptions</a>
            <a href="{{ route('medicines') }}" class="block px-3 py-2 rounded-lg text-xs font-semibold {{ $active === 'storefront' ? 'bg-yellow-400 text-green-900' : 'text-green-100 hover:bg-green-800' }}">Pharmacy</a>
            <a href="{{ route('patient.dashboard') }}#chat" class="block px-3 py-2 rounded-lg text-xs font-semibold {{ $active === 'chat' ? 'bg-yellow-400 text-green-900' : 'text-green-100 hover:bg-green-800' }}">AI Assistant</a>
            <a href="{{ route('patient.profile') }}" class="block px-3 py-2 rounded-lg text-xs font-semibold {{ $active === 'profile' ? 'bg-yellow-400 text-green-900' : 'text-green-100 hover:bg-green-800' }}">My Profile</a>
        </div>
    </header>

    <!-- ── Main Content ── -->
    <main class="flex-1 flex flex-col">
        {{ $slot }}
    </main>
</body>
</html>
