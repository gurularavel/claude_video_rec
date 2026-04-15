<div class="dash-wrap">

    {{-- ─── Page header ─────────────────────────────────── --}}
    <div class="dash-header">
        <div>
            <h1 class="dash-title">Operator Paneli</h1>
            <p class="dash-sub">Müştəri zənglərini idarə edin</p>
        </div>
        <div class="status-pill available">
            <span class="status-dot"></span>
            Aktiv
        </div>
    </div>

    {{-- ─── Generate link section ────────────────────────── --}}
    <div class="section-card generate-card">
        <div class="section-head">
            <div class="section-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13.828 10.172a4 4 0 0 0-5.656 0l-4 4a4 4 0 1 0 5.656 5.656l1.102-1.101m-.758-4.899a4 4 0 0 0 5.656 0l4-4a4 4 0 0 0-5.656-5.656l-1.1 1.1"/></svg>
            </div>
            <div>
                <h2 class="section-title">Yeni Dəstək Linki</h2>
                <p class="section-desc">Müştəriyə göndərmək üçün link yaradın</p>
            </div>
        </div>

        <form wire:submit.prevent="generateLink" class="gen-form">
            <div class="form-row">
                <div class="form-field">
                    <label class="form-label">Ad Soyad</label>
                    <input type="text" wire:model="customerName" class="form-input" placeholder="Müştəri adı (istəyə bağlı)">
                </div>
                <div class="form-field">
                    <label class="form-label">Telefon</label>
                    <input type="tel" wire:model="customerPhone" class="form-input" placeholder="+994 __ ___ __ __">
                </div>
                <div class="form-field">
                    <label class="form-label">E-poçt</label>
                    <input type="email" wire:model="customerEmail" class="form-input" placeholder="email@example.com">
                </div>
                <div class="form-field form-field--btn">
                    <button type="submit" class="btn-generate" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16"><path d="M12 5v14M5 12h14"/></svg>
                            Link Yarat
                        </span>
                        <span wire:loading style="display:flex;align-items:center;gap:8px">
                            <svg class="spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                            Yaradılır...
                        </span>
                    </button>
                </div>
            </div>
        </form>

        {{-- Generated link box --}}
        @if($showLinkModal)
        <div class="link-result" wire:transition>
            <div class="link-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                Link hazırdır — müştəriyə göndərin
            </div>
            <div class="link-row">
                <input type="text" value="{{ $generatedLink }}" readonly class="link-input" id="generated-link">
                <button class="btn-copy" wire:click="copyLink" title="Kopyala">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                </button>
                <button class="btn-close-link" wire:click="closeModal" title="Bağla">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
        @endif
    </div>

    {{-- ─── Waiting customers ─────────────────────────────── --}}
    <div class="section-card">
        <div class="section-head">
            <div class="section-icon {{ count($waitingSessions) > 0 ? 'pulsing' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/></svg>
            </div>
            <div>
                <h2 class="section-title">
                    Gözləyən Müştərilər
                    @if(count($waitingSessions) > 0)
                        <span class="count-badge">{{ count($waitingSessions) }}</span>
                    @endif
                </h2>
                <p class="section-desc">
                    {{ count($waitingSessions) > 0 ? count($waitingSessions) . ' müştəri dəstək gözləyir' : 'Hazırda gözləyən müştəri yoxdur' }}
                </p>
            </div>
        </div>

        @if(count($waitingSessions) > 0)
            <div class="waiting-list">
                @foreach($waitingSessions as $session)
                <div class="waiting-card" wire:key="session-{{ $session['uuid'] }}">
                    {{-- Sonar pulse rings --}}
                    <div class="sonar-wrap">
                        <div class="sonar-ring sonar-ring--1"></div>
                        <div class="sonar-ring sonar-ring--2"></div>
                        <div class="sonar-ring sonar-ring--3"></div>
                        <div class="sonar-core">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 0 0-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.12-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z"/></svg>
                        </div>
                    </div>

                    <div class="waiting-info">
                        <div class="waiting-name">{{ $session['customer_name'] ?? 'Anonim Müştəri' }}</div>
                        <div class="waiting-meta">
                            @if(!empty($session['customer_phone']))
                                <span class="meta-item">
                                    <svg viewBox="0 0 24 24" fill="currentColor" width="12" height="12"><path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 0 0-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.12-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z"/></svg>
                                    {{ $session['customer_phone'] }}
                                </span>
                            @endif
                            @if(!empty($session['customer_email']))
                                <span class="meta-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                    {{ $session['customer_email'] }}
                                </span>
                            @endif
                            <span class="meta-item meta-time">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
                                {{ \Carbon\Carbon::parse($session['customer_joined_at'])->diffForHumans() }} gözləyir
                            </span>
                        </div>
                    </div>

                    <button
                        wire:click="acceptCall('{{ $session['uuid'] }}')"
                        class="btn-accept"
                        wire:loading.attr="disabled"
                        wire:target="acceptCall('{{ $session['uuid'] }}')"
                    >
                        <span wire:loading.remove wire:target="acceptCall('{{ $session['uuid'] }}')">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 0 0-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.12-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z"/></svg>
                            Qəbul et
                        </span>
                        <span wire:loading wire:target="acceptCall('{{ $session['uuid'] }}')" style="display:flex;align-items:center;gap:6px">
                            <svg class="spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                            Yüklənir...
                        </span>
                    </button>
                </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="40" height="40"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg>
                </div>
                <p>Gözləyən müştəri yoxdur</p>
                <span>Yeni link yaradaraq müştəriyə göndərin</span>
            </div>
        @endif
    </div>

    {{-- ─── Styles ─────────────────────────────────────────────── --}}
    <style>
