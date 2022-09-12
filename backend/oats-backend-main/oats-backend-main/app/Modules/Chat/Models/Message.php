<?php

namespace App\Modules\Chat\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Account\User\Models\User;

class Message extends Model
{
    protected $fillable = [
        'content',
        'chat_id',
        'sentiment',
        'system_if_offer',
        'seller_if_offer'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function chat() {
        return $this->belongsTo(Chat::class, 'chat_id', 'id');
    }
}
