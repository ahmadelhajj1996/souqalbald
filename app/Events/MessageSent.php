<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        //  \Log::info('MessageSent event constructed with message ID: ' . $message->id);
        $this->message = $message->load('sender');
    }

    public function broadcastOn()
    {
        //  \Log::info('Broadcasting MessageSent event for chat_id: ' . $this->message->chat_id);
        return new PrivateChannel('chat.'.$this->message->chat_id);
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->message->message,
            'sender' => $this->message->sender->name,
            'created_at' => $this->message->created_at->toDateTimeString(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
