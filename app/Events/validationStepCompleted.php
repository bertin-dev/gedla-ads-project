<?php

namespace App\Events;

use App\Models\ValidationStep;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class validationStepCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $validationStepDetailsMedia;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($validationStepDetailsMedia)
    {
        $this->validationStepDetailsMedia = $validationStepDetailsMedia;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
