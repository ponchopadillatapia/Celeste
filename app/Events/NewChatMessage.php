<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ChatMessage $chatMessage) {}

    public function broadcastOn(): array
    {
        // Canal privado por departamento destino
        return [
            new PresenceChannel('chat.department.' . $this->chatMessage->department_to),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->chatMessage->id,
            'user_id' => $this->chatMessage->user_id,
            'user_name' => $this->chatMessage->user->name,
            'department_from' => $this->chatMessage->department_from,
            'department_to' => $this->chatMessage->department_to,
            'message' => $this->chatMessage->message,
            'created_at' => $this->chatMessage->created_at->toISOString(),
        ];
    }
}