:root {
    --bg-base:    #070d18;
    --bg-surface: #0f1924;
    --bg-elevated:#152033;
    --border:     rgba(6, 182, 212, 0.12);
    --cyan:       #06b6d4;
    --cyan-dim:   rgba(6, 182, 212, 0.12);
    --success:    #10b981;
    --warning:    #f59e0b;
    --text:       #e2e8f0;
    --muted:      #64748b;
    --subtle:     #94a3b8;
}

.dash-wrap {
    max-width: 900px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.dash-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 0;
}

.dash-title {
    font-family: 'Inter', sans-serif;
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--text);
}

.dash-sub { font-size: 0.875rem; color: var(--muted); margin-top: 4px; }

.status-pill {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.3px;
}

.status-pill.available {
    background: rgba(16,185,129,.1);
    color: #10b981;
    border: 1px solid rgba(16,185,129,.25);
}

.status-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: currentColor;
    animation: blink-dot 2s ease-in-out infinite;
}

@keyframes blink-dot {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

/* ─── Section cards ─── */
.section-card {
    background: var(--bg-surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 1.5rem;
    transition: border-color 0.3s;
}

.section-head {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    margin-bottom: 1.25rem;
}

.section-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    background: var(--cyan-dim);
    border: 1px solid rgba(6,182,212,.2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--cyan);
    flex-shrink: 0;
}

.section-icon svg { width: 18px; height: 18px; }

.section-icon.pulsing {
    animation: icon-pulse 2s ease-in-out infinite;
}

@keyframes icon-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(6,182,212,.4); }
    50% { box-shadow: 0 0 0 8px rgba(6,182,212,0); background: rgba(6,182,212,.2); }
}

.section-title {
    font-family: 'Inter', sans-serif;
    font-size: 1rem;
    font-weight: 700;
    color: var(--text);
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-desc { font-size: 0.8rem; color: var(--muted); margin-top: 3px; }

.count-badge {
    background: rgba(239,68,68,.15);
    color: #ef4444;
    border: 1px solid rgba(239,68,68,.3);
    font-size: 0.7rem;
    font-weight: 700;
    padding: 1px 8px;
    border-radius: 20px;
    font-family: 'Inter', sans-serif;
    animation: count-blink 1s ease-in-out infinite;
}

@keyframes count-blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

/* ─── Generate form ─── */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr auto;
    gap: 12px;
    align-items: end;
}

.form-field { display: flex; flex-direction: column; gap: 6px; }
.form-field--btn { min-width: 140px; }

.form-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--subtle);
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

.form-input {
    background: rgba(255,255,255,.04);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 10px 14px;
    color: var(--text);
    font-family: 'Inter', sans-serif;
    font-size: 0.875rem;
    outline: none;
    transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
    width: 100%;
}

.form-input:focus {
    border-color: var(--cyan);
    background: rgba(6,182,212,.05);
    box-shadow: 0 0 0 3px rgba(6,182,212,.1);
}

.form-input::placeholder { color: var(--muted); }

