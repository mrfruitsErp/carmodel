<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ERP Ortofrutta</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --green:    #2d6a4f;
            --green-l:  #40916c;
            --green-xl: #d8f3dc;
            --accent:   #f4a261;
            --dark:     #1b2d27;
            --text:     #2c3e35;
            --muted:    #7a9e8e;
            --bg:       #f5f7f5;
            --card:     #ffffff;
            --border:   #e2ebe5;
            --sidebar-w: 230px;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--dark);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
        }

        .sidebar-logo {
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-logo a {
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-icon {
            width: 36px; height: 36px;
            background: var(--green-l);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }

        .logo-text {
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.3px;
        }

        .logo-sub {
            font-size: 10px;
            color: var(--muted);
            font-weight: 400;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .sidebar-nav {
            padding: 16px 10px;
            flex: 1;
            overflow-y: auto;
        }

        .nav-section {
            margin-bottom: 24px;
        }

        .nav-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--muted);
            padding: 0 10px;
            margin-bottom: 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            text-decoration: none;
            color: rgba(255,255,255,0.65);
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.15s;
            margin-bottom: 2px;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.07);
            color: #fff;
        }

        .nav-item.active {
            background: var(--green-l);
            color: #fff;
        }

        .nav-item .icon { font-size: 16px; width: 20px; text-align: center; }

        .sidebar-footer {
            padding: 16px 10px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        /* ── MAIN ── */
        .main-wrap {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── TOPBAR ── */
        .topbar {
            height: 60px;
            background: var(--card);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--text);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-home {
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--green-xl);
            color: var(--green);
            border: none;
            border-radius: 8px;
            padding: 7px 14px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s;
            font-family: 'DM Sans', sans-serif;
        }

        .btn-home:hover {
            background: var(--green-l);
            color: #fff;
        }

        .btn-logout {
            display: flex;
            align-items: center;
            gap: 6px;
            background: transparent;
            color: var(--muted);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 7px 14px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s;
            font-family: 'DM Sans', sans-serif;
        }

        .btn-logout:hover {
            border-color: #e74c3c;
            color: #e74c3c;
        }

        /* ── CONTENT ── */
        .content {
            padding: 28px;
            flex: 1;
        }

        /* ── FLASH MESSAGES ── */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .alert-success { background: var(--green-xl); color: var(--green); border: 1px solid #b7e4c7; }
        .alert-error   { background: #fde8e8; color: #c0392b; border: 1px solid #f5c6c6; }

        /* ── UTILITY CLASSES ── */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--dark);
            letter-spacing: -0.5px;
        }

        .page-sub {
            font-size: 13px;
            color: var(--muted);
            margin-top: 2px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.15s;
            font-family: 'DM Sans', sans-serif;
        }

        .btn-primary {
            background: var(--green-l);
            color: #fff;
        }

        .btn-primary:hover { background: var(--green); }

        .btn-secondary {
            background: var(--card);
            color: var(--text);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover { border-color: var(--green-l); color: var(--green-l); }

        .btn-danger {
            background: #fde8e8;
            color: #c0392b;
            border: 1px solid #f5c6c6;
        }

        .btn-danger:hover { background: #c0392b; color: #fff; }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        table th {
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: var(--muted);
            padding: 10px 14px;
            border-bottom: 1px solid var(--border);
        }

        table td {
            padding: 11px 14px;
            border-bottom: 1px solid var(--border);
            color: var(--text);
        }

        table tr:last-child td { border-bottom: none; }
        table tbody tr:hover { background: var(--bg); }

        input, select, textarea {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 13.5px;
            font-family: 'DM Sans', sans-serif;
            color: var(--text);
            background: var(--card);
            transition: border 0.15s;
            outline: none;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--green-l);
            box-shadow: 0 0 0 3px rgba(64,145,108,0.1);
        }

        label {
            font-size: 12px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.4px;
            display: block;
            margin-bottom: 5px;
        }

        .form-group { margin-bottom: 16px; }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-wrap { margin-left: 0; }
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <a href="{{ url('/dashboard') }}">
            <div class="logo-icon">🥦</div>
            <div>
                <div class="logo-text">OrtoPro ERP</div>
                <div class="logo-sub">Gestionale B2B</div>
            </div>
        </a>
    </div>

    <nav class="sidebar-nav">

        <div class="nav-section">
            <div class="nav-label">Principale</div>
            <a href="{{ url('/dashboard') }}" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                <span class="icon">📊</span> Dashboard
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-label">Vendite</div>
            <a href="{{ url('/documents') }}" class="nav-item {{ request()->is('documents*') ? 'active' : '' }}">
                <span class="icon">📄</span> Documenti
            </a>
            <a href="{{ url('/clients') }}" class="nav-item {{ request()->is('clients*') ? 'active' : '' }}">
                <span class="icon">👥</span> Clienti
            </a>
            <a href="{{ url('/payments') }}" class="nav-item {{ request()->is('payments*') ? 'active' : '' }}">
                <span class="icon">💶</span> Pagamenti
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-label">Magazzino</div>
            <a href="{{ url('/products') }}" class="nav-item {{ request()->is('products*') ? 'active' : '' }}">
                <span class="icon">🧺</span> Prodotti
            </a>
            <a href="{{ url('/magazzino') }}" class="nav-item {{ request()->is('magazzino*') ? 'active' : '' }}">
                <span class="icon">📦</span> Magazzino
            </a>
            <a href="{{ url('/movimenti-magazzino') }}" class="nav-item {{ request()->is('movimenti*') ? 'active' : '' }}">
                <span class="icon">🔄</span> Movimenti
            </a>
            <a href="{{ url('/carico-magazzino') }}" class="nav-item {{ request()->is('carico*') ? 'active' : '' }}">
                <span class="icon">🚛</span> Carico Merce
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-label">Fornitori</div>
            <a href="{{ url('/suppliers') }}" class="nav-item {{ request()->is('suppliers*') ? 'active' : '' }}">
                <span class="icon">🏭</span> Fornitori
            </a>
            <a href="{{ url('/purchases') }}" class="nav-item {{ request()->is('purchases*') ? 'active' : '' }}">
                <span class="icon">🛒</span> Acquisti
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-label">Logistica</div>
            <a href="{{ url('/routes') }}" class="nav-item {{ request()->is('routes*') ? 'active' : '' }}">
                <span class="icon">🗺️</span> Giri Consegna
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-label">Report</div>
            <a href="{{ url('/report-vendite') }}" class="nav-item {{ request()->is('report-vendite*') ? 'active' : '' }}">
                <span class="icon">📈</span> Vendite
            </a>
            <a href="{{ url('/report/prodotti') }}" class="nav-item {{ request()->is('report/prodotti*') ? 'active' : '' }}">
                <span class="icon">📉</span> Prodotti
            </a>
            <a href="{{ url('/report/clienti') }}" class="nav-item {{ request()->is('report/clienti*') ? 'active' : '' }}">
                <span class="icon">👤</span> Clienti
            </a>
        </div>

    </nav>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item" style="width:100%; border:none; cursor:pointer; background:transparent;">
                <span class="icon">🚪</span> Logout
            </button>
        </form>
    </div>
</aside>

<!-- MAIN -->
<div class="main-wrap">

    <header class="topbar">
        <span class="topbar-title">
            @yield('page-title', 'ERP Ortofrutta')
        </span>
        <div class="topbar-right">
            <a href="{{ url('/dashboard') }}" class="btn-home">
                🏠 Dashboard
            </a>
        </div>
    </header>

    <main class="content">
        @if(session('success'))
            <div class="alert alert-success">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">❌ {{ session('error') }}</div>
        @endif

        @yield('content')
    </main>

</div>

</body>
</html>
