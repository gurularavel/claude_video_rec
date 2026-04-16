<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Video Dəstək</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/js/app.js'])
    {{-- Chrome↔Firefox WebRTC uyğunluğunu avtomatik həll edir --}}
    <script src="https://unpkg.com/webrtc-adapter@9/out/adapter.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:      #070d18;
            --surface: #0f1924;
            --border:  rgba(6,182,212,.1);
            --cyan:    #06b6d4;
            --success: #10b981;
            --danger:  #ef4444;
            --warning: #f59e0b;
            --text:    #e2e8f0;
            --muted:   #64748b;
        }

        html, body {
            height: 100%;
            background: var(--bg);
            font-family: 'Inter', sans-serif;
            color: var(--text);
            overflow: hidden;
        }

        .video-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            padding: 6px;
            height: calc(100vh - 80px);
        }

        .video-box {
            position: relative;
            background: #000;
            border-radius: 12px;
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
            bottom: 14px;
            left: 14px;
            background: rgba(0,0,0,.68);
            backdrop-filter: blur(8px);
            color: #fff;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            border: 1px solid rgba(255,255,255,.1);
        }

        /* Operator box gets a subtle teal tint */
        .video-box.remote-box {
            border-color: rgba(6,182,212,.15);
        }

        /* ─── Status bar ─── */
        .status-bar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.8rem;
            font-weight: 500;
            z-index: 100;
            transition: background 0.4s, color 0.4s;
        }

        .status-bar.connecting {
            background: rgba(245,158,11,.12);
            color: var(--warning);
            border-bottom: 1px solid rgba(245,158,11,.2);
        }

        .status-bar.connected {
            background: rgba(16,185,129,.1);
            color: var(--success);
            border-bottom: 1px solid rgba(16,185,129,.2);
        }

        .status-bar.ended {
            background: rgba(100,116,139,.1);
            color: var(--muted);
            border-bottom: 1px solid rgba(100,116,139,.2);
        }

        .status-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .status-bar.connected .status-dot {
            animation: pulse-s 2s ease-in-out infinite;
        }

        @keyframes pulse-s {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        /* ─── Content wrapper (offset for status bar) ─── */
        .content-wrap {
            padding-top: 36px;
            height: 100vh;
        }

        /* ─── Bottom controls ─── */
        .controls-bar {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 0 1rem;
        }

        .ctrl-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 10px;
            padding: 10px 20px;
            color: var(--text);
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            font-size: 0.72rem;
            font-weight: 500;
            transition: background 0.2s, border-color 0.2s;
            min-width: 70px;
        }

        .ctrl-btn svg { width: 20px; height: 20px; }
        .ctrl-btn:hover { background: rgba(255,255,255,.14); }

        .ctrl-btn.active {
            background: rgba(6,182,212,.1);
            border-color: rgba(6,182,212,.3);
            color: var(--cyan);
        }

        .ctrl-btn.muted {
            background: rgba(239,68,68,.1);
            border-color: rgba(239,68,68,.3);
            color: var(--danger);
        }
    </style>
