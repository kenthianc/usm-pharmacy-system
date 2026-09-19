<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In &mdash; USM Hospital Pharmacy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --g9: #14532d;
            --g8: #166534;
            --g7: #15803d;
            --g6: #16a34a;
            --g1: #dcfce7;
            --y5: #eab308;
            --y4: #facc15;
            --y1: #fef9c3;
            --font: 'Inter', system-ui, -apple-system, sans-serif;
        }
        body {
            font-family: var(--font);
            background-color: var(--g9);
            color: #111827;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1rem;
            -webkit-font-smoothing: antialiased;
        }

        .login-wrapper {
            width: 100%;
            max-width: 24rem; /* 384px */
        }

        /* ── LOGO & HEADER ── */
        .header-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 2rem;
            text-align: center;
        }
        .logo-box {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .25);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            box-shadow: 0 8px 24px rgba(0,0,0,.2);
        }
        .logo-box svg {
            width: 38px;
            height: 38px;
        }
        .brand-title {
            color: #ffffff;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .brand-subtitle {
            color: #86efac;
            font-size: .875rem;
            margin-top: .25rem;
        }

        /* ── CARD ── */
        .login-card {
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            overflow: hidden;
        }
        .card-header-bar {
            background: var(--g8);
            border-bottom: 4px solid var(--y4);
            padding: 1rem 1.5rem;
        }
        .card-header-bar h2 {
            color: #ffffff;
            font-size: .875rem;
            font-weight: 600;
        }
        .card-header-bar p {
            color: #86efac;
            font-size: .75rem;
            margin-top: 2px;
        }

        .card-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        /* Error Alert */
        .alert-error {
            display: flex;
            align-items: flex-start;
            gap: .5rem;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            font-size: .875rem;
            border-radius: .5rem;
            padding: .625rem .75rem;
        }
        .alert-error svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        /* Form Controls */
        .form-group {
            display: flex;
            flex-direction: column;
            gap: .375rem;
        }
        .form-label {
            font-size: .875rem;
            font-weight: 500;
            color: #374151;
        }
        .input-relative {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-icon {
            position: absolute;
            left: .75rem;
            width: 16px;
            height: 16px;
            color: #9ca3af;
            pointer-events: none;
        }
        .form-input {
            width: 100%;
            padding: .625rem .75rem .625rem 2.25rem;
            border: 1px solid #d1d5db;
            border-radius: .5rem;
            font-size: .875rem;
            color: #111827;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            font-family: inherit;
        }
        .form-input:focus {
            border-color: var(--g6);
            box-shadow: 0 0 0 3px rgba(22, 163, 74, .2);
        }
        .btn-toggle-pw {
            position: absolute;
            right: .75rem;
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2px;
        }
        .btn-toggle-pw:hover { color: #4b5563; }
        .btn-toggle-pw svg { width: 16px; height: 16px; }

        .btn-submit {
            width: 100%;
            background: var(--g8);
            color: #ffffff;
            font-weight: 600;
            font-size: .875rem;
            padding: .625rem;
            border-radius: .5rem;
            border: none;
            border-bottom: 2px solid var(--y4);
            cursor: pointer;
            transition: background .15s, transform .1s;
            margin-top: .25rem;
            font-family: inherit;
        }
        .btn-submit:hover {
            background: var(--g7);
        }
        .btn-submit:active {
            transform: translateY(1px);
        }

        .register-link-text {
            text-align: center;
            font-size: .875rem;
            color: #6b7280;
        }
        .btn-register-trigger {
            background: none;
            border: none;
            color: var(--g7);
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            font-size: inherit;
        }
        .btn-register-trigger:hover {
            text-decoration: underline;
        }

        /* Demo Accounts */
        .demo-section {
            padding-top: .5rem;
            border-top: 1px solid #f3f4f6;
        }
        .demo-heading {
            font-size: .75rem;
            color: #9ca3af;
            margin-bottom: .5rem;
        }
        .demo-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .375rem;
        }
        .demo-btn {
            text-align: left;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: .5rem;
            padding: .5rem .75rem;
            cursor: pointer;
            transition: all .15s;
            font-family: inherit;
        }
        .demo-btn:hover {
            background: var(--g0);
            border-color: #86efac;
        }
        .demo-label {
            font-size: .75rem;
            font-weight: 600;
            color: var(--g8);
        }
        .demo-pw {
            font-size: 10px;
            color: #9ca3af;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            margin-top: 2px;
        }

        /* ── FOOTER NOTE ── */
        .footer-note {
            text-align: center;
            color: #4ade80;
            font-size: .75rem;
            margin-top: 1.5rem;
        }

        /* ── REGISTRATION MODAL ── */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .55);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            z-index: 50;
        }
        .modal-card {
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, .35);
            width: 100%;
            max-width: 24rem;
            overflow: hidden;
            animation: modalFadeIn .2s ease-out;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(.96); }
            to { opacity: 1; transform: scale(1); }
        }
        .modal-header-bar {
            background: var(--g8);
            border-bottom: 4px solid var(--y4);
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-header-bar h3 {
            color: #ffffff;
            font-size: .875rem;
            font-weight: 600;
        }
        .modal-header-bar p {
            color: #86efac;
            font-size: .75rem;
            margin-top: 2px;
        }
        .btn-modal-close {
            background: none;
            border: none;
            color: #86efac;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color .15s;
        }
        .btn-modal-close:hover { color: #ffffff; }
        .btn-modal-close svg { width: 18px; height: 18px; }

        .modal-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }
        .modal-body .form-input, .modal-body select {
            padding: .625rem .75rem;
        }
        .modal-footer-note {
            font-size: .75rem;
            color: #9ca3af;
            text-align: center;
            margin-top: .25rem;
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    {{-- LOGO & HEADER --}}
    <div class="header-brand">
        <a href="{{ route('home') }}" style="text-decoration: none;">
            <div class="logo-box">
                <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 3L4 8v8c0 6.627 5.373 12 12 12s12-5.373 12-12V8L16 3z" stroke="#facc15" stroke-width="2.2" stroke-linejoin="round"/>
                    <path d="M11 16l3 3 7-7" stroke="#facc15" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </a>
        <h1 class="brand-title">USM Hospital Pharmacy</h1>
        <p class="brand-subtitle">Management System</p>
    </div>

    {{-- LOGIN CARD --}}
    <div class="login-card">
        <div class="card-header-bar">
            <h2>Sign in to your account</h2>
            <p>Use your USM institutional email</p>
        </div>

        <div class="card-body">

            {{-- ERROR BANNER --}}
            @if ($errors->any())
                <div class="alert-error" id="errorBanner">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @else
                <div class="alert-error" id="jsErrorBanner" style="display: none;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span id="jsErrorMsg"></span>
                </div>
            @endif

            {{-- LOGIN FORM --}}
            <form method="POST" action="{{ route('login') }}" id="loginForm" style="display: flex; flex-direction: column; gap: 1rem;">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <div class="input-relative">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                        </svg>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            placeholder="yourname@usm.edu.ph"
                            class="form-input"
                            required
                            autocomplete="email"
                            autofocus
                        />
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-relative">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Enter your password"
                            class="form-input"
                            style="padding-right: 2.5rem;"
                            required
                            autocomplete="current-password"
                        />
                        <button type="button" class="btn-toggle-pw" onclick="togglePasswordVisibility()" title="Toggle password visibility">
                            <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg id="eyeOffIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; font-size: .8125rem;">
                    <label style="display: flex; align-items: center; gap: .375rem; color: #4b5563; cursor: pointer;">
                        <input type="checkbox" name="remember" style="accent-color: var(--g7); cursor: pointer;">
                        <span>Remember me</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="color: var(--g7); text-decoration: none; font-weight: 500;">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <button type="submit" class="btn-submit">
                    Sign In
                </button>
            </form>

            <p class="register-link-text">
                No account yet?
                <button type="button" class="btn-register-trigger" onclick="openRegisterModal()">
                    Register
                </button>
            </p>

            {{-- DEMO ACCOUNTS AUTOFILL --}}
            <div class="demo-section">
                <p class="demo-heading">Demo accounts &mdash; click to autofill</p>
                <div class="demo-grid">
                    <button
                        type="button"
                        class="demo-btn"
                        onclick="autofillLogin('nurse001@usm.edu.ph', 'nurse123')"
                    >
                        <p class="demo-label">Nurse</p>
                        <p class="demo-pw">nurse123</p>
                    </button>

                    <button
                        type="button"
                        class="demo-btn"
                        onclick="autofillLogin('pharm001@usm.edu.ph', 'pharm123')"
                    >
                        <p class="demo-label">Pharmacist</p>
                        <p class="demo-pw">pharm123</p>
                    </button>

                    <button
                        type="button"
                        class="demo-btn"
                        onclick="autofillLogin('stock001@usm.edu.ph', 'stock123')"
                    >
                        <p class="demo-label">Stock Keeper</p>
                        <p class="demo-pw">stock123</p>
                    </button>

                    <button
                        type="button"
                        class="demo-btn"
                        onclick="autofillLogin('patient001@student.usm.edu.ph', 'patient123')"
                    >
                        <p class="demo-label">Patient</p>
                        <p class="demo-pw">patient123</p>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <p class="footer-note">
        University of Southern Mindanao &mdash; Authorized Access Only
    </p>

</div>

{{-- REGISTER MODAL (PATIENTS ONLY) --}}
<div class="modal-overlay" id="registerModal" onclick="onModalOverlayClick(event)">
    <div class="modal-card">
        <div class="modal-header-bar">
            <div>
                <h3>Patient Registration</h3>
                <p>Open to all patients</p>
            </div>
            <button type="button" class="btn-modal-close" onclick="closeRegisterModal()" title="Close">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('register') }}" class="modal-body">
            @csrf

            <div class="form-group">
                <label class="form-label" for="reg_name">Full Name</label>
                <input
                    type="text"
                    name="name"
                    id="reg_name"
                    placeholder="e.g. Juan dela Cruz"
                    class="form-input"
                    required
                />
            </div>

            <div class="form-group">
                <label class="form-label" for="reg_patient_type">Patient Type</label>
                <select name="patient_type" id="reg_patient_type" class="form-input" style="background: #ffffff;" required>
                    <option value="">Select patient type</option>
                    <option value="student" selected>Student</option>
                    <option value="faculty">Faculty / Employee</option>
                    <option value="community">Community Member</option>
                    <option value="walkin">Walk-in Patient</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="reg_email">Email Address</label>
                <input
                    type="email"
                    name="email"
                    id="reg_email"
                    placeholder="e.g. yourname@student.usm.edu.ph"
                    class="form-input"
                    required
                />
            </div>

            <div class="form-group">
                <label class="form-label" for="reg_contact">Contact Number</label>
                <input
                    type="tel"
                    name="contact_number"
                    id="reg_contact"
                    placeholder="e.g. 09XX XXX XXXX"
                    class="form-input"
                />
            </div>

            <div class="form-group">
                <label class="form-label" for="reg_password">Password</label>
                <input
                    type="password"
                    name="password"
                    id="reg_password"
                    placeholder="Create a password"
                    class="form-input"
                    required
                />
            </div>

            <button type="submit" class="btn-submit" style="margin-top: .5rem;">
                Create Account
            </button>

            <p class="modal-footer-note">
                A confirmation will be sent to your email after registration.
            </p>
        </form>
    </div>
