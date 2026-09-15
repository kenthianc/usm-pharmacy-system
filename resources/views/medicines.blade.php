<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Browse Medicines — USM Pharmacy</title>
    <meta name="description" content="Check real-time medicine availability at USM Health Services Pharmacy.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --navy:#0f3460; --navy-l:#1a4a7a; --teal:#0d9488; --teal-l:#ccfbf1;
            --cream:#fafaf9; --white:#fff;
            --g100:#f1f5f9; --g200:#e2e8f0; --g300:#cbd5e1;
            --g400:#94a3b8; --g500:#64748b; --g700:#334155; --g900:#0f172a;
            --green:#16a34a; --amber:#d97706; --red:#dc2626;
        }
        body { font-family:'Inter',sans-serif; background:var(--cream); color:var(--g900); -webkit-font-smoothing:antialiased; }

        /* NAV */
        .nav { position:sticky;top:0;z-index:50;background:rgba(255,255,255,.92);backdrop-filter:blur(16px);border-bottom:1px solid var(--g200); }
        .nav-inner { max-width:1140px;margin:0 auto;padding:0 1.5rem;height:64px;display:flex;align-items:center;justify-content:space-between;gap:2rem; }
        .logo { display:flex;align-items:center;gap:10px;text-decoration:none; }
        .logo-mark { width:38px;height:38px;border-radius:10px;background:var(--navy);display:flex;align-items:center;justify-content:center;flex-shrink:0; }
        .logo-mark svg { width:20px;height:20px;stroke:white;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round; }
        .logo-name { font-size:.95rem;font-weight:700;color:var(--navy);line-height:1.1; }
        .logo-sub  { font-size:.7rem;color:var(--g400); }
        .nav-right { display:flex;align-items:center;gap:.5rem; }
        .nav-link { padding:.45rem 1rem;border-radius:8px;font-size:.875rem;font-weight:500;color:var(--g700);text-decoration:none;transition:background .15s; }
        .nav-link:hover { background:var(--g100); }
        .nav-btn { padding:.45rem 1.1rem;border-radius:8px;font-size:.875rem;font-weight:600;background:var(--navy);color:white;text-decoration:none;border:none;cursor:pointer;transition:background .15s; }
        .nav-btn:hover { background:var(--navy-l); }

        /* PAGE HEADER */
        .page-header { background:linear-gradient(135deg,#f0fdf9 0%,var(--cream) 100%);border-bottom:1px solid var(--g200);padding:2.5rem 1.5rem; }
        .page-header-inner { max-width:1140px;margin:0 auto; }
        .breadcrumb { font-size:.8rem;color:var(--g400);margin-bottom:.75rem; }
        .breadcrumb a { color:var(--teal);text-decoration:none;font-weight:500; }
        .breadcrumb a:hover { text-decoration:underline; }
        .page-header h1 { font-size:clamp(1.6rem,3vw,2.25rem);font-weight:800;color:var(--g900);letter-spacing:-.02em;margin-bottom:.5rem; }
        .page-header p { color:var(--g500);font-size:.95rem; }

        /* CONTROLS */
        .controls { max-width:1140px;margin:2rem auto;padding:0 1.5rem;display:flex;flex-wrap:wrap;gap:1rem;align-items:center;justify-content:space-between; }
        .search-wrap { position:relative;flex:1;min-width:220px;max-width:380px; }
        .search-wrap svg { position:absolute;left:.85rem;top:50%;transform:translateY(-50%);width:16px;height:16px;stroke:var(--g400);fill:none;stroke-width:2;stroke-linecap:round; }
        .search-input { width:100%;padding:.7rem 1rem .7rem 2.5rem;border:1.5px solid var(--g200);border-radius:10px;font-family:inherit;font-size:.875rem;color:var(--g900);outline:none;transition:border-color .15s;background:white; }
        .search-input:focus { border-color:var(--teal); }
        .search-input::placeholder { color:var(--g400); }
        .filter-chips { display:flex;gap:.5rem;flex-wrap:wrap; }
        .fchip { padding:.35rem .9rem;border-radius:999px;font-size:.78rem;font-weight:600;border:1.5px solid var(--g200);background:white;color:var(--g500);cursor:pointer;transition:all .15s;white-space:nowrap; }
        .fchip:hover,.fchip.active { background:var(--navy);border-color:var(--navy);color:white; }
        .results-count { font-size:.83rem;color:var(--g400);padding:0 1.5rem;max-width:1140px;margin:0 auto .75rem; }

        /* GRID */
        .grid { max-width:1140px;margin:0 auto;padding:0 1.5rem 4rem;display:grid;gap:1.25rem;grid-template-columns:repeat(auto-fill,minmax(230px,1fr)); }
        .med-card { background:white;border-radius:16px;border:1px solid var(--g200);padding:1.25rem;transition:box-shadow .2s,transform .2s;display:flex;flex-direction:column;gap:.75rem; }
        .med-card:hover { box-shadow:0 8px 32px rgba(15,52,96,.1);transform:translateY(-3px); }
        .med-card-top { display:flex;align-items:flex-start;justify-content:space-between;gap:.5rem; }
        .med-icon { width:44px;height:44px;border-radius:11px;background:var(--teal-l);display:flex;align-items:center;justify-content:center;flex-shrink:0; }
        .med-icon svg { width:22px;height:22px;stroke:var(--teal);fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round; }
        .stock-pill { font-size:.68rem;font-weight:700;padding:.25rem .65rem;border-radius:999px;white-space:nowrap; }
        .stock-pill.in  { background:#dcfce7;color:var(--green); }
        .stock-pill.low { background:#fef9c3;color:var(--amber); }
        .stock-pill.out { background:#fee2e2;color:var(--red); }
        .med-name { font-size:.9rem;font-weight:700;color:var(--g900);line-height:1.3; }
        .med-generic { font-size:.77rem;color:var(--g500);margin-top:.15rem; }
        .med-cat { display:inline-block;font-size:.7rem;font-weight:500;background:var(--g100);color:var(--g500);padding:.2rem .65rem;border-radius:999px;margin-top:.25rem; }
        .med-footer { display:flex;align-items:center;justify-content:space-between;margin-top:auto;padding-top:.75rem;border-top:1px solid var(--g100); }
        .med-price { font-size:1rem;font-weight:700;color:var(--navy); }
        .med-price small { font-size:.72rem;font-weight:400;color:var(--g400);margin-left:2px; }
        .med-qty { font-size:.75rem;color:var(--g400); }
        .no-results { grid-column:1/-1;padding:3.5rem;text-align:center; }
        .no-results svg { width:48px;height:48px;stroke:var(--g300);fill:none;stroke-width:1.5;margin:0 auto 1rem;display:block; }
        .no-results p { color:var(--g400);font-size:.9rem; }

        @media(max-width:600px) { .controls { flex-direction:column;align-items:stretch; } .search-wrap { max-width:100%; } }
    </style>
</head>
<body>

<nav class="nav">
    <div class="nav-inner">
        <a href="{{ route('home') }}" class="logo">
            <div class="logo-mark">
                <svg viewBox="0 0 24 24"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
            </div>
            <div>
                <div class="logo-name">USM Pharmacy</div>
                <div class="logo-sub">Health Services</div>
            </div>
        </a>
        <div class="nav-right">
            @auth
                <a href="{{ route('patient.dashboard') }}" class="nav-link">My Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button class="nav-link" type="submit" style="cursor:pointer;background:none;border:none;font-family:inherit;">Log out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="nav-link">Log in</a>
                <a href="{{ route('register') }}" class="nav-btn">Create account</a>
            @endauth
        </div>
    </div>
</nav>

<div class="page-header">
    <div class="page-header-inner">
        <div class="breadcrumb"><a href="{{ route('home') }}">Home</a> &rsaquo; Browse Medicines</div>
        <h1>Available Medicines</h1>
        <p>Real-time availability from USM Health Services Pharmacy &mdash; updated live.</p>
    </div>
</div>

<div class="controls">
    <div class="search-wrap">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" class="search-input" id="searchInput" placeholder="Search by name, generic, or category…" oninput="applyFilters()" />
    </div>
    <div class="filter-chips">
        <button class="fchip active" data-cat="all" onclick="setCategory('all',this)">All</button>
        @foreach($medicines->pluck('category')->filter()->unique()->sort()->values() as $cat)
            <button class="fchip" data-cat="{{ $cat }}" onclick="setCategory('{{ $cat }}',this)">{{ $cat }}</button>
        @endforeach
    </div>
</div>
<div class="results-count" id="resultsCount">Showing {{ $medicines->count() }} medicines</div>

<div class="grid" id="medGrid">
    @foreach ($medicines as $med)
        @php
            $stockClass = $med['available_stock'] === 0 ? 'out' : ($med['available_stock'] <= 30 ? 'low' : 'in');
            $stockLabel = $med['available_stock'] === 0 ? 'Out of Stock' : ($med['available_stock'] <= 30 ? 'Low Stock' : 'In Stock');
        @endphp
        <div class="med-card"
             data-name="{{ strtolower($med['name']) }} {{ strtolower($med['generic_name'] ?? '') }}"
             data-cat="{{ $med['category'] ?? '' }}">
            <div class="med-card-top">
                <div class="med-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <span class="stock-pill {{ $stockClass }}">{{ $stockLabel }}</span>
            </div>
            <div>
                <div class="med-name">{{ $med['name'] }}</div>
                @if (!empty($med['generic_name']))
                    <div class="med-generic">{{ $med['generic_name'] }}</div>
                @endif
                @if (!empty($med['category']))
                    <span class="med-cat">{{ $med['category'] }}</span>
                @endif
            </div>
            <div class="med-footer">
                <div class="med-price">₱{{ number_format($med['unit_price'], 2) }}<small>/ {{ $med['unit'] ?? 'pc' }}</small></div>
                @if ($med['available_stock'] > 0)
                    <div class="med-qty">{{ $med['available_stock'] }} left</div>
                @endif
            </div>
        </div>
    @endforeach
    <div class="no-results" id="noResults" style="display:none">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <p>No medicines found. Try a different search or filter.</p>
    </div>
</div>

<script>
let activeCategory = 'all';

function setCategory(cat, el) {
    activeCategory = cat;
    document.querySelectorAll('.fchip').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    applyFilters();
}

function applyFilters() {
    const q = document.getElementById('searchInput').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.med-card');
    let count = 0;
    cards.forEach(card => {
        const nameMatch = q === '' || card.dataset.name.includes(q);
        const catMatch  = activeCategory === 'all' || card.dataset.cat === activeCategory;
        const show = nameMatch && catMatch;
        card.style.display = show ? '' : 'none';
        if (show) count++;
    });
    document.getElementById('noResults').style.display = count === 0 ? 'block' : 'none';
    document.getElementById('resultsCount').textContent = `Showing ${count} medicine${count !== 1 ? 's' : ''}`;
}
</script>
</body>
</html>
