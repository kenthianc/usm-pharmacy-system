<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patient Registration &mdash; USM Hospital &amp; Health Services Pharmacy</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-full font-sans antialiased text-slate-900 bg-slate-50 selection:bg-yellow-400 selection:text-green-950 flex flex-col justify-between"
      x-data="{
          track: '{{ old('patient_type', request('track', 'student')) }}',
          init() {
              if (this.track !== 'student' && this.track !== 'resident') {
                  this.track = 'student';
              }
          }
      }">

    <!-- Background Ambient Accents -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10">
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-100/60 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 -right-32 w-96 h-96 bg-amber-100/40 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-teal-100/50 rounded-full blur-3xl"></div>
        <div class="absolute inset-0 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:24px_24px] opacity-40"></div>
    </div>

    <!-- Top Navigation Header -->
    <header class="w-full max-w-5xl mx-auto px-4 sm:px-6 pt-6 pb-2 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-800 to-green-900 text-yellow-400 flex items-center justify-center font-black text-xl shadow-md shadow-emerald-950/20 border border-emerald-700/60 group-hover:scale-105 transition-transform shrink-0">
                ℞
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-base font-extrabold tracking-tight text-slate-900 leading-none">USM Pharmacy</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Hospital System
                    </span>
                </div>
                <p class="text-[11px] font-medium text-slate-500 mt-0.5">University Health Services Clinic &bull; Kabacan</p>
            </div>
        </a>

        <!-- Back to Sign In Link -->
        <a href="{{ route('login') }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-emerald-800 transition-colors py-2 px-3 rounded-lg hover:bg-slate-200/60">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            <span>Sign In</span>
        </a>
    </header>

    <!-- Main Registration Container (Centered, Focused Card Layout) -->
    <main class="w-full max-w-xl mx-auto px-4 sm:px-6 py-6 sm:py-10 flex-1 flex flex-col justify-center">

        <!-- Card Wrapper -->
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/70 border border-slate-200/80 p-6 sm:p-10 space-y-7 transition-all">

            <!-- Card Header -->
            <div class="text-center space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-[11px] font-bold uppercase tracking-wider shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Patient Onboarding Portal</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Create Patient Account
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto">
                    Select your classification below to proceed with the appropriate clinical onboarding procedure.
                </p>
            </div>

            <!-- 1. REGISTRATION TYPE SWITCHER (HCI Segment Control) -->
            <div class="space-y-1.5">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider text-center">
                    Select Patient Category
                </div>
                <div class="p-1.5 bg-slate-100/90 rounded-2xl flex gap-1.5 border border-slate-200/80 shadow-inner" role="tablist">
                    <!-- Student Option -->
                    <button type="button"
                            @click="track = 'student'"
                            :class="track === 'student'
                                ? 'bg-white text-emerald-950 font-bold shadow-sm border border-slate-200/60'
                                : 'text-slate-500 hover:text-slate-800 font-medium border border-transparent'"
                            class="flex-1 py-3 px-3.5 rounded-xl text-xs sm:text-sm flex items-center justify-center gap-2 transition-all cursor-pointer min-h-[44px]"
                            role="tab"
                            :aria-selected="track === 'student'">
                        <span class="text-base">🎓</span>
                        <span>USM Student</span>
                        <span class="hidden xs:inline-flex text-[10px] px-1.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60">
                            SSO
                        </span>
                    </button>

                    <!-- Local Resident Option -->
                    <button type="button"
                            @click="track = 'resident'"
                            :class="track === 'resident'
                                ? 'bg-white text-emerald-950 font-bold shadow-sm border border-slate-200/60'
                                : 'text-slate-500 hover:text-slate-800 font-medium border border-transparent'"
                            class="flex-1 py-3 px-3.5 rounded-xl text-xs sm:text-sm flex items-center justify-center gap-2 transition-all cursor-pointer min-h-[44px]"
                            role="tab"
                            :aria-selected="track === 'resident'">
                        <span class="text-base">🏡</span>
                        <span>Local Resident</span>
                        <span class="hidden xs:inline-flex text-[10px] px-1.5 py-0.5 rounded-full bg-amber-50 text-amber-800 font-bold border border-amber-200/60">
                            Intake Form
                        </span>
                    </button>
                </div>
            </div>

            <!-- Global Error / Notice Banner -->
            @if ($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-3 shadow-2xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="space-y-1">
                        <p class="font-bold">Please correct the following:</p>
                        <ul class="list-disc list-inside text-rose-700 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- 2. TRACK: STUDENT ONBOARDING (Single Sign-On Focus) -->
            <div x-show="track === 'student'"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-6">

                <!-- Institutional Domain Callout -->
                <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/70 flex items-start gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-xs">
                        ✓
                    </div>
                    <div class="space-y-1 text-xs">
                        <div class="font-bold text-emerald-950 flex items-center gap-1.5 flex-wrap">
                            <span>Institutional Domain Verification</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-200/70 text-emerald-900">
                                @usm.edu.ph
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-200/70 text-emerald-900">
                                @student.usm.edu.ph
                            </span>
                        </div>
                        <p class="text-emerald-800/90 leading-relaxed">
                            Sign in with your University Google account to automatically link student health subsidies and verify clinic records.
                        </p>
                    </div>
                </div>

                <!-- Google OAuth Form -->
                <form method="GET" action="{{ route('auth.google.redirect') }}" class="space-y-4">
                    <!-- Extra Field: Student ID Number (Optional / Prior to SSO) -->
                    <div class="space-y-1.5">
                        <label for="student_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Student ID Number <span class="text-slate-400 font-normal normal-case">(Optional)</span>
                        </label>
                        <div class="relative flex items-center">
                            <div class="absolute left-3.5 text-slate-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                                </svg>
                            </div>
                            <input
                                type="text"
                                name="student_id"
                                id="student_id"
                                value="{{ old('student_id', request('student_id')) }}"
                                placeholder="e.g. 2023-01452"
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all shadow-2xs min-h-[44px]"
                            />
                        </div>
                        <p class="text-[11px] text-slate-400">If omitted, an official digital health record ID will be generated upon first authorization.</p>
                    </div>

                    <!-- Primary SSO Button: "Continue with Google" -->
                    <button type="submit"
                            class="w-full py-3.5 px-5 bg-white hover:bg-slate-50 active:bg-slate-100 text-slate-800 border-2 border-slate-200 hover:border-slate-300 rounded-2xl text-sm font-bold shadow-sm transition-all flex items-center justify-center gap-3 transform active:scale-98 cursor-pointer min-h-[48px]">
                        <!-- Official Multi-colored Google "G" Icon -->
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                        </svg>
                        <span>Continue with USM Google Account</span>
                    </button>
                </form>

                <!-- Dev Simulation Helper (Only when Google Client ID is unconfigured or in local testing) -->
                @if (empty(config('services.google.client_id')) || request('demo_notice') === 'google_unconfigured')
                    <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200/80 space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-amber-900">
                            <span class="text-amber-600">⚡</span>
                            <span>Development Mode: Quick Google SSO Simulation</span>
                        </div>
                        <p class="text-[11px] text-amber-800 leading-relaxed">
                            Google OAuth keys are not configured in your <code class="px-1 py-0.5 bg-amber-100 rounded text-amber-950 font-mono">.env</code>. You can test institutional domain validation instantly below:
                        </p>
                        <form method="POST" action="{{ route('auth.google.callback') }}" class="space-y-2">
                            @csrf
                            <div class="flex gap-2">
                                <input type="email"
                                       name="email"
                                       value="student.sample@student.usm.edu.ph"
                                       placeholder="Institutional email"
                                       class="flex-1 py-2 px-3 bg-white border border-amber-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-1 focus:ring-amber-500"
                                       required>
                                <button type="submit"
                                        class="py-2 px-3.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold shrink-0 transition-colors cursor-pointer">
                                    Simulate Login
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- Information Badges -->
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Secure OAuth 2.0 Domain Verification
                    </span>
                    <span>No separate password required</span>
                </div>
            </div>

            <!-- 3. TRACK: LOCAL RESIDENT ONBOARDING (Standard Intake Form) -->
            <div x-show="track === 'resident'"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-5">

                <div class="p-3.5 rounded-2xl bg-amber-50/60 border border-amber-200/60 flex items-start gap-3">
                    <span class="text-base text-amber-700 shrink-0 mt-0.5">📋</span>
                    <p class="text-xs text-amber-900 leading-relaxed">
                        Please provide accurate clinical intake details. Your mobile number will be utilized for instant SMS notifications when prescriptions are ready for dispensary collection.
                    </p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="patient_type" value="resident">

                    <!-- Name Fields (First Name & Last Name in 2 columns) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1.5">
                            <label for="first_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                First Name <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="first_name"
                                id="first_name"
                                value="{{ old('first_name') }}"
                                placeholder="e.g. Maria"
                                class="w-full py-2.5 sm:py-3 px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all shadow-2xs min-h-[44px]"
                                required
                                autocomplete="given-name"
                            />
                        </div>

                        <div class="space-y-1.5">
                            <label for="last_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Last Name <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="last_name"
                                id="last_name"
                                value="{{ old('last_name') }}"
                                placeholder="e.g. Santos"
                                class="w-full py-2.5 sm:py-3 px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all shadow-2xs min-h-[44px]"
                                required
                                autocomplete="family-name"
                            />
                        </div>
                    </div>

                    <!-- Mobile Phone (Essential for Prescription & Dispensation SMS) -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="contact_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Mobile Phone Number <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60">
                                SMS Alerts Enabled
                            </span>
                        </div>
                        <div class="relative flex items-center">
                            <div class="absolute left-3.5 text-slate-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <input
                                type="tel"
                                name="contact_number"
                                id="contact_number"
                                value="{{ old('contact_number') }}"
                                placeholder="e.g. 0917 123 4567"
                                class="w-full pl-10 pr-4 py-2.5 sm:py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all shadow-2xs min-h-[44px]"
                                required
                                autocomplete="tel"
                            />
                        </div>
                        <p class="text-[11px] text-slate-400">Used strictly for prescription readiness notices and clinic dispensations.</p>
                    </div>

                    <!-- Complete Barangay / Address -->
                    <div class="space-y-1.5">
                        <label for="address" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Complete Barangay / Address <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <div class="absolute left-3.5 text-slate-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <input
                                type="text"
                                name="address"
                                id="address"
                                value="{{ old('address') }}"
                                placeholder="e.g. Purok 4, Poblacion, Kabacan, Cotabato"
                                class="w-full pl-10 pr-4 py-2.5 sm:py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all shadow-2xs min-h-[44px]"
                                required
                                autocomplete="street-address"
                            />
                        </div>
                    </div>

                    <!-- Date of Birth & Sex (Demographic Intake) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <!-- Date of Birth -->
                        <div class="space-y-1.5">
                            <label for="date_of_birth" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Date of Birth <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="date"
                                name="date_of_birth"
                                id="date_of_birth"
                                value="{{ old('date_of_birth') }}"
                                class="w-full py-2.5 sm:py-3 px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all shadow-2xs min-h-[44px]"
                                required
                            />
                        </div>

                        <!-- Sex -->
                        <div class="space-y-1.5">
                            <label for="sex" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Sex <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <select
                                    name="sex"
                                    id="sex"
                                    class="w-full py-2.5 sm:py-3 pl-3.5 pr-8 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all shadow-2xs appearance-none cursor-pointer min-h-[44px]"
                                    required>
                                    <option value="" disabled {{ old('sex') ? '' : 'selected' }}>Select biological sex</option>
                                    <option value="Male" {{ old('sex') === 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('sex') === 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('sex') === 'Other' ? 'selected' : '' }}>Other / Prefer not to say</option>
                                </select>
                                <div class="absolute right-3.5 text-slate-400 pointer-events-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Email Address <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <div class="absolute left-3.5 text-slate-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email') }}"
                                placeholder="e.g. maria.santos@gmail.com"
                                class="w-full pl-10 pr-4 py-2.5 sm:py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all shadow-2xs min-h-[44px]"
                                required
                                autocomplete="email"
                            />
                        </div>
                    </div>

                    <!-- Password & Confirm Password Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <!-- Password -->
                        <div class="space-y-1.5">
                            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Password <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    placeholder="Min 8 chars"
                                    class="w-full pl-3.5 pr-9 py-2.5 sm:py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all shadow-2xs min-h-[44px]"
                                    required
                                    autocomplete="new-password"
                                />
                                <button type="button"
                                        onclick="togglePasswordVisibility('password', 'pwdEye', 'pwdEyeOff')"
                                        class="absolute right-2 text-slate-400 hover:text-slate-700 p-1.5 rounded-lg transition-colors cursor-pointer"
                                        title="Toggle password">
                                    <svg id="pwdEye" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg id="pwdEyeOff" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="space-y-1.5">
                            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Confirm Password <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    placeholder="Re-type password"
                                    class="w-full pl-3.5 pr-9 py-2.5 sm:py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all shadow-2xs min-h-[44px]"
                                    required
                                    autocomplete="new-password"
                                />
                                <button type="button"
                                        onclick="togglePasswordVisibility('password_confirmation', 'confEye', 'confEyeOff')"
                                        class="absolute right-2 text-slate-400 hover:text-slate-700 p-1.5 rounded-lg transition-colors cursor-pointer"
                                        title="Toggle password">
                                    <svg id="confEye" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg id="confEyeOff" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Clinical Terms Agreement -->
                    <div class="pt-1">
                        <label class="flex items-start gap-2.5 text-xs text-slate-600 cursor-pointer select-none">
                            <input type="checkbox"
                                   required
                                   class="w-4 h-4 mt-0.5 rounded text-emerald-700 focus:ring-emerald-600 border-slate-300">
                            <span>
                                I certify that the information entered is accurate, and I consent to health records storage under <span class="font-semibold text-emerald-800">USM Clinic Governance</span>.
                            </span>
                        </label>
                    </div>

                    <!-- Submit Register Button -->
                    <button type="submit"
                            class="w-full py-3.5 px-4 bg-gradient-to-r from-emerald-800 to-emerald-700 hover:from-emerald-900 hover:to-emerald-800 active:bg-emerald-950 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-emerald-900/20 hover:shadow-lg transition-all flex items-center justify-center gap-2 transform active:scale-98 cursor-pointer min-h-[48px] mt-2">
                        <span>Complete Resident Registration</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Card Bottom: Link to Login -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500">Already have an account?</span>
                <a href="{{ route('login') }}"
                   class="font-bold text-emerald-800 hover:text-emerald-950 hover:underline inline-flex items-center gap-1">
                    <span>Sign In to Portal</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="w-full max-w-5xl mx-auto px-4 sm:px-6 py-6 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} University of Southern Mindanao. University Health Services Clinic &bull; All rights reserved.
    </footer>

    <!-- Password Visibility Toggle Script -->
    <script>
        function togglePasswordVisibility(inputId, eyeId, eyeOffId) {
            const input = document.getElementById(inputId);
            const eye = document.getElementById(eyeId);
            const eyeOff = document.getElementById(eyeOffId);
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            eye.style.display = isPassword ? 'none' : 'block';
            eyeOff.style.display = isPassword ? 'block' : 'none';
        }
    </script>

</body>
</html>
