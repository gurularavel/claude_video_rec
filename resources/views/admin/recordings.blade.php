@extends('layouts.app')

@section('content')
<style>
:root {
    --bg-base:    #070d18;
    --bg-surface: #0f1924;
    --bg-elevated:#152033;
    --border:     rgba(6, 182, 212, 0.12);
    --cyan:       #06b6d4;
    --cyan-dim:   rgba(6, 182, 212, 0.1);
    --success:    #10b981;
    --warning:    #f59e0b;
    --danger:     #ef4444;
    --purple:     #8b5cf6;
    --text:       #e2e8f0;
    --muted:      #64748b;
    --subtle:     #94a3b8;
}

.rec-page { max-width: 1100px; margin: 0 auto; }

/* ─── Page header ─── */
.rec-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.75rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.rec-title {
    font-family: 'Inter', sans-serif;
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--text);
}

.rec-sub { font-size: 0.875rem; color: var(--muted); margin-top: 4px; }

.header-badge {
    background: rgba(139,92,246,.12);
    border: 1px solid rgba(139,92,246,.3);
    color: var(--purple);
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* ─── Stats row ─── */
.stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 1.75rem;
}

.stat-card {
    background: var(--bg-surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1rem 1.25rem;
}

.stat-card-label {
    font-size: 0.72rem;
    font-weight: 600;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.stat-card-value {
    font-family: 'Inter', sans-serif;
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--text);
}

.stat-card-value.cyan   { color: var(--cyan); }
.stat-card-value.green  { color: var(--success); }
.stat-card-value.purple { color: var(--purple); }
.stat-card-value.amber  { color: var(--warning); }

/* ─── Sessions grid ─── */
.sessions-grid {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.session-card {
    background: var(--bg-surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    transition: border-color 0.2s;
}

.session-card:hover { border-color: rgba(6,182,212,.25); }

.session-head {
    padding: 1.125rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
    user-select: none;
}

.session-head:hover .expand-btn { opacity: 1; }

.customer-avatar-sm {
    width: 40px; height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--cyan), var(--purple));
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Inter', sans-serif;
    font-size: 0.875rem;
    font-weight: 800;
    color: #fff;
    flex-shrink: 0;
}

.session-info { flex: 1; min-width: 0; }

.session-customer {
    font-family: 'Inter', sans-serif;
    font-size: 0.9375rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 4px;
}

.session-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.smeta {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.78rem;
    color: var(--muted);
}

.smeta svg { flex-shrink: 0; color: var(--muted); }

/* ─── Status chip ─── */
.chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 600;
    border: 1px solid transparent;
}

.chip.completed { background: rgba(16,185,129,.1); color: var(--success); border-color: rgba(16,185,129,.2); }
.chip.active    { background: rgba(6,182,212,.1);  color: var(--cyan);    border-color: rgba(6,182,212,.2); }

/* ─── Duration ─── */
.duration-pill {
    background: var(--bg-elevated);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 6px 14px;
    font-family: 'Inter', sans-serif;
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--text);
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 6px;
}

.expand-btn {
    width: 30px; height: 30px;
    border-radius: 6px;
    background: rgba(255,255,255,.04);
    border: 1px solid var(--border);
    color: var(--muted);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    opacity: 0.7;
    flex-shrink: 0;
}

.expand-btn:hover { background: rgba(6,182,212,.1); color: var(--cyan); }
.expand-btn.open svg { transform: rotate(180deg); }
.expand-btn svg { transition: transform 0.2s; }

/* ─── Recording body ─── */
.recording-body {
    display: none;
    border-top: 1px solid var(--border);
    padding: 1.25rem;
    background: rgba(0,0,0,.15);
}

.recording-body.open { display: block; }

.recordings-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.recording-item {
    background: var(--bg-elevated);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.rec-thumb {
    width: 120px; height: 68px;
    border-radius: 7px;
    background: #000;
    flex-shrink: 0;
    overflow: hidden;
    position: relative;
    cursor: pointer;
    border: 1px solid rgba(255,255,255,.08);
}

.rec-thumb video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    pointer-events: none;
}

.rec-thumb::after {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(6,182,212,.15);
    opacity: 0;
    transition: opacity 0.2s;
}

.rec-thumb:hover::after { opacity: 1; }

.play-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
}

.play-icon {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: rgba(6,182,212,.85);
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
    transition: transform 0.15s;
}

.rec-thumb:hover .play-icon { transform: scale(1.1); }

.rec-details { flex: 1; min-width: 0; }

.rec-filename {
    font-family: 'Inter', sans-serif;
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 6px;
}

.rec-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.rec-meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.78rem;
    color: var(--muted);
}

