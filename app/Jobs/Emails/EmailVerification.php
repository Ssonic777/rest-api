<?php

namespace App\Jobs\Emails;

use App\Jobs\DefaultJob;
use App\Mail\EmailVerification as MailEmailVerification;
use App\Models\User;
use Blockster\QueueProvider\DTO\Channel;
use Illuminate\Support\Facades\Mail;

class EmailVerification extends DefaultJob
{
    public const CHANNEL = 'Channel'; // Channel::QUEUE_EMAIL_VERIFICATION;

    private User $user;

    /**
     * EmailVerification constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    public function handle(): void
    {
        $user = $this->getUser();

        Mail::to($user)->send(new MailEmailVerification($user));
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
