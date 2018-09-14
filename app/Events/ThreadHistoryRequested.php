<?php

namespace App\Events;

use App\Thread;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ThreadHistoryRequested implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $thread;
    public $start_message_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Thread $thread, $start_message_id)
    {
        $this->thread = $thread;
        $this->start_message_id = $start_message_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('thread:'.$this->thread->id);
    }

    public function broadcastWith(){
        return [
            'thread' => $this->thread->load(['messages' => function($query){
                return $query->where('id', '>=', $this->start_message_id);
            }])
        ];
    }

}
