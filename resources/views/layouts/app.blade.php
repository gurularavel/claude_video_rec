<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'VideoSupport') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        :root {
            --bg-base:    #070d18;
            --bg-surface: #0f1924;
            --bg-elevated:#152033;
            --border:     rgba(6, 182, 212, 0.12);
            --border-hover: rgba(6, 182, 212, 0.3);
            --cyan:       #06b6d4;
            --cyan-dim:   rgba(6, 182, 212, 0.15);
            --purple:     #8b5cf6;
            --success:    #10b981;
            --warning:    #f59e0b;
            --danger:     #ef4444;
            --text-primary: #e2e8f0;
            --text-muted:   #64748b;
            --text-subtle:  #94a3b8;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            background: var(--bg-base);
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            font-weight: 400;
            min-height: 100vh;
        }

        /* ─── Navbar ─────────────────────────────────── */
        .app-nav {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(7, 13, 24, 0.92);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-brand {
            font-family: 'Inter', sans-serif;
            font-weight: 800;
            font-size: 1.2rem;
            color: var(--cyan);
            text-decoration: none;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-brand svg {
            width: 22px;
            height: 22px;
            fill: var(--cyan);
        }

        .nav-brand span {
            color: var(--text-primary);
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link-item {
            color: var(--text-subtle);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 6px 14px;
            border-radius: 6px;
            transition: color 0.2s, background 0.2s;
        }

        .nav-link-item:hover {
            color: var(--text-primary);
            background: rgba(255,255,255,0.05);
        }

        .nav-link-item.active {
            color: var(--cyan);
        }

        .nav-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--cyan-dim);
            color: var(--cyan);
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: 8px;
            padding: 6px 16px;
            font-size: 0.875rem;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .nav-btn:hover {
            background: rgba(6, 182, 212, 0.25);
            color: var(--cyan);
        }

        .nav-btn.danger {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border-color: rgba(239, 68, 68, 0.3);
        }

        .nav-btn.danger:hover {
            background: rgba(239, 68, 68, 0.2);
        }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 12px 4px 4px;
            border-radius: 30px;
            border: 1px solid var(--border);
            background: var(--bg-surface);
        }

        .nav-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--cyan), var(--purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .nav-username {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-primary);
        }

        .nav-divider {
            width: 1px;
            height: 20px;
            background: var(--border);
            margin: 0 4px;
        }

        /* ─── Dropdown ─────────────────────────────── */
        .nav-dropdown-wrap {
            position: relative;
        }

        .nav-dropdown-wrap:hover .nav-dropdown-menu {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .nav-dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 6px;
            min-width: 180px;
            opacity: 0;
            pointer-events: none;
            transform: translateY(-8px);
            transition: opacity 0.2s, transform 0.2s;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            z-index: 200;
        }

        .dropdown-item-link {
            display: block;
            padding: 8px 14px;
            border-radius: 6px;
            color: var(--text-subtle);
            text-decoration: none;
            font-size: 0.875rem;
            transition: background 0.15s, color 0.15s;
        }

        .dropdown-item-link:hover {
            background: var(--bg-elevated);
            color: var(--text-primary);
        }

        .dropdown-item-link.danger {
            color: #ef4444;
        }

        .dropdown-item-link.danger:hover {
            background: rgba(239, 68, 68, 0.1);
        }

        .dropdown-sep {
            height: 1px;
            background: var(--border);
            margin: 4px 0;
        }

        /* ─── Admin badge ─────────────────────────── */
        .superadmin-badge {
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 20px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* ─── Page Content ─────────────────────────── */
        .page-content {
            padding: 2rem;
            min-height: calc(100vh - 60px);
        }

        /* ─── Cards ────────────────────────────────── */
        .card-dark {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
        }

        /* ─── Scrollbar ────────────────────────────── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--border-hover); }
    </style>
</head>
<body>
    <div id="app">
        <nav class="app-nav">
            <a class="nav-brand" href="{{ url('/') }}">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17 10.5V7a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-3.5l4 4v-11l-4 4z"/>
                </svg>
                <span>{{ config('app.name', 'VideoSupport') }}</span>
            </a>

            <div class="nav-right">
                @auth
                    @if(auth()->user()->is_superadmin ?? false)
                        <a href="{{ route('admin.recordings') }}" class="nav-link-item {{ request()->routeIs('admin.recordings') ? 'active' : '' }}">
                            Bütün Yazılar
                        </a>
                        <div class="nav-divider"></div>
                    @endif

                    <a href="{{ route('dashboard') }}" class="nav-link-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        Dashboard
                    </a>

                    <div class="nav-dropdown-wrap">
                        <div class="nav-user" style="cursor:pointer">
                            <div class="nav-avatar">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                            <span class="nav-username">{{ auth()->user()->name }}</span>
                            @if(auth()->user()->is_superadmin ?? false)
                                <span class="superadmin-badge">Admin</span>
                            @endif
                        </div>
                        <div class="nav-dropdown-menu">
                            <div style="padding:8px 14px 6px; font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">
                                {{ auth()->user()->email }}
                            </div>
                            <div class="dropdown-sep"></div>
                            <a href="{{ route('logout') }}"
                               class="dropdown-item-link danger"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Çıxış
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="nav-btn">Daxil ol</a>
                @endauth
            </div>
        </nav>

        <main class="page-content">
            @yield('content')
        </main>
    </div>
</body>
</html>
