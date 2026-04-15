<?php

namespace App\Events;

use App\Models\SupportSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebRTCSignal implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public SupportSession $session,
        public string $from,
        public string $type,
        public mixed $payload
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('call-signal.' . $this->session->uuid),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'from'    => $this->from,
            'type'    => $this->type,
            'payload' => $this->payload,
        ];
    }
}
