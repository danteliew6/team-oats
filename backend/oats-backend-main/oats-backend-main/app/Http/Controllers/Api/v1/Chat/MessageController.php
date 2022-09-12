<?php

namespace App\Http\Controllers\Api\v1\Chat;

use Auth;
use Exception;
use Carbon\Carbon;
use App\Jobs\CheckOffer;
use App\Events\MessageSent;
use Spatie\Fractal\Fractal;
use Illuminate\Http\Request;
use App\Jobs\CheckSentiment;
use App\Jobs\BroadcastPoint;
use App\Jobs\BroadcastUpdate;
use App\Jobs\BroadcastMessage;
use GuzzleHttp\RequestOptions;
use App\Modules\Chat\Models\Chat;
use App\Modules\Chat\Models\Message;
use App\Modules\Chat\Models\Listing;
use Spatie\Fractalistic\ArraySerializer;
use App\Modules\Chat\Jobs\LiftSuspension;
use App\Modules\Account\User\Models\User;
use App\Modules\Chat\Events\UserSuspended;
use GuzzleHttp\Client as GuzzleHttpClient;
use App\Http\Controllers\Api\ApiController;
use App\Modules\Chat\Transformers\MessageTransformer;
use App\Modules\Chat\Transformers\ChatMessagesTransformer;

/**
* @group Chat endpoints
*/
class MessageController extends ApiController
{
    public function index()
    {
        $messages = Chat::with('message.user')
                        ->get();

        return $this->respondSuccess($messages, trans('api.generic.index.success', ['resource' => 'Messages']));
    }

    public function messagesByChatId($chatId)
    {
        $user = Auth::user();

        $messages = Chat::findOrFail($chatId)
                        ->with('message.user','chatParticipant', 'listing', 'listing.user');

        $messages = $messages->where('id', $chatId)
                                ->first();
        
        $participants = $messages->chatParticipant->pluck('user_id')->toArray();

        if (!in_array($user->id, $participants)) {
            return $this->respondUnauthorized();
        }

        $messages = Fractal($messages, new ChatMessagesTransformer($user->id))
                        ->serializeWith(new ArraySerializer())
                        ->toArray();

        return $this->respondSuccess($messages, trans('api.generic.index.success', ['resource' => 'Messages']));
    }


    public function store($chatId, Request $request)
    {
        $user = Auth::user();

        $message = $user->messages()->create([
            'chat_id' => $chatId,
            'content' => $request->input('message')
        ]);

        $chatListing = Chat::findOrFail($chatId)
                        ->with('listing');
        $chatListing = $chatListing->where('id', $chatId)
                            ->first();

        $seller = $chatListing->listing->user_id;
        if ($seller !== $user->id) {
            $payload = [
             'message' => $request->input('message')
            ];

            $client = new GuzzleHttpClient;
            $res = $client->post('https://m0yvj161p3.execute-api.us-east-1.amazonaws.com/oats-staging/checkifoffer', [
                RequestOptions::JSON => $payload
            ]);

            $body = json_decode($res->getBody()->getContents()); 

            if ($body->statusCode == 200) {
                if ($body->body->quantity and $body->body->offer <= ($chatListing->listing->price * 0.8)) {
                    $message->system_if_offer = 1;
                }else {
                    $message->system_if_offer = 0;
                }
            } else {
                return $this->respondError('System Error',500);
            }
            
            $sentimentAnalysis = $client->put("https://m0yvj161p3.execute-api.us-east-1.amazonaws.com/oats-staging/SentimentAnalysisOats", [
                RequestOptions::JSON => $payload
            ]);
            $sentimentAnalysis = json_decode($sentimentAnalysis->getBody()->getContents());
            if ($sentimentAnalysis->statusCode == 200) {
                $sentiment = $sentimentAnalysis->body->Sentiment;
                $message->sentiment = $sentiment;
                if ($sentiment == 'NEGATIVE') {
                    $user->caroupoint--;
                    $user->save();
                    BroadcastPoint::dispatchAfterResponse($user);
                }
               
            } else {
                return $this->respondError('System Error',500);
            }
            $message->save();
        }

        $message = $message->fresh();

        broadcast(new MessageSent($user, $message))->toOthers();

        $message = Fractal($message, new MessageTransformer($user->id))->toArray();

        return $this->respondSuccess($message, trans('success', ['resource' => 'Messages']));
    }

    public function storeAsync($chatId, Request $request)
    {
        $user = Auth::user();

        $message = $user->messages()->create([
            'chat_id' => $chatId,
            'content' => $request->input('message')
        ]);

        $message = $message->fresh();

        BroadcastMessage::dispatchAfterResponse($user,$message);
        CheckSentiment::dispatchAfterResponse($user,$message);

        $chatListing = Chat::findOrFail($chatId)
                        ->with('listing');
        $chatListing = $chatListing->where('id', $chatId)
                            ->first();

        $seller = $chatListing->listing->user_id;

        if ($seller !== $user->id) {
            CheckOffer::dispatchAfterResponse($message,$chatListing->listing->price);
        }

        $message = Fractal($message, new MessageTransformer($user->id))->toArray();

        return $this->respondSuccess($message, trans('success', ['resource' => 'Messages']));
    }

    public function updateSellerOffer($messageId, $status)
    {
        $user = Auth::user();

        $message = Message::where('id', $messageId)
                                ->first();

        $message->seller_if_offer = $status;
        $message->save();

        $messages = Message::where('chat_id', $message->chat_id)
                            ->get();
        if ($status == 0){
            $sender = User::where('id',$message->sender_id)
                            ->first();
            $sender->caroupoint--;
            $sender->save();
            BroadcastPoint::dispatchAfterResponse($sender);
        }

        BroadcastUpdate::dispatchAfterResponse($message->chat_id, $message->sender_id);

        $messages = Fractal($messages, new MessageTransformer($user->id))
                        ->serializeWith(new ArraySerializer())
                        ->toArray();

        return $this->respondSuccess($messages, trans('success', ['resource' => 'Messages']));
    }

}
