<?php

namespace App\Http\Controllers\Api\v1\Account\User\Register;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Modules\Account\User\Models\User;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Foundation\Auth\RegistersUsers;

/**
* @group Account endpoints
*/
class RegisterApiController extends ApiController
{
    /**
    * Register User Account
    *
    * Registration of user account. Return 201 Created response if successfully created.
    *
    * Otherwise, return 422 Unproccessable Entity and error messages
    *
    * @unauthenticated
    *
    * @bodyParam name name required The name of the user. Example: tester
    * @bodyParam email email required The email of the user. Example: test@email.com
    * @bodyParam password password required The password of the user. Example: password
    * @bodyParam password_confirmation password_confirmation required Confirmation of the user password. Example: password
    *
    * @responseField data The data of user account created
    * @responseField message The message of the response
    * @responseField errors The errors of the response e.g. (`The email has already been taken.` or `The password field is required.`)
    *
    * @response 201 {
    *   "data": {
    *     "name": "tester",
    *     "email": "test@email.com",
    *     "password": "$2y$10$CQoSjXMD299Z4\/gE4FYYdOuLYDbE9asceZVy.2PAPbPIkQ.v0cmC2",
    *     "updated_at": "2021-05-25T10:14:09.000000Z",
    *     "created_at": "2021-05-25T10:14:09.000000Z",
    *     "id": 1
    *   },
    *   "success": {
    *     "message": "User registered successfully"
    *   }
    * }
    *
    * @response 422 {
    *   "message": "The given data was invalid.",
    *   "errors": {
    *     "email": [
    *       "The email has already been taken."
    *     ],
    *     "password": [
    *       "The password field is required."
    *     ]
    *   }
    * }
    *
    * @param Request $request
    * @return Response
    */
    public function registerByEmail(Request $request)
    {
        $validator = $this->validator($request->all())->validate();
        $user = $this->create($request->all());
        return $this->respondCreated($user, 'User registered successfully');
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return App\Modules\Account\User\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);
    }
}
