<?php

declare(strict_types=1);

namespace App\DTOs\Header;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * class ClientHeaderInfoDTO
 * @package App\DTOs\Header
 */
class ClientHeaderInfoDTO
{
    public const HEADER_PROPERTY_OS = 'x-os';
    public const HEADER_PROPERTY_OS_VERSION = 'x-os-version';
    public const HEADER_PROPERTY_APP_VERSION = 'x-app-version';
    public const HEADER_PROPERTY_DEVICE_ID = 'x-device-id';

    public const PREFIX_OS_IOS = 'IOS';
    public const PREFIX_OS_ANDROID = 'Android';

    public const HEADER_PROPERTIES = [
       self::HEADER_PROPERTY_OS,
       self::HEADER_PROPERTY_OS_VERSION,
       self::HEADER_PROPERTY_APP_VERSION,
       self::HEADER_PROPERTY_DEVICE_ID,
    ];

    public string $os;
    public string $osVersion;
    public string $appVersion;
    public string $deviceId;

    public function __construct(array $parameters = [])
    {
        foreach ($parameters as $headerProperty => $value) {
            $property = Str::camel(str_replace('x-', '', $headerProperty));
            $this->{$property} = $value;
        }
    }

    /**
     * @param Request $request
     * @return static
     */
    public static function makeFromRequest(Request $request): self
    {
        $properties = [];

        foreach (self::HEADER_PROPERTIES as $key => $headerProperty) {
            $headerPropertyValue = $request->headers->get($headerProperty);
            $properties[$headerProperty] = $headerPropertyValue;
        }

        return new self($properties);
    }
}
