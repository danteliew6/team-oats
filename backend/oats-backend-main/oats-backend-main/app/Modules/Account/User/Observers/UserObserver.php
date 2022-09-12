<?php

namespace App\Modules\Account\User\Observers;

use Illuminate\Support\Arr;
use App\Modules\Chat\Models\Listing;
use App\Modules\Account\User\Models\User;
use App\Modules\Chat\Events\UserSuspended;


class UserObserver
{
    public $afterCommit = true;
    /**
     * Handle the User "updating" event
     *
     * @param      \App\Modules\Account\User\Models\User  $user   The user
     *
     * @return     \App\Modules\Account\User\Models\User
     */
    public function updating(User $user)
    {
        if ($user->isDirty('caroupoint')) {
            $caroupoint = $user->caroupoint;
            if ($caroupoint <= 80) {
                event(new UserSuspended($user));
            }
            else if ($caroupoint <= 95) {
                Listing::where(['user_id'=>$user->id,'deprioritized'=>0])->update(['deprioritized'=>1]);
            } 
            else if ($caroupoint == 96) {
                Listing::where(['user_id'=>$user->id,'deprioritized'=>1])->update(['deprioritized'=>0]);
            }
        }
    }

}