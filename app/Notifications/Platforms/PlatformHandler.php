<?php

declare(strict_types=1);

namespace App\Notifications\Platforms;

/**
 * class CrossPlatformHandler
 * @package App\Notifications\Platforms
 */
class PlatformHandler
{
    /**
     * @param array $nativeData
     * @param array $customData
     */
    public static function setNativeData(array &$nativeData, array &$customData): void
    {
        foreach ($nativeData as $nativeKey => &$nativeVal) {
            if (!array_key_exists($nativeKey, $customData)) {
                // Changing: $nativeKey => $customData[$nativeVal] (customVal)
                if (is_string($nativeVal) && array_key_exists($nativeVal, $customData)) {
                    $nativeData[$nativeKey] = $customData[$nativeVal];
                    unset($nativeData[$nativeVal]);
                }

                continue;
            }
            $nativeVal = $customData[$nativeKey];

            unset($customData[$nativeKey]);
        }

        $nativeData = array_filter($nativeData);
    }

    /**
     * @param array $nativeData
     * @param array $nativeDatesetDefaultValue
     */
    public static function setDefaultValue(array &$nativeData, array $nativeDatesetDefaultValue): void
    {
        foreach (array_keys($nativeData) as $nativeKey) {
            if (array_key_exists($nativeKey, $nativeDatesetDefaultValue)) {
                unset($nativeDatesetDefaultValue[$nativeKey]);
            }
        }

        $nativeData = array_filter(array_merge($nativeData, $nativeDatesetDefaultValue));
    }
}
