<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>USM Pharmacy System - Your Health, Answered</title>
    <meta name="description" content="AI-powered pharmacy portal for USM students and staff.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,700&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{--g9:#14532d;--g8:#166534;--g7:#15803d;--g6:#16a34a;--g1:#dcfce7;--g0:#f0fdf4;--y5:#eab308;--y4:#facc15;--y1:#fef9c3;--f:'Inter',system-ui,sans-serif}
        html{scroll-behavior:smooth}
        body{font-family:var(--f);background:#fff;color:#111827;-webkit-font-smoothing:antialiased;overflow-x:hidden}
        .navbar{position:sticky;top:0;z-index:40;background:linear-gradient(to right,var(--g8),var(--g7),var(--g6));border-bottom:4px solid var(--y4);box-shadow:0 4px 20px rgba(0,0,0,.18)}
        .navbar-inner{max-width:1280px;margin:0 auto;padding:.75rem 1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1.5rem}
        .nav-brand{display:flex;align-items:center;gap:.75rem;text-decoration:none}
        .nav-brand-logo{width:40px;height:40px;background:rgba(255,255,255,.12);border-radius:10px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(255,255,255,.2)}
        .nav-brand-logo svg{width:26px;height:26px}
        .nav-brand-name{font-weight:700;font-size:.875rem;color:var(--y4);line-height:1.2}
        .nav-brand-sub{font-size:.7rem;color:#bbf7d0}
        .nav-links{display:flex;align-items:center;gap:1.5rem}
        .nav-link{font-size:.875rem;font-weight:500;color:#d1fae5;text-decoration:none;background:none;border:none;cursor:pointer;transition:color .15s}
        .nav-link:hover,.nav-link.active{color:var(--y4)}
        .nav-right{display:flex;align-items:center;gap:.75rem}
        .btn-store{display:inline-flex;align-items:center;gap:.5rem;background:linear-gradient(to right,var(--y4),var(--y5));color:var(--g9);font-weight:700;font-size:.875rem;padding:.5rem 1.25rem;border-radius:9999px;text-decoration:none;border:none;cursor:pointer;transition:all .15s;box-shadow:0 2px 8px rgba(0,0,0,.15)}
        .btn-store:hover{background:linear-gradient(to right,var(--y5),#f97316);transform:translateY(-1px)}
        .btn-store svg{width:15px;height:15px}
        .btn-logout{background:none;border:none;cursor:pointer;color:#86efac;padding:.375rem;transition:color .15s}
        .btn-logout:hover{color:#fca5a5}
        .hero{background:linear-gradient(to bottom,var(--g0),rgba(254,249,195,.2),#fff);padding:5rem 1.5rem 0;overflow:hidden;position:relative}
        .hero-glow{position:absolute;top:0;left:50%;transform:translateX(-50%);width:700px;height:350px;background:rgba(134,239,172,.4);border-radius:50%;filter:blur(80px);pointer-events:none}
        .hero-inner{max-width:900px;margin:0 auto;text-align:center;position:relative;z-index:1}
        .hero-badge{display:inline-flex;align-items:center;gap:.4rem;background:var(--y1);color:#854d0e;border:1px solid #fde68a;font-size:.75rem;font-weight:600;padding:.375rem .75rem;border-radius:9999px;margin-bottom:1.5rem}
        .hero-badge svg{width:12px;height:12px}
        .hero-title{font-size:clamp(2.6rem,6vw,4rem);font-weight:800;color:var(--g9);line-height:1.1;margin-bottom:1rem}
        .hero-title em{font-style:italic;color:var(--y5)}
        .hero-subtitle{font-size:1.0625rem;color:#6b7280;margin-top:1.25rem;margin-bottom:2.5rem;max-width:560px;margin-left:auto;margin-right:auto;line-height:1.65}
        .hero-ctas{display:flex;align-items:center;justify-content:center;gap:1rem;flex-wrap:wrap;margin-bottom:3rem}
        .btn-hero-dark{background:var(--g8);color:#fff;font-weight:700;font-size:.9375rem;padding:.75rem 1.75rem;border-radius:9999px;text-decoration:none;border:none;cursor:pointer;transition:all .15s;box-shadow:0 4px 14px rgba(22,101,52,.35)}
        .btn-hero-dark:hover{background:var(--g9);transform:translateY(-1px)}
        .btn-hero-yellow{display:inline-flex;align-items:center;gap:.4rem;background:linear-gradient(to right,var(--y4),var(--y5));color:var(--g9);font-weight:700;font-size:.9375rem;padding:.75rem 1.75rem;border-radius:9999px;text-decoration:none;border:none;cursor:pointer;transition:all .15s;box-shadow:0 4px 14px rgba(234,179,8,.35)}
        .btn-hero-yellow:hover{background:linear-gradient(to right,var(--y5),#f97316);transform:translateY(-1px)}
        .btn-hero-yellow svg{width:16px;height:16px}
        .chatbot-panel-wrap{max-width:900px;margin:0 auto;padding:0 1.5rem;margin-top:0.5rem}
        .chatbot-panel{position:relative;border-radius:24px;overflow:hidden;background:rgba(22,101,52,.8);min-height:200px;display:flex;align-items:center;justify-content:center}
        .chatbot-orb{position:absolute;border-radius:50%;pointer-events:none;filter:blur(60px)}
        .orb-1{bottom:-2rem;left:3rem;width:13rem;height:13rem;background:rgba(250,204,21,.5)}
        .orb-2{bottom:-1rem;right:4rem;width:16rem;height:16rem;background:rgba(253,224,71,.3)}
        .orb-3{top:2.5rem;right:2.5rem;width:8rem;height:8rem;background:rgba(74,222,128,.25)}
        .orb-4{top:1.5rem;left:33%;width:6rem;height:6rem;background:rgba(234,179,8,.2)}
        .chatbot-input-wrap{position:relative;z-index:10;width:100%;padding:2rem}
        .chatbot-input-bar{background:rgba(255,255,255,.96);backdrop-filter:blur(8px);border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,.25);display:flex;align-items:center;gap:1rem;padding:1rem 1rem 1rem 1.25rem}
        .chatbot-logo{width:28px;height:28px;flex-shrink:0;background:rgba(22,101,52,.1);border-radius:8px;display:flex;align-items:center;justify-content:center}
        .chatbot-logo svg{width:16px;height:16px}
        #heroInput{flex:1;border:none;outline:none;background:transparent;font-family:var(--f);font-size:.9375rem;color:#374151}
        #heroInput::placeholder{color:#9ca3af}
        .chatbot-send-btn{background:var(--y4);color:var(--g9);border:none;border-radius:12px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;transition:all .15s;box-shadow:0 2px 8px rgba(0,0,0,.1)}
        .chatbot-send-btn:hover{background:var(--y5);transform:scale(1.05)}
        .chatbot-send-btn svg{width:16px;height:16px;stroke-width:2.5}
        .chips-wrap{display:flex;flex-wrap:wrap;gap:.5rem;justify-content:center;padding:1.25rem 1.5rem;max-width:900px;margin:0 auto}
        .chip{display:inline-flex;align-items:center;gap:.375rem;font-size:.8125rem;font-weight:500;color:#4b5563;background:#fff;border:1px solid #e5e7eb;padding:.5rem 1rem;border-radius:9999px;cursor:pointer;transition:all .15s;box-shadow:0 1px 4px rgba(0,0,0,.06)}
        .chip svg{width:12px;height:12px}
        .chip:hover{background:var(--y1);color:var(--g8);border-color:var(--y4)}
        .chat-section{display:none;max-width:900px;margin:0 auto;padding:0 1.5rem 1.5rem}
        .chat-section.active{display:flex;flex-direction:column}
        .chat-panel{background:#fff;border:1px solid #e5e7eb;border-radius:20px;box-shadow:0 4px 20px rgba(0,0,0,.06);overflow:hidden}
        .chat-header{background:var(--g0);border-bottom:1px solid #d1fae5;padding:.75rem 1rem;display:flex;align-items:center;justify-content:space-between}
        .chat-header-left{display:flex;align-items:center;gap:.5rem}
        .online-dot{width:8px;height:8px;border-radius:50%;background:#22c55e;box-shadow:0 0 0 2px rgba(34,197,94,.25);animation:pulse 2s infinite}
        @keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
        .chat-title{font-size:.8125rem;font-weight:700;color:var(--g8)}
        .btn-clear{background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:.25rem .625rem;font-size:.75rem;font-weight:600;color:#6b7280;cursor:pointer;transition:all .15s}
        .btn-clear:hover{background:#f3f4f6}
        .chat-messages{max-height:380px;overflow-y:auto;padding:1.25rem;display:flex;flex-direction:column;gap:1rem}
        .chat-messages::-webkit-scrollbar{width:4px}
        .chat-messages::-webkit-scrollbar-thumb{background:#d1fae5;border-radius:2px}
        .msg-row{display:flex;gap:.625rem;align-items:flex-start}
        .msg-row.user{justify-content:flex-end}
        .msg-avatar{width:28px;height:28px;border-radius:10px;flex-shrink:0;margin-top:2px;display:flex;align-items:center;justify-content:center}
        .msg-avatar.ai{background:var(--g1)}
        .msg-avatar.user{background:var(--y4)}
        .msg-avatar svg{width:14px;height:14px}
        .msg-bubble{max-width:82%;padding:.75rem 1rem;font-size:.875rem;line-height:1.65;white-space:pre-line}
        .msg-bubble.ai{background:#f3f4f6;color:#1f2937;border-radius:16px 16px 16px 4px}
        .msg-bubble.user{background:var(--g7);color:#fff;border-radius:16px 16px 4px 16px}
        .thinking-bar{display:inline-flex;align-items:center;gap:.5rem;background:#f3f4f6;padding:.625rem 1rem;border-radius:14px;font-size:.8125rem;color:#6b7280;font-weight:500}
        .dots{display:inline-flex;gap:4px;align-items:center}
        .dots span{width:6px;height:6px;border-radius:50%;background:var(--g6);animation:bounce 1.4s ease-in-out infinite}
        .dots span:nth-child(2){animation-delay:.2s}.dots span:nth-child(3){animation-delay:.4s}
        @keyframes bounce{0%,80%,100%{transform:translateY(0)}40%{transform:translateY(-5px)}}
        .fu-chips{display:flex;gap:6px;flex-wrap:wrap;margin-top:.625rem}
        .fu-chip{background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:3px 10px;font-size:.75rem;color:#374151;cursor:pointer;transition:all .15s;font-weight:500}
        .fu-chip:hover{background:var(--g8);color:#fff;border-color:var(--g8)}
        .med-in{display:inline-flex;background:#dcfce7;color:#166534;border:1px solid #86efac;font-size:.7rem;font-weight:700;padding:2px 7px;border-radius:5px;text-transform:uppercase}
        .med-low{display:inline-flex;background:#fefce8;color:#854d0e;border:1px solid #fde68a;font-size:.7rem;font-weight:700;padding:2px 7px;border-radius:5px;text-transform:uppercase}
        .med-out{display:inline-flex;background:#fef2f2;color:#991b1b;border:1px solid #fecaca;font-size:.7rem;font-weight:700;padding:2px 7px;border-radius:5px;text-transform:uppercase}
        .med-price{display:inline-flex;background:#f0fdf4;color:#166534;font-size:.75rem;font-weight:700;padding:2px 8px;border-radius:5px}
        .ai-btn{display:inline-flex;align-items:center;gap:.375rem;background:var(--g8);color:#fff;font-size:.75rem;font-weight:700;padding:4px 10px;border-radius:6px;text-decoration:none;margin-top:6px;transition:background .15s}
        .ai-btn:hover{background:var(--g9)}
        .ai-note{margin-top:.625rem;padding-top:.5rem;border-top:1px solid #f3f4f6;font-size:.75rem;color:#6b7280;font-style:italic;line-height:1.5}
        .guest-counter-badge{display:inline-flex;align-items:center;gap:5px;font-size:.75rem;font-weight:600;padding:3px 10px;border-radius:9999px;background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;margin-left:.5rem}
        .guest-counter-badge.authed{background:#dcfce7;color:#166534;border-color:#86efac}
        .guest-limit-box{background:linear-gradient(135deg,#fefce8,#fef2f2);border:1.5px solid #f87171;border-radius:16px;padding:1.25rem;box-shadow:0 4px 16px rgba(239,68,68,.12);text-align:center;margin:4px 0}
        .guest-limit-title{font-size:.9375rem;font-weight:800;color:#991b1b;margin-bottom:.375rem}
        .guest-limit-desc{font-size:.8125rem;color:#4b5563;line-height:1.55;margin-bottom:.875rem}
        .guest-limit-actions{display:flex;justify-content:center;gap:.75rem}
        .guest-limit-btn{display:inline-flex;align-items:center;gap:.375rem;background:#166534;color:#fff;font-weight:700;font-size:.8125rem;padding:.5rem 1.25rem;border-radius:9999px;text-decoration:none;transition:background .15s;box-shadow:0 2px 8px rgba(22,101,52,.25)}
        .guest-limit-btn:hover{background:#14532d}
        .features-section{padding:6rem 1.5rem;background:#fff}
        .features-inner{max-width:1200px;margin:0 auto}
        .section-label{text-align:center;font-size:.7rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--g7);margin-bottom:.75rem}
        .section-title{font-size:clamp(1.75rem,4vw,2.5rem);font-weight:800;color:var(--g9);text-align:center;margin-bottom:3.5rem;line-height:1.15}
        .features-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1.5rem}
        .feature-card{background:var(--g0);border:1px solid var(--g1);border-radius:20px;padding:1.5rem;cursor:default;transition:all .2s}
        .feature-card:hover{background:var(--y1);border-color:#fde68a;transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.06)}
        .feat-icon{width:44px;height:44px;border-radius:12px;background:var(--g1);display:flex;align-items:center;justify-content:center;margin-bottom:1rem;transition:background .2s}
        .feature-card:hover .feat-icon{background:#fde68a}
        .feat-icon svg{width:20px;height:20px;stroke:var(--g7);stroke-width:2;fill:none;stroke-linecap:round;stroke-linejoin:round}
        .feat-title{font-size:.9375rem;font-weight:700;color:var(--g9);margin-bottom:.375rem}
        .feat-desc{font-size:.875rem;color:#6b7280;line-height:1.65}
        .stats-band{background:linear-gradient(to right,var(--g8),var(--g7),var(--g6));border-top:4px solid var(--y4);border-bottom:4px solid var(--y4);padding:3.5rem 1.5rem}
        .stats-inner{max-width:1100px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:2rem;text-align:center}
        .stat-val{font-size:2.25rem;font-weight:800;color:var(--y4);margin-bottom:.25rem}
        .stat-lbl{font-size:.8125rem;color:#bbf7d0}
        .cta-section{padding:5rem 1.5rem;background:linear-gradient(to bottom right,var(--y1),var(--g0))}
        .cta-inner{max-width:720px;margin:0 auto;text-align:center}
        .cta-title{font-size:clamp(1.5rem,3.5vw,2rem);font-weight:800;color:var(--g9);margin-bottom:1rem}
        .cta-sub{font-size:1rem;color:#6b7280;margin-bottom:2rem;line-height:1.65}
        .btn-cta{display:inline-flex;align-items:center;gap:.5rem;background:linear-gradient(to right,var(--g7),var(--g8));color:#fff;font-weight:700;font-size:1rem;padding:.875rem 2rem;border-radius:9999px;text-decoration:none;border:none;cursor:pointer;transition:all .15s;box-shadow:0 8px 24px rgba(22,101,52,.3)}
        .btn-cta:hover{background:linear-gradient(to right,var(--g8),var(--g9));transform:translateY(-2px)}
        .btn-cta svg{width:18px;height:18px}
        .btn-cta-amber{background:linear-gradient(to right,#92400e,#a16207)!important}
        footer{background:var(--g9);border-top:4px solid var(--y4);padding:2.5rem 1.5rem;text-align:center;font-size:.8125rem;color:#86efac}
        @media(max-width:768px){.nav-links{display:none}.hero-title{font-size:2.25rem}.chatbot-panel{min-height:160px}.chatbot-input-wrap{padding:1.25rem}}
    </style>
</head>
<body>

{{-- NAVBAR --}}
<header class="navbar">
    <div class="navbar-inner">
        <a href="{{ route('home') }}" class="nav-brand">
            <div class="nav-brand-logo">
                <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 3L4 8v8c0 6.627 5.373 12 12 12s12-5.373 12-12V8L16 3z" stroke="#facc15" stroke-width="2" stroke-linejoin="round"/>
                    <path d="M11 16l3 3 7-7" stroke="#facc15" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <div class="nav-brand-name">USM Pharmacy</div>
                <div class="nav-brand-sub">Patient Portal</div>
            </div>
        </a>
        <nav class="nav-links">
            <a href="{{ route('home') }}" class="nav-link active">Home</a>
            <a href="#chatbot" class="nav-link">AI Assistant</a>
            @auth
                <a href="{{ route('patient.prescriptions') }}" class="nav-link">My Prescriptions</a>
            @else
                <a href="{{ route('login') }}" class="nav-link">My Prescriptions</a>
            @endauth
        </nav>
        <div class="nav-right">
            <a href="{{ route('medicines') }}" class="btn-store">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                Pharmacy Store
            </a>
            @auth
                <form method="POST" action="{{ route('logout') }}" style="display:inline" onsubmit="return confirm('Are you sure you want to log out?');">
                    @csrf
                    <button type="submit" class="btn-logout" title="Log out">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="nav-link">Log in</a>
            @endauth
        </div>
    </div>
</header>

{{-- HERO --}}
<section class="hero">
    <div class="hero-glow"></div>
    <div class="hero-inner">
        <div class="hero-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            Powered by USM Health AI
        </div>
        <h1 class="hero-title">Your health, <em>answered</em><br>in every visit.</h1>
        <p class="hero-subtitle">Ask about symptoms, browse pharmacy products, check your prescriptions &mdash; all in one place built for USM students and staff.</p>
        <div class="hero-ctas">
            <a href="#chatbot" class="btn-hero-dark">Talk to AI Assistant</a>
            <a href="{{ route('medicines') }}" class="btn-hero-yellow">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                Browse Pharmacy
            </a>
        </div>
    </div>

    <div class="chatbot-panel-wrap" id="chatbot">
        <div class="chat-section" id="chatSection">
            <div class="chat-panel">
                <div class="chat-header">
                    <div class="chat-header-left">
                        <span class="online-dot"></span>
                        <span class="chat-title">USM Pharmacy AI Assistant</span>
                        @guest
                            <span class="guest-counter-badge" id="guestCounterBadge">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <span id="guestCountText">15/15 messages left</span>
                            </span>
                        @else
                            <span class="guest-counter-badge authed">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                Unlimited Member Access
                            </span>
                        @endguest
                    </div>
                    <button type="button" class="btn-clear" onclick="clearChat()">&#x2715; Clear</button>
                </div>
                <div class="chat-messages" id="chatMsgs"></div>
            </div>
        </div>

        <div class="chatbot-panel">
            <div class="chatbot-orb orb-1"></div>
            <div class="chatbot-orb orb-2"></div>
            <div class="chatbot-orb orb-3"></div>
            <div class="chatbot-orb orb-4"></div>
            <div class="chatbot-input-wrap">
                <div class="chatbot-input-bar">
                    <div class="chatbot-logo">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#166534" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <input type="text" id="heroInput" placeholder="Ask USM Health anything..." autocomplete="off">
                    <button type="button" class="chatbot-send-btn" onclick="sendMsg()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CHIPS --}}
<div class="chips-wrap">
    <button type="button" class="chip" onclick="fillAndSend('Check Paracetamol 500mg stock and price at USM Pharmacy')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        Look up a medicine
    </button>
    <button type="button" class="chip" onclick="fillAndSend('What medicines are currently available at USM Pharmacy?')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
        Check availability
    </button>
    <button type="button" class="chip" onclick="fillAndSend('I have a cut on my finger, what first aid steps should I follow?')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
        First aid help
    </button>
    <button type="button" class="chip" onclick="fillAndSend('How does the prescription and dispensing process work at USM Pharmacy?')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        My prescriptions
    </button>
</div>

{{-- FEATURES --}}
<section class="features-section">
    <div class="features-inner">
        <p class="section-label">What we offer</p>
        <h2 class="section-title">Everything you need,<br>in one portal.</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feat-icon"><svg viewBox="0 0 24 24"><path d="M12 2a2 2 0 012 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 017 7H3a7 7 0 017-7h1V5.73A2 2 0 0110 4a2 2 0 012-2z"/></svg></div>
                <h3 class="feat-title">AI Health Assistant</h3>
                <p class="feat-desc">Ask anything about symptoms, medicines, or first aid &mdash; get instant, reliable answers powered by USM Health AI.</p>
            </div>
            <div class="feature-card">
                <div class="feat-icon"><svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg></div>
                <h3 class="feat-title">Pharmacy Storefront</h3>
                <p class="feat-desc">Browse all available medicines at USM Pharmacy with real-time stock levels and transparent pricing.</p>
            </div>
            <div class="feature-card">
                <div class="feat-icon"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div>
                <h3 class="feat-title">Prescription Tracking</h3>
                <p class="feat-desc">View your active prescriptions and track dispensing status &mdash; from Pending to At Pharmacy to Dispensed.</p>
            </div>
            <div class="feature-card">
                <div class="feat-icon"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg></div>
                <h3 class="feat-title">Safe &amp; Secure</h3>
                <p class="feat-desc">Your health data is protected with institutional-grade security. Accessible only to you and authorized staff.</p>
            </div>
        </div>
    </div>
</section>

{{-- STATS --}}
<section class="stats-band">
    <div class="stats-inner">
        <div><div class="stat-val">5,000+</div><div class="stat-lbl">Students &amp; Staff Served</div></div>
        <div><div class="stat-val">{{ $medicines->count() }}+</div><div class="stat-lbl">Medicines in Catalogue</div></div>
        <div><div class="stat-val">15+</div><div class="stat-lbl">Years of Service</div></div>
        <div><div class="stat-val">24/7</div><div class="stat-lbl">AI Assistant Availability</div></div>
    </div>
</section>

{{-- CTA --}}
<section class="cta-section">
    <div class="cta-inner">
        <h2 class="cta-title">Browse what's available now.</h2>
        <p class="cta-sub">Check real-time stock, prices, and availability of all medicines at USM Pharmacy &mdash; no need to visit in person first.</p>
        <div style="display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap">
            @guest
                <a href="{{ route('register') }}" class="btn-cta">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Register now
                </a>
            @else
                <a href="{{ route('patient.dashboard') }}" class="btn-cta">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Go to Dashboard
                </a>
            @endguest
            <a href="{{ route('medicines') }}" class="btn-cta btn-cta-amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                Open Pharmacy Store
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    </div>
</section>

<footer>&copy; {{ date('Y') }} University of Southern Mindanao &mdash; Pharmacy Patient Portal. All rights reserved.</footer>

<script>
const medicines = @json($medicines);
const chatSection = document.getElementById('chatSection');
const chatMsgs = document.getElementById('chatMsgs');
const heroInput = document.getElementById('heroInput');
heroInput.addEventListener('keydown', e => { if (e.key === 'Enter') sendMsg(); });
function fillAndSend(t) { heroInput.value = t; sendMsg(); }
function clearChat() { chatMsgs.innerHTML = ''; chatSection.classList.remove('active'); heroInput.value = ''; heroInput.focus(); }
function escHtml(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
function scrollBottom() { chatMsgs.scrollTop = chatMsgs.scrollHeight; }
const BI = `<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="#166534" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a2 2 0 012 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 017 7H3a7 7 0 017-7h1V5.73A2 2 0 0110 4a2 2 0 012-2z"/></svg>`;
const UI = `<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="#166534" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>`;

const isGuest = @json(auth()->guest());
const MAX_GUEST_MSGS = 15;
let guestMsgCount = isGuest ? parseInt(localStorage.getItem('usm_guest_msg_count') || '0', 10) : 0;

function updateGuestUI() {
    if (!isGuest) return;
    const remaining = Math.max(0, MAX_GUEST_MSGS - guestMsgCount);
    const countEl = document.getElementById('guestCountText');
    if (countEl) countEl.textContent = `${remaining}/15 messages left`;

    if (guestMsgCount >= MAX_GUEST_MSGS) {
        heroInput.disabled = true;
        heroInput.placeholder = "Guest limit reached (15/15). Log in to continue...";
        const sendBtn = document.querySelector('.chatbot-send-btn');
        if (sendBtn) {
            sendBtn.disabled = true;
            sendBtn.style.opacity = '0.4';
            sendBtn.style.cursor = 'not-allowed';
        }
    }
}

function showGuestLimitNotice() {
    chatSection.classList.add('active');
    if (document.getElementById('guestLimitNotice')) return;
    const row = document.createElement('div');
    row.className = 'msg-row';
    row.id = 'guestLimitNotice';
    row.innerHTML = `
        <div class="msg-avatar ai">${BI}</div>
        <div class="guest-limit-box" style="flex:1">
            <div class="guest-limit-title">&#9888;&#65039; Guest Preview Limit Reached (15/15 Messages)</div>
            <p class="guest-limit-desc">
                You have reached your 15-message complimentary guest preview. Please log in with your USM student or staff account for unlimited AI consultations, inventory lookups, and prescription tracking.
            </p>
            <div class="guest-limit-actions">
                <a href="{{ route('login') }}" class="guest-limit-btn">Log In to Continue &rarr;</a>
            </div>
        </div>
    `;
    chatMsgs.appendChild(row);
    scrollBottom();
}

updateGuestUI();

function appendMsg(role, html) {
    const row = document.createElement('div');
    row.className = 'msg-row' + (role === 'user' ? ' user' : '');
    row.innerHTML = role !== 'user'
        ? `<div class="msg-avatar ai">${BI}</div><div class="msg-bubble ai">${html}</div>`
        : `<div class="msg-bubble user">${escHtml(html)}</div><div class="msg-avatar user">${UI}</div>`;
    chatMsgs.appendChild(row); scrollBottom();
}
function showThinking() {
    const row = document.createElement('div'); row.className = 'msg-row'; row.id = 'thinkRow';
    row.innerHTML = `<div class="msg-avatar ai">${BI}</div><div class="thinking-bar"><div class="dots"><span></span><span></span><span></span></div>Thinking&hellip;</div>`;
    chatMsgs.appendChild(row); scrollBottom();
}
function hideThinking() { document.getElementById('thinkRow')?.remove(); }
function buildResponse(query) {
    const lq = query.toLowerCase();
    for (const m of medicines) {
        const n = m.name.toLowerCase().split(' ')[0];
        const g = (m.generic_name || '').toLowerCase().split(' ')[0];
        if (lq.includes(n) || (g && lq.includes(g))) {
            const badge = m.available_stock === 0 ? `<span class="med-out">Out of Stock</span>`
                : m.available_stock <= 30 ? `<span class="med-low">Low Stock (${m.available_stock} ${m.unit}s)</span>`
                : `<span class="med-in">In Stock (${m.available_stock} ${m.unit}s)</span>`;
            const price = `<span class="med-price">&#8369;${Number(m.unit_price).toFixed(2)} / ${m.unit}</span>`;
            return { html: `Current info for <strong>${m.name}</strong>:<br><br><div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:8px">${badge} ${price}</div>&bull; <strong>Unit:</strong> ${m.unit}<br><br><a href="{{ route('medicines') }}" class="ai-btn">View in Store &rarr;</a><div class="ai-note">&#9877;&#65039; Prescription items require a valid doctor&apos;s order.</div>`,
                followUps: [`Do I need a prescription for ${m.name}?`, 'What other medicines are in stock?', 'Pharmacy opening hours'] };
        }
    }
    if (/what medicines|available|catalogue|catalog|list of med|in stock/.test(lq)) {
        const top = medicines.filter(m => m.available_stock > 0).slice(0, 6).map(m => `&bull; <strong>${m.name}</strong> &mdash; <span class="med-in">In Stock</span> &middot; &#8369;${Number(m.unit_price).toFixed(2)}/${m.unit}`).join('<br>');
        return { html: `<strong>USM Pharmacy &mdash; Stock Overview:</strong><br><br>${top || 'No medicines listed.'}<br><br><a href="{{ route('medicines') }}" class="ai-btn">View Full Store &rarr;</a><div class="ai-note">&#9877;&#65039; Stock updated in real-time.</div>`, followUps: ['Check Paracetamol stock', 'How do prescriptions work?', 'Pharmacy hours'] };
    }
    if (/headache|fever|lagnat|sakit ng ulo|body ache/.test(lq)) {
        return { html: `For fever and headache, <strong>Paracetamol 500mg</strong> is the first-line recommendation:<br><br>&bull; <strong>Dose:</strong> 1 tablet every 4&ndash;6 hours (max 8/day)<br>&bull; <strong>USM Status:</strong> <span class="med-in">In Stock</span><br><br><a href="{{ route('medicines') }}" class="ai-btn">Check Store &rarr;</a><div class="ai-note">&#9888;&#65039; If fever exceeds 38.5&deg;C or lasts 48+ hours, visit USM Health Services.</div>`, followUps: ['Is Cetirizine in stock?', 'Medicines for cough', 'Pharmacy hours'] };
    }
    if (/cough|sipon|cold|ubo/.test(lq)) {
        return { html: `For cough and colds:<br><br>&bull; <strong>Salbutamol Syrup</strong> &mdash; bronchospasm and wheezing<br>&bull; <strong>Cetirizine 10mg</strong> &mdash; allergy-triggered cough<br><br><a href="{{ route('medicines') }}" class="ai-btn">View Store &rarr;</a><div class="ai-note">&#9877;&#65039; If symptoms persist with discoloured phlegm, see a campus physician.</div>`, followUps: ['What medicines are in stock?', 'Pharmacy hours'] };
    }
    if (/cut|wound|sugat|bleed|first aid/.test(lq)) {
        return { html: `For minor cuts and wounds:<br><br>1. Wash hands thoroughly<br>2. Rinse wound with clean water<br>3. Apply gentle pressure to stop bleeding<br>4. Apply Betadine and cover with sterile bandage<br><br><span class="med-in">Betadine &amp; gauze available at USM Pharmacy</span><br><br><a href="{{ route('medicines') }}" class="ai-btn">View Wound Care Supplies &rarr;</a><div class="ai-note">&#9877;&#65039; Seek care if the cut is deep or shows signs of infection.</div>`, followUps: ['Betadine stock and price', 'What else is available?', 'Pharmacy hours'] };
    }
    if (/prescription|reseta|dispens/.test(lq)) {
        return { html: `<strong>USM Pharmacy Prescription System:</strong><br><br>1. <strong>Doctor Consultation:</strong> Prescription encoded into the system<br>2. <strong>Routing:</strong> Routed in real time to USM Pharmacy queue<br>3. <strong>Tracking:</strong> Pending &rarr; At Pharmacy &rarr; Dispensed<br><br><a href="{{ route('login') }}" class="ai-btn">Log in to View Prescriptions &rarr;</a>`, followUps: ['How do I register?', 'What medicines are in stock?', 'Pharmacy hours'] };
    }
    if (/hours|oras|open|schedule|location|where/.test(lq)) {
        return { html: `<strong>USM Health Services Pharmacy:</strong><br><br>&bull; <strong>Schedule:</strong> Mon &ndash; Fri, 8:00 AM &ndash; 5:00 PM<br>&bull; <strong>Location:</strong> USM Health Services Building, Main Campus<br>&bull; <strong>Emergency:</strong> Proceed to nearest hospital for after-hours emergencies.`, followUps: ['What medicines are available?', 'How do prescriptions work?'] };
    }
    if (/hello|hi|kumusta|hey/.test(lq)) {
        return { html: `Hello! &#128075; I&apos;m the <strong>USM Pharmacy AI Assistant</strong>. I can help you:<br><br>&bull; Check real-time medicine stock &amp; pricing<br>&bull; Recommend medicines for common symptoms<br>&bull; Explain the prescription process<br><br>How can I help?`, followUps: ['Check Paracetamol stock', 'Medicines for cough', 'How do prescriptions work?'] };
    }
    return { html: `I searched for &ldquo;<strong>${escHtml(query)}</strong>&rdquo; in the USM Pharmacy catalogue.<br><br>Try asking about a specific symptom (fever, cough, headache) or browse the store directly.<br><br><a href="{{ route('medicines') }}" class="ai-btn">Browse Medicine Store &rarr;</a><div class="ai-note">&#9877;&#65039; You can also consult the pharmacist at the USM Health Services counter.</div>`, followUps: ['Check Paracetamol stock', 'Medicines for fever', 'Prescription info'] };
}
function sendMsg() {
    const text = heroInput.value.trim();
    if (!text) return;
    if (isGuest && guestMsgCount >= MAX_GUEST_MSGS) {
        showGuestLimitNotice();
        return;
    }
    heroInput.value = '';
    chatSection.classList.add('active');
    appendMsg('user', text);
    showThinking();

    if (isGuest) {
        guestMsgCount++;
        localStorage.setItem('usm_guest_msg_count', guestMsgCount);
        updateGuestUI();
    }

    setTimeout(() => {
        hideThinking();
        const { html, followUps } = buildResponse(text);
        const row = document.createElement('div'); row.className = 'msg-row';
        row.innerHTML = `<div class="msg-avatar ai">${BI}</div><div><div class="msg-bubble ai">${html}</div>${followUps && followUps.length ? `<div class="fu-chips">${followUps.map(f => `<button type="button" class="fu-chip" onclick="fillAndSend('${f.replace(/'/g,"\\'")}')"> ${f}</button>`).join('')}</div>` : ''}</div>`;
        chatMsgs.appendChild(row);
        scrollBottom();

        if (isGuest && guestMsgCount >= MAX_GUEST_MSGS) {
            setTimeout(showGuestLimitNotice, 800);
        }
    }, 600);
}
</script>
</body>
</html>
