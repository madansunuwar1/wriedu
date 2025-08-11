<?php

namespace App\Events;

use App\Models\RawLead;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
// ... other imports
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RawLeadCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $rawLead;

    public function __construct(RawLead $rawLead)
    {
        $this->rawLead = $rawLead->load('assignee:id,name');
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('raw-leads'),
        ];
    }

    /**
     * [NEW] Define the name the event will be broadcast as.
     * This makes the frontend listener simpler and more reliable.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'raw-lead.created';
    }
}