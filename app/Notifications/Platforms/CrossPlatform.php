<?php

declare(strict_types=1);

namespace App\Notifications\Platforms;

use App\Notifications\Platforms\Android\AndroidPlatform;
use App\Notifications\Platforms\Apns\ApnsPlatform;
use App\Notifications\Platforms\Contracts\PlatformInterface;
use Kreait\Firebase\Messaging;

/**
 * class CrossDevice
 * @package App\Notifications\Devices
 */
class CrossPlatform
{
    /**
     * @var array|string[] $platforms
     */
    private array $platforms = [
        'android' => AndroidPlatform::class,
        'apns' => ApnsPlatform::class
    ];

    /**
     * @param array $data
     * @param string $type
     * @param array $config
     * @return Messaging\CloudMessage
     */
    public function makeFCMNotification(array $data, string $type, array $config = []): Messaging\CloudMessage
    {
        return Messaging\CloudMessage::fromArray($this->toArray($data, $type, $config));
    }

    /**
     * @param array $data
     * @param string $type
     * @param array $config
     * @return array
     */
    public function toArray(array $data, string $type, array $config = []): array
    {
        $platformsData = [];

        /** @var PlatformInterface $platform */
        foreach ($this->platforms as $key => $platform) {
            $platformsData[$key] = $platform::build($data, $type, $config);
        }

        return $platformsData;
    }
}
