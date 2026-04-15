<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dəstək Gözlənilir...</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:     #070d18;
            --cyan:   #06b6d4;
            --purple: #8b5cf6;
            --text:   #e2e8f0;
            --muted:  #64748b;
            --subtle: #94a3b8;
        }

        html, body {
            min-height: 100vh;
            background: var(--bg);
            font-family: 'Inter', sans-serif;
            color: var(--text);
            overflow: hidden;
        }

        /* ─── Background ─── */
        .bg-mesh {
            position: fixed; inset: 0; overflow: hidden; z-index: 0;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.12;
        }

        .orb-1 {
            width: 700px; height: 700px;
            background: var(--cyan);
            top: -300px; left: -200px;
        }

        .orb-2 {
            width: 500px; height: 500px;
            background: var(--purple);
            bottom: -200px; right: -100px;
        }

        /* ─── Main ─── */
        .waiting-page {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0;
            padding: 2rem;
        }

        /* ─── Brand ─── */
        .brand-top {
            position: fixed;
            top: 24px;
            left: 50%;
            transform: translateX(-50%);
            font-family: 'Inter', sans-serif;
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--cyan);
            letter-spacing: 1px;
            text-transform: uppercase;
            opacity: 0.7;
        }

        /* ─── Pulse ring system ─── */
        .pulse-system {
            position: relative;
            width: 200px;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 3rem;
        }

        .ring {
            position: absolute;
            border-radius: 50%;
            border: 1.5px solid var(--cyan);
            animation: ring-expand 3s ease-out infinite;
        }

        .ring-1 { animation-delay: 0s; }
        .ring-2 { animation-delay: 0.75s; }
        .ring-3 { animation-delay: 1.5s; }
        .ring-4 { animation-delay: 2.25s; }

        @keyframes ring-expand {
            0% {
                width: 72px; height: 72px;
                opacity: 0.8;
                border-color: var(--cyan);
            }
            100% {
                width: 200px; height: 200px;
                opacity: 0;
                border-color: rgba(6, 182, 212, 0.1);
            }
        }

        /* Inner glow ring */
        .ring-inner {
            position: absolute;
            width: 90px; height: 90px;
            border-radius: 50%;
            border: 2px solid rgba(6,182,212,.3);
            background: rgba(6,182,212,.05);
            animation: inner-pulse 2s ease-in-out infinite;
        }

        @keyframes inner-pulse {
            0%, 100% { transform: scale(1); border-color: rgba(6,182,212,.3); }
            50% { transform: scale(1.06); border-color: rgba(6,182,212,.6); }
        }

        /* Core icon */
        .pulse-core {
            position: relative;
            z-index: 5;
            width: 64px; height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(6,182,212,.3), rgba(139,92,246,.25));
            border: 2px solid rgba(6,182,212,.5);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--cyan);
            box-shadow:
                0 0 30px rgba(6,182,212,.3),
                0 0 60px rgba(6,182,212,.1),
                inset 0 1px 0 rgba(255,255,255,.1);
            animation: core-glow 2s ease-in-out infinite;
        }

        @keyframes core-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(6,182,212,.3), 0 0 50px rgba(6,182,212,.1); }
            50% { box-shadow: 0 0 40px rgba(6,182,212,.5), 0 0 80px rgba(6,182,212,.2); }
        }

        .pulse-core svg {
            width: 30px; height: 30px;
            animation: icon-ring 2s ease-in-out infinite;
        }

        @keyframes icon-ring {
            0%, 90%, 100% { transform: rotate(0deg); }
            15% { transform: rotate(-15deg); }
            30% { transform: rotate(15deg); }
            45% { transform: rotate(-10deg); }
            60% { transform: rotate(8deg); }
        }

        /* ─── Text content ─── */
        .waiting-content {
            text-align: center;
            max-width: 420px;
        }

        .waiting-title {
            font-family: 'Inter', sans-serif;
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 12px;
        }

        .waiting-subtitle {
            font-size: 1rem;
            color: var(--subtle);
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        /* ─── Status steps ─── */
        .status-steps {
            display: flex;
            flex-direction: column;
            gap: 10px;
            text-align: left;
            background: rgba(255,255,255,.03);
            border: 1px solid rgba(6,182,212,.08);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 2rem;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.875rem;
        }

        .step-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .step-dot.done {
            background: #10b981;
            box-shadow: 0 0 8px rgba(16,185,129,.5);
        }

        .step-dot.active {
            background: var(--cyan);
            box-shadow: 0 0 8px rgba(6,182,212,.5);
            animation: dot-pulse 1.5s ease-in-out infinite;
        }

        .step-dot.pending {
            background: rgba(255,255,255,.15);
        }

        @keyframes dot-pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.4); opacity: 0.8; }
        }

        .step-text { color: var(--subtle); }
        .step-text.done { color: #10b981; }
        .step-text.active { color: var(--text); font-weight: 500; }

        /* ─── Session ID ─── */
        .session-id {
            font-size: 0.75rem;
            color: var(--muted);
            font-family: monospace;
            background: rgba(255,255,255,.03);
            padding: 6px 14px;
            border-radius: 6px;
            border: 1px solid rgba(255,255,255,.06);
            display: inline-block;
        }

        /* ─── Connecting dots ─── */
        .connecting-dots {
            display: inline-flex;
            gap: 5px;
            margin-left: 2px;
        }

        .connecting-dots span {
            display: inline-block;
            width: 4px; height: 4px;
            border-radius: 50%;
            background: var(--cyan);
            animation: dot-bounce 1.4s ease-in-out infinite;
        }

        .connecting-dots span:nth-child(2) { animation-delay: 0.2s; }
        .connecting-dots span:nth-child(3) { animation-delay: 0.4s; }

        @keyframes dot-bounce {
            0%, 80%, 100% { transform: translateY(0); opacity: 0.5; }
            40% { transform: translateY(-6px); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="bg-mesh">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
    </div>

    <div class="brand-top">{{ config('app.name', 'VideoSupport') }}</div>

    <div class="waiting-page">
        <!-- Pulse animation -->
        <div class="pulse-system">
            <div class="ring ring-1"></div>
            <div class="ring ring-2"></div>
            <div class="ring ring-3"></div>
            <div class="ring ring-4"></div>
            <div class="ring-inner"></div>
            <div class="pulse-core">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 0 0-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.12-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z"/>
                </svg>
            </div>
        </div>

        <!-- Text content -->
        <div class="waiting-content">
            <h1 class="waiting-title">Zəng Yönləndirilir</h1>
            <p class="waiting-subtitle">
                Operator sizinlə bağlanmaq üçün hazırlanır. Bir az gözləyin
                <span class="connecting-dots">
                    <span></span><span></span><span></span>
                </span>
            </p>

            <div class="status-steps">
                <div class="step-item">
                    <div class="step-dot done"></div>
                    <span class="step-text done">Sessiya yaradıldı</span>
                </div>
                <div class="step-item">
                    <div class="step-dot done"></div>
                    <span class="step-text done">Gözləmə otağına daxil oldunuz</span>
                </div>
                <div class="step-item">
                    <div class="step-dot active"></div>
                    <span class="step-text active">Operator axtarılır...</span>
                </div>
                <div class="step-item">
                    <div class="step-dot pending"></div>
                    <span class="step-text">Video zəng başlayacaq</span>
                </div>
            </div>

            <div class="session-id">Sessiya: {{ $sessionUuid }}</div>
        </div>
    </div>

    <script type="module">
        const Echo = window.Echo;
        const sessionUuid = '{{ $sessionUuid }}';

        // Join waiting room
        fetch(`/support/call/${sessionUuid}/join`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        // Listen for operator acceptance
        window.Echo.channel(`support-session.${sessionUuid}`)
            .listen('OperatorAccepted', (e) => {
                console.log('Operator accepted!', e);
                window.location.href = `/support/call/${sessionUuid}/video`;
            });
    </script>
</body>
</html>
