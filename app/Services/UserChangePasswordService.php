<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerification;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * class UserChangePasswordService
 * @package App\Services
 */
class UserChangePasswordService
{
    /**
     * @param User $user
     */
    public function requestPasswordChange(User $user): void
    {
        $user->email_code = User::generateEmailCode();
        $user->save();
        Mail::to($user)->send(new EmailVerification($user));
    }

    /**
     * @param User $user
     * @param array $data
     */
    public function verifyPasswordChange(User $user, array $data): void
    {
        if ($user->email_code !== $data['email_code']) {
            throw new BadRequestException('Invalid email_code.');
        }

        $user->update(array_merge($data, ['email_code' => '']));
    }
}
