<?php

declare(strict_types=1);

namespace App\Notifications\Platforms\Contracts;

interface PlatformInterface
{

    /**
     * @param array $data
     * @param string $type
     * @param array $config
     * @return array
     */
    public static function build(array $data, string $type, array $config = []): array;
}