.btn-generate {
    background: linear-gradient(135deg, var(--cyan), #0891b2);
    border: none;
    border-radius: 8px;
    padding: 10px 18px;
    color: #fff;
    font-family: 'Inter', sans-serif;
    font-size: 0.875rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 7px;
    white-space: nowrap;
    transition: opacity 0.2s, box-shadow 0.2s;
    width: 100%;
    justify-content: center;
}

.btn-generate:hover { box-shadow: 0 6px 20px rgba(6,182,212,.35); }
.btn-generate:disabled { opacity: 0.7; cursor: not-allowed; }

/* ─── Link result ─── */
.link-result {
    margin-top: 1rem;
    background: rgba(16,185,129,.06);
    border: 1px solid rgba(16,185,129,.2);
    border-radius: 10px;
    padding: 14px;
}

.link-label {
    font-size: 0.8rem;
    color: #10b981;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 10px;
    font-weight: 500;
}

.link-row { display: flex; gap: 8px; }

.link-input {
    flex: 1;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(16,185,129,.2);
    border-radius: 7px;
    padding: 9px 14px;
    color: var(--subtle);
    font-family: 'Inter', sans-serif;
    font-size: 0.8rem;
    outline: none;
}

.btn-copy, .btn-close-link {
    background: rgba(255,255,255,.05);
    border: 1px solid var(--border);
    border-radius: 7px;
    color: var(--subtle);
    padding: 9px 12px;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
    display: flex;
    align-items: center;
}

.btn-copy:hover { background: rgba(6,182,212,.1); color: var(--cyan); border-color: rgba(6,182,212,.3); }
.btn-close-link:hover { background: rgba(239,68,68,.1); color: #ef4444; border-color: rgba(239,68,68,.3); }

/* ─── Waiting list ─── */
.waiting-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.waiting-card {
    background: var(--bg-elevated);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1.125rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    transition: border-color 0.3s, box-shadow 0.3s;
    animation: card-appear 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
}

.waiting-card:hover {
    border-color: rgba(6,182,212,.3);
    box-shadow: 0 0 20px rgba(6,182,212,.08);
}

@keyframes card-appear {
    from { opacity: 0; transform: translateX(-16px); }
    to   { opacity: 1; transform: translateX(0); }
}

/* ─── Sonar pulse ─── */
.sonar-wrap {
    position: relative;
    width: 52px; height: 52px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sonar-ring {
    position: absolute;
    border-radius: 50%;
    border: 1.5px solid var(--cyan);
    animation: sonar-expand 2.4s ease-out infinite;
}

.sonar-ring--1 { animation-delay: 0s; }
.sonar-ring--2 { animation-delay: 0.8s; }
.sonar-ring--3 { animation-delay: 1.6s; }

@keyframes sonar-expand {
    0%   { width: 34px; height: 34px; opacity: 0.9; }
    100% { width: 80px; height: 80px; opacity: 0; }
}

.sonar-core {
    position: relative;
    z-index: 2;
    width: 36px; height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(6,182,212,.25), rgba(139,92,246,.2));
    border: 1px solid rgba(6,182,212,.4);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--cyan);
}

/* ─── Waiting info ─── */
.waiting-info { flex: 1; min-width: 0; }

.waiting-name {
    font-family: 'Inter', sans-serif;
    font-size: 0.9375rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 6px;
}

.waiting-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.78rem;
    color: var(--muted);
}

.meta-item svg { color: var(--muted); flex-shrink: 0; }
.meta-time { color: var(--warning); }
.meta-time svg { color: var(--warning); }

/* ─── Accept button ─── */
.btn-accept {
    background: linear-gradient(135deg, #10b981, #059669);
    border: none;
    border-radius: 9px;
    padding: 10px 20px;
    color: #fff;
    font-family: 'Inter', sans-serif;
    font-size: 0.875rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 7px;
    white-space: nowrap;
    transition: box-shadow 0.2s, opacity 0.2s;
    flex-shrink: 0;
}

.btn-accept:hover { box-shadow: 0 6px 20px rgba(16,185,129,.4); }
.btn-accept:disabled { opacity: 0.7; cursor: not-allowed; }

/* ─── Empty state ─── */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem 1rem;
    gap: 10px;
    text-align: center;
}

.empty-icon {
    width: 72px; height: 72px;
    border-radius: 50%;
    background: var(--bg-elevated);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--muted);
    margin-bottom: 4px;
}

.empty-state p { font-size: 0.9375rem; color: var(--subtle); font-weight: 500; }
.empty-state span { font-size: 0.8rem; color: var(--muted); }

/* ─── Spinner ─── */
@keyframes spin { to { transform: rotate(360deg); } }
.spin { animation: spin 0.8s linear infinite; }

@media (max-width: 700px) {
    .form-row { grid-template-columns: 1fr; }
    .waiting-card { flex-wrap: wrap; }
    .btn-accept { width: 100%; justify-content: center; }
}
    </style>

</div>

@script
<script>
    $wire.on('copy-to-clipboard', (event) => {
        navigator.clipboard.writeText(event.text).then(() => {
            const btn = document.querySelector('.btn-copy');
            if (btn) {
                const orig = btn.innerHTML;
                btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" width="16" height="16"><path d="M20 6L9 17l-5-5"/></svg>';
                btn.style.color = '#10b981';
                setTimeout(() => { btn.innerHTML = orig; btn.style.color = ''; }, 2000);
            }
        });
    });

    $wire.on('customer-waiting', (event) => {
        // Browser notification
        if (Notification.permission === 'granted') {
            const data = event.data || event;
            new Notification('Yeni Müştəri!', {
                body: (data.customer_name || 'Anonim') + ' dəstək gözləyir',
                icon: '/favicon.ico'
            });
        }

        // Play notification sound
        try {
            const ctx = new AudioContext();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.setValueAtTime(880, ctx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(440, ctx.currentTime + 0.3);
            gain.gain.setValueAtTime(0.3, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.5);
            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.5);
        } catch(e) {}
    });

    if (Notification.permission === 'default') {
        Notification.requestPermission();
    }
</script>
@endscript
