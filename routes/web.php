<?php

use App\Http\Controllers\SupportSessionController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Auth::routes();

// Operator routes (protected by auth middleware)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('operator.dashboard');
    })->name('dashboard');

    // Support session management
    Route::post('/support/generate', [SupportSessionController::class, 'generate'])
        ->name('support.generate');

    Route::post('/support/{session}/accept', [SupportSessionController::class, 'accept'])
        ->name('support.accept');

    Route::post('/support/{session}/start-call', [SupportSessionController::class, 'startCall'])
        ->name('support.start-call');

    Route::post('/support/{session}/end-call', [SupportSessionController::class, 'endCall'])
        ->name('support.end-call');

    Route::post('/support/{session}/token', [SupportSessionController::class, 'getToken'])
        ->name('support.token');

    Route::get('/support/{session}/status', [SupportSessionController::class, 'status'])
        ->name('support.status');

    // Video room view
    Route::get('/support/{session}/video', function ($session) {
        return view('operator.video-room', ['sessionUuid' => $session]);
    })->name('support.video-room');
});

// Customer routes (no auth required)
Route::get('/support/call/{session}', function ($session) {
    return view('customer.waiting-room', ['sessionUuid' => $session]);
})->name('support.waiting-room');

Route::post('/support/call/{session}/join', [SupportSessionController::class, 'joinWaitingRoom'])
    ->name('support.join-waiting-room');

Route::post('/support/call/{session}/token', [SupportSessionController::class, 'getToken'])
    ->name('support.customer-token');

Route::get('/support/call/{session}/video', function ($session) {
    return view('customer.video-room', ['sessionUuid' => $session]);
})->name('support.customer-video-room');

// Webhook routes (no auth/CSRF required)
Route::post('/webhooks/twilio/room-status', [WebhookController::class, 'roomStatus'])
    ->name('webhooks.twilio.room-status')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::post('/webhooks/twilio/recording-status', [WebhookController::class, 'recordingStatus'])
    ->name('webhooks.twilio.recording-status')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