</div>

<script>
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    const eyeOffIcon = document.getElementById('eyeOffIcon');
    const registerModal = document.getElementById('registerModal');
    const jsErrorBanner = document.getElementById('jsErrorBanner');
    const jsErrorMsg = document.getElementById('jsErrorMsg');

    function togglePasswordVisibility() {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        eyeIcon.style.display = isPassword ? 'none' : 'block';
        eyeOffIcon.style.display = isPassword ? 'block' : 'none';
    }

    function autofillLogin(email, password) {
        emailInput.value = email;
        passwordInput.value = password;

        if (jsErrorBanner) jsErrorBanner.style.display = 'none';
        const serverError = document.getElementById('errorBanner');
        if (serverError) serverError.style.display = 'none';

        // Add subtle flash effect to fields
        emailInput.style.backgroundColor = 'var(--g0)';
        passwordInput.style.backgroundColor = 'var(--g0)';
        setTimeout(() => {
            emailInput.style.backgroundColor = '';
            passwordInput.style.backgroundColor = '';
        }, 400);
    }

    function openRegisterModal() {
        registerModal.style.display = 'flex';
    }

    function closeRegisterModal() {
        registerModal.style.display = 'none';
    }

    function onModalOverlayClick(e) {
        if (e.target === registerModal) {
            closeRegisterModal();
        }
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && registerModal.style.display === 'flex') {
            closeRegisterModal();
        }
    });
</script>

</body>
</html>
