<?php

use App\Http\Controllers\SupportSessionController;
use App\Models\Recording;
use App\Models\SupportSession;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Disable registration
Auth::routes(['register' => false]);

// Operator routes (auth required)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('operator.dashboard');
    })->name('dashboard');

    Route::post('/support/generate', [SupportSessionController::class, 'generate'])
        ->name('support.generate');

    Route::post('/support/{session}/accept', [SupportSessionController::class, 'accept'])
        ->name('support.accept');

    Route::post('/support/{session}/start-call', [SupportSessionController::class, 'startCall'])
        ->name('support.start-call');

    Route::post('/support/{session}/end-call', [SupportSessionController::class, 'endCall'])
        ->name('support.end-call');

    // WebRTC signaling from operator side
    Route::post('/support/{session}/signal', [SupportSessionController::class, 'signal'])
        ->name('support.signal');

    Route::get('/support/{session}/poll-signals', [SupportSessionController::class, 'pollSignals'])
        ->name('support.poll-signals');

    // Recording upload (operator's browser sends the blob)
    Route::post('/support/{session}/recording', [SupportSessionController::class, 'uploadRecording'])
        ->name('support.upload-recording');

    Route::get('/support/{session}/status', [SupportSessionController::class, 'status'])
        ->name('support.status');

    Route::get('/support/{session}/video', function (SupportSession $session) {
        return view('operator.video-room', [
            'sessionUuid'    => $session->uuid,
            'session'        => $session,
        ]);
    })->name('support.video-room');

    // Superadmin: recordings overview
    Route::get('/admin/recordings', function () {
        if (!auth()->user()->is_superadmin) {
            abort(403);
        }

        $sessions = SupportSession::with(['operator', 'acceptedBy', 'recordings'])
            ->whereHas('recordings')
            ->latest()
            ->paginate(20);

        return view('admin.recordings', compact('sessions'));
    })->name('admin.recordings');
});

// Customer routes (no auth required)
Route::get('/support/call/{session}', function ($session) {
    return view('customer.waiting-room', ['sessionUuid' => $session]);
})->name('support.waiting-room');

Route::post('/support/call/{session}/join', [SupportSessionController::class, 'joinWaitingRoom'])
    ->name('support.join-waiting-room');

// WebRTC signaling from customer side (no auth)
Route::post('/support/call/{session}/signal', [SupportSessionController::class, 'signal'])
    ->name('support.customer-signal');

Route::get('/support/call/{session}/poll-signals', [SupportSessionController::class, 'pollSignals'])
    ->name('support.customer-poll-signals');

Route::get('/support/call/{session}/video', function ($session) {
    return view('customer.video-room', ['sessionUuid' => $session]);
})->name('support.customer-video-room');

Route::get('/support/call/{session}/status', [SupportSessionController::class, 'status'])
    ->name('support.customer-status');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
