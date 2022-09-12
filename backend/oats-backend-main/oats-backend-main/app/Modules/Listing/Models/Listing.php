<?php

namespace App\Modules\Listing\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Account\User\Models\User;

class Listing extends Model
{
    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    
}