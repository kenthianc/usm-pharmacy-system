<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>USM Hospital Pharmacy — Product Catalog</title>
    <meta name="description" content="Browse available medicines and supplies at USM Health Services Pharmacy with real-time stock and pricing.">
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
            --g0: #f0fdf4;
            --y5: #eab308;
            --y4: #facc15;
            --y1: #fef9c3;
            --font: 'Inter', system-ui, -apple-system, sans-serif;
        }
        html { scroll-behavior: smooth; }
        body {
            font-family: var(--font);
            background: #f8fafc;
            color: #111827;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        /* ── NAVBAR ── */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 40;
            background: linear-gradient(to right, var(--g8), var(--g7), var(--g6));
            border-bottom: 4px solid var(--y4);
            box-shadow: 0 4px 20px rgba(0,0,0,.15);
        }
        .navbar-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
        }
        .nav-brand {
            display: flex;
            align-items: center;
            gap: .75rem;
            text-decoration: none;
        }
        .nav-brand-logo {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,.12);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255,255,255,.2);
        }
        .nav-brand-logo svg { width: 24px; height: 24px; }
        .nav-brand-name {
            font-weight: 700;
            font-size: .875rem;
            color: var(--y4);
            line-height: 1.2;
        }
        .nav-brand-sub {
            font-size: .7rem;
            color: #bbf7d0;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        .nav-link {
            font-size: .875rem;
            font-weight: 500;
            color: #d1fae5;
            text-decoration: none;
            background: none;
            border: none;
            cursor: pointer;
            transition: color .15s;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--y4);
        }
        .nav-right {
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .btn-store {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            background: linear-gradient(to right, var(--y4), var(--y5));
            color: var(--g9);
            font-weight: 700;
            font-size: .875rem;
            padding: .5rem 1.25rem;
            border-radius: 9999px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,.15);
            transition: all .15s;
        }
        .btn-store:hover {
            background: linear-gradient(to right, var(--y5), #f97316);
            transform: translateY(-1px);
        }
        .btn-store svg { width: 15px; height: 15px; }
        .btn-logout {
            background: none;
            border: none;
            cursor: pointer;
            color: #86efac;
            padding: .375rem;
            transition: color .15s;
        }
        .btn-logout:hover { color: #fca5a5; }

        /* ── STORE HEADER BAR ── */
        .store-header-bar {
            background: var(--g8);
            border-bottom: 4px solid var(--y5);
            padding: 1rem 1.5rem;
        }
        .store-header-inner {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }
        @media (min-width: 640px) {
            .store-header-inner {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        .store-title-wrap {
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .store-title-wrap svg {
            width: 18px;
            height: 18px;
            stroke: var(--y4);
            flex-shrink: 0;
        }
        .store-title {
            color: #ffffff;
            font-weight: 700;
            font-size: .9375rem;
        }
        .store-subtitle {
            color: #86efac;
            font-size: .75rem;
        }
        .search-container {
            display: flex;
            align-items: center;
            gap: .5rem;
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: .5rem;
            padding: .5rem .75rem;
            width: 100%;
            max-width: 320px;
            transition: border-color .15s, box-shadow .15s;
        }
        .search-container:focus-within {
            border-color: var(--g6);
            box-shadow: 0 0 0 3px rgba(22, 163, 74, .2);
        }
        .search-container svg {
            width: 15px;
            height: 15px;
            stroke: #9ca3af;
            flex-shrink: 0;
        }
        .search-input {
            border: none;
            outline: none;
            width: 100%;
            font-size: .875rem;
            color: #374151;
            background: transparent;
            font-family: inherit;
        }
        .search-input::placeholder { color: #9ca3af; }
        .btn-clear-search {
            background: none;
            border: none;
            cursor: pointer;
            color: #9ca3af;
            padding: 2px;
            display: none;
            align-items: center;
            justify-content: center;
            transition: color .15s;
        }
        .btn-clear-search:hover { color: #4b5563; }
        .btn-clear-search svg { width: 13px; height: 13px; stroke: currentColor; }

        /* ── MAIN CONTENT ── */
        .main-container {
            max-width: 1280px;
            margin: 0 auto;
            width: 100%;
            padding: 1.5rem;
            display: flex;
            gap: 1.75rem;
            flex: 1;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 200px;
            flex-shrink: 0;
            display: none;
        }
        @media (min-width: 768px) {
            .sidebar { display: block; }
        }
        .sidebar-heading {
            font-size: .75rem;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: .75rem;
        }
        .category-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .cat-btn {
            width: 100%;
            display: flex;
            align-items: center;
            gap: .625rem;
            padding: .625rem .75rem;
            border-radius: .5rem;
            font-size: .875rem;
            text-align: left;
            border: none;
            background: transparent;
            color: #4b5563;
            cursor: pointer;
            transition: all .15s;
            font-family: inherit;
        }
        .cat-btn:hover {
            background: #f1f5f9;
            color: #111827;
        }
        .cat-btn.active {
            background: var(--g7);
            color: #ffffff;
            font-weight: 600;
        }
        .cat-btn svg {
            width: 15px;
            height: 15px;
            flex-shrink: 0;
            stroke: currentColor;
        }
        .cat-count {
            margin-left: auto;
            font-size: .75rem;
            color: #9ca3af;
        }
        .cat-btn.active .cat-count {
            color: #bbf7d0;
        }

        .sidebar-notes {
            margin-top: 1.75rem;
            padding-top: 1.25rem;
            border-top: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            gap: .875rem;
        }
        .note-item {
            display: flex;
            align-items: flex-start;
            gap: .5rem;
            font-size: .75rem;
            color: #64748b;
            line-height: 1.4;
        }
        .note-item svg {
            width: 14px;
            height: 14px;
            stroke: var(--g6);
            flex-shrink: 0;
            margin-top: 2px;
        }

        /* ── CATALOG AREA ── */
        .catalog-area {
            flex: 1;
            min-width: 0;
        }
        .results-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        .results-text {
            font-size: .875rem;
            color: #64748b;
        }
        .results-text strong {
            color: #1e293b;
            font-weight: 600;
        }
        .btn-reset-filters {
            font-size: .75rem;
            color: var(--g7);
            background: none;
            border: none;
            cursor: pointer;
            display: none;
            align-items: center;
            gap: 4px;
            font-weight: 600;
        }
        .btn-reset-filters:hover { text-decoration: underline; }

        /* Mobile categories scroll */
        .mobile-cat-scroll {
            display: flex;
            gap: .5rem;
            overflow-x: auto;
            padding-bottom: .5rem;
            margin-bottom: 1rem;
        }
        @media (min-width: 768px) {
            .mobile-cat-scroll { display: none; }
        }
        .mobile-cat-pill {
            flex-shrink: 0;
            font-size: .75rem;
            font-weight: 600;
            padding: .375rem .75rem;
            border-radius: 9999px;
            border: 1px solid #d1d5db;
            background: #ffffff;
            color: #4b5563;
            cursor: pointer;
            transition: all .15s;
            font-family: inherit;
        }
        .mobile-cat-pill.active {
            background: var(--g7);
            color: #ffffff;
            border-color: var(--g7);
        }

        /* ── PRODUCTS GRID ── */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }
        @media (min-width: 640px) {
            .products-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
        @media (min-width: 1024px) {
            .products-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        }

        .product-card {
            border: 1px solid #e2e8f0;
            border-radius: .75rem;
            overflow: hidden;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            transition: box-shadow .2s, transform .2s;
        }
        .product-card:hover {
            box-shadow: 0 10px 25px -5px rgba(0,0,0,.08);
            transform: translateY(-2px);
        }
        .product-card.out-of-stock {
            opacity: .65;
        }

        /* Card Image Area */
        .card-img-area {
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 0;
            position: relative;
            border-bottom: 1px solid #f1f5f9;
        }
        .pill-box {
            width: 54px;
            height: 54px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: .75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }
        .pill-box svg {
            width: 26px;
            height: 26px;
            stroke: var(--g6);
        }

        /* Badges */
        .badge-tag {
            position: absolute;
            top: .5rem;
            left: .5rem;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            letter-spacing: .02em;
        }
        .badge-sale {
            background: #ef4444;
            color: #ffffff;
        }
        .badge-new {
            background: var(--y4);
            color: var(--g9);
        }
        .badge-crit {
            background: #f97316;
            color: #ffffff;
        }

        .out-of-stock-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,.75);
            backdrop-filter: blur(1px);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .out-of-stock-overlay span {
            font-size: .75rem;
            font-weight: 700;
            color: #ef4444;
            background: #fee2e2;
            padding: 4px 8px;
            border-radius: 4px;
            border: 1px solid #fca5a5;
        }

        /* Card Body */
        .card-body {
            padding: .75rem;
            display: flex;
            flex-direction: column;
            flex: 1;
        }
        .card-brand-cat {
            font-size: 10px;
            color: #9ca3af;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .card-title {
            font-size: .875rem;
            font-weight: 600;
            color: #0f172a;
            line-height: 1.35;
            margin-bottom: .5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.38rem;
        }
        .rating-row {
            display: flex;
            align-items: center;
            gap: 2px;
            margin-bottom: .5rem;
        }
        .rating-star {
            width: 10px;
            height: 10px;
            fill: #e2e8f0;
            stroke: #e2e8f0;
        }
        .rating-star.filled {
            fill: #facc15;
            stroke: #facc15;
        }
        .sold-count {
            font-size: 10px;
            color: #9ca3af;
            margin-left: 4px;
        }

        .card-footer {
            margin-top: auto;
        }
        .price-row {
            display: flex;
            align-items: baseline;
            gap: 4px;
            margin-bottom: 4px;
        }
        .current-price {
            font-size: 1rem;
            font-weight: 700;
            color: var(--g8);
        }
        .unit-text {
            font-size: .75rem;
            color: #9ca3af;
        }
        .original-price {
            font-size: .75rem;
            color: #9ca3af;
            text-decoration: line-through;
            margin-left: 4px;
        }

        .rx-badge {
            font-size: 10px;
            color: var(--g7);
            margin-bottom: .5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 2px;
        }
        .stock-alert {
            font-size: 10px;
            color: #f97316;
            margin-bottom: .5rem;
            font-weight: 600;
        }

        .btn-inquire {
            width: 100%;
            font-size: .75rem;
            font-weight: 600;
            padding: .5rem 0;
            border-radius: .5rem;
            border: 1px solid var(--g6);
            background: #ffffff;
            color: var(--g7);
            cursor: pointer;
            transition: all .15s;
            font-family: inherit;
        }
        .btn-inquire:hover {
            background: var(--g7);
            color: #ffffff;
            border-color: var(--g7);
        }
        .btn-inquire:disabled, .btn-inquire.disabled {
            background: #f1f5f9;
            color: #94a3b8;
            border-color: #e2e8f0;
            cursor: not-allowed;
        }

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center;
            padding: 5rem 1rem;
            color: #94a3b8;
            grid-column: 1 / -1;
            display: none;
        }
        .empty-state svg {
            width: 42px;
            height: 42px;
            stroke: #cbd5e1;
            margin: 0 auto .75rem;
            display: block;
        }
        .empty-state p {
            font-size: .9375rem;
            font-weight: 600;
            color: #64748b;
        }
        .empty-state button {
            margin-top: .75rem;
            font-size: .875rem;
            color: var(--g7);
            background: none;
            border: none;
            cursor: pointer;
            text-decoration: underline;
            font-weight: 600;
        }

        /* ── MODAL ── */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.5);
            backdrop-filter: blur(4px);
            z-index: 50;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .modal-card {
            background: #ffffff;
            border-radius: 1rem;
            max-width: 480px;
            width: 100%;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,.25);
            animation: modalPop .2s ease-out;
        }
        @keyframes modalPop {
            from { opacity: 0; transform: scale(.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .modal-header {
            background: linear-gradient(to right, var(--g8), var(--g7));
            color: #ffffff;
            padding: 1.25rem;
            border-bottom: 3px solid var(--y4);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-header h3 {
            font-size: 1.125rem;
            font-weight: 700;
            color: #ffffff;
        }
        .btn-close-modal {
            background: none;
            border: none;
            color: #bbf7d0;
            cursor: pointer;
            font-size: 1.25rem;
            line-height: 1;
            padding: 4px;
        }
        .btn-close-modal:hover { color: #ffffff; }
        .modal-body {
            padding: 1.25rem;
            font-size: .875rem;
            color: #374151;
            line-height: 1.6;
        }
        .modal-med-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 2px;
        }
        .modal-med-brand {
            font-size: .75rem;
            color: #64748b;
            margin-bottom: 1rem;
        }
        .modal-meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .75rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: .5rem;
            padding: .75rem 1rem;
            margin-bottom: 1rem;
        }
        .meta-field label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
            display: block;
        }
        .meta-field span {
            font-size: .9375rem;
            font-weight: 700;
            color: #1e293b;
        }
        .modal-notice {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: .75rem;
            border-radius: 0 .5rem .5rem 0;
            font-size: .75rem;
            color: #1e40af;
            margin-bottom: 1rem;
        }
        .modal-footer {
            padding: .75rem 1.25rem 1.25rem;
            display: flex;
            justify-content: flex-end;
            gap: .5rem;
        }
        .btn-modal-close {
            background: var(--g8);
            color: #ffffff;
            font-weight: 600;
            font-size: .875rem;
            padding: .5rem 1.25rem;
            border-radius: .5rem;
            border: none;
            cursor: pointer;
            transition: background .15s;
        }
        .btn-modal-close:hover { background: var(--g9); }

        /* ── GUEST STORE BANNER ── */
        .guest-banner {
            background: #fffbeb;
            border-bottom: 1px solid #fef08a;
            padding: .75rem 1.5rem;
        }
        .guest-banner-inner {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .guest-banner-text {
            font-size: .875rem;
            color: #78350f;
            line-height: 1.4;
        }
        .guest-banner-text strong {
            font-weight: 700;
            color: #92400e;
        }
        .btn-guest-signin {
            display: inline-flex;
            align-items: center;
            gap: .375rem;
            background: #facc15;
            color: #1f2937;
            font-size: .8125rem;
            font-weight: 700;
            padding: .45rem 1rem;
            border-radius: .5rem;
            text-decoration: none;
            transition: all .15s;
            box-shadow: 0 1px 2px rgba(0,0,0,.05);
            white-space: nowrap;
        }
        .btn-guest-signin:hover {
            background: #eab308;
            color: #111827;
            transform: translateY(-1px);
        }
        .btn-guest-signin svg {
            width: 15px;
            height: 15px;
            stroke-width: 2.5;
        }

        .btn-guest-inquire {
            width: 100%;
            font-size: .8125rem;
            font-weight: 600;
            padding: .5rem 0;
            border-radius: .625rem;
            border: 1.5px solid #16a34a;
            background: #ffffff;
            color: #166534;
            cursor: pointer;
            transition: all .15s;
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .375rem;
        }
        .btn-guest-inquire:hover {
            background: #f0fdf4;
            border-color: #15803d;
            color: #14532d;
        }
        .btn-guest-inquire svg {
            width: 14px;
            height: 14px;
            stroke-width: 2.2;
            flex-shrink: 0;
        }
        .btn-guest-inquire:disabled, .btn-guest-inquire.disabled {
            background: #f1f5f9;
            color: #94a3b8;
            border-color: #e2e8f0;
            cursor: not-allowed;
        }

        .rx-badge {
            font-size: 11px;
            color: #0284c7;
            margin-bottom: .5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .rx-badge svg {
            width: 12px;
            height: 12px;
            stroke: #0284c7;
            flex-shrink: 0;
        }
        .current-price {
            font-size: 1.125rem;
            font-weight: 800;
            color: #166534;
        }
        .unit-text {
            font-size: .8125rem;
            color: #94a3b8;
        }

        /* ── FOOTER ── */
        footer {
            background: var(--g9);
            border-top: 4px solid var(--y4);
            color: #86efac;
            padding: 2.5rem 1.5rem;
            text-align: center;
            font-size: .875rem;
            margin-top: auto;
        }
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
            <a href="{{ route('home') }}" class="nav-link">Home</a>
            <a href="{{ route('home') }}#chatbot" class="nav-link">AI Assistant</a>
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
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
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

{{-- STORE HEADER BAR --}}
<div class="store-header-bar">
    <div class="store-header-inner">
        <div class="store-title-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>
            </svg>
            <span class="store-title">USM Hospital Pharmacy</span>
            <span class="store-subtitle">&mdash; Product Catalog</span>
        </div>
        <div class="search-container">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input
                type="text"
                id="searchInput"
                class="search-input"
                placeholder="Search products..."
                autocomplete="off"
                oninput="onSearchInput(this.value)"
            />
            <button id="btnClearSearch" class="btn-clear-search" onclick="clearSearch()" title="Clear search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
    </div>
</div>

@guest
    <div class="guest-banner">
        <div class="guest-banner-inner">
            <p class="guest-banner-text">
                <strong>You are browsing as a guest.</strong> Sign in to inquire or request medicines.
            </p>
            <a href="{{ route('login') }}" class="btn-guest-signin">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                    <polyline points="10 17 15 12 10 7"/>
                    <line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
                Sign In
            </a>
        </div>
    </div>
@endguest

{{-- MAIN LAYOUT --}}
<div class="main-container">

    {{-- LEFT SIDEBAR --}}
    <aside class="sidebar">
        <p class="sidebar-heading">CATEGORIES</p>
        <ul class="category-list" id="categoryList">
            @php
                $categoriesMeta = [
                    'All' => ['icon' => '<path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>'],
                    'IV Fluids' => ['icon' => '<path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/>'],
                    'Injectables' => ['icon' => '<path d="M4.5 3h15"/><path d="M6 3v16a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V3"/><path d="M6 14h12"/>'],
                    'Oral Medications' => ['icon' => '<path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z"/><path d="m8.5 8.5 7 7"/>'],
                    'Wound Care' => ['icon' => '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>'],
                    'Respiratory' => ['icon' => '<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>'],
                    'Consumables' => ['icon' => '<line x1="16.5" y1="9.4" x2="7.5" y2="4.21"/><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>'],
                    'Supplements' => ['icon' => '<path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>'],
                ];
                $inStockMeds = $medicines->where('available_stock', '>', 0);
            @endphp

            @foreach ($categoriesMeta as $catName => $meta)
                @php
                    $count = $catName === 'All' 
                        ? $inStockMeds->count() 
                        : $inStockMeds->where('category', $catName)->count();
                @endphp
                <li>
                    <button
                        type="button"
                        class="cat-btn {{ $catName === 'All' ? 'active' : '' }}"
                        data-category="{{ $catName }}"
                        onclick="selectCategory('{{ $catName }}')"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            {!! $meta['icon'] !!}
                        </svg>
                        <span>{{ $catName }}</span>
                        <span class="cat-count">{{ $count }}</span>
                    </button>
                </li>
            @endforeach
        </ul>

        <div class="sidebar-notes">
            <div class="note-item">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
                <span>All items FDA registered</span>
            </div>
            <div class="note-item">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="3" width="15" height="13"/>
                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/>
                    <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
                <span>Pick up at USM Pharmacy, Bldg A</span>
            </div>
            <div class="note-item">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
                <span>Rx items require valid prescription</span>
            </div>
        </div>
    </aside>

    {{-- RIGHT CATALOG COLUMN --}}
    <main class="catalog-area">

        {{-- TOP RESULTS HEADER --}}
        <div class="results-header">
            <p class="results-text" id="resultsLabel">
                Showing <strong id="resCount">{{ $inStockMeds->count() }}</strong> <span id="resContext">products</span>
            </p>
            <button type="button" id="btnResetFilters" class="btn-reset-filters" onclick="resetFilters()">
                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Clear
            </button>
        </div>

        {{-- MOBILE CATEGORY PILLS SCROLL --}}
        <div class="mobile-cat-scroll">
            @foreach (array_keys($categoriesMeta) as $catName)
                <button
                    type="button"
                    class="mobile-cat-pill {{ $catName === 'All' ? 'active' : '' }}"
                    data-category="{{ $catName }}"
                    onclick="selectCategory('{{ $catName }}')"
                >
                    {{ $catName }}
                </button>
            @endforeach
        </div>

        {{-- PRODUCT GRID --}}
        <div class="products-grid" id="productsGrid">
            @foreach ($medicines as $med)
                @php
                    $isOutOfStock = ($med['available_stock'] ?? 0) <= 0;
                    $badge = $med['badge'] ?? null;
                    $brand = $med['brand'] ?? 'USM Health';
                    $category = $med['category'] ?? 'General';
                    $rating = (float) ($med['rating'] ?? 4.8);
                    $sold = $med['sold'] ?? 100;
                    $unit = $med['unit'] ?? 'pc';
                    $unitPrice = (float) ($med['unit_price'] ?? 0);
                    $origPrice = isset($med['original_price']) ? (float) $med['original_price'] : null;
                    $rx = !empty($med['rx']);
                    $stock = (int) ($med['available_stock'] ?? 0);
                @endphp
                <div
                    class="product-card {{ $isOutOfStock ? 'out-of-stock' : '' }}"
                    data-id="{{ $med['id'] }}"
                    data-name="{{ strtolower($med['name']) }}"
                    data-brand="{{ strtolower($brand) }}"
                    data-generic="{{ strtolower($med['generic_name'] ?? '') }}"
                    data-category="{{ $category }}"
                    data-stock="{{ $stock }}"
                    data-price="{{ $unitPrice }}"
                    data-unit="{{ $unit }}"
                    data-desc="{{ $med['description'] ?? '' }}"
                    data-rx="{{ $rx ? '1' : '0' }}"
                >
                    {{-- Image Area --}}
                    <div class="card-img-area">
                        <div class="pill-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z"/>
                                <path d="m8.5 8.5 7 7"/>
                            </svg>
                        </div>

                        @if ($badge === 'SALE')
                            <span class="badge-tag badge-sale">SALE</span>
                        @elseif ($badge === 'NEW')
                            <span class="badge-tag badge-new">NEW</span>
                        @elseif ($badge === 'CRIT')
                            <span class="badge-tag badge-crit">CRIT</span>
                        @endif

                        @if ($isOutOfStock)
                            <div class="out-of-stock-overlay">
                                <span>Out of Stock</span>
                            </div>
                        @endif
                    </div>

                    {{-- Card Info --}}
                    <div class="card-body">
                        <p class="card-brand-cat">{{ $brand }} &middot; {{ $category }}</p>
                        <h4 class="card-title">{{ $med['name'] }}</h4>

                        <div class="rating-row">
                            @for ($s = 1; $s <= 5; $s++)
                                <svg class="rating-star {{ $s <= round($rating) ? 'filled' : '' }}" viewBox="0 0 24 24">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                            @endfor
                            <span class="sold-count">({{ $sold }})</span>
                        </div>

                        <div class="card-footer">
                            <div class="price-row">
                                <span class="current-price">&#8369;{{ number_format($unitPrice, 2) }}</span>
                                <span class="unit-text">/ {{ $unit }}</span>
                                @if ($origPrice)
                                    <span class="original-price">&#8369;{{ number_format($origPrice, 2) }}</span>
                                @endif
                            </div>

                            @if ($rx)
                                <p class="rx-badge">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                    Prescription required
                                </p>
                            @endif

                            @if ($stock > 0 && $stock < 100)
                                <p class="stock-alert">Only {{ $stock }} left</p>
                            @endif

                            @guest
                                <button
                                    type="button"
                                    class="btn-guest-inquire {{ $isOutOfStock ? 'disabled' : '' }}"
                                    {{ $isOutOfStock ? 'disabled' : '' }}
                                    onclick="openInquireModal({{ $med['id'] }})"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                                        <polyline points="10 17 15 12 10 7"/>
                                        <line x1="15" y1="12" x2="3" y2="12"/>
                                    </svg>
                                    {{ $isOutOfStock ? 'Unavailable' : 'Sign in to Inquire' }}
                                </button>
                            @else
                                <button
                                    type="button"
                                    class="btn-inquire {{ $isOutOfStock ? 'disabled' : '' }}"
                                    {{ $isOutOfStock ? 'disabled' : '' }}
                                    onclick="openInquireModal({{ $med['id'] }})"
                                >
                                    {{ $isOutOfStock ? 'Unavailable' : 'Inquire at Pharmacy' }}
                                </button>
                            @endguest
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Empty State --}}
            <div class="empty-state" id="emptyState">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="16.5" y1="9.4" x2="7.5" y2="4.21"/>
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                    <line x1="12" y1="22.08" x2="12" y2="12"/>
                </svg>
                <p>No products found</p>
                <button type="button" onclick="resetFilters()">Clear search</button>
            </div>
        </div>
    </main>
</div>

{{-- INQUIRE MODAL --}}
<div class="modal-backdrop" id="inquireModal" onclick="closeModalOnBackdrop(event)">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Medicine Details & Inquiries</h3>
            <button type="button" class="btn-close-modal" onclick="closeInquireModal()" title="Close">&times;</button>
        </div>
        <div class="modal-body">
            <h4 class="modal-med-title" id="modalMedName">Normal Saline 0.9% 1L</h4>
            <p class="modal-med-brand" id="modalMedBrand">Baxter &middot; IV Fluids</p>

            <div class="modal-meta-grid">
                <div class="meta-field">
                    <label>Pharmacy Price</label>
                    <span id="modalMedPrice">&#8369;85.00</span>
                </div>
                <div class="meta-field">
                    <label>Current Stock</label>
                    <span id="modalMedStock" style="color: var(--g7);">300 bags</span>
                </div>
            </div>

            <p id="modalMedDesc" style="margin-bottom: 1rem; color: #4b5563; font-size: .8125rem;">
                Isotonic crystalloid solution for fluid and electrolyte replenishment.
            </p>

            <div class="modal-notice" id="modalRxNotice">
                <strong>&#9764; Prescription Policy:</strong> This medication requires an active prescription from a licensed physician or the USM University Clinic.
            </div>

            <div style="font-size: .8125rem; color: #64748b; background: #f8fafc; border-radius: .5rem; padding: .75rem; border: 1px solid #e2e8f0;">
                <p style="font-weight: 600; color: #1e293b; margin-bottom: 2px;">Where to claim:</p>
                <p>USM Health Services Pharmacy (Building A)</p>
                <p>Hours: Mon &ndash; Fri, 8:00 AM &ndash; 5:00 PM</p>
                <p style="margin-top: 4px; font-size: 11px; color: #94a3b8;">Please present your Student/Staff ID at the dispensing counter.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-modal-close" onclick="closeInquireModal()">Close</button>
        </div>
    </div>
</div>

{{-- GUEST ACCESS REQUIRED MODAL --}}
@guest
<div class="modal-backdrop" id="guestAccessModal" onclick="closeGuestModalOnBackdrop(event)">
    <div class="modal-card">
        <div class="modal-header" style="background: linear-gradient(135deg, #14532d, #166534);">
            <h3 style="color:#ffffff;display:flex;align-items:center;gap:.5rem">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#facc15" stroke-width="2.2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                Sign In Required
            </h3>
            <button type="button" class="btn-close-modal" onclick="closeGuestModal()" style="color:#ffffff">&times;</button>
        </div>
        <div class="modal-body" style="text-align: center; padding: 2rem 1.5rem;">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: #fef3c7; border: 2px solid #fde68a; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
                <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="#b45309" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <h4 style="font-size: 1.125rem; font-weight: 800; color: #1e293b; margin-bottom: .5rem;" id="guestModalTitle">USM Account Required to Inquire</h4>
            <p style="font-size: .875rem; color: #64748b; line-height: 1.6; max-width: 420px; margin: 0 auto 1.5rem;">
                You are currently browsing as a guest. Please sign in with your verified USM Student or Staff account to submit inquiries, request prescriptions, or coordinate with on-duty pharmacists.
            </p>
            <div style="display: flex; flex-direction: column; gap: .75rem; max-width: 300px; margin: 0 auto;">
                <a href="{{ route('login') }}" class="btn-guest-signin" style="justify-content: center; padding: .625rem 1.25rem; font-size: .875rem;">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    Sign In with USM Account
                </a>
                <a href="{{ route('register') }}" class="btn-store-register" style="justify-content: center;">
                    Create Free Patient Account
                </a>
            </div>
        </div>
        <div class="modal-footer" style="justify-content: center; background: #f8fafc;">
            <button type="button" class="btn-modal-close" onclick="closeGuestModal()">Continue Browsing</button>
        </div>
    </div>
</div>
@endguest

{{-- FOOTER --}}
<footer>
    <p>&copy; 2026 University of Southern Mindanao &mdash; Pharmacy Patient Portal. All rights reserved.</p>
</footer>

<script>
    const isGuest = @json(auth()->guest());
    let activeCategory = 'All';
    let searchQuery = '';

    const cards = Array.from(document.querySelectorAll('.product-card'));
    const searchInput = document.getElementById('searchInput');
    const btnClearSearch = document.getElementById('btnClearSearch');
    const btnResetFilters = document.getElementById('btnResetFilters');
    const resCount = document.getElementById('resCount');
    const resContext = document.getElementById('resContext');
    const emptyState = document.getElementById('emptyState');
    const guestAccessModal = document.getElementById('guestAccessModal');

    function selectCategory(category) {
        activeCategory = category;

        // Update desktop buttons
        document.querySelectorAll('.cat-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.category === category);
        });

        // Update mobile pills
        document.querySelectorAll('.mobile-cat-pill').forEach(pill => {
            pill.classList.toggle('active', pill.dataset.category === category);
        });

        applyFilters();
    }

    function onSearchInput(val) {
        searchQuery = val.trim();
        btnClearSearch.style.display = searchQuery ? 'flex' : 'none';
        applyFilters();
    }

    function clearSearch() {
        searchInput.value = '';
        searchQuery = '';
        btnClearSearch.style.display = 'none';
        applyFilters();
    }

    function resetFilters() {
        clearSearch();
        selectCategory('All');
    }

    function applyFilters() {
        const q = searchQuery.toLowerCase();
        let visibleCount = 0;

        cards.forEach(card => {
            const name = card.dataset.name || '';
            const generic = card.dataset.generic || '';
            const brand = card.dataset.brand || '';
            const category = card.dataset.category || '';
            const stock = parseInt(card.dataset.stock, 10) || 0;

            const matchesCategory = activeCategory === 'All' || category === activeCategory;
            const matchesSearch = !q || name.includes(q) || generic.includes(q) || brand.includes(q) || category.toLowerCase().includes(q);
            const matchesStock = searchQuery.trim() ? true : stock > 0;

            const isVisible = matchesCategory && matchesSearch && matchesStock;
            card.style.display = isVisible ? 'flex' : 'none';

            if (isVisible) {
                visibleCount++;
            }
        });

        // Update results label
        resCount.textContent = visibleCount;
        if (searchQuery) {
            resContext.textContent = `results for "${searchQuery}"`;
        } else if (activeCategory === 'All') {
            resContext.textContent = 'products';
        } else {
            resContext.textContent = `in ${activeCategory}`;
        }

        // Toggle clear link
        btnResetFilters.style.display = (searchQuery || activeCategory !== 'All') ? 'inline-flex' : 'none';

        // Toggle empty state
        emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
    }

    // Modal Logic
    const inquireModal = document.getElementById('inquireModal');
    const modalMedName = document.getElementById('modalMedName');
    const modalMedBrand = document.getElementById('modalMedBrand');
    const modalMedPrice = document.getElementById('modalMedPrice');
    const modalMedStock = document.getElementById('modalMedStock');
    const modalMedDesc = document.getElementById('modalMedDesc');
    const modalRxNotice = document.getElementById('modalRxNotice');

    function openInquireModal(id) {
        const card = cards.find(c => c.dataset.id == id);
        if (!card) return;

        const name = card.querySelector('.card-title').textContent.trim();

        if (isGuest) {
            const guestTitle = document.getElementById('guestModalTitle');
            if (guestTitle) {
                guestTitle.textContent = `USM Account Required: ${name}`;
            }
            if (guestAccessModal) {
                guestAccessModal.style.display = 'flex';
            }
            return;
        }

        const brandCat = card.querySelector('.card-brand-cat').textContent.trim();
        const price = card.dataset.price ? `₱${parseFloat(card.dataset.price).toFixed(2)} / ${card.dataset.unit}` : '';
        const stock = parseInt(card.dataset.stock, 10) || 0;
        const desc = card.dataset.desc || 'Available at USM Health Services Pharmacy.';
        const rx = card.dataset.rx === '1';

        modalMedName.textContent = name;
        modalMedBrand.textContent = brandCat;
        modalMedPrice.textContent = price;
        modalMedStock.textContent = stock > 0 ? `${stock} in stock` : 'Out of stock';
        modalMedStock.style.color = stock > 0 ? 'var(--g7)' : '#ef4444';
        modalMedDesc.textContent = desc;
        modalRxNotice.style.display = rx ? 'block' : 'none';

        inquireModal.style.display = 'flex';
    }

    function closeInquireModal() {
        inquireModal.style.display = 'none';
    }

    function closeModalOnBackdrop(e) {
        if (e.target === inquireModal) {
            closeInquireModal();
        }
    }

    function closeGuestModal() {
        if (guestAccessModal) {
            guestAccessModal.style.display = 'none';
        }
    }

    function closeGuestModalOnBackdrop(e) {
        if (e.target === guestAccessModal) {
            closeGuestModal();
        }
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (inquireModal && inquireModal.style.display === 'flex') {
                closeInquireModal();
            }
            if (guestAccessModal && guestAccessModal.style.display === 'flex') {
                closeGuestModal();
            }
        }
    });

    // Run initial filter to respect initial state
    applyFilters();
</script>
</body>
</html>
