<?php

namespace App\Events;

use App\Models\SupportSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public SupportSession $session;
    public string $roomName;
    public string $roomSid;

    /**
     * Create a new event instance.
     */
    public function __construct(SupportSession $session, string $roomName, string $roomSid)
    {
        $this->session = $session;
        $this->roomName = $roomName;
        $this->roomSid = $roomSid;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('support-session.' . $this->session->uuid),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'session_uuid' => $this->session->uuid,
            'room_name' => $this->roomName,
            'room_sid' => $this->roomSid,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
