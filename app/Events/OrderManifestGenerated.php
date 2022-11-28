<?php

namespace App\Events;

use App\PdfAsset;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderManifestGenerated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $Target;
    public $Asset;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($Target, PdfAsset $Asset)
    {
        $this->Target = $Target;
        $this->Asset = $Asset;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
