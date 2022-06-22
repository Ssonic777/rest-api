<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Mail\EmailVerification;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Testing\Fakes\MailFake;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Tests\Unit\Traits\AuthTrait;

/**
 * class ActiveTest
 * @package Tests\Unit\Auth
 */
class ActiveTest extends TestCase
{
    use AuthTrait;

    /**
     * @var MailFake $mailFake
     */
    private MailFake $mailFake;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->mailFake = Mail::fake();
    }

    public function testUserSentActiveEmail(): void
    {
        /** @var User $user */
        $user = User::inRandomOrder()
                        ->where('active', '=', User::USER_STATUS_NOT_ACTIVE)
                        ->first();

        $response = $this->getJson(route('auth.active.email', ['email' => $user->email]));

        $this->mailFake->assertSent(
            EmailVerification::class,
            fn (EmailVerification $mail): bool => $mail->user->user_id === $user->user_id
                                                  && $mail->hasTo($user->email)
        );

        $response->assertStatus(Response::HTTP_ACCEPTED);
        $response->assertJsonFragment(['message' => 'Verification email was successfully sent']);

        $response->assertJsonFragment([
            'message' => 'Verification email was successfully sent'
        ]);

        $user = $user->refresh();
        $this->assertDatabaseHas(User::class, [
            'email' => $user->email,
            'email_code' => $user->email_code,
        ]);

        if (config('app.debug')) {
            $response->assertJsonStructure([
                'message',
                'email_code'
            ]);

            ['email_code' => $this->emailCode] = $response->getOriginalContent();

            Cache::forget('test_email_code');
            Cache::set('test_email_code', $this->emailCode, now()->addMinutes(15));
        }
    }

    public function testUserActiveAccount(): void
    {
        /** @var User $user */
        $user = User::inRandomOrder()
                    ->where('active', '=', User::USER_STATUS_NOT_ACTIVE)
                    ->whereNotNull('email_code')
                    ->first();

        $data = [
            'email' => $user->email,
            'otp_code' => $user->email_code
        ];

        /** @var TestResponse $response */
        $response = $this->putJson(route('auth.active.activate'), $data)->assertOk();
        $response->assertOk();
        $this->assertAuth($response);
    }
}
