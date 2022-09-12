<?php

namespace App\Modules\Chat\Transformers;

use League\Fractal\TransformerAbstract;
use App\Modules\Chat\Models\Message;

class MessageTransformer extends TransformerAbstract
{
	protected $currentUserId;

	public function __construct($currentUserId)
	{
		$this->currentUserId = $currentUserId;
	}

    public function transform(Message $message)
    {
        $messageArray = [
        	'id' => $message->id,
            'username' => $message->user->name,
            'message' => $message->content,
            'own_message' => $message->sender_id == $this->currentUserId,
            'system_offer' => $message->system_if_offer,
            'seller_offer' => $message->seller_if_offer,
            'sentiment' => $message->sentiment
        ];

        return $messageArray;
    }
}
