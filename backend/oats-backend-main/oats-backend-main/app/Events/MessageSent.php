<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use App\Modules\Chat\Models\Message;
use Illuminate\Queue\SerializesModels;
use App\Modules\Account\User\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * User that sent the message
     *
     * @var User
     */
    public $user;

    /**
     * Message details
     *
     * @var Message
     */
    public $message;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Message $message)
    {
        $this->user = $user;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('chat'.$this->message->chat_id);
    }

    public function broadcastAs()
    {
        return "MessageSent";
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'username' => $this->user->name,
            'message' => $this->message->content,
            'own_message' => false,
            'system_offer' => $this->message->system_if_offer,
            'seller_offer' => $this->message->seller_if_offer
        ];
    }
}
