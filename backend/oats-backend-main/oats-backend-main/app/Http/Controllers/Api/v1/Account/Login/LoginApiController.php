<?php

namespace App\Http\Controllers\Api\v1\Account\Login;

use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Modules\Account\User\Models\User;
use App\Http\Controllers\Api\ApiController;

/**
* @group Account endpoints
*/
class LoginApiController extends ApiController
{
    /**
    * Login by Email and Password
    *
    * Login for user using email and password. If user credential exists in database, return 200 OK response and API token
    *
    * Otherwise, return 401 Unauthoriszed.
    *
    * @unauthenticated
    *
    * @bodyParam email email required The email of the user. Example: test@email.com
    * @bodyParam password password required The password of the user. Example: password
    *
    * @responseField access_token The Token for the user.
    *
    * @response 200 {
    *   "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiZWU1YmE5YTg4YmJkYjJjM2VmYzlhZTk5ZDk1OGViYTc0ODg1M2RlY2MzMDc1NDZmMmI3YThmNGIyZjkxNDA0MmFhYTNjN2M5MGYyMzY3NWUiLCJpYXQiOjE2MjMyOTU0NDcuMDUxNDE4LCJuYmYiOjE2MjMyOTU0NDcuMDUxNDIzLCJleHAiOjE2NTQ4MzE0NDYuODUwNTY1LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.KCWNlW8JKBpgdpqYV3PK2Iud30pPwzXxk5dz1ShWoBLPMIRE6NY0E0yZcQo-NB9jutVdH-gPVfC9yUB_eqZPjAaIT9cLWlAMMPw76l2Ap6H6TIYqMm6dcR7JRwGTuJoQkspfYZDuLx-Xz6cWEMJrYzrdtOLdha_0e12w0H_raoanbFNHxjtzmuR-DT1TU74VkYMDD3r3aeNxuByuKRqhZq1WB7pWOPb3GP5mavTgyPOjB5_EeioHZuGvZMdae4y19GBVrUCSOlo_h9lRh88YHj-ek9c_NuufijsvFGJ-EGD9VRiEvc7graCPJbsYA1Z8XpbHheyqVuCcvDT84_2QuABipK8ScVnHRj5OTHjDeRIfA-yICQzZajuoEEAUgCDD1rKdfLTh1Hl3FE8Fnk44f89213_Rd0e3QNsJLnHrGKPRmVk3aWWbPbK_7Hoy0LXVymILGd5isVQQCDq-5W0JJWaxZR0HNzxYZCShm15MR8mDr7vja6sLh-vM9EVl_eEHHbjlPJUJkDILa9BI0o-m164PpF0YreaXni0yIs04S_WLF3J91KrLcoij4pNVzd7GpOKb_knx7lY_9zFqjbkZec-KSAQiI4YjpMynl15eiq5iVB7wWVe5gX6c-rPlJdgpObvWVLWPwgQj3xrWGS9HUo8-obucLqFoCtyC8Vepkfw"
    * }
    *
    * @param Request $request
    * @return Response
    */
    public function loginByEmail(Request $request)
    {
        // var_dump($request->input('hi'));
        $email = $request->input('email');
        $password = $request->input('password');
        $include = $request->input('include');

        $tokenName = User::INSIGHTS_TOKEN_NAME;

        if (Auth::attempt(['email'=> $email, 'password'=> $password])) {
            $user = User::find(Auth::id());

            //Remove all existing tokens
            $user->revokeExistingTokensFor($tokenName);

            $payload['access_token'] = $user->createToken($tokenName)->accessToken;
            $payload['username'] = $user->username;
            $payload['user_id'] = $user->id;
            $payload['is_ban'] = !is_null($user->suspension_period);
            $payload['ban_period'] = $user->suspension_period;
            $payload['points'] = $user->caroupoint;

            return $payload;
        }
        
        return $this->respondUnauthorized();
    }
}
