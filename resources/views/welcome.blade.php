<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>USM Pharmacy — Your Campus Health Partner</title>
    <meta name="description" content="AI-powered pharmacy assistant for USM students and staff. Check medicine availability, track prescriptions, and get health guidance.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lora:ital,wght@0,600;1,600;1,700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --navy:  #0f2d52;
            --teal:  #0d9488;
            --teal-l:#e0f7f5;
            --gold:  #f0c84a;
            --gold-d:#d4a800;
            --warm:  #faf8f3;
            --white: #ffffff;
            --g100:  #f4f4f4;
            --g200:  #e5e7eb;
            --g300:  #d1d5db;
            --g400:  #9ca3af;
            --g500:  #6b7280;
            --g700:  #374151;
            --g900:  #111827;
            --green: #16a34a;
            --amber: #d97706;
            --red:   #dc2626;
        }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background: var(--warm); color: var(--g900); -webkit-font-smoothing: antialiased; }

        /* ─── BANNER ────────────────────────────────── */
        .top-banner {
            background: var(--gold);
            padding: .55rem 1.5rem;
            text-align: center;
            font-size: .8rem; font-weight: 600; color: var(--navy);
            letter-spacing: .01em;
        }
        .top-banner a { color: var(--navy); text-decoration: underline; font-weight: 700; }

        /* ─── NAVBAR ────────────────────────────────── */
        .nav {
            position: sticky; top: 0; z-index: 100;
            background: rgba(250,248,243,.93);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(0,0,0,.07);
        }
        .nav-inner {
            max-width: 1100px; margin: 0 auto; padding: 0 1.5rem;
            height: 62px; display: flex; align-items: center; justify-content: space-between; gap: 1.5rem;
        }
        .logo { display: flex; align-items: center; gap: 9px; text-decoration: none; }
        .logo-mark {
            width: 36px; height: 36px; border-radius: 10px;
            background: var(--navy); display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .logo-mark svg { width: 19px; height: 19px; stroke: white; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
        .logo-text { font-size: .9rem; font-weight: 700; color: var(--navy); line-height: 1.15; }
        .logo-sub  { font-size: .67rem; color: var(--g400); font-weight: 400; }
        .nav-links { display: flex; align-items: center; gap: .25rem; }
        .nav-link {
            padding: .45rem .9rem; border-radius: 8px;
            font-size: .875rem; font-weight: 500; color: var(--g700);
            text-decoration: none; transition: background .15s;
            border: none; background: none; cursor: pointer; font-family: inherit;
        }
        .nav-link:hover { background: rgba(0,0,0,.05); }
        .nav-right { display: flex; align-items: center; gap: .5rem; }
        .btn-login {
            padding: .45rem .9rem; border-radius: 8px;
            font-size: .875rem; font-weight: 500; color: var(--g700);
            text-decoration: none; transition: background .15s;
        }
        .btn-login:hover { background: rgba(0,0,0,.05); }
        .btn-signup {
            padding: .5rem 1.15rem; border-radius: 8px;
            font-size: .875rem; font-weight: 700;
            background: var(--navy); color: white;
            text-decoration: none; border: none; cursor: pointer;
            transition: background .15s, transform .1s;
        }
        .btn-signup:hover { background: #163d6e; transform: translateY(-1px); }

        /* ─── HERO ──────────────────────────────────── */
        .hero {
            padding: 4.5rem 1.5rem 0;
            text-align: center;
            max-width: 1100px; margin: 0 auto;
        }
        .hero-h1 {
            font-family: 'Lora', serif;
            font-size: clamp(2.6rem, 6vw, 4.2rem);
            font-weight: 600; line-height: 1.12;
            color: var(--g900); letter-spacing: -.02em;
            margin-bottom: 1.25rem;
        }
        .hero-h1 em { font-style: italic; color: var(--teal); }
        .hero-sub {
            font-size: clamp(.95rem, 2vw, 1.1rem);
            color: var(--g500); max-width: 480px;
            margin: 0 auto 2rem; line-height: 1.7;
        }
        .hero-btns { display: flex; gap: .875rem; justify-content: center; flex-wrap: wrap; margin-bottom: 3.5rem; }
        .btn-cta-dark {
            padding: .8rem 1.75rem; border-radius: 10px;
            font-size: .95rem; font-weight: 700; text-decoration: none;
            background: var(--navy); color: white;
            box-shadow: 0 4px 14px rgba(15,45,82,.3);
            transition: transform .15s, box-shadow .15s, background .15s;
        }
        .btn-cta-dark:hover { background: #163d6e; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(15,45,82,.3); }
        .btn-cta-gold {
            padding: .8rem 1.75rem; border-radius: 10px;
            font-size: .95rem; font-weight: 700; text-decoration: none;
            background: var(--gold); color: var(--navy);
            transition: transform .15s, box-shadow .15s;
            display: flex; align-items: center; gap: .5rem;
        }
        .btn-cta-gold:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(240,200,74,.4); }
        .btn-cta-gold svg { width: 17px; height: 17px; stroke: var(--navy); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

        /* ─── CHATBOT SECTION ───────────────────────── */
        .chat-section {
            background: radial-gradient(circle at 50% 120%, rgba(254, 240, 138, 0.35) 0%, transparent 60%), linear-gradient(175deg, #e3f2fb 0%, #cbe5f6 100%);
            border-radius: 32px 32px 0 0;
            padding: 3.5rem 1.5rem 4.5rem;
            display: flex; flex-direction: column; align-items: center; gap: 1rem;
            position: relative; overflow: hidden;
        }
        .chat-section::before {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(ellipse 70% 50% at 50% 0%, rgba(255,255,255,.6) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Messages thread - Claude / Gemini style */
        .chat-thread-wrap {
            width: 100%; max-width: 740px; position: relative; z-index: 10;
            display: none; flex-direction: column; gap: 0.75rem;
        }
        .chat-thread-wrap.active { display: flex; }
        .chat-header-bar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.5rem 0.75rem; font-size: 0.8rem; color: #475569;
        }
        .chat-header-title {
            display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #0f2d52;
        }
        .online-dot {
            width: 8px; height: 8px; border-radius: 50%; background: #10b981;
            box-shadow: 0 0 0 2px rgba(16,185,129,.25);
            animation: pulse-dot 2s infinite;
        }
        @keyframes pulse-dot { 0%,100%{opacity:1;} 50%{opacity:.5;} }
        .btn-clear-chat {
            background: rgba(255,255,255,.7); border: 1px solid #cbd5e1;
            padding: 0.25rem 0.65rem; border-radius: 8px; font-size: 0.75rem;
            font-weight: 600; color: #475569; cursor: pointer; transition: all .15s;
        }
        .btn-clear-chat:hover { background: white; color: #0f172a; border-color: #94a3b8; }

        .chat-msgs {
            width: 100%; max-height: 380px; overflow-y: auto;
            display: flex; flex-direction: column; gap: 1rem;
            padding: 0.5rem 0.25rem; scroll-behavior: smooth;
        }
        .chat-msgs::-webkit-scrollbar { width: 5px; }
        .chat-msgs::-webkit-scrollbar-thumb { background: rgba(148,163,184,.5); border-radius: 3px; }

        .msg-row { display: flex; gap: 10px; align-items: flex-start; }
        .msg-row.user { justify-content: flex-end; }
        .msg-avatar {
            width: 32px; height: 32px; border-radius: 10px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem; font-weight: 700;
        }
        .msg-avatar.ai {
            background: #ffffff; color: #0f2d52;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 6px rgba(15,45,82,.08);
        }
        .msg-content-user {
            max-width: 82%; background: #0f2d52; color: #ffffff;
            padding: 0.75rem 1.15rem; border-radius: 18px 18px 4px 18px;
            font-size: 0.92rem; line-height: 1.55;
            box-shadow: 0 3px 10px rgba(15,45,82,.18);
        }
        .msg-content-ai {
            max-width: 88%; background: #ffffff; color: #1e293b;
            padding: 1.1rem 1.35rem; border-radius: 18px 18px 18px 4px;
            font-size: 0.92rem; line-height: 1.65;
            box-shadow: 0 4px 20px rgba(15,45,82,.07);
            border: 1px solid #f1f5f9;
        }
        .stream-cursor {
            display: inline-block; width: 6px; height: 14px;
            background: #0d9488; margin-left: 2px;
            animation: blink 0.8s infinite; vertical-align: middle;
        }
        @keyframes blink { 0%,100%{opacity:1;} 50%{opacity:0;} }

        /* Thinking animation */
        .thinking-bubble {
            display: flex; align-items: center; gap: 6px;
            background: #ffffff; padding: 0.75rem 1.1rem; border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,.06); border: 1px solid #f1f5f9;
            font-size: 0.85rem; color: #64748b; font-weight: 500;
        }
        .thinking-dots { display: inline-flex; gap: 4px; align-items: center; }
        .thinking-dots span {
            width: 6px; height: 6px; border-radius: 50%; background: #0d9488;
            animation: bounce 1.4s ease-in-out infinite;
        }
        .thinking-dots span:nth-child(2) { animation-delay: .2s; }
        .thinking-dots span:nth-child(3) { animation-delay: .4s; }
        @keyframes bounce { 0%,80%,100%{transform:translateY(0)} 40%{transform:translateY(-6px)} }

        /* Real-time stock tags & rich prompt items */
        .med-badge-stock {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 0.75rem; font-weight: 700; padding: 3px 8px;
            border-radius: 6px; text-transform: uppercase; letter-spacing: .02em;
        }
        .stock-in { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .stock-low { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .stock-out { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .med-badge-price {
            display: inline-flex; align-items: center;
            font-size: 0.78rem; font-weight: 700; padding: 3px 8px;
            border-radius: 6px; background: #f1f5f9; color: #0f2d52;
        }
        .ai-action-btn {
            display: inline-flex; align-items: center; gap: 5px;
            margin-top: 0.65rem; padding: 0.4rem 0.85rem; border-radius: 8px;
            font-size: 0.8rem; font-weight: 600; text-decoration: none;
            background: #0f2d52; color: #ffffff; transition: background .15s;
        }
        .ai-action-btn:hover { background: #1a4a7a; }
        .ai-disclaimer {
            margin-top: 0.75rem; padding-top: 0.65rem; border-top: 1px solid #f1f5f9;
            font-size: 0.78rem; color: #64748b; line-height: 1.5; font-style: italic;
        }

        /* Follow up prompt suggestions */
        .follow-ups {
            display: flex; gap: 6px; flex-wrap: wrap; margin-top: 0.85rem;
        }
        .fu-chip {
            background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 8px; padding: 3px 10px; font-size: 0.78rem;
            color: #334155; cursor: pointer; transition: all .15s; font-weight: 500;
        }
        .fu-chip:hover { background: #0f2d52; color: #ffffff; border-color: #0f2d52; }

        /* Heidi input box - pixel-perfect to screenshot */
        .chat-bar-wrap {
            width: 100%; max-width: 740px; position: relative; z-index: 10;
        }
        .chat-bar {
            width: 100%; background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 6px 24px -2px rgba(15,45,82,.08), 0 2px 6px -1px rgba(0,0,0,.04);
            display: flex; align-items: center; gap: 0.85rem;
            padding: 0.75rem 1rem 0.75rem 1.35rem;
            border: 1px solid rgba(0,0,0,.05);
            transition: box-shadow .2s, border-color .2s;
            min-height: 60px;
        }
        .chat-bar:focus-within {
            box-shadow: 0 10px 32px -2px rgba(15,45,82,.14), 0 3px 10px -1px rgba(0,0,0,.06);
            border-color: rgba(13,148,136,.3);
        }
        .chat-bar-icon {
            flex-shrink: 0; display: flex; align-items: center; justify-content: center;
        }
        .chat-bar-icon svg {
            display: block;
        }
        .chat-input {
            flex: 1; border: none; outline: none; background: transparent;
            font-family: inherit; font-size: 1.05rem; color: #1e293b;
            line-height: 1.45; resize: none; min-height: 26px; max-height: 140px;
            overflow-y: auto; padding: 2px 0;
        }
        .chat-input::placeholder { color: #52525b; font-weight: 400; }
        .send-btn {
            flex-shrink: 0; width: 38px; height: 38px;
            background: #d6c8ce; border: none; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            cursor: not-allowed; transition: background .2s, transform .15s, box-shadow .2s;
        }
        .send-btn svg {
            width: 18px; height: 18px; stroke: #ffffff; fill: none;
            stroke-width: 2.6; stroke-linecap: round; stroke-linejoin: round;
        }
        .send-btn.active {
            background: #0f2d52; cursor: pointer;
            box-shadow: 0 4px 12px rgba(15,45,82,.25);
        }
        .send-btn.active:hover {
            background: #1a4a7a; transform: scale(1.05);
        }

        /* 5 Quick chips below input - matching screenshot */
        .chips {
            display: flex; gap: 0.55rem; flex-wrap: wrap; justify-content: center;
            width: 100%; max-width: 740px; position: relative; z-index: 10;
            margin-top: 0.25rem;
        }
        .chip {
            padding: 0.45rem 1.05rem; border-radius: 10px;
            font-size: 0.84rem; font-weight: 500;
            background: #ffffff; color: #374151;
            border: 1px solid #ebebf0; cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,.03);
            transition: all .16s ease; white-space: nowrap;
            display: inline-flex; align-items: center; gap: 0.45rem;
        }
        .chip svg {
            width: 15px; height: 15px; stroke: #4b5563; fill: none;
            stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
        }
        .chip:hover {
            background: #fafafa; border-color: #cbd5e1; color: #111827;
            transform: translateY(-1.5px); box-shadow: 0 4px 10px rgba(0,0,0,.06);
        }
        .chip:active { transform: translateY(0); }

        /* ─── FEATURES ──────────────────────────────── */
        .features { padding: 5rem 1.5rem; background: white; }
        .feat-inner { max-width: 1100px; margin: 0 auto; }
        .s-tag { text-align: center; font-size: .75rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--teal); margin-bottom: .65rem; }
        .s-h { font-family: 'Lora', serif; text-align: center; font-size: clamp(1.6rem,3vw,2.2rem); font-weight: 600; color: var(--g900); letter-spacing: -.02em; margin-bottom: .75rem; }
        .s-h em { font-style: italic; }
        .s-p { text-align: center; color: var(--g500); font-size: .95rem; max-width: 460px; margin: 0 auto 3.5rem; line-height: 1.7; }
        .feat-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(280px,1fr)); gap: 1.25rem; }
        .feat-card {
            border-radius: 16px; border: 1px solid var(--g200);
            padding: 1.75rem; background: var(--warm);
            transition: box-shadow .2s, transform .2s;
        }
        .feat-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,.07); transform: translateY(-3px); }
        .feat-icon {
            width: 46px; height: 46px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; margin-bottom: 1.1rem;
        }
        .feat-icon svg { width: 22px; height: 22px; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
        .fi-teal { background: var(--teal-l); } .fi-teal svg { stroke: var(--teal); }
        .fi-navy { background: #dbeafe; }        .fi-navy svg { stroke: var(--navy); }
        .fi-gold { background: #fef9c3; }        .fi-gold svg { stroke: var(--gold-d); }
        .feat-h { font-size: .95rem; font-weight: 700; color: var(--g900); margin-bottom: .4rem; }
        .feat-p { font-size: .855rem; color: var(--g500); line-height: 1.65; }

        /* ─── STATS ─────────────────────────────────── */
        .stats { background: var(--navy); padding: 4rem 1.5rem; }
        .stats-inner {
            max-width: 1100px; margin: 0 auto;
            display: grid; grid-template-columns: repeat(auto-fit,minmax(160px,1fr));
            gap: 2rem; text-align: center;
        }
        .stat-n { font-size: 2.5rem; font-weight: 800; color: white; }
        .stat-n span { color: var(--gold); }
        .stat-l { font-size: .85rem; color: rgba(255,255,255,.55); margin-top: .35rem; }

        /* ─── CTA ───────────────────────────────────── */
        .cta-wrap { padding: 5rem 1.5rem; background: var(--warm); }
        .cta-box {
            max-width: 1100px; margin: 0 auto;
            background: linear-gradient(130deg, #0f2d52 0%, #1a4a7a 55%, #0d9488 100%);
            border-radius: 24px; padding: 3rem 2.5rem;
            display: flex; align-items: center; justify-content: space-between; gap: 2rem; flex-wrap: wrap;
        }
        .cta-text h2 { font-family: 'Lora', serif; font-size: 1.5rem; font-weight: 600; font-style: italic; color: white; margin-bottom: .45rem; }
        .cta-text p  { font-size: .9rem; color: rgba(255,255,255,.65); max-width: 380px; }
        .cta-btns { display: flex; gap: .75rem; flex-wrap: wrap; }
        .btn-w { padding: .75rem 1.5rem; border-radius: 10px; font-size: .9rem; font-weight: 700; text-decoration: none; background: white; color: var(--navy); transition: transform .15s, box-shadow .15s; }
        .btn-w:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,.15); }
        .btn-ow { padding: .75rem 1.5rem; border-radius: 10px; font-size: .9rem; font-weight: 600; text-decoration: none; color: white; border: 1.5px solid rgba(255,255,255,.3); transition: all .15s; }
        .btn-ow:hover { border-color: white; background: rgba(255,255,255,.08); }

        /* ─── FOOTER ────────────────────────────────── */
        footer { padding: 1.75rem; text-align: center; border-top: 1px solid var(--g200); font-size: .78rem; color: var(--g400); }

        @media(max-width:640px) {
            .hero { padding-top: 3rem; }
            .nav-links { display: none; }
            .cta-box { padding: 2rem 1.5rem; }
        }
    </style>
</head>
<body>

{{-- BANNER --}}
<div class="top-banner">
    ✨ Now with AI-powered symptom checking &mdash; <a href="#chatbot">Try the pharmacy assistant →</a>
</div>

{{-- NAV --}}
<nav class="nav">
    <div class="nav-inner">
        <a href="{{ route('home') }}" class="logo">
            <div class="logo-mark">
                <svg viewBox="0 0 24 24"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
            </div>
            <div>
                <div class="logo-text">USM Pharmacy</div>
                <div class="logo-sub">Health Services</div>
            </div>
        </a>
        <div class="nav-links">
            <a href="{{ route('medicines') }}" class="nav-link">Store</a>
            @auth
                <a href="{{ route('patient.dashboard') }}" class="nav-link">My Dashboard</a>
            @endauth
        </div>
        <div class="nav-right">
            @auth
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="nav-link">Log out</button>
                </form>
                <a href="{{ route('patient.dashboard') }}" class="btn-signup">My Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn-login">Log in</a>
                <a href="{{ route('register') }}" class="btn-signup">Get started free</a>
            @endauth
        </div>
    </div>
</nav>

{{-- HERO --}}
<section class="hero">
    <h1 class="hero-h1"><em>Relief</em> on every visit.<br>Right from your screen.</h1>
    <p class="hero-sub">The AI pharmacy assistant for USM students and staff — check medicine availability, understand symptoms, and track prescriptions.</p>
    <div class="hero-btns">
        @auth
            <a href="{{ route('patient.dashboard') }}" class="btn-cta-dark">Go to my dashboard</a>
            <a href="{{ route('medicines') }}" class="btn-cta-gold">
                <svg viewBox="0 0 24 24"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 2.3A1 1 0 0 0 5 17h12m0 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-8 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/></svg>
                Browse Store
            </a>
        @else
            <a href="{{ route('register') }}" class="btn-cta-dark">Get started — it's free</a>
            <a href="{{ route('medicines') }}" class="btn-cta-gold">
                <svg viewBox="0 0 24 24"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 2.3A1 1 0 0 0 5 17h12m0 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-8 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/></svg>
                Browse Store
            </a>
        @endauth
    </div>
</section>

{{-- CHATBOT SECTION --}}
<div style="max-width:1100px;margin:0 auto;padding:0 1.5rem;">
    <section class="chat-section" id="chatbot">

        {{-- Messages thread - Claude / Gemini style (visible when conversation is active) --}}
        <div class="chat-thread-wrap" id="chatThreadWrap">
            <div class="chat-header-bar">
                <div class="chat-header-title">
                    <span class="online-dot"></span>
                    <span>Heidi &bull; USM Pharmacy AI</span>
                </div>
                <button type="button" class="btn-clear-chat" onclick="resetChat()">
                    ✕ Clear chat
                </button>
            </div>
            <div class="chat-msgs" id="chatMsgs"></div>
        </div>

        {{-- Big Heidi-style input bar --}}
        <div class="chat-bar-wrap">
            <div class="chat-bar">
                <div class="chat-bar-icon" title="Heidi AI">
                    <svg viewBox="0 0 28 20" width="26" height="19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <ellipse cx="9" cy="10" rx="6.5" ry="6.5" stroke="#4b5563" stroke-width="2.2"/>
                        <ellipse cx="19" cy="10" rx="6.5" ry="6.5" stroke="#4b5563" stroke-width="2.2"/>
                    </svg>
                </div>
                <textarea id="chatInput" class="chat-input" rows="1"
                    placeholder="Ask Heidi anything..."
                    oninput="handleInput(this)"
                    onkeydown="handleKeyDown(event)"></textarea>
                <button type="button" class="send-btn" id="sendBtn" onclick="sendMsg()" disabled title="Send prompt">
                    <svg viewBox="0 0 24 24"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
                </button>
            </div>
        </div>

        {{-- 5 Quick chips below bar --}}
        <div class="chips" id="quickChips">
            <button type="button" class="chip" onclick="triggerChip('lookup')">
                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <span>Look up</span>
            </button>
            <button type="button" class="chip" onclick="triggerChip('research')">
                <svg viewBox="0 0 24 24"><path d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 6 6v0a6 6 0 0 0 6-6V4a2 2 0 0 0-2-2h-1a.2.2 0 1 0 .3.3"/><path d="M8 15v1a6 6 0 0 0 6 6v0a6 6 0 0 0 6-6v-4"/><circle cx="20" cy="10" r="2"/></svg>
                <span>Research</span>
            </button>
            <button type="button" class="chip" onclick="triggerChip('treat')">
                <svg viewBox="0 0 24 24"><path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z"/><path d="m8.5 8.5 7 7"/></svg>
                <span>Treat</span>
            </button>
            <button type="button" class="chip" onclick="triggerChip('explain')">
                <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><line x1="8" y1="9" x2="16" y2="9"/><line x1="8" y1="13" x2="14" y2="13"/></svg>
                <span>Explain</span>
            </button>
            <button type="button" class="chip" onclick="triggerChip('compare')">
                <svg viewBox="0 0 24 24"><polyline points="17 4 21 8 17 12"/><line x1="3" y1="8" x2="21" y2="8"/><polyline points="7 20 3 16 7 12"/><line x1="21" y1="16" x2="3" y2="16"/></svg>
                <span>Compare</span>
            </button>
        </div>

    </section>
</div>

{{-- FEATURES --}}
<section class="features">
    <div class="feat-inner">
        <div class="s-tag">What we offer</div>
        <h2 class="s-h">Everything you need,<br><em>in one place</em></h2>
        <p class="s-p">The USM patient portal connects you to pharmacy services, your health records, and AI-powered guidance.</p>
        <div class="feat-grid">
            <div class="feat-card">
                <div class="feat-icon fi-teal"><svg viewBox="0 0 24 24"><path d="M12 2a2 2 0 0 1 2 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 0 1 7 7H3a7 7 0 0 1 7-7h1V5.73A2 2 0 0 1 10 4a2 2 0 0 1 2-2z"/></svg></div>
                <div class="feat-h">AI Pharmacy Assistant</div>
                <p class="feat-p">Describe your symptoms and get instant medicine recommendations with real-time stock availability — powered by AI.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon fi-navy"><svg viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg></div>
                <div class="feat-h">Prescription History</div>
                <p class="feat-p">View all your past prescriptions — prescribing doctor, medicines, dosage instructions, and dispensing status — in one place.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon fi-gold"><svg viewBox="0 0 24 24"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 2.3A1 1 0 0 0 5 17h12m0 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-8 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/></svg></div>
                <div class="feat-h">Medicine Store</div>
                <p class="feat-p">Browse real-time medicine availability at USM Pharmacy before your visit — so you know exactly what's in stock.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon fi-teal"><svg viewBox="0 0 24 24"><path d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7z"/></svg></div>
                <div class="feat-h">Medical Profile</div>
                <p class="feat-p">Keep your allergies, conditions, and contact info up to date so our pharmacists can serve you safely.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon fi-navy"><svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0 1 12 2.944a11.955 11.955 0 0 1-8.618 3.04A12.02 12.02 0 0 0 3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
                <div class="feat-h">Secure & Private</div>
                <p class="feat-p">Your medical records are only accessible to you — protected by your secure USM account.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon fi-gold"><svg viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg></div>
                <div class="feat-h">Track Prescription Status</div>
                <p class="feat-p">Know if your prescription is Pending, At Pharmacy, or Dispensed — without calling or visiting the clinic.</p>
            </div>
        </div>
    </div>
</section>

{{-- STATS --}}
<section class="stats">
    <div class="stats-inner">
        <div><div class="stat-n">{{ $medicines->count() }}<span>+</span></div><div class="stat-l">Medicines Available</div></div>
        <div><div class="stat-n">24<span>/7</span></div><div class="stat-l">AI Assistant Access</div></div>
        <div><div class="stat-n">100<span>%</span></div><div class="stat-l">Secure & Private</div></div>
        <div><div class="stat-n">₱<span>0</span></div><div class="stat-l">Cost to Register</div></div>
    </div>
</section>

{{-- CTA --}}
@guest
<section class="cta-wrap">
    <div class="cta-box">
        <div class="cta-text">
            <h2>Ready to get started?</h2>
            <p>Create your free USM patient account and access your prescriptions, medical history, and AI health assistant.</p>
        </div>
        <div class="cta-btns">
            <a href="{{ route('register') }}" class="btn-w">Create free account</a>
            <a href="{{ route('login') }}" class="btn-ow">Log in</a>
        </div>
    </div>
</section>
@endguest

<footer>
    &copy; {{ date('Y') }} University of Southern Mindanao &mdash; Health Services Pharmacy &nbsp;&bull;&nbsp; All rights reserved
</footer>

<script>
const medicines = @json($medicines);
const threadWrap = document.getElementById('chatThreadWrap');
const msgs = document.getElementById('chatMsgs');
const input = document.getElementById('chatInput');
const sendBtn = document.getElementById('sendBtn');

function handleInput(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 140) + 'px';
    const hasText = el.value.trim().length > 0;
    sendBtn.disabled = !hasText;
    sendBtn.classList.toggle('active', hasText);
}

function handleKeyDown(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMsg();
    }
}

function resetChat() {
    msgs.innerHTML = '';
    threadWrap.classList.remove('active');
    input.value = '';
    handleInput(input);
    input.focus();
}

function triggerChip(type) {
    const existing = input.value.trim();
    let prompt = '';
    if (type === 'lookup') {
        prompt = existing ? `Look up availability and price for ${existing}` : 'Look up Paracetamol 500mg availability and stock in USM Pharmacy';
    } else if (type === 'research') {
        prompt = existing ? `Research clinical precautions and uses for ${existing}` : 'What are the early symptoms, causes, and care for seasonal flu and cough?';
    } else if (type === 'treat') {
        prompt = existing ? `How to treat symptoms using ${existing}?` : 'I have a headache, mild fever, and muscle soreness. What medicine is recommended?';
    } else if (type === 'explain') {
        prompt = existing ? `Explain the dosage instructions and mechanism of ${existing}` : 'Explain how doctor prescription routing and medicine dispensing work at USM Pharmacy';
    } else if (type === 'compare') {
        prompt = existing ? `Compare ${existing} with common alternative medicines` : 'Compare Paracetamol vs Mefenamic Acid: when should I take which?';
    }
    input.value = prompt;
    handleInput(input);
    sendMsg();
}

function askFollowUp(text) {
    input.value = text;
    handleInput(input);
    sendMsg();
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function showThinking() {
    const r = document.createElement('div');
    r.className = 'msg-row';
    r.id = 'thinkingRow';
    r.innerHTML = `
        <div class="msg-avatar ai">
            <svg viewBox="0 0 28 20" width="18" height="13" fill="none"><ellipse cx="9" cy="10" rx="6.5" ry="6.5" stroke="#0f2d52" stroke-width="2.2"/><ellipse cx="19" cy="10" rx="6.5" ry="6.5" stroke="#0f2d52" stroke-width="2.2"/></svg>
        </div>
        <div class="thinking-bubble">
            <div class="thinking-dots"><span></span><span></span><span></span></div>
            <span>Thinking...</span>
        </div>`;
    msgs.appendChild(r);
    msgs.scrollTop = msgs.scrollHeight;
}

function hideThinking() {
    document.getElementById('thinkingRow')?.remove();
}

function buildResponse(query) {
    const lq = query.toLowerCase();
    let matchedMed = null;

    for (const m of medicines) {
        const namePart = m.name.toLowerCase().split(' ')[0];
        const genericPart = (m.generic_name || '').toLowerCase().split(' ')[0];
        if (lq.includes(namePart) || (genericPart && lq.includes(genericPart))) {
            matchedMed = m;
            break;
        }
    }

    if (matchedMed) {
        const m = matchedMed;
        let stockBadge = '';
        if (m.available_stock === 0) {
            stockBadge = `<span class="med-badge-stock stock-out">Out of Stock</span>`;
        } else if (m.available_stock <= 30) {
            stockBadge = `<span class="med-badge-stock stock-low">Low Stock (${m.available_stock} ${m.unit}s)</span>`;
        } else {
            stockBadge = `<span class="med-badge-stock stock-in">In Stock (${m.available_stock} ${m.unit}s)</span>`;
        }
        const priceBadge = `<span class="med-badge-price">₱${Number(m.unit_price).toFixed(2)} / ${m.unit}</span>`;

        let advice = `Here is the current information for <strong>${m.name}</strong> (${m.generic_name || 'Generic'}):<br><br>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:8px;">
            ${stockBadge} ${priceBadge}
        </div>
        &bull; <strong>Dosage &amp; Form:</strong> ${m.dosage_form || 'Standard dosage form'}<br>
        &bull; <strong>Storage &amp; Category:</strong> ${m.category ? m.category.name : 'General Pharmacy'}<br>
        &bull; <strong>Campus Availability:</strong> Available for dispensing at USM Health Services.<br><br>
        <a href="{{ route('medicines') }}" class="ai-action-btn">
            View in Store &rarr;
        </a>
        <div class="ai-disclaimer">⚕️ Note: Prescription medicines require doctor sign-off before dispensing. Always follow physician instructions.</div>`;

        const followUps = [
            `What are the side effects of ${m.name}?`,
            `How often should I take ${m.name}?`,
            `Do I need a prescription for ${m.name}?`
        ];
        return { html: advice, followUps };
    }

    if (/compare|vs|versus/.test(lq) && /paracetamol|mefenamic/.test(lq)) {
        return {
            html: `<strong>Paracetamol vs. Mefenamic Acid: Comparison</strong><br><br>
            &bull; <strong>Paracetamol 500mg:</strong> Best for mild-to-moderate fever and headaches. Gentle on the stomach and safe when taken as directed (max 4,000mg/day).<br>
            &bull; <strong>Mefenamic Acid 500mg:</strong> An NSAID specifically targeted for acute pain and inflammation, such as severe toothaches or dysmenorrhea (menstrual cramps). Should always be taken with food.<br><br>
            <span class="med-badge-stock stock-in">Both In Stock at USM</span>
            <div class="ai-disclaimer">⚕️ Note: Avoid taking multiple NSAIDs simultaneously. Consult USM clinic staff if pain persists.</div>`,
            followUps: ['Paracetamol stock and price', 'Mefenamic Acid price', 'Treat severe migraine']
        };
    }

    if (/headache|fever|lagnat|sakit ng ulo|body ache/.test(lq)) {
        return {
            html: `For fever and headache relief, <strong>Paracetamol 500mg</strong> is the primary first-line recommendation:<br><br>
            &bull; <strong>Recommended Use:</strong> 1 tablet every 4 to 6 hours as needed (not exceeding 8 tablets in 24 hours).<br>
            &bull; <strong>Hydration:</strong> Drink plenty of water and get adequate rest.<br>
            &bull; <strong>USM Stock Status:</strong> <span class="med-badge-stock stock-in">In Stock</span> &bull; ₱5.00/tablet.<br><br>
            <a href="{{ route('medicines') }}" class="ai-action-btn">Check Store Inventory &rarr;</a>
            <div class="ai-disclaimer">⚠️ Warning: If fever exceeds 38.5°C or continues for more than 48 hours, visit USM Health Services immediately.</div>`,
            followUps: ['Is Cetirizine also in stock?', 'Can I take it on an empty stomach?', 'Pharmacy opening hours']
        };
    }

    if (/cough|sipon|cold|ubo/.test(lq)) {
        return {
            html: `For cough and bronchial irritation:<br><br>
            &bull; <strong>Salbutamol Syrup:</strong> Available for bronchospasm and wheezing cough (₱85.00/bottle).<br>
            &bull; <strong>Cetirizine 10mg:</strong> Ideal if the cough is triggered by allergies, post-nasal drip, or allergic rhinitis (₱6.00/tablet).<br><br>
            <div class="ai-disclaimer">⚕️ Note: If coughing persists with discolored phlegm or shortness of breath, consult a campus physician.</div>`,
            followUps: ['Look up Salbutamol stock', 'Cetirizine dosage', 'Store medicines list']
        };
    }

    if (/prescription|reseta|dispens|route/.test(lq)) {
        return {
            html: `<strong>USM Pharmacy Prescription System:</strong><br><br>
            1. <strong>Doctor Consultation:</strong> Doctors or nurses encode your prescription directly into the system.<br>
            2. <strong>Routing:</strong> Your prescription is routed in real time to the USM Pharmacy queue.<br>
            3. <strong>Dispensing:</strong> Pharmacists verify stock and prepare your medicines. You can track status (Pending &rarr; At Pharmacy &rarr; Dispensed) on your dashboard.<br><br>
            <a href="{{ route('login') }}" class="ai-action-btn">Log In to View Prescriptions &rarr;</a>`,
            followUps: ['How to register as student?', 'Store medicine pricing', 'Opening hours']
        };
    }

    if (/hours|oras|open|schedule|location/.test(lq)) {
        return {
            html: `<strong>USM Health Services Pharmacy Hours:</strong><br><br>
            &bull; <strong>Schedule:</strong> Monday to Friday, 8:00 AM &ndash; 5:00 PM<br>
            &bull; <strong>Location:</strong> USM Health Services Building, Main Campus<br>
            &bull; <strong>Emergency:</strong> For night or weekend emergencies, please proceed to the nearest hospital facility.`,
            followUps: ['Look up medicines', 'Prescription tracking', 'What medicines are in stock?']
        };
    }

    if (/hello|hi|kumusta|hey/.test(lq)) {
        return {
            html: `Hello! 👋 I am your USM AI Pharmacy Assistant. You can ask me to:<br><br>
            &bull; Look up any medicine's real-time stock and pricing<br>
            &bull; Recommend over-the-counter options for common symptoms<br>
            &bull; Explain prescription procedures and tracking<br><br>
            How can I help you today?`,
            followUps: ['Paracetamol stock', 'Medicines for cough', 'How do prescriptions work?']
        };
    }

    return {
        html: `I searched the USM Pharmacy repository for "<strong>${escapeHtml(query)}</strong>".<br><br>
        You can check our full catalogue with real-time stock counts on the Store page, or ask about specific symptoms (e.g. fever, headache, allergies, cough).<br><br>
        <a href="{{ route('medicines') }}" class="ai-action-btn">Browse Medicine Store &rarr;</a>
        <div class="ai-disclaimer">⚕️ You can also consult the pharmacist directly at the USM Health Services clinic counter.</div>`,
        followUps: ['Look up Paracetamol', 'Check cough medicines', 'Prescription info']
    };
}

function sendMsg() {
    const text = input.value.trim();
    if (!text) return;

    input.value = '';
    handleInput(input);

    threadWrap.classList.add('active');

    // Add user message
    const userRow = document.createElement('div');
    userRow.className = 'msg-row user';
    userRow.innerHTML = `<div class="msg-content-user">${escapeHtml(text)}</div>`;
    msgs.appendChild(userRow);
    msgs.scrollTop = msgs.scrollHeight;

    showThinking();

    setTimeout(() => {
        hideThinking();
        const responseData = buildResponse(text);

        const aiRow = document.createElement('div');
        aiRow.className = 'msg-row';
        aiRow.innerHTML = `
            <div class="msg-avatar ai">
                <svg viewBox="0 0 28 20" width="18" height="13" fill="none"><ellipse cx="9" cy="10" rx="6.5" ry="6.5" stroke="#0f2d52" stroke-width="2.2"/><ellipse cx="19" cy="10" rx="6.5" ry="6.5" stroke="#0f2d52" stroke-width="2.2"/></svg>
            </div>
            <div class="msg-content-ai">
                <div class="ai-text-container"><span class="stream-text"></span><span class="stream-cursor"></span></div>
                <div class="follow-ups" style="display:none;"></div>
            </div>`;
        msgs.appendChild(aiRow);
        msgs.scrollTop = msgs.scrollHeight;

        const textContainer = aiRow.querySelector('.stream-text');
        const cursor = aiRow.querySelector('.stream-cursor');
        const fuContainer = aiRow.querySelector('.follow-ups');

        // Streaming effect like Claude / Gemini
        const rawHtml = responseData.html;
        // Stream text smoothly
        let index = 0;
        const step = Math.max(3, Math.floor(rawHtml.length / 35));
        const timer = setInterval(() => {
            index += step;
            if (index >= rawHtml.length) {
                clearInterval(timer);
                textContainer.innerHTML = rawHtml;
                cursor.remove();

                if (responseData.followUps && responseData.followUps.length) {
                    fuContainer.innerHTML = responseData.followUps.map(f =>
                        `<button type="button" class="fu-chip" onclick="askFollowUp('${f.replace(/'/g, "\\'")}')">${f}</button>`
                    ).join('');
                    fuContainer.style.display = 'flex';
                }
                msgs.scrollTop = msgs.scrollHeight;
            } else {
                // partial slice safely before tag
                textContainer.innerHTML = rawHtml.slice(0, index);
                msgs.scrollTop = msgs.scrollHeight;
            }
        }, 18);

    }, 550);
}
</script>
</body>
</html>
