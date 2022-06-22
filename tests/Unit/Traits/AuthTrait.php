<?php

declare(strict_types=1);

namespace Tests\Unit\Traits;

use Illuminate\Testing\TestResponse;

trait AuthTrait
{
    /**
     * @var string $email
     */
    private string $email = 'user@gmail.com';

    /**
     * @var string $password
     */
    private string $password = '12345Secret!';

    /**
     * @var int $emailCode
     */
    private int $emailCode;

    /**
     * @var string|null $accessToken
     */
    private ?string $accessToken;

    /**
     * @var string|null $tokenType
     */
    private ?string $tokenType;

    /**
     * @var int|null $expiresIn
     */
    private ?int $expiresIn;

    /**
     * @param TestResponse $response
     */
    private function assertAuth(TestResponse $response): void
    {
        $this->assertAuthenticated();
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ]);

        [
            'access_token' => $this->accessToken,
            'token_type' => $this->tokenType,
            'expires_in' => $this->expiresIn
        ] = $response->getOriginalContent();

        $this->assertNotNull($this->accessToken);
        $this->assertSame($this->tokenType, 'bearer');
        $this->assertSame($this->expiresIn, 216000);
    }
}
