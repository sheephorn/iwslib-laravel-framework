<?php

namespaceIwslibLaravel\Events\Model;

use IwslibLaravel\Models\Feature\IModelFeature;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class ModelChangeEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public IModelFeature $model;

    /**
     * Create a new event instance.
     */
    public function __construct(IModelFeature $model)
    {
        $this->model = $model;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
