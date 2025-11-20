<?php

namespace App\Http\Controllers;

use App\Models\SupportSession;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected TwilioService $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    /**
     * Handle Twilio room status webhook
     */
    public function roomStatus(Request $request): Response
    {
        Log::info('Twilio Room Status Webhook', $request->all());

        $roomSid = $request->input('RoomSid');
        $roomStatus = $request->input('RoomStatus');

        $session = SupportSession::where('twilio_room_sid', $roomSid)->first();

        if (!$session) {
            Log::warning('Session not found for room', ['room_sid' => $roomSid]);
            return response('Session not found', 404);
        }

        // If room is completed, fetch and save recordings
        if ($roomStatus === 'completed') {
            $this->processRecordings($session);
        }

        return response('OK', 200);
    }

    /**
     * Handle Twilio recording status webhook
     */
    public function recordingStatus(Request $request): Response
    {
        Log::info('Twilio Recording Status Webhook', $request->all());

        $recordingSid = $request->input('RecordingSid');
        $status = $request->input('RecordingStatus');
        $duration = $request->input('RecordingDuration');
        $size = $request->input('RecordingSize');

        // Find recording in database and update status
        $recording = \App\Models\Recording::where('twilio_recording_sid', $recordingSid)->first();

        if ($recording) {
            $recording->update([
                'recording_status' => $status,
                'duration' => $duration,
                'size' => $size,
                'recording_completed_at' => $status === 'completed' ? now() : null,
            ]);
        }

        return response('OK', 200);
    }

    /**
     * Process recordings for a session
     */
    protected function processRecordings(SupportSession $session): void
    {
        try {
            // Wait a few seconds to ensure Twilio has processed the recordings
            sleep(5);

            $recordings = $this->twilioService->getRoomRecordings($session->twilio_room_sid);

            foreach ($recordings as $twilioRecording) {
                // Check if recording already exists
                $exists = $session->recordings()
                    ->where('twilio_recording_sid', $twilioRecording->sid)
                    ->exists();

                if (!$exists) {
                    $this->twilioService->saveRecording($session, $twilioRecording);
                    Log::info('Recording saved', [
                        'session_id' => $session->id,
                        'recording_sid' => $twilioRecording->sid,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to process recordings', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
