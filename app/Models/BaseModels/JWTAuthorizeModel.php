<?php

declare(strict_types=1);

namespace App\Models\BaseModels;

use App\Models\User;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class JWTAuthorizeModel
 * @package App\Models\BaseModels
 */
abstract class JWTAuthorizeModel extends Authenticatable implements JWTSubject
{

    /**
     * User id
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    public static function createUser(Request $request): User
    {
        $user = new User();
        $user->password = $request->password;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->username = $request->first_name . $request->last_name . random_int(1, 100);
        $user->email = $request->email;
        $user->email_code = self::generateEmailCode();

        return $user;
    }

    public static function generateEmailCode(): int
    {
        return mt_rand(100000, 999999);
    }

    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * @return bool
     */
    public function activeAccount(): bool
    {
        $this->attributes['active'] = User::USER_STATUS_ACTIVE;
        $this->attributes['email_code'] = '';

        return $this->save();
    }
}
