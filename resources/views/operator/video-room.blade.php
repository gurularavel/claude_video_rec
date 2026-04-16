<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Video Dəstək — {{ $session->customer_name ?? 'Müştəri' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:      #070d18;
            --surface: #0f1924;
            --elevated:#152033;
            --border:  rgba(6,182,212,.12);
            --cyan:    #06b6d4;
            --success: #10b981;
            --danger:  #ef4444;
            --warning: #f59e0b;
            --text:    #e2e8f0;
            --muted:   #64748b;
            --subtle:  #94a3b8;
        }

        html, body {
            height: 100%;
            background: var(--bg);
            font-family: 'Inter', sans-serif;
            color: var(--text);
            overflow: hidden;
        }

        /* ─── Layout ─── */
        .room-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            grid-template-rows: 100vh;
            height: 100vh;
        }

        /* ─── Sidebar ─── */
        .sidebar {
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .sidebar-header {
            padding: 1.25rem;
            border-bottom: 1px solid var(--border);
            background: rgba(6,182,212,.03);
        }

        .sidebar-brand {
            font-family: 'Inter', sans-serif;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--cyan);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 14px;
        }

        /* ─── Customer card in sidebar ─── */
        .customer-card {
            background: var(--elevated);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 1rem;
        }

        .customer-avatar {
            width: 48px; height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--cyan), #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            font-size: 1.1rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 12px;
        }

        .customer-name {
            font-family: 'Inter', sans-serif;
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 8px;
        }

        .customer-detail {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 0.8rem;
            color: var(--muted);
            margin-bottom: 5px;
        }

        .customer-detail svg { color: var(--muted); flex-shrink: 0; }

        /* ─── Sidebar body ─── */
        .sidebar-body {
            flex: 1;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        /* ─── Stats ─── */
        .stat-block {
            background: var(--elevated);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.875rem 1rem;
        }

        .stat-label {
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .stat-value {
            font-family: 'Inter', sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text);
        }

        .stat-value.cyan { color: var(--cyan); }
        .stat-value.success { color: var(--success); }
        .stat-value.danger { color: var(--danger); }

        /* ─── Recording indicator ─── */
        .rec-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(239,68,68,.08);
            border: 1px solid rgba(239,68,68,.2);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.8rem;
            color: var(--danger);
            font-weight: 600;
        }

        .rec-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--danger);
            animation: rec-blink 1.2s ease-in-out infinite;
        }

        @keyframes rec-blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.2; }
        }

        /* ─── Connection status ─── */
        .conn-status {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 500;
            border: 1px solid transparent;
            transition: all 0.3s;
        }

        .conn-status.connecting {
            background: rgba(245,158,11,.08);
            border-color: rgba(245,158,11,.2);
            color: var(--warning);
        }

        .conn-status.connected {
            background: rgba(16,185,129,.08);
            border-color: rgba(16,185,129,.2);
            color: var(--success);
        }

        .conn-status.disconnected {
            background: rgba(239,68,68,.08);
            border-color: rgba(239,68,68,.2);
            color: var(--danger);
        }

        .conn-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: currentColor;
            flex-shrink: 0;
        }

        .conn-status.connected .conn-dot {
            animation: pulse-dot 2s ease-in-out infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.3); }
        }

        /* ─── End call button in sidebar ─── */
        .btn-end-call {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: box-shadow 0.2s, opacity 0.2s;
            margin-top: auto;
        }

        .btn-end-call:hover { box-shadow: 0 8px 24px rgba(239,68,68,.4); }
        .btn-end-call:disabled { opacity: 0.6; cursor: not-allowed; }

        /* ─── Video area ─── */
        .video-area {
            position: relative;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .video-grid {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            padding: 6px;
        }

        .video-box {
            position: relative;
            background: #000;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,.04);
        }

        .video-box video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .video-label {
            position: absolute;
            bottom: 12px;
            left: 12px;
            background: rgba(0,0,0,.7);
            backdrop-filter: blur(8px);
            color: #fff;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            border: 1px solid rgba(255,255,255,.1);
        }

        /* Customer box highlight */
        .video-box.remote-box {
            border-color: rgba(6,182,212,.15);
        }

        /* ─── Bottom controls ─── */
        .controls-bar {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            background: linear-gradient(to top, rgba(7,13,24,.9) 60%, transparent);
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .ctrl-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 10px;
            padding: 10px 18px;
            color: var(--text);
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            font-size: 0.72rem;
            font-weight: 500;
            transition: background 0.2s, border-color 0.2s;
            min-width: 64px;
        }

        .ctrl-btn svg { width: 20px; height: 20px; }

        .ctrl-btn:hover { background: rgba(255,255,255,.14); }

        .ctrl-btn.active {
            background: var(--cyan-dim, rgba(6,182,212,.12));
            border-color: rgba(6,182,212,.3);
            color: var(--cyan);
        }

        .ctrl-btn.muted {
            background: rgba(239,68,68,.1);
            border-color: rgba(239,68,68,.3);
            color: var(--danger);
        }

        /* ─── Timer overlay ─── */
        .timer-overlay {
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(0,0,0,.6);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 8px;
            padding: 6px 14px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text);
            z-index: 10;
        }

        /* ─── Spinner ─── */
        @keyframes spin { to { transform: rotate(360deg); } }
        .spin { animation: spin 0.7s linear infinite; }

        /* ─── Responsive sidebar toggle ─── */
        @media (max-width: 800px) {
            .room-layout {
                grid-template-columns: 1fr;
                grid-template-rows: auto 1fr;
            }
            .sidebar {
                border-right: none;
                border-bottom: 1px solid var(--border);
                flex-direction: row;
                height: auto;
            }
            .sidebar-header { border-bottom: none; border-right: 1px solid var(--border); }
        }
    </style>
