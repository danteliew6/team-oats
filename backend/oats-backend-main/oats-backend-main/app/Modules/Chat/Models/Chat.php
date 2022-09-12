<?php

namespace App\Modules\Chat\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Listing\Models\Listing;
use App\Modules\Account\User\Models\User;

class Chat extends Model
{
    protected $fillable = [
        'creator_id',
        'listing_id'
    ];

    public function chatParticipant()
    {
        return $this->hasMany(ChatParticipant::class, 'chat_id');
    }

    public function message()
    {
        return $this->hasMany(Message::class, 'chat_id');
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }

    public function latestMessage()
    {
         return $this->hasOne(Message::class, 'chat_id', 'id')->latest();
    }
}