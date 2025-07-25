<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessagesRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $messageId;

    public $readerId;

    public function __construct($messageId, $readerId)
    {
        $this->messageId = $messageId;
        $this->readerId = $readerId;

        Message::where('id', $messageId)
            ->update(['is_seen' => true]);
    }

    public function broadcastOn()
    {
        return new PrivateChannel("chat.{$this->readerId}");
    }

    public function broadcastWith()
    {
        return [
            'message_id' => $this->messageId,
            //  'reader_id' => $this->readerId,
            'is_read' => true,
        ];
    }
}
