<?php

namespace App\Services;

use App\Models\Recording;
use App\Models\SupportSession;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;
use Twilio\Rest\Client as TwilioClient;

class TwilioService
{
    protected TwilioClient $client;
    protected string $accountSid;
    protected string $apiKey;
    protected string $apiSecret;

    public function __construct()
    {
        $this->accountSid = config('services.twilio.account_sid');
        $this->apiKey = config('services.twilio.api_key');
        $this->apiSecret = config('services.twilio.api_secret');

        $authToken = config('services.twilio.auth_token');
        $this->client = new TwilioClient($this->accountSid, $authToken);
    }

    /**
     * Create a video room
     */
    public function createRoom(string $roomName): object
    {
        return $this->client->video->v1->rooms->create([
            'uniqueName' => $roomName,
            'type' => 'group',
            'recordParticipantsOnConnect' => true,
            'statusCallback' => route('webhooks.twilio.room-status'),
        ]);
    }

    /**
     * Generate access token for a participant
     */
    public function generateToken(string $identity, string $roomName): string
    {
        $token = new AccessToken(
            $this->accountSid,
            $this->apiKey,
            $this->apiSecret,
            3600,
            $identity
        );

        $videoGrant = new VideoGrant();
        $videoGrant->setRoom($roomName);
        $token->addGrant($videoGrant);

        return $token->toJWT();
    }

    /**
     * Get room by SID
     */
    public function getRoom(string $roomSid): object
    {
        return $this->client->video->v1->rooms($roomSid)->fetch();
    }

    /**
     * Complete a room
     */
    public function completeRoom(string $roomSid): object
    {
        return $this->client->video->v1->rooms($roomSid)->update(['status' => 'completed']);
    }

    /**
     * Get recordings for a room
     */
    public function getRoomRecordings(string $roomSid): array
    {
        $recordings = $this->client->video->v1->recordings->read([
            'groupingSid' => [$roomSid]
        ]);

        return iterator_to_array($recordings);
    }

    /**
     * Save recording to database
     */
    public function saveRecording(SupportSession $session, object $twilioRecording): Recording
    {
        return Recording::create([
            'support_session_id' => $session->id,
            'twilio_recording_sid' => $twilioRecording->sid,
            'recording_url' => $this->getRecordingUrl($twilioRecording->sid),
            'recording_status' => $twilioRecording->status,
            'duration' => $twilioRecording->duration,
            'size' => $twilioRecording->size ?? null,
            'format' => $twilioRecording->containerFormat ?? 'mka',
            'recording_started_at' => $twilioRecording->dateCreated,
        ]);
    }

    /**
     * Get recording URL
     */
    public function getRecordingUrl(string $recordingSid): string
    {
        return sprintf(
            'https://video.twilio.com/v1/Recordings/%s/Media',
            $recordingSid
        );
    }

    /**
     * Get recording media content
     */
    public function getRecordingMedia(string $recordingSid): string
    {
        return $this->client->video->v1->recordings($recordingSid)
            ->media()
            ->fetch()
            ->redirectTo;
    }
}
