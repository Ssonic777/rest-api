<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * @param string $token
     * @param string $refreshToken
     * @return array
     */
    protected function generateAuthToken(string $token, string $refreshToken): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'refresh_token' => $refreshToken,
            'expires_in' => auth('api')->factory()->getTTL() * 60 * 60
        ];
    }
}
