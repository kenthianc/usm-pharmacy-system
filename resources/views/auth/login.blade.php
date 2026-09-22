<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In &mdash; USM Hospital &amp; Health Services Pharmacy</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full font-sans antialiased text-slate-900 bg-slate-100 selection:bg-yellow-400 selection:text-green-950">

<div class="min-h-screen flex flex-col lg:flex-row">

    <!-- ── LEFT HERO SHOWCASE PANEL (55% on Desktop) ─────────────────── -->
    <div class="relative lg:w-[52%] xl:w-[55%] text-white p-8 sm:p-12 lg:p-14 flex flex-col justify-between overflow-hidden shrink-0"
         style="background: linear-gradient(145deg, rgba(2, 32, 17, 0.88) 0%, rgba(6, 78, 43, 0.82) 45%, rgba(10, 95, 53, 0.88) 100%), url('{{ asset('images/auth-mesh-bg.png') }}') center/cover no-repeat !important; color: #ffffff !important;">
        
        <!-- Ambient Glowing Orbs & Background Decoration -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-10 right-0 w-[30rem] h-[30rem] bg-emerald-400/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(255,255,255,0.06)_0%,_transparent_60%)] pointer-events-none"></div>
        <div class="absolute inset-0 opacity-[0.04] bg-[linear-gradient(to_right,#ffffff_1px,transparent_1px),linear-gradient(to_bottom,#ffffff_1px,transparent_1px)] bg-[size:32px_32px] pointer-events-none"></div>

        <!-- Left Top: USM Official Branding Header -->
        <div class="relative z-10">
            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3.5 group">
                    <div class="w-11 h-11 rounded-2xl bg-yellow-400 text-green-950 flex items-center justify-center font-black text-2xl shadow-lg shadow-yellow-500/20 border-2 border-yellow-300 transform group-hover:scale-105 transition-transform duration-200 shrink-0">
                        ℞
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-black tracking-tight text-white leading-none">USM Pharmacy</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-yellow-400 text-green-950 shadow-xs">
                                Hospital System
                            </span>
                        </div>
                        <p class="text-xs font-semibold text-emerald-200/90 mt-1">University of Southern Mindanao</p>
                    </div>
                </a>

                <!-- Campus Live Pulse Status -->
                <div class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-950/70 border border-emerald-500/40 text-emerald-200 text-xs font-medium backdrop-blur-md shadow-xs">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                    </span>
                    <span>Kabacan Campus Active</span>
                </div>
            </div>
        </div>

        <!-- Left Middle: Compelling Value Proposition & Feature Cards -->
        <div class="relative z-10 my-10 sm:my-14 space-y-8">
            <div class="space-y-4 max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-yellow-400/15 border border-yellow-400/30 text-yellow-300 text-xs font-bold uppercase tracking-wider">
                    <span>Clinical Operations &amp; Intelligence Hub</span>
                </div>
                <h1 class="text-3xl sm:text-4xl xl:text-5xl font-extrabold tracking-tight text-white leading-[1.15]">
                    Advancing Campus <span class="text-yellow-400">Patient Care</span> &amp; Smart Pharmacy Operations
                </h1>
                <p class="text-sm sm:text-base text-emerald-100/90 leading-relaxed font-normal">
                    The integrated medical dispensary platform connecting triage nurses, outpatient doctors, hospital pharmacists, and warehouse inventory controllers in real time.
                </p>
            </div>

            <!-- 4 Grid Feature Highlights -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 max-w-2xl">
                <!-- Feature 1 -->
                <div class="p-4 rounded-2xl transition-colors backdrop-blur-md flex items-start gap-3.5"
                     style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.16);">
                    <div class="w-9 h-9 rounded-xl bg-amber-400/20 text-amber-300 flex items-center justify-center font-bold text-base shrink-0 border border-amber-400/30">
                        ⚡
                    </div>
                    <div>
                        <h2 class="text-xs font-bold text-white tracking-wide">Dual-Risk Engine</h2>
                        <p class="text-[11px] text-emerald-100/80 mt-0.5 leading-snug">Predictive stockout horizon &amp; batch expiration vulnerability alarms.</p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="p-4 rounded-2xl transition-colors backdrop-blur-md flex items-start gap-3.5"
                     style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.16);">
                    <div class="w-9 h-9 rounded-xl bg-emerald-400/20 text-emerald-300 flex items-center justify-center font-bold text-base shrink-0 border border-emerald-400/30">
                        📋
                    </div>
                    <div>
                        <h2 class="text-xs font-bold text-white tracking-wide">Digital Rx Triage</h2>
                        <p class="text-[11px] text-emerald-100/80 mt-0.5 leading-snug">Doctor and nurse prescription encoding with instantaneous dispensary routing.</p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="p-4 rounded-2xl transition-colors backdrop-blur-md flex items-start gap-3.5"
                     style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.16);">
                    <div class="w-9 h-9 rounded-xl bg-yellow-400/20 text-yellow-300 flex items-center justify-center font-bold text-base shrink-0 border border-yellow-400/30">
                        💊
                    </div>
                    <div>
                        <h2 class="text-xs font-bold text-white tracking-wide">Dispensary POS</h2>
                        <p class="text-[11px] text-emerald-100/80 mt-0.5 leading-snug">Rapid barcode checkout with built-in student, employee, &amp; senior benefits.</p>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="p-4 rounded-2xl transition-colors backdrop-blur-md flex items-start gap-3.5"
                     style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.16);">
                    <div class="w-9 h-9 rounded-xl bg-teal-400/20 text-teal-300 flex items-center justify-center font-bold text-base shrink-0 border border-teal-400/30">
                        🛡️
                    </div>
                    <div>
                        <h2 class="text-xs font-bold text-white tracking-wide">Regulatory Governance</h2>
                        <p class="text-[11px] text-emerald-100/80 mt-0.5 leading-snug">Complete audit trail logging and role-segregated clinical permissions.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Left Bottom: Institutional Trust Statement -->
        <div class="relative z-10 pt-6 border-t border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs text-emerald-200/75">
            <p>University Health Services Clinic &bull; Kabacan, North Cotabato</p>
            <span class="font-medium text-amber-300">Authorized Personnel &amp; Patients Only</span>
        </div>
    </div>

    <!-- ── RIGHT AUTHENTICATION CARD PANEL (45% on Desktop) ────────── -->
    <div class="flex-1 bg-white flex flex-col justify-between p-6 sm:p-10 lg:p-12 xl:p-14 overflow-y-auto">

        <!-- Right Top Navigation: Back to Home -->
        <div class="flex items-center justify-between pb-6 sm:pb-8">
            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-emerald-800 transition-colors group">
                <svg class="w-4 h-4 transform group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Back to Storefront</span>
            </a>

            <div class="flex items-center gap-1.5 text-xs text-slate-400 font-semibold">
                <span>USM Portal</span>
                <span>&bull;</span>
                <span class="text-emerald-700 font-bold">Secure SSL</span>
            </div>
        </div>

        <!-- Right Middle: Login Form Card Body -->
        <div class="max-w-md w-full mx-auto my-auto space-y-6">
            
            <!-- Greeting & Header -->
            <div class="space-y-1.5">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Welcome back
                </h2>
                <p class="text-xs sm:text-sm text-slate-500">
                    Sign in to access your role-specific clinical or patient dashboard.
                </p>
            </div>

            <!-- Error Banner -->
            @if ($errors->any())
                <div id="errorBanner" class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="space-y-0.5">
                        <p class="font-bold">Authentication failed</p>
                        <p class="text-rose-700/90">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            <!-- Session Flash Status -->
            @if (session('status'))
                <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                    <span class="text-emerald-600 font-bold text-sm">&check;</span>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email Input -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider" for="email">
                        Institutional Email
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
                            placeholder="yourname@usm.edu.ph"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all shadow-2xs"
                            required
                            autocomplete="username"
                            autofocus
                        />
                    </div>
                </div>

                <!-- Password Input -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider" for="password">
                            Password
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 hover:underline">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    <div class="relative flex items-center">
                        <div class="absolute left-3.5 text-slate-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" stroke-width="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" stroke-width="2"/>
                            </svg>
                        </div>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Enter your account password"
                            class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition-all shadow-2xs"
                            required
                            autocomplete="current-password"
                        />
                        <button type="button"
                                onclick="togglePasswordVisibility()"
                                class="absolute right-3 text-slate-400 hover:text-slate-700 p-1 rounded-md transition-colors"
                                title="Toggle password visibility">
                            <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eyeOffIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 cursor-pointer select-none">
                        <input type="checkbox"
                               name="remember"
                               class="w-4 h-4 rounded text-emerald-700 focus:ring-emerald-600 border-slate-300">
                        <span>Remember my login</span>
                    </label>
                </div>

                <!-- Submit Sign In Button -->
                <button type="submit"
                        class="w-full py-3 px-4 bg-gradient-to-r from-emerald-800 to-emerald-700 hover:from-emerald-900 hover:to-emerald-800 active:bg-emerald-950 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-emerald-900/20 hover:shadow-lg transition-all flex items-center justify-center gap-2 transform active:scale-98 cursor-pointer mt-2">
                    <span>Sign In to Portal</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>

            <!-- Patient Registration Link -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between text-xs">
                <div>
                    <span class="font-medium text-slate-600">New patient or student?</span>
                    <p class="text-[11px] text-slate-400">Create an outpatient medical profile</p>
                </div>
                <a href="{{ route('register') }}"
                   class="px-3.5 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-900 font-bold rounded-lg transition-colors cursor-pointer shrink-0 inline-flex items-center gap-1">
                    <span>Register Now</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
            </div>

            <!-- ── 1-CLICK DEMO CREDENTIALS AUTOFILL ── -->
            <div class="pt-4 border-t border-slate-200/80 space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Demo Accounts &bull; Click to Autofill
                    </span>
                    <span class="text-[10px] text-amber-600 font-bold bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                        1-Click Access
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <!-- Admin (Full Width Accent) -->
                    <button type="button"
                            onclick="autofillLogin('admin@usm.edu.ph', 'admin123')"
                            class="col-span-2 p-2.5 rounded-xl bg-amber-50/80 hover:bg-amber-100/80 border border-amber-300 text-left transition-all hover:shadow-xs group cursor-pointer flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="text-base">👑</span>
                            <div>
                                <div class="text-xs font-bold text-amber-950 group-hover:text-amber-900">Administrator</div>
                                <div class="text-[10px] text-amber-800/80 font-mono">admin@usm.edu.ph</div>
                            </div>
                        </div>
                        <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-md bg-yellow-400 text-green-950 shadow-2xs">
                            Admin Portal
                        </span>
                    </button>

                    <!-- Nurse -->
                    <button type="button"
                            onclick="autofillLogin('nurse001@usm.edu.ph', 'nurse123')"
                            class="p-2.5 rounded-xl bg-slate-50 hover:bg-emerald-50 hover:border-emerald-300 border border-slate-200 text-left transition-all cursor-pointer group">
                        <div class="flex items-center gap-1.5">
                            <span>🩺</span>
                            <span class="text-xs font-bold text-slate-800 group-hover:text-emerald-900">Nurse</span>
                        </div>
                        <div class="text-[10px] text-slate-500 font-mono truncate mt-0.5">nurse001@usm.edu.ph</div>
                    </button>

                    <!-- Pharmacist -->
                    <button type="button"
                            onclick="autofillLogin('pharm001@usm.edu.ph', 'pharm123')"
                            class="p-2.5 rounded-xl bg-slate-50 hover:bg-emerald-50 hover:border-emerald-300 border border-slate-200 text-left transition-all cursor-pointer group">
                        <div class="flex items-center gap-1.5">
                            <span>💊</span>
                            <span class="text-xs font-bold text-slate-800 group-hover:text-emerald-900">Pharmacist</span>
                        </div>
                        <div class="text-[10px] text-slate-500 font-mono truncate mt-0.5">pharm001@usm.edu.ph</div>
                    </button>

                    <!-- Stock Keeper -->
                    <button type="button"
                            onclick="autofillLogin('stock001@usm.edu.ph', 'stock123')"
                            class="p-2.5 rounded-xl bg-slate-50 hover:bg-emerald-50 hover:border-emerald-300 border border-slate-200 text-left transition-all cursor-pointer group">
                        <div class="flex items-center gap-1.5">
                            <span>📦</span>
                            <span class="text-xs font-bold text-slate-800 group-hover:text-emerald-900">Stock Keeper</span>
                        </div>
                        <div class="text-[10px] text-slate-500 font-mono truncate mt-0.5">stock001@usm.edu.ph</div>
                    </button>

                    <!-- Patient -->
                    <button type="button"
                            onclick="autofillLogin('patient001@student.usm.edu.ph', 'patient123')"
                            class="p-2.5 rounded-xl bg-slate-50 hover:bg-emerald-50 hover:border-emerald-300 border border-slate-200 text-left transition-all cursor-pointer group">
                        <div class="flex items-center gap-1.5">
                            <span>🧑‍🎓</span>
                            <span class="text-xs font-bold text-slate-800 group-hover:text-emerald-900">Patient</span>
                        </div>
                        <div class="text-[10px] text-slate-500 font-mono truncate mt-0.5">patient001@...</div>
                    </button>
                </div>
            </div>

        </div>

        <!-- Right Bottom: Footer Copyright -->
        <div class="pt-8 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} University of Southern Mindanao. All rights reserved.
        </div>
    </div>

</div>

<script>
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    const eyeOffIcon = document.getElementById('eyeOffIcon');

    function togglePasswordVisibility() {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        eyeIcon.style.display = isPassword ? 'none' : 'block';
        eyeOffIcon.style.display = isPassword ? 'block' : 'none';
    }

    function autofillLogin(email, password) {
        emailInput.value = email;
        passwordInput.value = password;

        const serverError = document.getElementById('errorBanner');
        if (serverError) serverError.style.display = 'none';

        // Flash highlight animation
        emailInput.classList.add('ring-2', 'ring-amber-400', 'bg-amber-50');
        passwordInput.classList.add('ring-2', 'ring-amber-400', 'bg-amber-50');
        setTimeout(() => {
            emailInput.classList.remove('ring-2', 'ring-amber-400', 'bg-amber-50');
            passwordInput.classList.remove('ring-2', 'ring-amber-400', 'bg-amber-50');
        }, 500);
    }
</script>

</body>
</html>
