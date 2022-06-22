<?php

declare(strict_types=1);

namespace App\Notifications\Platforms\Android;

use App\Notifications\Platforms\Contracts\PlatformInterface;
use App\Notifications\Platforms\PlatformHandler;

/**
 * class AndroidPlatform
 * @package App\Notifications\Devices
 */
class AndroidPlatform implements PlatformInterface
{
    /**
     * @var string|null $type
     */
    private static ?string $type;

    #region Native Default Values
    private static array $nativeConfigDefaultVal = [

    ];

    // Link: https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#androidnotification
    private static array $nativeNotificationDefaultVal = [
        // Basic
        // 'title' => null,
        // 'body' => null,
        // 'image' => null,
    ];

    private static array $nativeNotificationConfigDefaultVal = [
        // Any
        // 'icon' => null,
        // 'color' => null,
        'sound' => false,
        // 'tag' => 'group',
        // 'click_action' => null,
        //
        // 'body_loc_key' => null,
        // 'body_loc_args' => [
        //
        // ],
        //
        // 'title_loc_key' => null,
        // 'title_loc_args' => [
        //
        // ],
        //
        // 'channel_id' => null,
        // 'ticker' => null,
        // 'sticky' => null,
        // 'event_time' => null,
        // 'local_only' => null,
        // 'notification_priority' => null,
        // 'default_sound' => null,
        // 'default_vibrate_timings' => null,
        // 'default_light_settings' => null,
        // 'vibrate_timings' => [
        //
        // ],
        // 'visibility' => null,
        // 'notification_count' => 5,
    ];
    #region

    #region Native Values
    // https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#androidconfig
    private static array $nativeConfig = [
        'collapse_key' => null,
        'priority' => null,
        'ttl' => null,
        'restricted_package_name' => null,
        'direct_boot_ok' => null
    ];

    // Link: https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#androidnotification
    private static array $nativeNotification = [
        // Basic
        // 'title' => null,
        // 'body' => null,
        // 'image' => null,
    ];

    private static array $nativeNotificationConfig = [
        // Any
        'icon' => null,
        'color' => null,
        'sound' => null,
        'tag' => 'group',
        'click_action' => null,

        'body_loc_key' => null,
        'body_loc_args' => [

        ],

        'title_loc_key' => null,
        'title_loc_args' => [

        ],

        'channel_id' => null,
        'ticker' => null,
        'sticky' => null,
        'event_time' => null,
        'local_only' => null,
        'notification_priority' => null,
        'default_sound' => null,
        'default_vibrate_timings' => null,
        'default_light_settings' => null,
        'vibrate_timings' => [

        ],
        'visibility' => null,
        'notification_count' => 5,
        // 'light_settings' => [
        //
        // ]
    ];

    private static array $nativeData = [

    ];

    // Link: https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#androidfcmoptions
    private static array $fcmOptions = [
        'analytics_label' => null
    ];
    #endregion

    /**
     * @return array
     */
    public static function toArray(): array
    {
        return array_merge(
            self::$nativeConfig,
            [
                'notification' => array_merge(
                    self::$nativeNotification,
                    // self::$nativeNotificationConfig
                ),
                'data' => array_merge(['type' => self::$type], self::$nativeData),
                'fcm_options' => self::$fcmOptions
            ]
        );
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

        // Native Config
        PlatformHandler::setNativeData(self::$nativeConfig, $data);
        PlatformHandler::setDefaultValue(self::$nativeConfig, self::$nativeConfigDefaultVal);

        // Native Notification
        // self::setNativeData(self::$nativeNotification, $data);
        // self::setDefaultValue(self::$nativeNotification, self::$nativeNotificationDefaultVal);

        // Native Notification Configs
        PlatformHandler::setNativeData(self::$nativeNotificationConfig, $config);
        PlatformHandler::setDefaultValue(self::$nativeNotificationConfig, self::$nativeNotificationConfigDefaultVal);

        self::setData($data);

        return self::toArray();
    }

    private static function setType(string $type): void
    {
        self::$type = $type;
    }

    /**
     * @param array $data
     */
    private static function setData(array $data): void
    {
        foreach ($data as $key => $value) {
            self::$nativeData[$key] = (string) $value;
        }
    }
}
