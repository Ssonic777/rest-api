<?php

declare(strict_types=1);

namespace App\Notifications\Platforms\Apns;

use App\Notifications\Platforms\Contracts\PlatformInterface;
use App\Notifications\Platforms\PlatformHandler;

/**
 * class ApnsPlatform
 * @package App\Notifications\Devices
 * Link https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#androidconfig
 */
class ApnsPlatform implements PlatformInterface
{

    /**
     * @var string|null $type
     */
    private static ?string $type;

    #region Native Default Values
    private static array $nativeHeadersDefaultVal = [

    ];

    private static array $nativeNotificationDefaultVal = [
        // Basic
        'title' => null,
        'body' => null,
        'image' => null,
    ];

    private static array $nativeAlertDefaultVal = [        // Into Data
        // Basic
        'title' => '',
        'subtitle' => '',
        'body' => ''
    ];

    private static array $nativeConfigDefaultVal = [
        // A remote notification payload for playing a sound
        'badge' => 1,
        'sound' => 'default'
    ];

    private static array $nativeCriticalDefaultVal = [
        'critical' => null,
        'name' => null,
        'volume' => null
    ];
    #endregion

    #region Native Values
    // Link: https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#apnsconfig
    private static array $nativeHeaders = [

    ];

    // Link: https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#androidnotification
    private static array $nativeNotification = [
        // Basic
        'title' => null,
        'body' => null,
        'image' => null,
    ];

    // Link: https://developer.apple.com/documentation/usernotifications/setting_up_a_remote_notification_server/generating_a_remote_notification
    private static array $nativeAlert =  [
        // Basic
        'title' => null,
        'subtitle' => null,
        'body' => null,

        // 'launch-image' => 'image',

        'title-loc-key' => null,
        'title-loc-args' => [

        ],

        'subtitle-loc-key' => null,
        'subtitle-loc-args' => [

        ],

        'loc-key' => null,
        'loc-args' => null
    ];

    private static array $data = [

    ];

    private static array $nativeConfig = [
        // A remote notification payload for playing a sound
        'badge' => null,
        'sound' => null,

        // Group
        'thread-id' => null,

        'category' => null,
        'content-available' => null,
        'mutable-content' => null,
        'target-content-id' => null,
        'interruption-level' => null,
        'relevance-score' => null,
    ];

    // Link: https://developer.apple.com/documentation/usernotifications/setting_up_a_remote_notification_server/generating_a_remote_notification#2990112
    private static array $nativeCritical = [
        'critical' => null,
        'name' => null,
        'volume' => null
    ];
    #endregion

    // Link: https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#androidfcmoptions
    private static array $fcmOptions = [
        'analytics_label' => null
    ];

    /**
     * @return array
     */
    public static function toArray(): array
    {
        return [
            'headers' => self::$nativeHeaders,
            'payload' => [
                'aps' => array_merge(
                    [
                        'alert' => self::$nativeAlert,
                        'data' => array_merge(['type' => self::$type], self::$data)
                    ],
                    self::$nativeConfig
                )
            ],
            'fcm_options' => self::$fcmOptions
        ];
    }

    /**
     * @param array $data
     * @param string $type
     * @param array $config
     * @return array
     */
    public static function build(array $data, string $type, array $config = []): array
    {
        self::setType($type);

        // Native Alert
        PlatformHandler::setNativeData(self::$nativeAlert, $data);
        PlatformHandler::setDefaultValue(self::$nativeAlert, self::$nativeAlertDefaultVal);

        // Native Config
        PlatformHandler::setNativeData(self::$nativeConfig, $config);
        PlatformHandler::setDefaultValue(self::$nativeConfig, self::$nativeConfigDefaultVal);

        self::setData($data);

        return self::toArray();
    }

    /**
     * @param string $type
     */
    private static function setType(string $type): void
    {
        self::$type = is_null(self::$nativeConfig['thread-id']) ? self::$nativeConfig['thread-id'] = $type : $type;
    }

    /**
     * @param array $data
     */
    private static function setData(array $data): void
    {
        self::$data = $data;
    }
}