</head>
<body>
    <!-- Status bar -->
    <div id="status-bar" class="status-bar connecting">
        <div class="status-dot"></div>
        <span id="status-text">Operator gözlənilir...</span>
    </div>

    <div class="content-wrap">
        <div class="video-grid">
            <div class="video-box remote-box">
                <video id="remote-video" autoplay playsinline></video>
                <div class="video-label">Operator</div>
            </div>
            <div class="video-box">
                <video id="local-video" autoplay muted playsinline></video>
                <div class="video-label">Siz</div>
            </div>
        </div>

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

        let pc, localStream, offerReceived = false;
        let videoEnabled = true, audioEnabled = true;
        let readyInterval;
        // ── Fix 2: queue ICE candidates until offer is received ──
        let pendingCandidates = [];

        const statusBar   = document.getElementById('status-bar');
        const statusText  = document.getElementById('status-text');
        const localVideo  = document.getElementById('local-video');
        const remoteVideo = document.getElementById('remote-video');

        function setStatus(state, text) {
            statusBar.className = 'status-bar ' + state;
            statusText.textContent = text;
        }

        async function post(url, body = {}) {
            return fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify(body),
            }).then(r => r.json());
        }

        async function sendSignal(type, payload) {
            return post(`/support/call/${SESSION}/signal`, { from: 'customer', type, payload });
        }

        // Chrome↔Firefox SDP uyğunluğu.
        // adapter.js əsas işi görür; bu funksiya əlavə qoruma kimi:
        // — a=ssrc sətirləri (Chrome Plan-B legacy)
        // — whitelist-dən kənar codec-lər (CN, telephone-event, RED köhnə, vs.)
        function cleanSdp(sdp) {
            const KEEP = /^(opus|vp8|vp9|h264|av1|rtx|red|ulpfec|flexfec|h265)/i;
            const lines = sdp.split('\n');

            // Whitelist-dən kənar PT-ləri tap
            const removePts = new Set();
            lines.forEach(line => {
                const m = line.match(/^a=rtpmap:(\d+)\s+([\w-]+)\//);
                if (m && !KEEP.test(m[2])) removePts.add(m[1]);
            });

            return lines
                .filter(line => {
                    if (line.startsWith('a=ssrc')) return false;
                    const ptM = line.match(/^a=(?:rtpmap|fmtp|rtcp-fb):(\d+)[\s/]/);
                    if (ptM && removePts.has(ptM[1])) return false;
                    return true;
                })
                .map(line => {
                    if (line.startsWith('m=') && removePts.size) {
                        const parts = line.trim().split(/\s+/);
                        return [...parts.slice(0, 3), ...parts.slice(3).filter(p => !removePts.has(p))].join(' ');
                    }
                    return line;
                })
                .join('\n');
        }

        function buildPeerConnection() {
            pc = new RTCPeerConnection(ICE_CFG);

            pc.onicecandidate = e => {
                if (e.candidate) {
                    console.log('[MÜŞTƏRİ] ICE candidate:', e.candidate.type, e.candidate.protocol);
                    sendSignal('ice-candidate', e.candidate.toJSON());
                } else {
                    console.log('[MÜŞTƏRİ] ICE gathering complete');
                }
            };

            pc.ontrack = e => {
                console.log('[MÜŞTƏRİ] ontrack:', e.track.kind, e.streams.length);
                if (e.streams && e.streams[0]) {
                    remoteVideo.srcObject = e.streams[0];
                } else {
                    if (!remoteVideo.srcObject) remoteVideo.srcObject = new MediaStream();
                    remoteVideo.srcObject.addTrack(e.track);
                }
                setStatus('connected', 'Bağlantı quruldu');
            };

            pc.oniceconnectionstatechange = () => {
                console.log('[MÜŞTƏRİ] ICE state:', pc.iceConnectionState);
                if (pc.iceConnectionState === 'failed') {
                    setStatus('ended', 'ICE bağlantı uğursuz — TURN server lazımdır');
                } else if (pc.iceConnectionState === 'checking') {
                    setStatus('connecting', 'ICE yoxlanılır...');
                } else if (pc.iceConnectionState === 'connected' || pc.iceConnectionState === 'completed') {
                    setStatus('connected', 'Bağlantı quruldu');
                }
            };

            pc.onconnectionstatechange = () => {
                console.log('[MÜŞTƏRİ] Connection state:', pc.connectionState);
                if (pc.connectionState === 'connected') {
                    setStatus('connected', 'Bağlantı quruldu');
                } else if (pc.connectionState === 'failed') {
                    setStatus('ended', 'Bağlantı uğursuz');
                    setTimeout(() => window.close(), 3000);
                }
            };

            localStream.getTracks().forEach(t => pc.addTrack(t, localStream));
        }

        document.getElementById('btn-video').addEventListener('click', () => {
            videoEnabled = !videoEnabled;
            localStream.getVideoTracks().forEach(t => t.enabled = videoEnabled);
            document.getElementById('btn-video').classList.toggle('active', videoEnabled);
            document.getElementById('btn-video').classList.toggle('muted', !videoEnabled);
        });

        document.getElementById('btn-audio').addEventListener('click', () => {
            audioEnabled = !audioEnabled;
            localStream.getAudioTracks().forEach(t => t.enabled = audioEnabled);
            document.getElementById('btn-audio').classList.toggle('active', audioEnabled);
            document.getElementById('btn-audio').classList.toggle('muted', !audioEnabled);
        });

        // ── Fake camera stream (canvas-based fallback) ──────────────
        function createFakeStream() {
            const canvas = document.createElement('canvas');
            canvas.width = 640; canvas.height = 480;
            const ctx = canvas.getContext('2d');
            let hue = 200, tick = 0;

            setInterval(() => {
                tick++;
                // Animated gradient background
                const grad = ctx.createLinearGradient(0, 0, 640, 480);
                grad.addColorStop(0, `hsl(${hue}, 60%, 12%)`);
                grad.addColorStop(1, `hsl(${(hue + 40) % 360}, 60%, 18%)`);
                ctx.fillStyle = grad;
                ctx.fillRect(0, 0, 640, 480);
                hue = (hue + 0.4) % 360;

                // Pulsing circle
                const pulse = 60 + Math.sin(tick / 15) * 12;
                ctx.beginPath();
                ctx.arc(320, 200, pulse, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(6, 182, 212, ${0.15 + Math.sin(tick / 15) * 0.05})`;
                ctx.fill();
                ctx.strokeStyle = 'rgba(6, 182, 212, 0.5)';
                ctx.lineWidth = 2;
                ctx.stroke();

                // Person icon
                ctx.fillStyle = 'rgba(6, 182, 212, 0.9)';
                ctx.beginPath();
                ctx.arc(320, 185, 30, 0, Math.PI * 2);
                ctx.fill();
                ctx.beginPath();
                ctx.ellipse(320, 248, 44, 30, 0, Math.PI, Math.PI * 2);
                ctx.fill();

                // Label
                ctx.fillStyle = 'rgba(255,255,255,0.9)';
                ctx.font = 'bold 22px Inter, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('Müştəri (Test)', 320, 320);

                // Clock
                ctx.fillStyle = 'rgba(100,116,139,1)';
                ctx.font = '15px Inter, sans-serif';
                ctx.fillText(new Date().toLocaleTimeString('az-AZ'), 320, 348);

                // TEST badge
                ctx.fillStyle = 'rgba(245,158,11,0.85)';
                ctx.fillRect(264, 362, 112, 28);
                ctx.fillStyle = '#fff';
                ctx.font = 'bold 13px Inter, sans-serif';
                ctx.fillText('FAKE KAMERA', 320, 382);
            }, 1000 / 25);

            const videoTrack = canvas.captureStream(25).getVideoTracks()[0];

            // Silent audio track
            const audioCtx = new AudioContext();
            const dst = audioCtx.createMediaStreamDestination();
            const silentTrack = dst.stream.getAudioTracks()[0];

            return new MediaStream([videoTrack, silentTrack]);
        }

        (async () => {
            try {
                localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                localVideo.srcObject = localStream;
            } catch (err) {
                // Real camera unavailable — use fake stream for testing
                console.warn('Real camera unavailable, using fake stream:', err.message);
                localStream = createFakeStream();
                localVideo.srcObject = localStream;
                setStatus('connecting', 'Test rejimi (fake kamera)');
            }

            buildPeerConnection();

            // ── Polling-based signal receiver (replaces Echo/WebSocket) ──
            let lastSignalId = 0;
            async function pollSignals() {
                try {
                    const res = await fetch(`/support/call/${SESSION}/poll-signals?for=customer&after=${lastSignalId}`);
                    const data = await res.json();
                    for (const e of (data.signals || [])) {
                        lastSignalId = Math.max(lastSignalId, e.id);
                        console.log('[MÜŞTƏRİ] Siqnal alındı:', e.from, e.type);

                        if (e.type === 'offer' && !offerReceived) {
                            offerReceived = true;
                            clearInterval(readyInterval);
                            try {
                                const cleanedSdp = cleanSdp(e.payload.sdp);
                                await pc.setRemoteDescription(new RTCSessionDescription({
                                    type: e.payload.type,
                                    sdp:  cleanedSdp,
                                }));
                                for (const c of pendingCandidates) {
                                    try { await pc.addIceCandidate(new RTCIceCandidate(c)); } catch {}
                                }
                                pendingCandidates = [];
                                const answer = await pc.createAnswer();
                                await pc.setLocalDescription(answer);
                                await sendSignal('answer', { type: answer.type, sdp: answer.sdp });
                                setStatus('connecting', 'Cavab göndərildi, bağlanılır...');
                            } catch (err) {
                                console.error('offer handling error:', err);
                            }
                        }

                        if (e.type === 'ice-candidate' && e.payload) {
                            if (pc.remoteDescription) {
                                try { await pc.addIceCandidate(new RTCIceCandidate(e.payload)); } catch {}
                            } else {
                                pendingCandidates.push(e.payload);
                            }
                        }

                        if (e.type === 'call-ended') {
                            clearInterval(pollInterval);
                            clearInterval(readyInterval);
                            setStatus('ended', 'Zəng operator tərəfindən bitirildi');
                            localStream?.getTracks().forEach(t => t.stop());
                            if (pc) pc.close();
                            setTimeout(() => window.close(), 3000);
                        }
                    }
                } catch (err) {
                    console.warn('[MÜŞTƏRİ] poll xətası:', err.message);
                }
            }
            const pollInterval = setInterval(pollSignals, 800);

            // Send customer-ready and retry until offer arrives
            console.log('[MÜŞTƏRİ] customer-ready göndərilir...');
            await sendSignal('customer-ready', { ready: true });
            readyInterval = setInterval(async () => {
                if (!offerReceived) {
                    console.log('[MÜŞTƏRİ] customer-ready retry...');
                    await sendSignal('customer-ready', { ready: true });
                }
            }, 3000);

            setStatus('connecting', 'Hazır — operator bağlanmasını gözləyir...');
        })();
    </script>
</body>
</html>
