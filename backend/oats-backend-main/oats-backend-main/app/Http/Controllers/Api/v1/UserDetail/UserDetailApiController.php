<?php

namespace App\Http\Controllers\Api\v1\UserDetail;

use Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Fractalistic\ArraySerializer;
use App\Modules\Account\User\Models\User;
use App\Http\Controllers\Api\ApiController;

/**
* @group User Details endpoints
*/
class UserDetailApiController extends ApiController
{
    // returns User's username and caroupoint
    public function index()
    {
        $user = Auth::user();

        $userDetails = [
            "username"=>$user->username,
            "caroupoint"=>$user->caroupoint
        ];

        return $this->respondSuccess($userDetails, trans('api.generic.index.success', ['resource' => 'Messages']));
    }
}
