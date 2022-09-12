<?php

namespace App\Modules\Account\User\Models;

use Auth;
use Schema;
use Laravel\Passport\HasApiTokens;
use App\Modules\Chat\Models\Message;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Password;
use App\Modules\Chat\Models\ChatParticipant;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Authenticatable implements CanResetPasswordContract
{
    use CanResetPassword,
        HasApiTokens;

    const INSIGHTS_TOKEN_NAME = 'Insights';
    
    protected $connection = 'mysql';

    protected $guarded = [];

    public function messages() {
        return $this->hasMany(Message::class, 'sender_id', 'id');
    }

    public function chatParticipant() {
        return $this->hasMany(ChatParticipant::class);
    }


    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * Generate random string for password
     *
     * @param Integer $length
     *
     * @return string
     */
    public static function generateRandomPassword($length = 10)
    {
        $charactersLength = strlen(self::CHARACTERS);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= self::CHARACTERS[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Removes access tokens.
     *
     * @param      string  $appName  The application name
     *
     * @return     void
     */
    public function revokeExistingTokensFor(string $appName)
    {
        $this->tokens()->where(
            [
                'name' => $appName,
                'revoked' => false
            ]
        )->update(['revoked' => true]);
    }
}
