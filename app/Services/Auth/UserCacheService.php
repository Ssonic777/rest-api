<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

/**
 * Class UserCacheService
 * @package App\Services\Auth
 */
class UserCacheService
{
    /**
     * @var string $rememberKey
     */
    private static string $rememberKey = "active-created-account";

    /**
     * @var array|string[] $rememberAttributeKeys
     */
    private static array $rememberAttributeKeys = [
        'email',
        'email_code',
    ];

    /**
     * @param User $user
     * @return User
     */
    public static function remember(User $user): User
    {
        foreach (self::$rememberAttributeKeys as $attribute) {
            $key = $user->getAttribute($attribute);
            Cache::put(self::$rememberKey . "-{$key}", $user, now()->addHour());
        }

        return $user;
    }

    /**
     * @param string $value
     * @param bool $isPull
     * @return User|null
     */
    public static function findBy(string $value, bool $isPull = false): ?User
    {
        $key = self::$rememberKey . "-{$value}";

        return $isPull ? Cache::pull($key) : Cache::get($key);
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function forget(string $key): bool
    {
        return Cache::forget($key);
    }
}
