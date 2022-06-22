<?php

declare(strict_types=1);

namespace App\ProjectClass;

use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * class GenerateAuthRefreshToken
 * @package App\ProjectClass
 */
class GenerateAuthRefreshToken
{
    private const REFRESH_TOKEN_CHARS = '==';
    public const JWT_TOKEN_REGEX = '/^[a-zA-Z0-9\-_]+?\.[a-zA-Z0-9\-_]+?\.([a-zA-Z0-9\-_]+)?$/';

    /**
     * @param string $accessToken
     * @return string
     */
    public static function encode(string $accessToken): string
    {
        if (!preg_match(self::JWT_TOKEN_REGEX, $accessToken)) {
            throw new BadRequestException();
        }

        return rtrim(Crypt::encryptString($accessToken), self::REFRESH_TOKEN_CHARS);
    }

    /**
     * @param string $refreshToken
     * @return string
     */
    public static function decode(string $refreshToken): string
    {
        return Crypt::decryptString(sprintf("%s%s", $refreshToken, self::REFRESH_TOKEN_CHARS));
    }
}
