<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\Unit\Traits\AuthTrait;

/**
 * class AuthorizationTest
 * @package Tests\Unit\Auth
 */
class AuthorizationTest extends TestCase
{
    use WithFaker;
    use AuthTrait;

    public function testAuthRegister(): void
    {
        $data = [
            'first_name' => $this->faker->name,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->email,
            'password' => $this->password
        ];

        $response = $this->postJson(route('auth.register'), $data)->assertOk();

        unset($data['password']);
        $this->assertDatabaseHas(User::class, array_merge(
            $data,
            ['email_code' => $response['email_code']]
        ));

        $response->assertJsonStructure([
            'message',
            'error_code',
            'email_code',
        ]);
    }

    public function testAuthRegisterNegative(): void
    {
        $data = [
            'first_name' => $this->faker->name . 'awd',
            'last_name' => $this->faker->lastName . 'awd',
            'email' => $this->faker->email . 'aawdwd',
            'password' => $this->password
        ];


        $response = $this->postJson(route('auth.register'), $data)->assertOk();

        $response->assertJsonStructure([
            'message',
            'error_code'
        ]);

        $responseNegative = $this->postJson(route('auth.login'), [
                'email' => $data['email'],
                'password' => $data['password']
            ]);

        $responseNegative->assertStatus(Response::HTTP_BAD_REQUEST);
        $responseNegative->assertExactJson([
            'error' => 'Bad request',
            'message' => 'Error verify user',
            'error_code' => -1400
        ]);

        unset($data['password']);
        $this->assertDatabaseHas(User::class, array_merge(
            $data,
            ['email_code' => $response['email_code']]
        ));
    }

    public function testAuthLogin(): void
    {
        $credentials = [
            'email' => $this->email,
            'password' => $this->password
        ];

        $response = $this->postJson(route('auth.login'), $credentials)->assertOk();

        $this->assertAuth($response);
    }

    public function testAuthRefreshToken(): void
    {
        $credentials = [
            'email' => $this->email,
            'password' => $this->password
        ];

        $response = $this->postJson(route('auth.login'), $credentials);
        $this->assertAuth($response);

        $response = $this->getJson(route('auth.refresh'), [
            'Authorization' => $this->accessToken
        ]);
        $this->assertAuth($response);
    }

    public function testAuthLogout(): void
    {
        $data = [
            'email' => $this->email,
            'password' => $this->password
        ];

        $response = $this->postJson(route('auth.login'), $data)->assertOk();
        $this->assertAuth($response);

        $responseLogout = $this->postJson(route('auth.logout'))->assertOk();
        $responseLogout->assertExactJson([
            'message' => 'Successfully logged out'
        ]);
    }
}
