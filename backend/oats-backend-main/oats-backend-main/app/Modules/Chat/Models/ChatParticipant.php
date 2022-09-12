<?php

namespace App\Modules\Chat\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Account\User\Models\User;

class ChatParticipant extends Model
{
    protected $fillable = [
        'user_id',
        'chat_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function chat() {
        return $this->belongsTo(Chat::class);
    }
}