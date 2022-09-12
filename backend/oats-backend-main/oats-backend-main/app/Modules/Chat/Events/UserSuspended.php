<?php

namespace App\Modules\Chat\Events;

use Illuminate\Queue\SerializesModels;
use App\Modules\Account\User\Models\User;

class UserSuspended
{
    use SerializesModels;
    public $user;
    /**
     * Create a new event instance.
     *
     * @param  User  $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}