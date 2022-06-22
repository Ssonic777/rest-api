<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Mail\EmailVerification;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Testing\Fakes\MailFake;
use Tests\TestCase;
use Tests\Unit\Traits\AuthTrait;

/**
 * class ForgotPasswordTest
 * @package Tests\Unit\Auth
 */
class RestorePasswordTest extends TestCase
{
    use AuthTrait;

    /**
     * @var MailFake $mailFake
     */
    private MailFake $mailFake;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mailFake = Mail::fake();
    }

    public function testUserSentLinkResetPassword(): void
    {
        $this->assertGuest();
        $response = $this->getJson(
            route('auth.restore_password.restore', ['email' => $this->email])
        )->assertStatus(Response::HTTP_ACCEPTED);

        $this->mailFake->assertSent(EmailVerification::class, function (EmailVerification $mail): bool {
            return $mail->user->email = $this->email && $mail->hasTo($this->email);
        });

        if (config('app.debug')) {
            ['email_code' => $this->emailCode] = $response->getOriginalContent();

            $response->assertExactJson([
                'message' => 'Password change request was sent',
                'email_code' => $this->emailCode
            ]);

            $this->assertDatabaseHas(User::class, [
                'email' => $this->email,
                'email_code' => $this->emailCode
            ]);

            Cache::forget('test_email_code');
            Cache::set('test_email_code', $this->emailCode, now()->addMinutes(15));
        } else {
            $response->assertNoContent(Response::HTTP_ACCEPTED);
        }
    }

    public function testUserRestoreNewPassword(): void
    {
        $this->handleValidationExceptions();
        $this->emailCode = (int) Cache::get('test_email_code');
        Cache::forget('test_email_code');

        $data = [
            'email_code' => $this->emailCode,
            'email' => $this->email,
            'password' => $this->password
        ];

        $this->assertGuest();
        $response = $this->putJson(route('auth.restore_password.restore'), $data)->assertOk();
        $this->assertAuth($response);
    }
}
