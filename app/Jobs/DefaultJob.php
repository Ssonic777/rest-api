<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Jobs\Emails\EmailVerification;
use Blockster\QueueProvider\DTO\Channel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class DefaultJob
 * @package App\Jobs
 */
abstract class DefaultJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public const SERVICE = 'rest_api';

    /**
     * Please override it in Job Class
     * @see EmailVerification
     */
    public const CHANNEL = '';

    /**
     * DefaultJob constructor.
     */
    public function __construct()
    {
//        $channel = new Channel(static::CHANNEL, static::SERVICE);
//        $this->onQueue($channel->getQueuePrefix());
    }

    /**
     * Execute the job.
     */
    abstract public function handle(): void;
}
