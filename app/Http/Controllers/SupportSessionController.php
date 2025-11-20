<?php

namespace App\Http\Controllers;

use App\Events\CallEnded;
use App\Events\CallStarted;
use App\Events\CustomerWaiting;
use App\Events\OperatorAccepted;
use App\Models\SupportSession;
use App\Services\TwilioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SupportSessionController extends Controller
{
    protected TwilioService $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    /**
     * Generate a new support link
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
        ]);

        $session = SupportSession::create([
            'operator_id' => auth()->id(),
            'status' => 'pending',
            'customer_name' => $request->input('customer_name'),
            'customer_email' => $request->input('customer_email'),
        ]);

        return response()->json([
            'success' => true,
            'session' => $session,
            'link' => route('support.waiting-room', $session->uuid),
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

        // Update session status to waiting
        $session->update([
            'status' => 'waiting',
            'customer_joined_at' => now(),
        ]);

        // Broadcast to all available operators
        broadcast(new CustomerWaiting($session))->toOthers();

        return response()->json([
            'success' => true,
            'session_uuid' => $session->uuid,
        ]);
    }

    /**
     * Operator accepts the call
     */
    public function accept(SupportSession $session): JsonResponse
    {
        // Use database transaction to prevent race conditions
        $accepted = DB::transaction(function () use ($session) {
            // Reload session with lock
            $session = SupportSession::where('id', $session->id)
                ->where('status', 'waiting')
                ->whereNull('accepted_by')
                ->lockForUpdate()
                ->first();

            if (!$session) {
                return false;
            }

            // Update session with operator
            $session->update([
                'accepted_by' => auth()->id(),
                'status' => 'active',
                'operator_joined_at' => now(),
            ]);

            return $session;
        });

        if (!$accepted) {
            return response()->json([
                'success' => false,
                'message' => 'This call has already been accepted by another operator.',
            ], 409);
        }

        // Broadcast that operator accepted
        broadcast(new OperatorAccepted($accepted))->toOthers();

        return response()->json([
            'success' => true,
            'session' => $accepted,
            'redirect_url' => route('support.video-room', $accepted->uuid),
        ]);
    }

    /**
     * Start video call (creates Twilio room)
     */
    public function startCall(SupportSession $session): JsonResponse
    {
        if ($session->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot start call - invalid session status.',
            ], 400);
        }

        // Create Twilio room
        $roomName = 'support-' . $session->uuid;

        try {
            $room = $this->twilioService->createRoom($roomName);

            // Update session with room details
            $session->update([
                'twilio_room_sid' => $room->sid,
                'twilio_room_name' => $room->uniqueName,
                'started_at' => now(),
            ]);

            // Broadcast call started
            broadcast(new CallStarted($session, $room->uniqueName, $room->sid))->toOthers();

            return response()->json([
                'success' => true,
                'room_name' => $room->uniqueName,
                'room_sid' => $room->sid,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create video room: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get access token for video call
     */
    public function getToken(SupportSession $session, Request $request): JsonResponse
    {
        $request->validate([
            'identity' => 'required|string',
        ]);

        if (!$session->twilio_room_name) {
            return response()->json([
                'success' => false,
                'message' => 'Video room not created yet.',
            ], 400);
        }

        try {
            $token = $this->twilioService->generateToken(
                $request->input('identity'),
                $session->twilio_room_name
            );

            return response()->json([
                'success' => true,
                'token' => $token,
                'room_name' => $session->twilio_room_name,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate token: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * End the call
     */
    public function endCall(SupportSession $session): JsonResponse
    {
        if (!$session->twilio_room_sid) {
            return response()->json([
                'success' => false,
                'message' => 'No active call to end.',
            ], 400);
        }

        try {
            // Complete the Twilio room
            $this->twilioService->completeRoom($session->twilio_room_sid);

            // Calculate duration
            $duration = $session->started_at ? now()->diffInSeconds($session->started_at) : 0;

            // Update session
            $session->update([
                'status' => 'completed',
                'ended_at' => now(),
                'duration_seconds' => $duration,
            ]);

            // Broadcast call ended
            broadcast(new CallEnded($session))->toOthers();

            return response()->json([
                'success' => true,
                'duration_seconds' => $duration,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to end call: ' . $e->getMessage(),
            ], 500);
        }
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
