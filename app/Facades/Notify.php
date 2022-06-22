<?php

declare(strict_types=1);

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * class Notify
 * @package App\Facades
 *
 * @method static store(int $notifierId, int $recipientId, string $type, array $data):
 */
class Notify extends Facade
{

    public const ACCESSOR = 'notify';

    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return self::ACCESSOR;
    }
}
