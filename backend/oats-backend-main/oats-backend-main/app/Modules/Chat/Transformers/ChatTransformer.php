<?php

namespace App\Modules\Chat\Transformers;

use League\Fractal\TransformerAbstract;
use App\Modules\Chat\Models\Chat;

class ChatTransformer extends TransformerAbstract
{
	protected $currentUserId;
	// protected $defaultIncludes = ['participants'];

	public function __construct($currentUserId)
	{
		$this->currentUserId = $currentUserId;
	}

    public function transform(Chat $chat)
    {
    	$userId = $this->currentUserId;
    	$participants = $chat->chatParticipant
                                ->filter(function($item) use ($userId) {
					    		 return $item->user_id != $userId;
					    		});
        $chatArray = [
            'chat_id' => $chat->id,
            'listing_id' => $chat->listing_id,
            'listing_item' => $chat->listing->title,
            'last_message' => empty($chat->latestMessage) ? null : $chat->latestMessage->content,
            'participants' => $participants->pluck('user.username')
        ];

        return $chatArray;
    }
}
