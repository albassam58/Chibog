<?php

namespace App\Events;

use App\Http\Controllers\API\v1\ChatController;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Chat implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $channel;

    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($channel, $data)
    {
        $this->channel = $channel;
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // // save notification before broadcast on socket
        // $request = [
        //     'channel'   => $this->channel,
        //     'message'   => $this->data['message'],
        //     'payload'   => json_encode($this->data['data']),
        //     'status'    => 1 // not yet read
        // ];

        // $controller = new ChatController;
        // $notification = $controller->store($request);
        // $this->data = $notification->original['data'];

        return new Channel($this->channel);
        // return new PrivateChannel('channel-name');
    }
}
