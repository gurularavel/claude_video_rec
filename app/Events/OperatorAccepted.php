<?php

namespace App\Events;

use App\Models\SupportSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OperatorAccepted implements ShouldBroadcastNow
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
            'session_id' => $this->session->id,
            'session_uuid' => $this->session->uuid,
            'operator_id' => $this->session->accepted_by,
            'operator_name' => $this->session->acceptedBy->name ?? 'Operator',
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