</head>
<body>
<div class="room-layout">

    {{-- ─── Sidebar ─── --}}
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">Aktiv Zəng</div>
            <div class="customer-card">
                <div class="customer-avatar">
                    {{ strtoupper(substr($session->customer_name ?? 'A', 0, 2)) }}
                </div>
                <div class="customer-name">
                    {{ $session->customer_name ?? 'Anonim Müştəri' }}
                </div>
                @if($session->customer_phone)
                    <div class="customer-detail">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="13" height="13"><path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 0 0-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.12-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z"/></svg>
                        {{ $session->customer_phone }}
                    </div>
                @endif
                @if($session->customer_email)
                    <div class="customer-detail">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        {{ $session->customer_email }}
                    </div>
                @endif
            </div>
        </div>

        <div class="sidebar-body">
            {{-- Timer --}}
            <div class="stat-block">
                <div class="stat-label">Zəng müddəti</div>
                <div class="stat-value cyan" id="sidebar-timer">00:00</div>
            </div>

            {{-- Connection status --}}
            <div class="conn-status connecting" id="conn-status">
                <div class="conn-dot"></div>
                <span id="conn-text">Qoşulur...</span>
            </div>

            {{-- Recording indicator --}}
            <div class="rec-indicator" id="rec-indicator" style="display:none">
                <div class="rec-dot"></div>
                Yazılır (REC)
            </div>

            {{-- End call --}}
            <button id="btn-end" class="btn-end-call">
                <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M20.384 4.616a8 8 0 0 0-11.313 0L4.93 8.757a8 8 0 0 0 0 11.314l.707.707a1 1 0 0 0 1.414 0L10 17.829a1 1 0 0 0 0-1.414l-1.628-1.627a1 1 0 0 1 0-1.415L12.243 9.5a1 1 0 0 1 1.414 0L15.286 11.13a1 1 0 0 0 1.414 0l2.95-2.95a1 1 0 0 0 0-1.415l-.707-.707a8 8 0 0 0-.559-.442z" opacity=".5"/><path d="M3 5a2 2 0 0 1 2-2h3.6a1 1 0 0 1 .98.804l.74 3.7a1 1 0 0 1-.527 1.077L8.45 9.37a10.01 10.01 0 0 0 6.18 6.18l.79-1.343a1 1 0 0 1 1.077-.527l3.7.74A1 1 0 0 1 21 15.4V19a2 2 0 0 1-2 2h-1c-9.389 0-15-5.611-15-15V5z"/></svg>
                Zəngi Bitir
            </button>
        </div>
    </aside>

    {{-- ─── Video area ─── --}}
    <div class="video-area">
        <div class="video-grid">
            <div class="video-box">
                <video id="local-video" autoplay muted playsinline></video>
                <div class="video-label">Siz (Operator)</div>
            </div>
            <div class="video-box remote-box">
                <video id="remote-video" autoplay playsinline></video>
                <div class="video-label" id="remote-label">
                    {{ $session->customer_name ?? 'Müştəri' }}
                </div>
            </div>
        </div>

        <div class="timer-overlay" id="timer-overlay">00:00</div>

        <div class="controls-bar">
            <button id="btn-video" class="ctrl-btn active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 10l4.553-2.276A1 1 0 0 1 21 8.67v6.66a1 1 0 0 1-1.447.894L15 14M3 8a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8z"/></svg>
                Video
            </button>
            <button id="btn-audio" class="ctrl-btn active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>
                Mikrofon
            </button>
        </div>
    </div>

