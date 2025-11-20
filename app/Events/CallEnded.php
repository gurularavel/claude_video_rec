<?php

namespace App\Events;

use App\Models\SupportSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallEnded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public SupportSession $session;

    /**
     * Create a new event instance.
     */
    public function __construct(SupportSession $session)
    {
        $this->session = $session;
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
            new Channel('operators'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'session_uuid' => $this->session->uuid,
            'duration_seconds' => $this->session->duration_seconds,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
