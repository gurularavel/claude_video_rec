<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daxil ol — {{ config('app.name', 'VideoSupport') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:      #070d18;
            --surface: #0f1924;
            --elevated:#152033;
            --border:  rgba(6,182,212,.14);
            --cyan:    #06b6d4;
            --cyan-glow: rgba(6,182,212,.35);
            --purple:  #8b5cf6;
            --text:    #e2e8f0;
            --muted:   #64748b;
            --subtle:  #94a3b8;
        }

        html, body {
            min-height: 100vh;
            background: var(--bg);
            font-family: 'Inter', sans-serif;
            color: var(--text);
            overflow: hidden;
        }

        /* ── Animated mesh background ── */
        .bg-mesh {
            position: fixed;
            inset: 0;
            overflow: hidden;
            z-index: 0;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.18;
            animation: orb-drift 20s ease-in-out infinite alternate;
        }

        .orb-1 {
            width: 600px; height: 600px;
            background: var(--cyan);
            top: -200px; left: -200px;
            animation-duration: 18s;
        }

        .orb-2 {
            width: 500px; height: 500px;
            background: var(--purple);
            bottom: -150px; right: -100px;
            animation-duration: 24s;
            animation-delay: -8s;
        }

        .orb-3 {
            width: 350px; height: 350px;
            background: #3b82f6;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            animation-duration: 30s;
            animation-delay: -15s;
            opacity: 0.1;
        }

        @keyframes orb-drift {
            0%   { transform: translate(0, 0) scale(1); }
            33%  { transform: translate(60px, 40px) scale(1.05); }
            66%  { transform: translate(-40px, 80px) scale(0.95); }
            100% { transform: translate(30px, -60px) scale(1.02); }
        }

        /* Grid overlay */
        .grid-overlay {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(6,182,212,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(6,182,212,.03) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: 1;
            pointer-events: none;
        }

        /* ── Main layout ── */
        .login-wrap {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 480px 1fr;
            grid-template-rows: 1fr;
            align-items: center;
        }

        /* Brand panel (left) */
        .brand-panel {
            grid-column: 1;
            padding: 4rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-icon {
            width: 50px; height: 50px;
            background: linear-gradient(135deg, var(--cyan), var(--purple));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-icon svg {
            width: 26px; height: 26px;
            fill: #fff;
        }

        .brand-name {
            font-family: 'Inter', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text);
        }

        .brand-tagline {
            font-family: 'Inter', sans-serif;
            font-size: 2.8rem;
            font-weight: 800;
            line-height: 1.15;
            color: var(--text);
        }

        .brand-tagline em {
            font-style: normal;
            background: linear-gradient(135deg, var(--cyan), var(--purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-desc {
            font-size: 1rem;
            color: var(--muted);
            line-height: 1.6;
            max-width: 320px;
        }

        .brand-stats {
            display: flex;
            gap: 2rem;
            margin-top: 1rem;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .stat-num {
            font-family: 'Inter', sans-serif;
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--cyan);
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ── Login card ── */
        .login-card {
            grid-column: 2;
            background: rgba(15, 25, 36, 0.85);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            box-shadow:
                0 0 0 1px rgba(6,182,212,.05),
                0 40px 80px rgba(0,0,0,.5),
                inset 0 1px 0 rgba(255,255,255,.04);
            animation: card-in 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes card-in {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .card-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .card-header h1 {
            font-family: 'Inter', sans-serif;
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 6px;
        }

        .card-header p {
            font-size: 0.875rem;
            color: var(--muted);
        }

        /* ── Form fields ── */
        .field-group {
            margin-bottom: 1.25rem;
        }

        .field-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--subtle);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .field-input {
            width: 100%;
            background: rgba(255,255,255,.04);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 16px;
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 0.9375rem;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .field-input:focus {
            border-color: var(--cyan);
            background: rgba(6,182,212,.05);
            box-shadow: 0 0 0 3px rgba(6,182,212,.12);
        }

        .field-input.is-invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239,68,68,.12);
        }

        .field-error {
            font-size: 0.8rem;
            color: #ef4444;
            margin-top: 6px;
        }

        /* ── Remember + forgot row ── */
        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 0.875rem;
            color: var(--subtle);
            user-select: none;
        }

        .remember-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--cyan);
            cursor: pointer;
        }

        .forgot-link {
            font-size: 0.875rem;
            color: var(--cyan);
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .forgot-link:hover { opacity: 0.8; }

        /* ── Submit button ── */
        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--cyan), #0891b2);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 0.9375rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.1s, box-shadow 0.2s;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,.15), transparent);
            opacity: 0;
            transition: opacity 0.2s;
        }

        .btn-login:hover::before { opacity: 1; }
        .btn-login:hover { box-shadow: 0 8px 24px rgba(6,182,212,.4); }
        .btn-login:active { transform: scale(0.99); }

        /* ── Alert for general errors ── */
        .alert-error {
            background: rgba(239,68,68,.1);
            border: 1px solid rgba(239,68,68,.3);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.875rem;
            color: #ef4444;
            margin-bottom: 1.25rem;
        }

        /* ── Decorative vertical line ── */
        .v-divider {
            grid-column: 2 / 3;
            display: none;
        }

        /* ── Right panel (empty/decorative) ── */
        .right-panel {
            grid-column: 3;
        }

        /* ─── Floating scan lines ── */
        @keyframes scan {
            from { transform: translateY(-100%); }
            to   { transform: translateY(100vh); }
        }

        .scanline {
            position: fixed;
            left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(6,182,212,.06), transparent);
            animation: scan 8s linear infinite;
            pointer-events: none;
            z-index: 2;
        }

        @media (max-width: 1100px) {
            .login-wrap {
                grid-template-columns: 1fr;
                justify-items: center;
                padding: 2rem;
            }
            .brand-panel { display: none; }
            .right-panel { display: none; }
            .login-card { width: 100%; max-width: 440px; }
            html, body { overflow-y: auto; }
        }
    </style>
</head>
<body>
    <div class="bg-mesh">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>
    <div class="grid-overlay"></div>
    <div class="scanline"></div>

    <div class="login-wrap">
        <!-- Brand left panel -->
        <div class="brand-panel">
            <div class="brand-logo">
                <div class="brand-icon">
                    <svg viewBox="0 0 24 24"><path d="M17 10.5V7a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-3.5l4 4v-11l-4 4z"/></svg>
                </div>
                <span class="brand-name">{{ config('app.name', 'VideoSupport') }}</span>
            </div>

            <div class="brand-tagline">
                Real-time<br><em>Video Dəstək</em><br>Platforması
            </div>

            <p class="brand-desc">
                Müştərilərinizlə birbaşa video əlaqə qurun. Hər görüşmə avtomatik yazılır və saxlanılır.
            </p>

            <div class="brand-stats">
                <div class="stat-item">
                    <span class="stat-num">HD</span>
                    <span class="stat-label">Video Keyfiyyəti</span>
                </div>
                <div class="stat-item">
                    <span class="stat-num">E2E</span>
                    <span class="stat-label">Şifrələnmiş</span>
                </div>
                <div class="stat-item">
                    <span class="stat-num">REC</span>
                    <span class="stat-label">Yazılır</span>
                </div>
            </div>
        </div>

        <!-- Login card -->
        <div class="login-card">
            <div class="card-header">
                <h1>Xoş gəldiniz</h1>
                <p>Hesabınıza daxil olun</p>
            </div>

            @if(session('status'))
                <div class="alert-error" style="background:rgba(16,185,129,.1);border-color:rgba(16,185,129,.3);color:#10b981;">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="field-group">
                    <label for="email" class="field-label">E-poçt ünvanı</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="field-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                        required
                        autocomplete="email"
                        autofocus
                        placeholder="siz@example.com"
                    >
                    @error('email')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field-group">
                    <label for="password" class="field-label">Şifrə</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="field-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                    >
                    @error('password')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="remember-row">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        Yadda saxla
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">Şifrəni unutdum?</a>
                    @endif
                </div>

                <button type="submit" class="btn-login">Daxil ol</button>
            </form>
        </div>

        <div class="right-panel"></div>
    </div>
</body>
</html>