</div>

<script type="module">
    const SESSION = '{{ $sessionUuid }}';
    const CSRF    = document.querySelector('meta[name="csrf-token"]').content;
    const ICE_CFG = {
        iceServers: [
            { urls: 'stun:stun.l.google.com:19302' },
            { urls: 'stun:stun1.l.google.com:19302' },
            { urls: 'stun:stun2.l.google.com:19302' },
            { urls: 'stun:stun3.l.google.com:19302' },
            { urls: 'stun:stun4.l.google.com:19302' },
        ]
    };

    let pc, localStream, recorder, recordingChunks = [], callStartTime;
    let videoEnabled = true, audioEnabled = true;
    let timerInterval;
    // ── Fix 1: guard against multiple offers ──
    let offerSent = false;
    // ── Fix 2: queue ICE candidates until remote desc is set ──
    let pendingCandidates = [];

    const localVideo   = document.getElementById('local-video');
    const remoteVideo  = document.getElementById('remote-video');
    const connStatus   = document.getElementById('conn-status');
    const connText     = document.getElementById('conn-text');
    const recIndicator = document.getElementById('rec-indicator');
    const sidebarTimer = document.getElementById('sidebar-timer');
    const timerOverlay = document.getElementById('timer-overlay');
    const btnVideo     = document.getElementById('btn-video');
    const btnAudio     = document.getElementById('btn-audio');
    const btnEnd       = document.getElementById('btn-end');

    function setStatus(state, text) {
        connStatus.className = 'conn-status ' + state;
        connText.textContent = text;
    }

    async function post(url, body = {}) {
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify(body),
        }).then(r => r.json());
    }

    async function sendSignal(type, payload) {
        return post(`/support/${SESSION}/signal`, { from: 'operator', type, payload });
    }

    // Chrome→Firefox SDP uyumsuzluğunu həll edir.
    // 1) a=ssrc sətirləri (Chrome Plan-B) — Firefox rədd edir
    // 2) telephone-event (DTMF) codec — Firefox bəzən rədd edir, video üçün lazım deyil
    function cleanSdp(sdp) {
        const lines = sdp.split('\n');

        // telephone-event payload type-larını tap
        const badPts = new Set();
        lines.forEach(line => {
            const m = line.match(/^a=rtpmap:(\d+)\s+telephone-event\//i);
            if (m) badPts.add(m[1]);
        });

        return lines
            .filter(line => {
                if (line.startsWith('a=ssrc')) return false;
                if (badPts.size) {
                    for (const pt of badPts) {
                        if (new RegExp(`^a=(rtpmap|fmtp|rtcp-fb):${pt}[\\s/]`).test(line)) return false;
                    }
                }
                return true;
            })
            .map(line => {
                // m= sətirindən bad PT-ləri çıxart: "m=audio 9 UDP/TLS/RTP/SAVPF 111 126 ..."
                if (line.startsWith('m=') && badPts.size) {
                    const parts = line.split(' ');
                    return parts.filter((p, i) => i < 3 || !badPts.has(p)).join(' ');
                }
                return line;
            })
            .join('\n');
    }

    // ── Peer connection ──
    function buildPeerConnection() {
        pc = new RTCPeerConnection(ICE_CFG);

        pc.ontrack = e => {
            console.log('[OPERATOR] ontrack:', e.track.kind, e.streams.length);
            if (e.streams && e.streams[0]) {
                remoteVideo.srcObject = e.streams[0];
            } else {
                if (!remoteVideo.srcObject) remoteVideo.srcObject = new MediaStream();
                remoteVideo.srcObject.addTrack(e.track);
            }
            setStatus('connected', 'Bağlantı quruldu');
        };

        pc.onicecandidate = e => {
            if (e.candidate) {
                console.log('[OPERATOR] ICE candidate:', e.candidate.type, e.candidate.protocol);
                sendSignal('ice-candidate', e.candidate.toJSON());
            } else {
                console.log('[OPERATOR] ICE gathering complete');
            }
        };

        pc.oniceconnectionstatechange = () => {
            console.log('[OPERATOR] ICE state:', pc.iceConnectionState);
            if (pc.iceConnectionState === 'failed') {
                setStatus('disconnected', 'ICE bağlantı uğursuz — TURN server lazımdır');
            } else if (pc.iceConnectionState === 'checking') {
                setStatus('connecting', 'ICE yoxlanılır...');
            } else if (pc.iceConnectionState === 'connected' || pc.iceConnectionState === 'completed') {
                setStatus('connected', 'Bağlantı quruldu');
            }
        };

        pc.onconnectionstatechange = () => {
            console.log('[OPERATOR] Connection state:', pc.connectionState);
            if (pc.connectionState === 'connected') {
                setStatus('connected', 'Bağlantı quruldu');
            } else if (pc.connectionState === 'failed') {
                setStatus('disconnected', 'Bağlantı uğursuz');
            }
        };

        localStream.getTracks().forEach(t => pc.addTrack(t, localStream));
    }

    // ── Fix 1: only create offer once ──
    async function createOffer() {
        if (offerSent) return;
        if (pc.signalingState !== 'stable') return;
        offerSent = true;
        try {
            const offer = await pc.createOffer();
            await pc.setLocalDescription(offer);
            await sendSignal('offer', { type: offer.type, sdp: offer.sdp });
            setStatus('connecting', 'Offer göndərildi, cavab gözlənilir...');
        } catch (err) {
            offerSent = false; // allow retry on error
            console.error('createOffer error:', err);
        }
    }

    // ── Fix 2: apply queued ICE candidates after remote desc is set ──
    async function applyPendingCandidates() {
        for (const c of pendingCandidates) {
            try { await pc.addIceCandidate(new RTCIceCandidate(c)); } catch {}
        }
        pendingCandidates = [];
    }

    // ── Recording ──
    function startRecording() {
        const mime = MediaRecorder.isTypeSupported('video/webm;codecs=vp9,opus')
            ? 'video/webm;codecs=vp9,opus' : 'video/webm';
        recorder = new MediaRecorder(localStream, { mimeType: mime });
        recorder.ondataavailable = e => { if (e.data.size > 0) recordingChunks.push(e.data); };
        recorder.start(1000);
        recIndicator.style.display = 'flex';
    }

    async function stopAndUploadRecording() {
        if (!recorder || recorder.state === 'inactive') return;
        return new Promise(resolve => {
            recorder.onstop = async () => {
                const blob = new Blob(recordingChunks, { type: 'video/webm' });
                const duration = callStartTime ? Math.round((Date.now() - callStartTime) / 1000) : 0;
                const form = new FormData();
                form.append('recording', blob, 'recording.webm');
                form.append('duration', duration);
                await fetch(`/support/${SESSION}/recording`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF },
                    body: form,
                });
                resolve();
            };
            recorder.stop();
        });
    }

    // ── Timer ──
    function startTimer() {
        callStartTime = Date.now();
        timerInterval = setInterval(() => {
            const s = Math.floor((Date.now() - callStartTime) / 1000);
            const m = Math.floor(s / 60).toString().padStart(2, '0');
            const sec = (s % 60).toString().padStart(2, '0');
            const fmt = `${m}:${sec}`;
            sidebarTimer.textContent = fmt;
            timerOverlay.textContent = fmt;
        }, 1000);
    }

    // ── Controls ──
    btnVideo.addEventListener('click', () => {
        videoEnabled = !videoEnabled;
        localStream.getVideoTracks().forEach(t => t.enabled = videoEnabled);
        btnVideo.classList.toggle('active', videoEnabled);
        btnVideo.classList.toggle('muted', !videoEnabled);
    });

    btnAudio.addEventListener('click', () => {
        audioEnabled = !audioEnabled;
        localStream.getAudioTracks().forEach(t => t.enabled = audioEnabled);
        btnAudio.classList.toggle('active', audioEnabled);
        btnAudio.classList.toggle('muted', !audioEnabled);
    });

    btnEnd.addEventListener('click', async () => {
        if (!confirm('Zəngi bitirmək istədiyinizə əminsiniz?')) return;
        btnEnd.disabled = true;
        btnEnd.innerHTML = `<svg class="spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg> Yüklənir...`;
        clearInterval(timerInterval);
        recIndicator.style.display = 'none';
        await post(`/support/${SESSION}/end-call`);
        await stopAndUploadRecording();
        if (pc) pc.close();
        localStream?.getTracks().forEach(t => t.stop());
        window.location.href = '/dashboard';
    });

    // ── Main init ──
    (async () => {
        try {
            localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
            localVideo.srcObject = localStream;
        } catch (err) {
            setStatus('disconnected', 'Kamera/Mikrofona icazə verilmədi: ' + err.message);
            return;
        }

        buildPeerConnection();
        setStatus('connecting', 'Müştəri gözlənilir...');

        // ── Polling-based signal receiver (replaces Echo/WebSocket) ──
        let lastSignalId = 0;
        async function pollSignals() {
            try {
                const res = await fetch(`/support/${SESSION}/poll-signals?for=operator&after=${lastSignalId}`, {
                    headers: { 'X-CSRF-TOKEN': CSRF }
                });
                const data = await res.json();
                for (const e of (data.signals || [])) {
                    lastSignalId = Math.max(lastSignalId, e.id);
                    console.log('[OPERATOR] Siqnal alındı:', e.from, e.type);

                    if (e.type === 'customer-ready') {
                        console.log('[OPERATOR] customer-ready alındı, offer yaradılır...');
                        await createOffer();
                    }

                    if (e.type === 'answer') {
                        if (pc.signalingState === 'have-local-offer') {
                            const cleanedSdp = cleanSdp(e.payload.sdp);
                            await pc.setRemoteDescription(new RTCSessionDescription({
                                type: e.payload.type,
                                sdp:  cleanedSdp,
                            }));
                            await applyPendingCandidates();
                        }
                    }

                    if (e.type === 'ice-candidate' && e.payload) {
                        if (pc.remoteDescription) {
                            try { await pc.addIceCandidate(new RTCIceCandidate(e.payload)); } catch {}
                        } else {
                            pendingCandidates.push(e.payload);
                        }
                    }
                }
            } catch (err) {
                console.warn('[OPERATOR] poll xətası:', err.message);
            }
        }
        const pollInterval = setInterval(pollSignals, 800);

        const startRes = await post(`/support/${SESSION}/start-call`);
        console.log('[OPERATOR] startCall cavabı:', startRes);
        startRecording();
        startTimer();
        setStatus('connecting', 'Müştəri bağlanmasını gözləyir...');
    })();
</script>
</body>
</html>
