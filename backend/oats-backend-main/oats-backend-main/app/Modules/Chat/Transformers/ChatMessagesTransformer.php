<?php

namespace App\Modules\Chat\Transformers;

use League\Fractal\TransformerAbstract;
use App\Modules\Chat\Models\Chat;

class ChatMessagesTransformer extends TransformerAbstract
{
	protected $currentUserId;
	protected $defaultIncludes = ['messages'];

	public function __construct($currentUserId)
	{
		$this->currentUserId = $currentUserId;
	}

    public function transform(Chat $chat)
    {
        $chatArray = [
            'chat_id' => $chat->id,
            'listing_id' => $chat->listing_id,
            'listing_item' => $chat->listing->title,
            'listing_user' => $chat->listing->user->username,
            'listing_price' => $chat->listing->price
        ];

        return $chatArray;
    }

    public function includeMessages($messages)
    {
        return $this->collection($messages->message, new MessageTransformer($this->currentUserId));
    }

}
