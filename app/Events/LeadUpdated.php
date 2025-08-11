<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $lead;

    public function __construct($lead)
    {
        $this->lead = $lead->load('application', 'creator');
    }

    public function broadcastOn()
    {
        return ['leads'];
    }

    public function broadcastAs()
    {
        return 'lead.updated';
    }
}