.btn-watch {
    background: var(--cyan-dim);
    border: 1px solid rgba(6,182,212,.25);
    border-radius: 8px;
    padding: 8px 16px;
    color: var(--cyan);
    font-family: 'Inter', sans-serif;
    font-size: 0.8rem;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: background 0.2s, box-shadow 0.2s;
    white-space: nowrap;
    flex-shrink: 0;
}

.btn-watch:hover { background: rgba(6,182,212,.2); box-shadow: 0 4px 12px rgba(6,182,212,.2); }

/* ─── Video modal ─── */
.video-modal-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.85);
    backdrop-filter: blur(12px);
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.video-modal-overlay.open { display: flex; }

.video-modal {
    background: var(--bg-surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    max-width: 900px;
    width: 100%;
    box-shadow: 0 40px 80px rgba(0,0,0,.6);
    animation: modal-in 0.3s cubic-bezier(0.16,1,0.3,1) both;
}

@keyframes modal-in {
    from { opacity: 0; transform: scale(0.95) translateY(20px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}

.modal-header {
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--border);
}

.modal-title {
    font-family: 'Inter', sans-serif;
    font-size: 0.9375rem;
    font-weight: 700;
    color: var(--text);
}

.modal-close {
    width: 32px; height: 32px;
    border-radius: 8px;
    background: rgba(255,255,255,.06);
    border: 1px solid var(--border);
    color: var(--muted);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s;
}

.modal-close:hover { background: rgba(239,68,68,.1); color: var(--danger); border-color: rgba(239,68,68,.3); }

.modal-body { padding: 0; background: #000; }

.modal-body video {
    width: 100%;
    max-height: 70vh;
    display: block;
}

/* ─── Pagination ─── */
.pagination-wrap {
    margin-top: 1.5rem;
    display: flex;
    justify-content: center;
}

.pagination-wrap .pagination { gap: 4px; }
.page-link {
    background: var(--bg-surface) !important;
    border-color: var(--border) !important;
    color: var(--subtle) !important;
    border-radius: 8px !important;
    font-size: 0.875rem;
    padding: 6px 12px;
}

.page-item.active .page-link {
    background: var(--cyan-dim) !important;
    border-color: rgba(6,182,212,.3) !important;
    color: var(--cyan) !important;
}

.page-link:hover {
    background: var(--bg-elevated) !important;
    color: var(--text) !important;
}

/* ─── Empty state ─── */
.empty-recordings {
    background: var(--bg-surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 4rem 2rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
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

.empty-recordings h3 { font-family: 'Inter', sans-serif; font-size: 1rem; font-weight: 700; color: var(--subtle); }
.empty-recordings p { font-size: 0.875rem; color: var(--muted); }
</style>

<div class="rec-page">
    {{-- Header --}}
    <div class="rec-header">
        <div>
            <h1 class="rec-title">Bütün Yazılar</h1>
            <p class="rec-sub">Video zənglərin tam arxivi</p>
        </div>
        <div class="header-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            Superadmin Görünüşü
        </div>
    </div>

    {{-- Stats row --}}
    @php
        $totalSessions  = $sessions->total();
        $totalDuration  = $sessions->pluck('duration_seconds')->sum();
        $totalRecordings = $sessions->sum(fn($s) => $s->recordings->count());
        $totalMins = $totalDuration > 0 ? round($totalDuration / 60) : 0;
    @endphp

    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-card-label">Sessiyalar</div>
            <div class="stat-card-value cyan">{{ $totalSessions }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-label">Yazılar</div>
            <div class="stat-card-value purple">{{ $totalRecordings }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-label">Cəmi Müddət</div>
            <div class="stat-card-value amber">{{ $totalMins }} dəq</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-label">Bu Səhifə</div>
            <div class="stat-card-value green">{{ $sessions->count() }}</div>
        </div>
    </div>

    {{-- Sessions list --}}
    @if($sessions->count() > 0)
        <div class="sessions-grid">
            @foreach($sessions as $session)
            <div class="session-card">
                {{-- Session head --}}
                <div class="session-head" onclick="toggleSession('sess-{{ $session->id }}', this)">
                    <div class="customer-avatar-sm">
                        {{ strtoupper(substr($session->customer_name ?? 'A', 0, 2)) }}
                    </div>

                    <div class="session-info">
                        <div class="session-customer">
                            {{ $session->customer_name ?? 'Anonim Müştəri' }}
                        </div>
                        <div class="session-meta">
                            @if($session->customer_phone)
                                <span class="smeta">
                                    <svg viewBox="0 0 24 24" fill="currentColor" width="12" height="12"><path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 0 0-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.12-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z"/></svg>
                                    {{ $session->customer_phone }}
                                </span>
                            @endif
                            @if($session->customer_email)
                                <span class="smeta">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                    {{ $session->customer_email }}
                                </span>
                            @endif
                            @if($session->acceptedBy)
                                <span class="smeta">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    {{ $session->acceptedBy->name }}
                                </span>
                            @endif
                            @if($session->started_at)
                                <span class="smeta">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    {{ $session->started_at->format('d.m.Y H:i') }}
                                </span>
                            @endif
                            <span class="chip {{ $session->status === 'completed' ? 'completed' : 'active' }}">
                                {{ $session->status === 'completed' ? 'Tamamlandı' : ucfirst($session->status) }}
                            </span>
                        </div>
                    </div>

                    @if($session->duration_seconds)
                        <div class="duration-pill">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            @php
                                $m = floor($session->duration_seconds / 60);
                                $s = $session->duration_seconds % 60;
                            @endphp
                            {{ $m }}:{{ str_pad($s, 2, '0', STR_PAD_LEFT) }}
                        </div>
                    @endif

                    <div class="expand-btn" id="expand-{{ $session->id }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>

                {{-- Recording body --}}
                <div class="recording-body" id="sess-{{ $session->id }}">
                    <div class="recordings-list">
                        @foreach($session->recordings as $recording)
                        <div class="recording-item">
                            {{-- Thumbnail --}}
                            <div class="rec-thumb" onclick="openModal('{{ Storage::url($recording->file_path) }}', '{{ $session->customer_name ?? 'Anonim' }} — {{ $session->started_at?->format('d.m.Y H:i') }}')">
                                <video src="{{ Storage::url($recording->file_path) }}" preload="metadata"></video>
                                <div class="play-overlay">
                                    <div class="play-icon">
                                        <svg viewBox="0 0 24 24" fill="white" width="14" height="14"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Details --}}
                            <div class="rec-details">
                                <div class="rec-filename">{{ $recording->file_name }}</div>
                                <div class="rec-meta">
                                    @if($recording->duration)
                                        <div class="rec-meta-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            @php $rm = floor($recording->duration/60); $rs = $recording->duration%60; @endphp
                                            {{ $rm }}:{{ str_pad($rs,2,'0',STR_PAD_LEFT) }}
                                        </div>
                                    @endif
                                    @if($recording->size)
                                        <div class="rec-meta-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                            {{ round($recording->size / (1024*1024), 1) }} MB
                                        </div>
                                    @endif
                                    @if($recording->recording_completed_at)
                                        <div class="rec-meta-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                            {{ $recording->recording_completed_at->format('d.m.Y H:i') }}
                                        </div>
                                    @endif
                                    <div class="rec-meta-item" style="color: var(--cyan);">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
                                        {{ strtoupper($recording->format ?? 'webm') }}
                                    </div>
                                </div>
                            </div>

                            {{-- Watch button --}}
                            <a href="{{ Storage::url($recording->file_path) }}"
                               target="_blank"
                               class="btn-watch"
                               onclick="event.preventDefault(); openModal('{{ Storage::url($recording->file_path) }}', '{{ $session->customer_name ?? 'Anonim' }}')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                İzlə
                            </a>
                        </div>
                        @endforeach

                        @if($session->recordings->isEmpty())
                            <div style="text-align:center;padding:1.5rem;color:var(--muted);font-size:.875rem;">
                                Bu sessiya üçün yazı tapılmadı
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="empty-recordings">
            <div class="empty-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="36" height="36">
                    <polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/>
                </svg>
            </div>
            <h3>Yazı Tapılmadı</h3>
            <p>Hələ heç bir video sessiya yazılmayıb</p>
        </div>
    @endif

    {{-- Pagination --}}
    @if($sessions->hasPages())
        <div class="pagination-wrap">
            {{ $sessions->links() }}
        </div>
    @endif
</div>

{{-- Video Modal --}}
<div class="video-modal-overlay" id="videoModal" onclick="closeModalOnOverlay(event)">
    <div class="video-modal">
        <div class="modal-header">
            <div class="modal-title" id="modal-title">Video İzlə</div>
            <button class="modal-close" onclick="closeModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <video id="modal-video" controls></video>
        </div>
    </div>
</div>

<script>
function toggleSession(bodyId, headEl) {
    const body   = document.getElementById(bodyId);
    const sessId = bodyId.replace('sess-', '');
    const expBtn = document.getElementById('expand-' + sessId);

    const isOpen = body.classList.contains('open');
    body.classList.toggle('open', !isOpen);
    expBtn.classList.toggle('open', !isOpen);
}

function openModal(src, title) {
    const overlay = document.getElementById('videoModal');
    const video   = document.getElementById('modal-video');
    const titleEl = document.getElementById('modal-title');

    video.src = src;
    titleEl.textContent = title || 'Video İzlə';
    overlay.classList.add('open');
    video.play().catch(() => {});
}

function closeModal() {
    const overlay = document.getElementById('videoModal');
    const video   = document.getElementById('modal-video');
    overlay.classList.remove('open');
    video.pause();
    video.src = '';
}

function closeModalOnOverlay(e) {
    if (e.target === document.getElementById('videoModal')) closeModal();
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>
@endsection
