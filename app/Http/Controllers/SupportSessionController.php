<?php

namespace App\Http\Controllers;

use App\Models\Recording;
use App\Models\SupportSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SupportSessionController extends Controller
{
    /**
     * Generate a new support link
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'customer_name'  => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
        ]);

        $session = SupportSession::create([
            'operator_id'    => auth()->id(),
            'status'         => 'pending',
            'customer_name'  => $request->input('customer_name'),
            'customer_email' => $request->input('customer_email'),
            'customer_phone' => $request->input('customer_phone'),
        ]);

        return response()->json([
            'success' => true,
            'session' => $session,
            'link'    => route('support.waiting-room', $session->uuid),
        ]);
    }

    /**
     * Customer joins waiting room
     */
    public function joinWaitingRoom(SupportSession $session, Request $request): JsonResponse
    {
        if ($session->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This support session is no longer available.',
            ], 400);
        }

        $session->update([
            'status'             => 'waiting',
            'customer_joined_at' => now(),
        ]);

        return response()->json([
            'success'      => true,
            'session_uuid' => $session->uuid,
        ]);
    }

    /**
     * Operator accepts the call (HTTP route, not used by Livewire dashboard)
     */
    public function accept(SupportSession $session): JsonResponse
    {
        $accepted = DB::transaction(function () use ($session) {
            $session = SupportSession::where('id', $session->id)
                ->where('status', 'waiting')
                ->whereNull('accepted_by')
                ->lockForUpdate()
                ->first();

            if (!$session) {
                return false;
            }

            $session->update([
                'accepted_by'         => auth()->id(),
                'status'              => 'active',
                'operator_joined_at'  => now(),
            ]);

            return $session;
        });

        if (!$accepted) {
            return response()->json([
                'success' => false,
                'message' => 'This call has already been accepted by another operator.',
            ], 409);
        }

        return response()->json([
            'success'      => true,
            'session'      => $accepted,
            'redirect_url' => route('support.video-room', $accepted->uuid),
        ]);
    }

    /**
     * Start video call — marks started_at
     */
    public function startCall(SupportSession $session): JsonResponse
    {
        if ($session->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot start call — invalid session status.',
            ], 400);
        }

        $session->update(['started_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Relay a WebRTC signaling message via cache-based polling.
     * Called by both operator (auth) and customer (public) routes.
     */
    public function signal(SupportSession $session, Request $request): JsonResponse
    {
        $request->validate([
            'from'    => 'required|string|in:operator,customer',
            'type'    => 'required|string',
            'payload' => 'present',
        ]);

        $from     = $request->input('from');
        $target   = $from === 'operator' ? 'customer' : 'operator';
        $cacheKey = "webrtc.{$session->uuid}.to_{$target}";

        $signals   = Cache::get($cacheKey, []);
        $signals[] = [
            'id'      => count($signals) + 1,
            'from'    => $from,
            'type'    => $request->input('type'),
            'payload' => $request->input('payload'),
        ];
        Cache::put($cacheKey, $signals, 3600);

        return response()->json(['success' => true]);
    }

    /**
     * Poll for new WebRTC signals.
     */
    public function pollSignals(SupportSession $session, Request $request): JsonResponse
    {
        $for      = $request->query('for');
        $afterId  = (int) $request->query('after', 0);
        $cacheKey = "webrtc.{$session->uuid}.to_{$for}";
        $signals  = Cache::get($cacheKey, []);

        $newSignals = array_values(array_filter($signals, fn($s) => $s['id'] > $afterId));

        return response()->json(['signals' => $newSignals]);
    }

    /**
     * End the call — updates session and notifies customer via polling signal
     */
    public function endCall(SupportSession $session): JsonResponse
    {
        $duration = $session->started_at
            ? now()->diffInSeconds($session->started_at)
            : 0;

        $session->update([
            'status'           => 'completed',
            'ended_at'         => now(),
            'duration_seconds' => $duration,
        ]);

        $cacheKey = "webrtc.{$session->uuid}.to_customer";
        $signals  = Cache::get($cacheKey, []);
        $signals[] = [
            'id'      => count($signals) + 1,
            'from'    => 'operator',
            'type'    => 'call-ended',
            'payload' => null,
        ];
        Cache::put($cacheKey, $signals, 3600);

        return response()->json([
            'success'          => true,
            'duration_seconds' => $duration,
        ]);
    }

    /**
     * Upload recorded video from operator's browser (MediaRecorder blob)
     */
    public function uploadRecording(SupportSession $session, Request $request): JsonResponse
    {
        $request->validate([
            'recording' => 'required|file',
            'duration'  => 'nullable|integer',
        ]);

        $file     = $request->file('recording');
        $fileName = 'recording.webm';
        $path     = $file->storeAs("recordings/{$session->uuid}", $fileName, 'public');

        Recording::create([
            'support_session_id'     => $session->id,
            'file_name'              => $fileName,
            'file_path'              => $path,
            'recording_status'       => 'completed',
            'duration'               => $request->input('duration'),
            'size'                   => $file->getSize(),
            'format'                 => 'webm',
            'recording_started_at'   => $session->started_at,
            'recording_completed_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Get session status
     */
    public function status(SupportSession $session): JsonResponse
    {
        return response()->json([
            'success' => true,
            'session' => $session->load(['operator', 'acceptedBy', 'recordings']),
        ]);
    }
}
