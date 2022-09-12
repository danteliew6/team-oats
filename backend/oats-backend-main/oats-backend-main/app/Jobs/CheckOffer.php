<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\RequestOptions;
use App\Events\UpdateChat;

class CheckOffer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $price;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($message, $price)
    {
        $this->message = $message;
        $this->price = $price;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $payload = [
         'message' => $this->message->content
        ];

        $client = new GuzzleHttpClient;
        $res = $client->post('https://m0yvj161p3.execute-api.us-east-1.amazonaws.com/oats-staging/checkifoffer', [
            RequestOptions::JSON => $payload
        ]);

        $body = json_decode($res->getBody()->getContents()); 

        if ($body->statusCode == 200) {
            if ($body->body->quantity and $body->body->offer <= ($this->price * 0.8)) {
                $this->message->system_if_offer = 1;
                UpdateChat::dispatch($this->message->chat_id, $this->message->sender_id);
            }else {
                $this->message->system_if_offer = 0;
            }
        } else {
            Log::error('Error checking offers: ', [
                            'messageId' => $this->message->id,
                            'resource' => 'AWS Lambda'
                        ]);
        }

        $this->message->save();
    }
}
