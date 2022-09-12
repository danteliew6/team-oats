<?php

namespace App\Http\Controllers\Api\v1\Chat;

use Auth;
use Exception;
use App\Events\MessageSent;
use Spatie\Fractal\Fractal;
use Illuminate\Http\Request;
use App\Modules\Chat\Models\Chat;
use App\Modules\Listing\Models\Listing;
use Spatie\Fractalistic\ArraySerializer;
use App\Modules\Account\User\Models\User;
use App\Http\Controllers\Api\ApiController;
use App\Modules\Chat\Models\ChatParticipant;
use App\Modules\Chat\Transformers\ChatTransformer;

/**
* @group Chat endpoints
*/
class ChatController extends ApiController
{
    public function index()
    {
        $user = Auth::user();

        $chats = Chat::with("chatParticipant.user","listing","latestMessage")
                        ->whereHas("chatParticipant", function ($query) use ($user) {
                            return $query->where('user_id', '=', $user->id);
                        })
                        ->get();

        $chats = Fractal::create()
                    ->collection($chats)
                    ->transformWith(new ChatTransformer($user->id))
                    ->serializeWith(new ArraySerializer())
                    ->toArray();

        return $this->respondSuccess($chats, trans('api.generic.index.success', ['resource' => 'Messages']));
    }

    public function createNewConversation(Request $request)
    {
        $creator = Auth::user();
        $listingId = $request->input('listing_id');

        $chat = Chat::where([
                            'creator_id' => $creator->id,
                            'listing_id' => $listingId,
                        ])->firstOr(function() use ($creator, $listingId) {
                            $chat = Chat::Create([
                                'creator_id' => $creator->id,
                                'listing_id' => $listingId,
                            ]);

                            $userChat = ChatParticipant::Create([
                                'user_id' => $creator->id,
                                'chat_id' => $chat->id,
                            ]);

                           $targetId = Listing::where('id', $listingId)
                                        ->first()
                                        ->user_id;

                            $targetChat = ChatParticipant::Create([
                                'user_id' => $targetId,
                                'chat_id' => $chat->id,
                            ]);

                            return $chat;
                        });
        $data = [
            'chat_id' => $chat->id
        ];
        return $this->respondSuccess($data, trans('success', ['resource' => 'Chat']));
    }
}
