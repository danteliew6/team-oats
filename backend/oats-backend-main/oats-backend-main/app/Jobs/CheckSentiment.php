<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\RequestOptions;
use App\Events\UpdateChat;
use App\Events\UpdatePoint;

class CheckSentiment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $message;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $message)
    {
        $this->user = $user;
        $this->message = $message;
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
        $sentimentAnalysis = $client->put("https://m0yvj161p3.execute-api.us-east-1.amazonaws.com/oats-staging/SentimentAnalysisOats", [
            RequestOptions::JSON => $payload
        ]);
        $sentimentAnalysis = json_decode($sentimentAnalysis->getBody()->getContents());
        if ($sentimentAnalysis->statusCode == 200) {
            $sentiment = $sentimentAnalysis->body->Sentiment;
            $this->message->sentiment = $sentiment;
            $this->message->save();
            if ($sentiment == 'NEGATIVE') {
                $this->user->caroupoint--;
                $this->user->save();
                UpdatePoint::dispatch($this->user);
                UpdateChat::dispatch($this->message->chat_id, $this->message->sender_id);
            }
           
        } else {
            Log::error('Error checking sentiment: ', [
                            'messageId' => $this->message->id,
                            'resource' => 'AWS Lambda'
                        ]);
        }
    }
}
